<?php

namespace DepotVente\BourseBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use DepotVente\BourseBundle\Entity\Article;

class ArticleHandler
{
    private $form;
    private $request;
    private $em;

    public function __construct(Form $form, Request $request, EntityManager $em)
    {
        $this->form    = $form;
        $this->request = $request;
        $this->em      = $em;
    }

    public function process()
    {

        if( $this->request->getMethod() == 'POST' )
        {
           $this->form->bindRequest($this->request);

            if( $this->form->isValid() )
            {
		      $this->onSuccess($this->form->getData());

                return true;
            }
        }
        return false;
    }

    public function onSuccess(Article $article)
    {
        $this->em->persist($article->getUser());
        $this->em->persist($article);
        $this->em->flush();
    }
}

