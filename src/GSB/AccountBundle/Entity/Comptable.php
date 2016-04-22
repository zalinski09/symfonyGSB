<?php

namespace GSB\AccountBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Comptable
 *
 * @ORM\Table(name="comptable")
 * @ORM\Entity
 */
class Comptable implements UserInterface,  \Serializable
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=4, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=30, nullable=true)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=30, nullable=true)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=20, nullable=true)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="mdp", type="string", length=20, nullable=true)
     */
    private $mdp;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="text", nullable=false)
     */
    private $salt;

    public function __construct()
    {
        $this->salt = md5(uniqid(null, true));
    }

    /**
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @param string $prenom
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    /**
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * @return string
     */
    public function getMdp()
    {
        return $this->mdp;
    }

    /**
     * @param string $mdp
     */
    public function setMdp($mdp)
    {
        $this->mdp = $mdp;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }


    public function setUsername($login)
    {
        $this->login = $login;
    }

    public function setPassword($mdp)
    {
        $this->mdp = $mdp;
    }

    public function getRoles()
    {
        return array('ROLE_COMPTABLE');
    }


    public function getPassword()
    {
        return $this->mdp;
    }

    public function getUsername()
    {
        return $this->login;
    }

    public function eraseCredentials(){
    }

     /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->login,
            $this->mdp,
            $this->salt
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->login,
            $this->mdp,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }
    
    public function getSalt()
    {
        return $this->salt;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    public function __toString(){

        return $this->id;
    }
}
