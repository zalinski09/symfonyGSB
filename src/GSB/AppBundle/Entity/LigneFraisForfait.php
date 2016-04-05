<?php

namespace GSB\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use GSB\AccountBundle\Entity\Visiteur;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * LigneFraisForfait
 *
 * @ORM\Table(name="lignefraisforfait")
 * @ORM\Entity(repositoryClass="GSB\AppBundle\Repository\LigneFraisForfaitRepository")
 */
class LigneFraisForfait
{
    /**
     * @var Visiteur
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="GSB\AccountBundle\Entity\Visiteur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idVisiteur", referencedColumnName="id")
     * })
     */
    private $visiteur;

    /**
     * @var FraisForfait
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="GSB\AppBundle\Entity\FraisForfait")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idFraisForfait", referencedColumnName="id")
     * })
     */
    private $fraisForfait;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="mois", type="string")
     */
    private $mois;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantite", type="integer")
     * @Assert\Type(type="integer", message="La valeur {{ value }} n'est pas un type valide pour une quantite.")
     */
    private $quantite;

    /**
     * @param $visiteur
     * @param \DateTime $mois
     * @param $fraisForfait
     */
    public function __construct($visiteur = null,$mois = null,$fraisForfait = null)
    {
//        dump($mois);
//        dump($mois->format('Y-m'));
//        die();
        $this->visiteur = $visiteur;

        if($mois instanceof \DateTime){
            $this->mois = $mois->format('Ym');
        }else{
            $this->mois = $mois;
        }

        $this->fraisForfait = $fraisForfait;
    }

    /**
     * Set quantite
     *
     * @param integer $quantite
     * @return LigneFraisForfait
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;
        return $this;
    }

    /**
     * Get quantite
     *
     * @return integer 
     */
    public function getQuantite()
    {
        return $this->quantite;
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
     * @return FraisForfait
     */
    public function getFraisForfait()
    {
        return $this->fraisForfait;
    }

    /**
     * @param FraisForfait $fraisForfait
     */
    public function setFraisForfait($fraisForfait)
    {
        $this->fraisForfait = $fraisForfait;
    }



    /**
     * @return mixed
     */
    public function getVisiteur()
    {
        return $this->visiteur;
    }

    /**
     * @param mixed $visiteur
     */
    public function setVisiteur($visiteur)
    {
        $this->visiteur = $visiteur;
    }



}
