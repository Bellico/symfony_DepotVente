<?php

namespace DepotVente\BourseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use DepotVente\BourseBundle\Entity\Article;
use DepotVente\BourseBundle\Form\ArticleHandler;
use DepotVente\BourseBundle\Form\ArticleType;

class DepotController extends Controller
{
	public function indexAction()
	{
		$article = new Article;
		$form = $this->createForm(new ArticleType, $article);
		$formHandler = new ArticleHandler($form, $this->get('request'), $this->getDoctrine()->getEntityManager());
		if($formHandler->process()){
			return $this->redirect($this->generateUrl('bourse_depotpage'));
		}

		return $this->render('BourseBundle:Depot:ajout.html.twig', array('form' => $form->createView()));
	}

}
