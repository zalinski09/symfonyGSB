<?php

namespace GSB\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use GSB\AccountBundle\Entity\Visiteur;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * LigneFraisHorsForfait
 *
 * @ORM\Table(name="lignefraishorsforfait")
 * @ORM\Entity(repositoryClass="GSB\AppBundle\Entity\LigneFraisHorsForfaitRepository")
 */
class LigneFraisHorsForfait 
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=100, nullable=true)
     */
    private $libelle;

    /**
     * @var \DateTime
     * @Assert\GreaterThanOrEqual("-1 year", message="La date doit avoir moins d'un an")
     * @Assert\DateTime(message="Valeur non valide : indiquer une date correct")
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var String
     * @ORM\Column(name="mois", type="string")
     */
    private $mois;

    /**
     * @var String
     *
     * @ORM\Column(name="montant", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $montant;

    /**
     * @var Visiteur
     * @ORM\ManyToOne(targetEntity="GSB\AccountBundle\Entity\Visiteur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idVisiteur", referencedColumnName="id")
     * })
     */
    private $visiteur;

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * @param string $libelle
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
    }

    /**
     * @return Visiteur
     */
    public function getVisiteur()
    {
        return $this->visiteur;
    }

    /**
     * @param Visiteur $visiteur
     */
    public function setVisiteur($visiteur)
    {
        $this->visiteur = $visiteur;
    }



    /**
     * @return string
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * @param string $montant
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;
    }

    /**
     * @return string
     */
    public function getMois()
    {
        return $this->mois;
    }

    /**
     * @param string $mois
     */
    public function setMois($mois)
    {
        $this->mois = $mois;
    }
}
