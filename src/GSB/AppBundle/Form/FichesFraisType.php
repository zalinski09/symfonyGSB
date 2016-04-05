<?php

namespace GSB\AppBundle\Form;

use Doctrine\ORM\EntityManager;
use GSB\AppBundle\Entity\FicheFrais;
use GSB\AccountBundle\Entity\Visiteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FichesFraisType
 * @package GSB\AppBundle\Form
 */
class FichesFraisType extends AbstractType
{
    /** @var \Doctrine\ORM\EntityManager $em */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct($em){
        $this->em = $em;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $choices = $this->em->getRepository('GSBAppBundle:FicheFrais')->getMois();
        $builder
            ->add('mois' ,'choice', array(
                    'choices' => $choices,
                    'multiple'=>false,
                    'expanded'=>false)
            )
            ->add('visiteur', 'entity', array(
                'class' => 'GSB\AccountBundle\Entity\Visiteur',
                'property' => 'nom'
            ));

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(//            'data_class' => 'GSB\AppBundle\Entity\FicheFrais'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gsb_appbundle_FichesFrais';
    }
}