<?php

namespace GSB\AppBundle\Form;

use Doctrine\ORM\EntityManager;
use GSB\AppBundle\Entity\FicheFrais;
use GSB\AccountBundle\Entity\Visiteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FicheFraisValidationType
 * @package GSB\AppBundle\Form
 */
class FicheFraisValidationType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nbjustificatifs')
            ->add('mois')
            ->add('visiteur', 'entity', array(
                'class' => 'GSB\AccountBundle\Entity\Visiteur',
                'property' => 'nom'))
            ->add('datemodif')
            ->add('etat','entity', array(
                'class' => 'GSB\AppBundle\Entity\Etat',
                'property' => 'libelle'))
            ->add('montantvalide', 'text', array(
                'read_only' => 'read_only'
                )
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GSB\AppBundle\Entity\FicheFrais'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gsb_appbundle_FicheFraisValidation';
    }

}