<?php


namespace GSB\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Collections\ArrayCollection;
use GSB\AppBundle\Entity\LigneFraisForfait;
use GSB\AppBundle\Entity\LigneFraisHorsForfait;
use GSB\AppBundle\Form\LigneFraisForfaitContainerType;
use GSB\AppBundle\Form\LigneFraisHorsForfaitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class FraisForfaitController extends Controller {

    /**
     * Action de controller qui gère la saisie des frais
     * par un visiteur authentifié.
     * Création des forms nécessaires, puis hydratation du formContainer pour
     * les lignesfraisforfaits. Calcule du total des frais et
     * appel de verifyFicheDeFrais. Au submit, si le form est valide
     * dans le cas d'une ligneFraisForfait comme d'une ligneFraisHorsForfait
     * on persist les données saisies et rendering.
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
            // Sinon on déclenche une exception « Accès interdit »
            return $this->render('GSBAppBundle:Security:security.html.twig');
        }
        //Initialisations
        $formContainer = $this->get('form.factory')->create(new LigneFraisForfaitContainerType());
        $dateActuelle = new \DateTime('now');
        $dateActuelle = $dateActuelle->format('Ym');
        $visiteur = $this->getUser();
        $Lignefraishorsforfait = new LigneFraisHorsForfait();
        $Lignefraishorsforfait->setMois($dateActuelle);
        $Lignefraishorsforfait->setVisiteur($visiteur);
        $lignesfraisforfait = new ArrayCollection();
        $lignesfraisforfait =  $this->get('gsb_app.fichefrais')->getFormCollectionLigneFrais($visiteur,$dateActuelle);

        //Hydratation du formulaire de fraisforfait
        $formContainer ->get('lignesfraisForfait')->setData($lignesfraisforfait);
        //Appel des fonctions après hydratation
        $total = $this->get('gsb_app.fichefrais')->calculerTotalFraisForfaits($formContainer);
        $this->get('gsb_app.fichefrais')->verifyFicheDeFrais($visiteur,$dateActuelle);
        //Création du formulaire des hors forfaits
        $formHorsForfait = $this->get('form.factory')->create(new LigneFraisHorsForfaitType(),$Lignefraishorsforfait);

        if('POST' == $request->getMethod())
        {
            if($request->request->has('gsb_appbundle_fraisforfaitcontainer'))
                if ($formContainer->handleRequest($request)->isValid()) {
                    $em = $this->get('doctrine')->getManager();
                    //On persist chaque fraisforfait de la collection du formContainer
                    /** @var LigneFraisForfait $lignefraisforfait */
                    foreach( $formContainer->get('lignesfraisForfait')->getData() as $lignefraisforfait) {
                        $em->merge($lignefraisforfait);//On merge parceque le persist n'arrive pas a update (à cause de la cle composée)
                        $total = $this->get('gsb_app.fichefrais')->calculerTotalFraisForfaits($formContainer);
                    }
                    $em->flush();
                    $request->getSession()->getFlashBag()->add('info', 'Frais bien enregistrés');
                }
            if($request->request->has('gsb_appbundle_lignefraishorsforfait'))
                if ($formHorsForfait->handleRequest($request)->isValid()) {
                    $em = $this->get('doctrine')->getManager();
                    $em->merge($Lignefraishorsforfait);
                    $em->flush();
                }
        }
        return $this->render('GSBAppBundle::saisieFrais.html.twig', array(
                'form'=> $formContainer->createView(),
                'formHorsForfait' => $formHorsForfait->createView(),
                'lignefraishorsforfait' => $Lignefraishorsforfait,
                'total' => $total)
        );
    }
}