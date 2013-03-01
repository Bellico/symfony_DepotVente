<?php

namespace DepotVente\BourseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BourseController extends Controller
{
    public function indexAction()
    {
        return $this->render('BourseBundle:Bourse:index.html.twig');
    }

    public function showArticleAction($nro){

    	$repository = $this->getDoctrine()->getRepository("BourseBundle:Article");
    	$article = $repository->findOneBy(array("nro" => $nro ));

        if($article == null) {
             throw $this->createNotFoundException('Cette article n\'existe pas');
        }
        if(!$article->getValidate()){
            return $this->render('BourseBundle:Bourse:validation.html.twig', array('article' => $article));
        }

    	return $this->render('BourseBundle:Bourse:article.html.twig', array('article' => $article));
    }

    public function showArticleGetAction(){
        $request = $this->getRequest();
        $nro = $request->query->get("nro");

        if(!isset($nro)){$nro = 0;}
        return $this->showArticleAction($nro);
    }


    public function showAllArticleAction($display){
        $request = $this->getRequest();
        $repository = $this->getDoctrine()->getRepository("BourseBundle:Article");
        $list = $repository->findBy(array(
            "validate" => true
            ));

        $d =  array(
            'listArticle' => $list,
            'text'=>'Liste de tous les articles',
            'url' => $this->generateUrl('bourse_all_article', array('display' =>'tableau'))
            );

        return ($display == "liste" ) ?
        $this->render('BourseBundle:Bourse:listArticle.html.twig', $d) :
        $this->render('BourseBundle:Bourse:listArticleTab.html.twig', $d);
    }
}
