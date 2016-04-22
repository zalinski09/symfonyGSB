<?php

namespace GSB\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use GSB\AccountBundle\Entity\Visiteur;
use GSB\AppBundle\Entity\Etat;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * FicheFrais
 * @ORM\Table(name="fichefrais", indexes={@ORM\Index(name="idEtat", columns={"idEtat"}), @ORM\Index(name="IDX_92D5AB081D06ADE3", columns={"idVisiteur"})})
 * @ORM\Entity(repositoryClass="GSB\AppBundle\Entity\FicheFraisRepository")
 */
class FicheFrais
{

    /**
     * @var Visiteur
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="GSB\AccountBundle\Entity\Visiteur")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idVisiteur", referencedColumnName="id")
     * })
     */
    private $visiteur;

    /**
     * @var string
     *
     * @ORM\Column(name="mois", type="string", length=6, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    private $mois;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbJustificatifs", type="integer", nullable=true)
     */
    private $nbjustificatifs;

    /**
     * @var string
     *
     * @ORM\Column(name="montantValide", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $montantvalide;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateModif", type="datetime", nullable=true)
     */
    private $datemodif;

    /**
     * @var Etat
     *
     * @ORM\ManyToOne(targetEntity="GSB\AppBundle\Entity\Etat")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idEtat", referencedColumnName="id")
     * })
     */
    private $etat;

    public function __construct(){
        $this->nbjustificatifs = 0;
        $this->montantvalide = 0;
    }
    
    /**
     * @return \DateTime
     */
    public function getDatemodif()
    {
        return $this->datemodif;
    }

    /**
     * @param \DateTime $datemodif
     */
    public function setDatemodif($datemodif)
    {
        $this->datemodif = $datemodif;
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
     * @return Etat
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param Etat $etat
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;
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

    /**
     * @return string
     */
    public function getMontantvalide()
    {
        return $this->montantvalide;
    }

    /**
     * @param string $montantvalide
     */
    public function setMontantvalide($montantvalide)
    {
        $this->montantvalide = $montantvalide;
    }

    /**
     * @return int
     */
    public function getNbjustificatifs()
    {
        return $this->nbjustificatifs;
    }

    /**
     * @param int $nbjustificatifs
     */
    public function setNbjustificatifs($nbjustificatifs)
    {
        $this->nbjustificatifs = $nbjustificatifs;
    }

    /**
     * Permet la conversion en string
     * @return string
     */
    public function __toString()
    {
       return $this->getMois();
    }
}
