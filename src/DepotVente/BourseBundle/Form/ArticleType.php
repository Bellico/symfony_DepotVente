<?php

namespace DepotVente\BourseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('label' => 'Nom de l\'article '))
            ->add('description', 'textarea', array('label' => 'Description'))
    	    ->add('price', 'number', array('label' => 'Prix'))
    	    ->add('user', new UserType())
	;
    }

    public function getName()
    {
        return 'depotvente_boursebundle_articletype';
    }
}

