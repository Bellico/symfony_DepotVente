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

    public function showListArticleAction($display) {
    	$request = $this->getRequest();

    	$keyword = $request->request->get('keyword');
    	$minPrice = $request->request->get('minPrice');
    	$maxPrice = $request->request->get('maxPrice');


        $repArticle = $this->getDoctrine()->getRepository("BourseBundle:Article");
        $repBourse = $this->getDoctrine()->getRepository("BourseBundle:Bourse");
        $qb = $repArticle->createQueryBuilder('a')
                         ->where("a.validate = true")
                         ->andWhere('a.bourse = :bourse')
                         ->andWhere('a.sold = false')
                         ->setParameter('bourse', $repBourse->getCurrentBourse());

    	if($minPrice != null) {
    	    $qb = $qb->andWhere('a.price > :minPrice')
    	    ->setParameter('minPrice', $minPrice);
    	}

    	if($maxPrice != null) {
    	    $qb = $qb->andWhere('a.price < :maxPrice')
    	   	->setParameter('maxPrice', $maxPrice);
    	}

    	if($keyword != null) {
    	    $qb = $qb->andWhere('a.name LIKE :keyword')
    	    ->orWhere('a.description LIKE :keyword')
    		->setParameter('keyword', '%'.$keyword.'%');
    	}

    	$list = $qb->getQuery()->getResult();
            $d =  array(
                'listArticle' => $list,
                'text'=>'Liste de tous les articles'
                );

        return ($display == "liste" ) ?
        $this->render('BourseBundle:Bourse:listArticle.html.twig', $d) :
        $this->render('BourseBundle:Bourse:listArticleTab.html.twig', $d);

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
            'text'=>'Liste de tous les articles'
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
        if($article == null || in_array($article->getId(),$facture)) {
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

            $totalFact = 0;
            foreach ($current_facture as $v) {
                $a = new Achat();
                $article = $repArt->findOneBy(array("id" => $v))->setSold(true)->setValidate(false);
                $a->setFacture($facture)->setArticle($article);
                $totalFact += $article->getTotalPrice();
                $em->persist($a);
            }
            $facture->setTotal($totalFact);
            $em -> flush();

            $session->remove(self::CURRENT_FACTURE);
            $session->getFlashBag()->add('addArt_success', 'Facture enregistrée ! .');
        }

        return $this->redirect($this->generateUrl('bourse_boursepage'));
    }
}
