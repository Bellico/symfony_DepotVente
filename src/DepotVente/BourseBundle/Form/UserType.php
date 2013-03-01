<?php

namespace DepotVente\BourseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('label' => 'Nom du déposant :'))
            ->add('firstName', 'text', array('label' => 'Prénom du déposant :'))
            ->add('address', 'text', array('label' => 'Adresse du déposant :'))
            ->add('tel', 'text', array('label' => 'Téléphone :'))
            ->add('mail', 'email', array('label' => 'Email :'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DepotVente\BourseBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'depotvente_boursebundle_usertype';
    }
}
