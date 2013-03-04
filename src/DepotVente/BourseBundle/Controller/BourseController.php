<?php

namespace DepotVente\BourseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use DepotVente\BourseBundle\Entity\Facture;
use DepotVente\BourseBundle\Entity\Achat;

class BourseController extends Controller
{

    CONST CURRENT_FACTURE = "current_facture";


    public function indexAction()
    {
        $session = $this->get('session');
        $facture = $session->get(self::CURRENT_FACTURE) ;
        $em = $this->getDoctrine()->getManager();
        $repArt = $em->getRepository("BourseBundle:Article");
        $repfact = $em->getRepository("BourseBundle:Facture");
        $list = (isset($facture)) ? array() : null;

        $lastFact = $repfact->getLast();
        $totalFact = 0;
        if(isset($facture)){
            foreach ($facture as $v) {
                $a = $repArt->findOneBy(array("id" => $v));
                array_push($list ,$a);
                $totalFact += $a->getTotalPrice();
            }
        }

        return $this->render('BourseBundle:Bourse:index.html.twig',array(
            self::CURRENT_FACTURE => $list ,
            'lenghtTab' => sizeof($list) ,
            "nroFact" => $lastFact,
            "totalFact" => $totalFact
            ));
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
            "bourse" => $bourse = $repBourse->getCurrentBourse(),
            "sold" => false
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

        $article = $repArt->findOneBy(array("nro" => $nro ,"validate" => true , "bourse" => $bourse , "sold" => false ));
        if($article == null) {
            $session->getFlashBag()->add('addArt_error', 'Aucun article correspondant.');
        }else{
            array_push($facture, $article->getId());
            $session->set(self::CURRENT_FACTURE, $facture);
        }
        return $this->redirect($this->generateUrl('bourse_boursepage'));
    }

    public function removeFactureAction(){
        $session = $this->get('session');
        $session->remove(self::CURRENT_FACTURE) ;
        return $this->redirect($this->generateUrl('bourse_boursepage'));
    }


    public function deleteArticleAction($id){
        $session = $this->get('session');
        $facture = $session->get(self::CURRENT_FACTURE) ;
        if(isset($facture)){
            unset($facture[array_search($id, $facture)]);
            $session->set(self::CURRENT_FACTURE, $facture);
        }
        return $this->redirect($this->generateUrl('bourse_boursepage'));
    }

    public function saveFactureAction(){
       $em = $this->getDoctrine()->getManager();
       $repBourse = $em->getRepository("BourseBundle:Bourse");
       $repArt = $em->getRepository("BourseBundle:Article");
       $session = $this->get('session');
       $current_facture = $session->get(self::CURRENT_FACTURE) ;

        if(isset($current_facture)){

            $facture = new Facture();
            $facture->setBourse($repBourse->getCurrentBourse())
            ->setTotal(15);
            $em -> persist($facture);

            foreach ($current_facture as $v) {
                $a = new Achat();
                $a->setFacture($facture)->setArticle($repArt->findOneBy(array("id" => $v))->setSold(true));
                $em->persist($a);
            }
            $em -> flush();

            $session->remove(self::CURRENT_FACTURE);
            $session->getFlashBag()->add('addArt_success', 'Facture enregistrée ! .');
        }

        return $this->redirect($this->generateUrl('bourse_boursepage'));
    }

}
