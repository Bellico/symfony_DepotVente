<?php

namespace DepotVente\BourseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BourseController extends Controller
{

    CONST CURRENT_FACTURE = "current_facture";


    public function indexAction()
    {
        $session = $this->get('session');
        $facture = $session->get(self::CURRENT_FACTURE) ;
        $em = $this->getDoctrine()->getManager();
        $repArt = $em->getRepository("BourseBundle:Article");
        $list = (isset($facture)) ? array() : null;

        if(isset($facture)){
            foreach ($facture as $v) {
                array_push($list , $repArt->findOneBy(array("id" => $v)));
            }
        }

        return $this->render('BourseBundle:Bourse:index.html.twig',array(self::CURRENT_FACTURE => $list , 'lenghtTab' => sizeof($list)));
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
        $repArticle = $this->getDoctrine()->getRepository("BourseBundle:Article");
        $repBourse = $this->getDoctrine()->getRepository("BourseBundle:Bourse");

        $list = $repArticle->findBy(array(
            "validate" => true,
            "bourse" => $bourse = $repBourse->getCurrentBourse()
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


    public function addArticleAction(){
        $request = $this->getRequest();
        $session = $this->get('session');
        $em = $this->getDoctrine()->getManager();
        $repArt = $em->getRepository("BourseBundle:Article");
        $repBourse = $em->getRepository("BourseBundle:Bourse");


        $facture = $session->get(self::CURRENT_FACTURE) ;
        if (!isset($facture)){
            $facture = array();
        }

        $bourse = $repBourse->getCurrentBourse();
        $nro=$request->get('nro');

        $article = $repArt->findOneBy(array("nro" => $nro ,"validate" => true , "bourse" => $bourse ));
        if($article == null) {
            throw $this->createNotFoundException('Cette article n\'existe pas');
        }

        array_push($facture, $article->getId());
        $session->set(self::CURRENT_FACTURE, $facture);

           // $session->getFlashBag()->add('addArt_success', 'Article enregistré.');

        return $this->redirect($this->generateUrl('bourse_boursepage'));
    }

    public function removeFactureAction(){
        $session = $this->get('session');
        $session->remove(self::CURRENT_FACTURE) ;
        return $this->redirect($this->generateUrl('bourse_boursepage'));
    }
}
