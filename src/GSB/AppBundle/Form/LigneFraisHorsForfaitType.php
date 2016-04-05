<?php

namespace GSB\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class LigneFraisHorsForfaitType
 * @package GSB\AppBundle\Form
 */
class LigneFraisHorsForfaitType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle')
            ->add('date', 'datetime', array(
                'format' => 'd-M-y',
                'widget' => 'single_text'
            ))
            ->add('mois')
            ->add('montant')
            ->add('visiteur','entity', array(
                'class' => 'GSB\AccountBundle\Entity\Visiteur',
                'property' => 'id'));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GSB\AppBundle\Entity\LigneFraisHorsForfait'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gsb_appbundle_lignefraishorsforfait';
    }
}
