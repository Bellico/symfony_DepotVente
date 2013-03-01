<?php

namespace DepotVente\BourseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('label' => 'Nom de votre Bourse :'))
            ->add('theme', 'text', array('label' => 'Choisissez un thème : '))
            ->add('lieu', 'text', array('label' => 'Lieu ou se déroulera votre bourse :'))
            ->add('description', 'textarea', array('label' => 'Description :'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DepotVente\BourseBundle\Entity\Bourse'
        ));
    }

    public function getName()
    {
        return 'depotvente_boursebundle_boursetype';
    }
}
