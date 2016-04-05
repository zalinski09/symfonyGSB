<?php


namespace GSB\AppBundle\Service;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use GSB\AppBundle\Entity\FicheFrais;
use GSB\AppBundle\Entity\LigneFraisForfait;
use GSB\AppBundle\Entity\LigneFraisHorsForfait;
use GSB\AccountBundle\Entity\Visiteur;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\Form;

class FicheFraisService {

    /** @var  Container */
    private $container;

    /** @var  EntityManager */
    private $em;


    public function __construct(Container $container){

        $this->container = $container;
        $this->em = $container->get('doctrine')->getManager();
    }


    /**
     * Cette fonction permet de vérifier ou non
     * l'existence d'une fiche de frais.
     * Effectue les MAJ nécessaires
     * Changement etat ou creation
     * @param $visiteur le visiteur en session
     * @param $dateActuelle
     */
    public function verifyFicheDeFrais($visiteur,$dateActuelle)
    {
        //1. Trouver la fdf pour le mois courant
        //2. Si la fdf existe pas, alors on est sur le 1er insert de lignes du mois
        //   donc, on ajoute une fiche pour le mois courant, AVANT d'ajouter une ligne.
        $ficheDeFrais = $this->em->getRepository('GSBAppBundle:FicheFrais')
            ->findOneBy(array(
                'visiteur' => $visiteur,
                'mois' => $dateActuelle
            ));

        if(!$ficheDeFrais){
            /** @var FicheFrais $moisFicheFraisPrecedent */
            $moisFicheFraisPrecedent = $this->em
                ->getRepository('GSBAppBundle:Fichefrais')
                ->getMaxMois(array(
                    'visiteur' => $visiteur,
                ));
            //Création d'une nouvelle ficheDeFrais
            $mois = $dateActuelle;
            $etat = $this->em->getRepository('GSBAppBundle:Etat')->find('CR');
            $nouvelleFicheDeFrais = new FicheFrais();
            $nouvelleFicheDeFrais->setVisiteur($visiteur);
            $nouvelleFicheDeFrais->setMois($mois);
            $nouvelleFicheDeFrais->setEtat($etat);
            $nouvelleFicheDeFrais->setDatemodif(new \DateTime('now'));
            $this->em->persist($nouvelleFicheDeFrais);
            $this->em->flush();

        }
    }


    /**
     * Fonction qui recupère tous les fraisforfaits
     * et qui verifie pour chaque fraisforfait si une LigneFraisForfait
     * existe pour le $visiteur et la $dateActuelle passés en paramètres.
     * Si il existe une ligne, on l'a recupère, sinon on en créer une nouvelle
     * avec le $fraisforfait concerné pour le $visiteur et la $dateActuelle donnés.
     * Retourne une collection lignesFraisForfait.
     * @param Visiteur $visiteur
     * @param  \DateTime $dateActuelle
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getFormCollectionLigneFrais($visiteur, $dateActuelle)
    {
        $fraisforfaits = $this->em->getRepository('GSBAppBundle:FraisForfait')->findAll();
        $lignesfraisforfait = new ArrayCollection();


        foreach($fraisforfaits as $fraisforfait)
        {
            //On check si une ligne existe déjà pour le visiteur en cours et le mois en cours et le fraisforfait parcouru
            $checkLigneExistante = $this->em->getRepository('GSBAppBundle:LigneFraisForfait')->findOneBy(
                array(
                    'visiteur' =>$visiteur,
                    'mois' => $dateActuelle,
                    'fraisForfait' => $fraisforfait
                )
            );

            //Si il n'y en a pas d'existante
            if(!$checkLigneExistante){
                $lignefraisforfait = new LigneFraisForfait($visiteur, $dateActuelle, $fraisforfait);
            }else{
                $lignefraisforfait = $checkLigneExistante;
            }
            //On ajoute a la collection la ligne nouvellement créee ou récupérée
            $lignesfraisforfait->add($lignefraisforfait);
        }
        return $lignesfraisforfait;
    }

    /**
     * Fonction de calcule des fraisForfait.
     * Recoit en paramètre le formContainer ou une ligneFraisForfait.
     * Verification du type d'instance d'objet passé en paramètre
     * Suivant le type on calcule le $total des fraisForfait.
     * Retourne $total
     * @param $formContainer
     * @return int
     */
    public function calculerTotalFraisForfaits($formContainer) {
        $total = 0;
        if($formContainer instanceof Form) {
            /** @var LigneFraisForfait $lignefraisforfait */
            foreach ($formContainer->get('lignesfraisForfait')->getData() as $lignefraisforfait) {
                $montant = $lignefraisforfait->getQuantite() * $lignefraisforfait->getFraisForfait()->getMontant();
                $total = $total + $montant;
            }
        }
        else
            /** @var LigneFraisForfait $lignefraisforfait */
            foreach($formContainer as $lignefraisforfait)
            {
                $montant = $lignefraisforfait->getQuantite() * $lignefraisforfait->getFraisForfait()->getMontant();
                $total = $total + $montant;
            }
        return $total;
    }

    /**
     * Fonction de calcule des lignesFraisHorsForfaits
     * @param $lignesFraisHorsForfaits
     * @return int
     */
    public function calculerTotalFraisHorsForfaits($lignesFraisHorsForfaits){

        $total = 0;
        /** @var LigneFraisHorsForfait $ligneFraisHorsForfait */
        foreach($lignesFraisHorsForfaits as $ligneFraisHorsForfait)
        {
            $montant = $ligneFraisHorsForfait->getMontant();
            $total = $total + $montant;
        }
        return $total;
    }
} 