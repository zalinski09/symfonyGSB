<?php


namespace GSB\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use GSB\AppBundle\Entity\FicheFrais;
use GSB\AppBundle\Entity\LigneFraisForfait;
use GSB\AppBundle\Entity\LigneFraisHorsForfait;
use GSB\AppBundle\Form\FicheFraisType;
use GSB\AppBundle\Form\FicheFraisValidationType;
use GSB\AppBundle\Form\FichesFraisType;
use GSB\AppBundle\Form\LigneFraisForfaitContainerType;
use GSB\AppBundle\Entity\Etat;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class FicheFraisController
 * @package GSB\AppBundle\Controller
 */
class FicheFraisController extends Controller {

    /**
     * Action de controller qui gère la récuperation
     * d'une fiche de frais en fonction du mois choisis
     * par le visiteur
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
            // Sinon on déclenche une exception « Accès interdit »
            return $this->render('GSBAppBundle:Security:security.html.twig');
        }
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $visiteur = $this->getUser();

        $formFicheFrais = $this->get('form.factory')->create(new FicheFraisType($visiteur,$em));
        $ligneFraisForfait = $lignesFraisHorsForfait = $mois = $etat = $dateModification = null;

        $total = 0;
        $droitSuppression = false;

        if($formFicheFrais->handleRequest($request)->isValid())
        {
            /** @var FicheFrais $ficheFrais */
            $ficheFrais = $em->getRepository('GSBAppBundle:Fichefrais')->findOneBy(
                array(
                    'mois' => $formFicheFrais->get('mois')->getData(),
                    'visiteur' => $visiteur
                )
            );

            $etat = $ficheFrais->getEtat();
            $dateModification = $ficheFrais->getDatemodif()->format('d/m/Y');

            $ligneFraisForfait = $em->getRepository('GSBAppBundle:LigneFraisForfait')->findBy(array(
                    'mois' => $formFicheFrais->get('mois')->getData(),
                    'visiteur' => $visiteur)
            );

            $lignesFraisHorsForfait = $em->getRepository('GSBAppBundle:Lignefraishorsforfait')->findBy(array(
                    'mois' => $formFicheFrais->get('mois')->getData(),
                    'visiteur'=>$visiteur)
            );
            //Calcule du total de la ficheDeFrais, lignefraisForfait et ligneFraisHorsForfait compris et persist
            $total = $this->get('gsb_app.fichefrais')->calculerTotalFraisForfaits($ligneFraisForfait) +
                $this->get('gsb_app.fichefrais')->calculerTotalFraisHorsForfaits($lignesFraisHorsForfait);
            $ficheFrais->setMontantvalide($total);

            $em->persist($ficheFrais);
            $em->flush();
            //Récupération du mois si la ligne fraisforfait existe pour l'envoyer dans la vue.
            if($ligneFraisForfait != null)
                $mois = $ligneFraisForfait[0]->getMois();
        }

        return $this->render('GSBAppBundle::etatFicheDeFrais.html.twig', array(
            'form' => $formFicheFrais->createView(),
            'LigneFraisForfait' => $ligneFraisForfait,
            'lignesfraishorsforfait' => $lignesFraisHorsForfait,
            'mois' => $mois,
            'etat' => $etat,
            'dateModif' => $dateModification,
            'suppression' => $droitSuppression,
            'total' => $total
        ));
    }

    /**
     * Action de controller en tant qu'utilisateur comptable identifié
     * Permet de récupérer une fiche de frais en fonction du visiteur
     * et du mois choisis. Gère la soumission d'un nouveau form
     * LigneFraisForfait en cas de modification et le refus d'une lignefraishorsforfait
     * Validation de la ficheDeFrais avec enregistrement de la date de modification
     * et du nombre de justificatifs reçus par la comptabilite.
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function validerAction(Request $request){
        //On verifie directement ici le rôle, l'annotation @Security ne permettant pas pour l'instant
        //Le display d'un message d'erreur
        if (!$this->get('security.context')->isGranted('ROLE_COMPTABLE')) {
            // Sinon on déclenche une exception « Accès interdit »
            return $this->render('GSBAppBundle:Security:security.html.twig');
        }
        //Initialisations
        $em = $this->getDoctrine()->getManager();
        $modify = false; //Booleen pour autoriser dans le cas d'un comptable la modif ou non
        $ficheFraisSelectionnee = $lignesfraisforfait = $lignesfraishorsforfait = $formValidationFicheFrais =  $ficheFraisValidation = $visiteur = null;
        $formSelectVisiteur = $this->get('form.factory')->create(new FichesFraisType($em));
        $formContainer = $this->get('form.factory')->create(new LigneFraisForfaitContainerType());
        $formValidationFicheFrais = $this->get('form.factory')->create(new FicheFraisValidationType());
        $total = 0;

        if('POST' == $request->getMethod()) {

            if ($request->request->has('gsb_appbundle_FichesFrais')) {
                if ($formSelectVisiteur->handleRequest($request)->isValid()) {
                    //Récuperation du visiteur et de la date choisi dans le form
                    $lignesfraisforfait = new ArrayCollection();
                    $dateSelectionee = $formSelectVisiteur->get('mois')->getData();
                    $visiteur = $formSelectVisiteur->get('visiteur')->getData();
                    /** @var FicheFrais $ficheFraisSelectionnee */
                    $ficheFraisSelectionnee = $em->getRepository('GSBAppBundle:FicheFrais')->findOneBy(array('visiteur' => $visiteur, 'mois' => $dateSelectionee));

                    if(!$ficheFraisSelectionnee)
                    {
                        $this->get('gsb_app.fichefrais')->verifyFicheDeFrais($visiteur,$dateSelectionee);
                    }

                    $ficheFraisValidation = $ficheFraisSelectionnee;
                    $modify = $this->allowModifyFicheFrais($visiteur, $dateSelectionee, $em);
                    //Récuperation des fraisforfaits et horsforfaits correspondants au visiteur et à la date précédemment séléctionnés.
                    $lignesfraisforfait = $this->get('gsb_app.fichefrais')->getFormCollectionLigneFrais($visiteur, $dateSelectionee);
                    $lignesfraishorsforfait = $this->getDoctrine()->getRepository('GSBAppBundle:LigneFraisHorsForfait')->findBy(array('visiteur' => $visiteur, 'mois' => $dateSelectionee));

                    //Calcule du total de la ficheDeFrais, lignefraisForfait et ligneFraisHorsForfait compris et persist
                    $total = $this->get('gsb_app.fichefrais')->calculerTotalFraisForfaits($lignesfraisforfait) +
                        $this->get('gsb_app.fichefrais')->calculerTotalFraisHorsForfaits($lignesfraishorsforfait);
                    $ficheFraisSelectionnee->setMontantvalide($total);
                    $em->persist($ficheFraisSelectionnee);
                    $em->flush();


                    $formValidationFicheFrais->setData($ficheFraisValidation);
                    $formContainer->get('lignesfraisForfait')->setData($lignesfraisforfait);
                }
            }
            if ($request->request->has('gsb_appbundle_fraisforfaitcontainer')){
                if ($formContainer->handleRequest($request)->isValid()) {
                    //On persist chaque fraisforfait de la collection du formContainer
                    /** @var LigneFraisForfait $lignefraisforfait */
                    $this->modifyLigneFraisForfait($formContainer,$formSelectVisiteur, $em);
                    $request->getSession()->getFlashBag()->add('info', 'Frais bien modifiés');
                }
            }
            if($request->request->has('gsb_app_FicheFraisValidation'))
            {
                if($formValidationFicheFrais->handleRequest($request)->isValid())
                {
                    /** @var FicheFrais $ficheTmp */
                    $ficheTmp = $formValidationFicheFrais->getData();
                    $em->merge($ficheTmp);
                    $em->flush();
                    $request->getSession()->getFlashBag()->add('info', 'Fiche de frais bien validée');
                }

            }
        }
        return $this->render('GSBAppBundle::validerFicheFrais.html.twig' ,array(
            'formSelect' => $formSelectVisiteur->createView(),
            'formFrais' => $formContainer->createView(),
            'lignesfraisforfait' => $lignesfraisforfait,
            'lignesfraishorsforfait' => $lignesfraishorsforfait,
            'modify' => $modify,
            'formValidation' =>$formValidationFicheFrais->createView(),
            'ficheFraisConcernee' => $ficheFraisSelectionnee
        ));
    }

    /**
     * Fonction de factorisation qui permet de persist
     * les lignesfraisforfait modifiées par le comptable.
     * On récupère pour cela le visiteur et le mois sélectionné
     * au préalable dans le formSelectVisiteur pour persist les données
     * au visiteur voulu pour le mois voulu.
     * @param $formContainer
     * @param $formSelectVisiteur
     * @param $em
     */
    private function modifyLigneFraisForfait($formContainer, $formSelectVisiteur){

        $em = $this->get('doctrine')->getManager();
        /** @var LigneFraisForfait $lignefraisforfait */
        foreach ($formContainer->get('lignesfraisForfait')->getData() as $lignefraisforfait) {
            //Récupération du visiteur et du mois selectionné dans le premier form, qu'on set sur la ligneFraisForfait contenu dans le formContainer
            $formSelectVisiteur->get('visiteur')->setData($lignefraisforfait->getVisiteur());
            $formSelectVisiteur->get('mois')->setData($lignefraisforfait->getMois());

            //On récupère les lignes qui concernent le visiteur et le mois concerné
            $lignesfraisforfait = $this->get('gsb_app.fichefrais')->getFormCollectionLigneFrais($lignefraisforfait->getVisiteur(),$lignefraisforfait->getMois());

            //Pareil mais pour les horsforfait
            $lignesfraishorsforfait = $this->getDoctrine()->getRepository('GSBAppBundle:LigneFraisHorsForfait')->findBy(array(
                'visiteur' => $lignefraisforfait->getVisiteur(),
                'mois' => $lignefraisforfait->getMois()
            ));

            $em->merge($lignefraisforfait);//On merge parceque le persist n'arrive pas a update (à cause de la cle composée)
            $em->flush();
        }
    }



    /**
     * Permet de gérer si oui ou non le comptable
     * peut modifier la ficheDeFrais en fonction de l'ID
     * de l'Etat de cette fiche. En paramètre le visiteur
     * et la date pour récuperer la ficheDeFraisSelectionnee.
     * Si on a une ficheDeFrais on récupère son ID et si c'est à CL, donc
     * cloturée, on autorise la modification. Dans tous les autres cas
     * on ne peut valider et donc modifier une fiche.
     * @param $visiteur
     * @param $dateSelectionee
     * @param $em
     * @return bool
     */
    public function allowModifyFicheFrais($visiteur, $dateSelectionee, $em)
    {
        if (!$this->get('security.context')->isGranted('ROLE_COMPTABLE')) {
            // Sinon on déclenche une exception « Accès interdit »
            return $this->render('GSBAppBundle:Security:security.html.twig');
        }
        $modify = false;

        $ficheFraisSelectionnee = $em->getRepository('GSBAppBundle:FicheFrais')->findBy(array('visiteur' => $visiteur, 'mois' => $dateSelectionee));
        if ($ficheFraisSelectionnee){
            $etatFicheFraisSelectionnee = $ficheFraisSelectionnee[0]->getEtat()->getId();

            if ($etatFicheFraisSelectionnee == 'CL') {

                $modify = true;
            }
        }
        return $modify;
    }

    /**
     * Action qui permet de récupérer le fraisHorsForfait selectionné
     * et de mettre devant le libelle REFUSE
     * Calcule et met à jour le montantValidé dans la base
     * Fais appelle à la fonction verifyFicheDeFrais
     * Pour créer une nouvelle fiche dans laquelle reportée
     * La ligneFraisHorsForfait refusée
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function refuserAction(Request $request, $id){

        if (!$this->get('security.context')->isGranted('ROLE_COMPTABLE')) {
            // Sinon on déclenche une exception « Accès interdit »
            return $this->render('GSBAppBundle:Security:security.html.twig');
        }
        $em = $this->getDoctrine()->getEntityManager();
        $ligneFraisHorsForfait = $em->getRepository('GSBAppBundle:LigneFraisHorsForfait')->find($id);
        $mois = $ligneFraisHorsForfait->getMois();
        $etat = $em->getRepository('GSBAppBundle:Etat')->find('VA');
        $total = 0;
        $ligneFraisHorsForfait->setLibelle('REFUSE - '.$ligneFraisHorsForfait->getLibelle());

        $visiteur = $ligneFraisHorsForfait->getVisiteur();
        /** @var FicheFrais $ficheFrais */
        $ficheFrais = $this->getDoctrine()->getManager()->getRepository('GSBAppBundle:FicheFrais')->findBy(array('visiteur' => $visiteur, 'mois' => $mois));



        //Permet de mettre à jour le montantvalide de la ficheDeFrais suivant le montant de la ligneFraisHorsForfait refusée
        $ficheFrais[0]->setMontantvalide($ficheFrais[0]->getMontantvalide() - $ligneFraisHorsForfait->getMontant());
        $ficheFrais[0]->setEtat($etat);
        $em->persist($ficheFrais[0]);
        $em->flush();
        //TODO envoyer mois + 1 pour créer la fiche ( exemple : 201505 + 000001 ) mais on travail sur un string
        //$ligneFraisHorsForfait->setMois();
        $this->get('gsb_app.fichefrais')->verifyFicheDeFrais($visiteur,$mois);

        return $this->redirect($this->generateUrl('gsb_app_validerFicheDeFrais'));
    }

    public function suiviAction(Request $request)
    {
        if (!$this->get('security.context')->isGranted('ROLE_COMPTABLE')) {
            // Sinon on déclenche une exception « Accès interdit »
            return $this->render('GSBAppBundle:Security:security.html.twig');
        }
        $em = $this->getDoctrine()->getManager();
        $formFicheFraisValide = $this->get('form.factory')->create(new FichesFraisType($em,'true'));

        return $this->render('GSBAppBundle::suiviPaiementFicheFrais.html.twig');
    }
}