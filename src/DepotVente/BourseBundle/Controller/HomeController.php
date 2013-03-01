<?php

namespace DepotVente\BourseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use DepotVente\BourseBundle\Entity\Article;

class HomeController extends Controller
{
    public function indexAction()
    {
    	/*$us = new Article();
        $us -> setName("Article de Test")
            -> setDescription("Description de Test Description de Test Description de Test Description de Test Description de Test Description de Test ")
            -> setPrice(105);

 		$em = $this->getDoctrine()->getManager();

		$bourseRep = $em->getRepository("BourseBundle:Bourse");
    	$bourse = $bourseRep->find(1);

    	$usereRep = $em->getRepository("BourseBundle:User");
    	$user = $usereRep->find(1);

    	$us -> setUser($user);
    	$us -> setBourse($bourse);


        $rep = $em->getRepository("BourseBundle:Article");
        $em -> persist($us);
        $em -> flush();*/

        return $this->render('BourseBundle:Home:index.html.twig');
    }
}
