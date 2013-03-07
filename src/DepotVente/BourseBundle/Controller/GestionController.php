<?php

namespace DepotVente\BourseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use DepotVente\BourseBundle\Entity\Article;

class GestionController extends Controller
{

	public function ficheDeposantAction($id){

		$em = $this->getDoctrine()->getManager();
        $repUser = $em->getRepository("BourseBundle:User");
        $repArticle = $em->getRepository("BourseBundle:Article");
        $repFact = $em->getRepository("BourseBundle:Facture");
        $repBourse = $em->getRepository("BourseBundle:Bourse");

        $user = $repUser->findOneBy(array("id" => $id ));
		if($user == null) {
			throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
		}

		$articleVendu = $repArticle->findBy(array(
            "bourse" => $bourse = $repBourse->getCurrentBourse(),
            "sold" => true,
            "user" => $user
            ));

		$totalVendu = $repArticle->createQueryBuilder('a')
            ->select('sum(a.price)')
            ->where('a.bourse = :bourse' , 'a.user = :user' , 'a.sold = :sold ')
            ->setParameter('bourse',$bourse)
            ->setParameter('user',$user)
            ->setParameter('sold', true)
            ->getQuery()->getSingleScalarResult();
        if($totalVendu == null ){$totalVendu = 0 ;}

		$articleNonVendu = $repArticle->findBy(array(
            "bourse" => $bourse = $repBourse->getCurrentBourse(),
            "sold" => false,
            "user" => $user
            ));

		return $this->render('BourseBundle:Gestion:ficheDeposant.html.twig',array(
			'user' => $user,
			'articleVendu' => $articleVendu,
			'articleNonVendu' => $articleNonVendu,
			"totalVendu" => $totalVendu,
            "nbArticleVendu" => count($articleVendu),
            "nbArticleNonVendu" => count($articleNonVendu),
			));
	}

	private function formArticle($a){
		return $form = $this->createFormBuilder($a)
        ->add('name', 'text', array('label' => 'Titre de l\'article : '))
        ->add('description', 'textarea', array('label' => 'Description :'))
    	->add('price', 'number', array('label' => 'Prix :'))
        ->getForm();
	}


	public function modifArticleAction($nro){
		$request = $this->getRequest();
		$em = $this->getDoctrine()->getManager();

		$repository = $em->getRepository("BourseBundle:Article");
    	$article = $repository->findOneBy(array("nro" => $nro ));
        if($article == null) {
             throw $this->createNotFoundException('Cette article n\'existe pas');
        }

        $form = $this->formArticle($article);

        if($request->getMethod() == 'POST' ){
          	$form->bindRequest($request);
            if($form->isValid() ){
            	$article = $form->getData();
       			$em->flush();
       			return $this->redirect($this->generateUrl('bourse_article' , array ('nro' => $article->getNro())));
            }
        }

        return $this->render('BourseBundle:Gestion:modifArticle.html.twig',array(
        	'formModif' => $form->createView(),
        	'nroArticle' => $nro
        	));
	}


	public function deleteArticleAction($nro){
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository("BourseBundle:Article");
    	$article = $repository->findOneBy(array("nro" => $nro ));
        if($article == null) {
             throw $this->createNotFoundException('Cette article n\'existe pas');
        }
        $article->setValidate(false);
        $em->flush();
        return $this->redirect($this->generateUrl('bourse_article',array("nro" => $nro )));
	}


	public function tableauDeBordAction(){
		$em = $this->getDoctrine()->getManager();
        $repArt = $em->getRepository("BourseBundle:Article");
        $repBourse = $em->getRepository("BourseBundle:Bourse");
        $repFacture = $em->getRepository("BourseBundle:Facture");
        $bourse = $repBourse->getCurrentBourse();

        if($bourse != null) {
            $listFacture = $repFacture->getFacturesFromBourse($bourse);
            $totalFacture = 0 ;
            foreach ($listFacture as $facture) {
                $totalFacture += $facture["total"];

            }
            $bourse->gain = $totalFacture;
            $listArticle = $repArt->findBy(array("bourse" => $bourse ));
            $bourse->artDepose = count($listArticle) ;
            $listArticle = $repArt->findBy(array("bourse" => $bourse , "sold" => true ));
            if(empty($bourse->artDepose)){$bourse->artDepose =0 ;}
            $bourse->artVendu = count($listArticle);
            if(empty($bourse->artVendu)){$bourse->artVendu =0 ; $bourse->percent = 0 ;}else{
                $bourse->percent=  round(($bourse->artVendu * 100) /  $bourse->artDepose)  ;
            }

            $listFacture = $repFacture->findBy(array("bourse" => $bourse ));
            $bourse->nbFacture = count($listFacture) ;

            $listArticleRetire = $repArt->findBy(array("bourse" => $bourse , "sold" => false , "validate" => false ));
            $bourse->nbArtRetire = count($listArticleRetire) ;

            $recette = 0;
            foreach ($listFacture as $v) {
                $recette += $v->getTotal();
            }
            $bourse->recette = $recette ;

             $totalVendu = $repArt->createQueryBuilder('a')
            ->select('sum(a.price)')
            ->where('a.bourse = :bourse' , 'a.sold = :sold ')
            ->setParameter('bourse',$bourse)
            ->setParameter('sold', true)
            ->getQuery()->getSingleScalarResult();
            if($totalVendu == null ){$totalVendu = 0 ;}
            $bourse->charge = $totalVendu ;
        }



        /*Ancien Bourse*/
		$listBourse = $repBourse->findBy(
            array(),
            array('dateCreated' => 'DESC','id'=>'DESC')
        );

        if($bourse != null) {
             unset($listBourse[0]);
        }

        foreach ($listBourse as $v) {
            $v->long = $v->getDateCreated()->diff($v->getDateClose(),true)->format('%a');
            $listFacture = $repFacture->getFacturesFromBourse($v);
            $totalFacture = 0 ;
            foreach ($listFacture as $facture) {
                $totalFacture += $facture["total"];
            }
            $v->gain = $totalFacture;

            $totalVendu = $repArt->createQueryBuilder('a')
            ->select('sum(a.price)')
            ->where('a.bourse = :bourse' , 'a.sold = :sold ')
            ->setParameter('bourse',$v)
            ->setParameter('sold', true)
            ->getQuery()->getSingleScalarResult();
            if($totalVendu == null ){$totalVendu = 0 ;}
            $v->charge = $totalVendu ;

            $listArticle = $repArt->findBy(array("bourse" => $v ));
            $v->artDepose = count($listArticle) ;
            $listArticle = $repArt->findBy(array("bourse" => $v , "sold" => true ));
            if(empty($v->artDepose)){$v->artDepose =0 ;}
            $v->artVendu = count($listArticle);
            if(empty($v->artVendu)){$v->artVendu =0 ;}else{
                $v->artVendu=  $v->artVendu . " ( ". round(($v->artVendu * 100) /  $v->artDepose) ." % ) " ;
            }
        }

       	return $this->render('BourseBundle:Gestion:tableaudebord.html.twig',array(
            "bourse" => $bourse,
            "listBourse" => $listBourse
            ));
	}


	public function closeBourseAction(){
		$em = $this->getDoctrine()->getManager();
		$repBourse = $em->getRepository("BourseBundle:Bourse");
		$bourse = $repBourse->getCurrentBourse();
		$bourse->setOpen(false);
		$em->flush();
       	return $this->redirect($this->generateUrl('bourse_homepage'));
	}

    public function listFactureAction(){
        $em = $this->getDoctrine()->getManager();
        $repFacture = $em->getRepository("BourseBundle:Facture");
        $repAchat = $em->getRepository("BourseBundle:Achat");
        $repBourse = $em->getRepository("BourseBundle:Bourse");
        $bourse = $repBourse->getCurrentBourse();


        $listFacture = $repFacture->findBy(array("bourse" => $bourse ));
        foreach ($listFacture as $v ) {
            $listAchat = $repAchat->findBy(array("facture" => $v ));
            $v->nbAchat = count($listAchat);
        }

        return $this->render('BourseBundle:Gestion:listFacture.html.twig',array(
            "listFacture" => $listFacture
            ));
    }

    public function detailsFactureAction($id){
        $repository = $this->getDoctrine()->getRepository("BourseBundle:Facture");
        $facture = $repository->findOneBy(array("id" => $id ));

        if($facture == null) {
            throw $this->createNotFoundException('Facture introuvable');
        }

        $em = $this->getDoctrine()->getManager();
        $repAchat = $em->getRepository("BourseBundle:Achat");
        $listAchat = $repAchat->findBy(array("facture" => $facture));

        return $this->render('BourseBundle:Gestion:listAchat.html.twig',array(
            "listAchat" => $listAchat,
            "facture" => $facture
            ));
    }

}