<?php

namespace GSB\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Date;

/**
 * Class LigneFraisForfaitType
 * @package GSB\AppBundle\Form
 */
class LigneFraisForfaitType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('visiteur','entity', array(
                'class' => 'GSB\AccountBundle\Entity\Visiteur',
                'property' => 'id'
            ))
            ->add('fraisforfait','entity', array(
                'class' => 'GSB\AppBundle\Entity\FraisForfait',
                'property' => 'id'
            ))
            ->add('quantite')
            ->add('mois');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GSB\AppBundle\Entity\LigneFraisForfait'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gsb_appbundle_lignefraisforfait';
    }
}
