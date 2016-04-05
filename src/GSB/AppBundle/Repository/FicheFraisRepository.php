<?php

namespace GSB\AppBundle\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use GSB\AccountBundle\Entity\Visiteur;

/**
 * fichefraisRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FicheFraisRepository extends EntityRepository
{

    /** Fonction qui permet suivant le visiteur
     * passé en paramètre de récupérer
     * le mois max d'une fiche de Frais
     * c'est à dire le dernier mois pour lequel
     * on a une fiche de frais
     * @param $visiteur
     * @return array
     */
    public function getMaxMois($visiteur)
    {

        $em = $this->getEntityManager();
        /** @var QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('f', $qb->expr()->max('f.mois'))
            ->from('GSBAppBundle:Fichefrais', 'f')
            ->where($qb->expr()->eq('f.visiteur', ':visiteur_id'))
            ->setParameter('visiteur_id', 'a17');

        return $qb->getQuery()
            ->getResult();
    }

    /** Fonction qui permet pour
     *  le visiteur passé en paramètre de récupérer
     *  tous les mois pour lequels on a bien une fiche de frais
     * @param \GSB\AccountBundle\Entity\Visiteur $visiteur
     * @return array
     */
    public function getMoisFicheDeFrais(Visiteur $visiteur)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('f.mois')
            ->from($this->_entityName, 'f')
            ->andWhere('f.visiteur = :visiteur')
            ->orderBy('f.mois', 'DESC')
            ->setParameter('visiteur', $visiteur);

        $array_result = $qb->getQuery()
            ->getResult();

        return $this->stringConversionMois($array_result);
    }


    /**
     * Fonction qui permet de récupérer
     * tous les mois (sans doublon) pour lesquels
     * une fiche de frais existe
     * @return array
     */
    public function getMois()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('DISTINCT f.mois')
            ->from($this->_entityName, 'f')
            ->orderBy('f.mois', 'DESC');
        $array_result = $qb->getQuery()->getResult();

        return $this->stringConversionMois($array_result);
    }

//    public function getMoisFicheFraisValid()
//    {
//        $em = $this->getEntityManager();
//        $qb = $em->createQueryBuilder()
//            ->select('DISTINCT f.mois')
//            ->from($this->_entityName, 'f')
//            ->andWhere('f.etat = :VA')
//            ->orderBy('f.mois', 'DESC');
//        $array_result = $qb->getQuery()->getResult();
//
//        return $this->stringConversionMois($array_result);
//    }

    /**
     * Fonction de conversion des mois en string
     * @param $array_result
     * @return array
     */
    private function stringConversionMois($array_result)
    {
        $arr = array();
        foreach ($array_result as $key => $subArray) {
            $year = substr($subArray['mois'], 0, 4);
            $month = substr($subArray['mois'], 4, 6);
            $arr[$subArray['mois']] = $month . '/' . $year;
        }
        return $arr;
    }
}

