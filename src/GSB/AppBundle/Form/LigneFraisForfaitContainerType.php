<?php

namespace GSB\AppBundle\Form;

use GSB\AppBundle\Entity\LigneFraisForfait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class LigneFraisForfaitContainerType
 * @package GSB\AppBundle\Form
 */
class LigneFraisForfaitContainerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lignesfraisForfait','collection', array(
                'type' => new LigneFraisForfaitType(),
                'allow_add' => true,
                'allow_delete' => true
               ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gsb_appbundle_fraisforfaitcontainer';
    }
}
