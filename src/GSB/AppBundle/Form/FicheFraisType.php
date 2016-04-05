<?php

namespace GSB\AppBundle\Form;

use Doctrine\ORM\EntityManager;
use GSB\AppBundle\Entity\FicheFrais;
use GSB\AccountBundle\Entity\Visiteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


/**
 * Class FicheFraisType
 * @package GSB\AppBundle\Form
 */
class FicheFraisType extends AbstractType
{
    /** @var Visiteur $visiteur */
    private $visiteur;

    /** @var \Doctrine\ORM\EntityManager $em */
    private $em;

    /**
     * @param Visiteur $visiteur
     * @param EntityManager $em
     */
    public function __construct($visiteur, $em){
        $this->visiteur = $visiteur;
        $this->em = $em;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //Récupération des fiches de frais concernant le visiteur
        $choices = $this->em->getRepository('GSBAppBundle:FicheFrais')->getMoisFicheDeFrais($this->visiteur);
        $builder
            ->add('mois' ,'choice', array(
                    'choices' => $choices,
                    'multiple'=>false,
                    'expanded'=>false)
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
//            'data_class' => 'GSB\AppBundle\Entity\FicheFrais'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gsb_appbundle_Fichefrais';
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
}