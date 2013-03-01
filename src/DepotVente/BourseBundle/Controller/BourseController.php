<?php

namespace DepotVente\BourseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BourseController extends Controller
{
    public function indexAction()
    {
        return $this->render('BourseBundle:Bourse:index.html.twig');
    }

    public function showArticleAction($id=0){

        if($id==0){
            $request = $this->getRequest();
            $id = $request->query->get("id");
            if(!isset($id)){$id = 0 ;}
        }

    	$repository = $this->getDoctrine()->getRepository("BourseBundle:Article");
    	$article = $repository->find($id);

        if($article == null) {
             throw $this->createNotFoundException('Cette article n\'existe pas');
        }
        if(!$article->getValidate()){
            return $this->render('BourseBundle:Bourse:validation.html.twig', array('article' => $article));
        }

    	return $this->render('BourseBundle:Bourse:article.html.twig', array('article' => $article));
    }


    public function showAllArticleAction(){
        $request = $this->getRequest();
        $repository = $this->getDoctrine()->getRepository("BourseBundle:Article");
        $list = $repository->findAll();
        return $this->render('BourseBundle:Bourse:listArticle.html.twig', array(
            'listArticle' => $list,
            'text'=>'Liste de tous les articles'
            ));
    }
}
