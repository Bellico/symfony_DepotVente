<?php

namespace DepotVente\BourseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use DepotVente\BourseBundle\Entity\Article;
use DepotVente\BourseBundle\Form\ArticleHandler;
use DepotVente\BourseBundle\Form\ArticleType;

class DepotController extends Controller
{
	CONST CURRENT_USER = "current_user";
	CONST CURRENT_DEPOT = "current_depot";

	public function indexAction()
	{
		$request = $this->getRequest();
		$session = $this->get('session');
		$em = $this->getDoctrine()->getManager();

		$article = new Article;
		$form = $this->createForm(new ArticleType, $article);

        if($request->getMethod() == 'POST' ){
          	$form->bindRequest($request);
            if($form->isValid() ){
            	$newArticle = $form->getData();
            	$em->persist($newArticle->getUser());
			    $em->flush();

			    $session->set(self::CURRENT_USER, $newArticle->getUser());
			    $dep=array();
				$dep[$newArticle ->getNro()] = $newArticle ;
				$session->set(self::CURRENT_DEPOT, $dep);
				$session->getFlashBag()->add('addArt_success', 'Déposant validé et article enregistré. Continuer ?');
            }else{
            	$session->getFlashBag()->add('addArt_error', 'Une erreure est survenue lors de l\'enregistrement.');
            }
        }


		$tab = array('form' => $form->createView());
		if($session->get(self::CURRENT_USER)){
			$tab["formArticleDepot"]= $this->formArticle()->createView();
		}
		return $this->render('BourseBundle:Depot:index.html.twig',$tab);
	}

	private function formArticle(){
		return $form = $this->createFormBuilder(new Article())
        ->add('name', 'text', array('label' => 'Titre de l\'article : '))
        ->add('description', 'textarea', array('label' => 'Description :'))
    	->add('price', 'number', array('label' => 'Prix :'))
        ->getForm();
	}

	public function listenUserAction(){
		$request = $this->getRequest();
		$session = $this->get('session');

		if( $request->getMethod() == 'POST' ) {
			$nroUsr = $request->get("nro");
			$repository = $this->getDoctrine()->getRepository("BourseBundle:User");
			$user = $repository->findOneBy(array("id" => $nroUsr ));

			if($user == null) {
				throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
			}else{
				$session->set(self::CURRENT_USER, $user);
				$session->set(self::CURRENT_DEPOT, array());
			}
		}

		return $this->redirect($this->generateUrl('bourse_depotpage'));

	}

	public function quitUserAction(){
		$session = $this->get('session');
		$session->remove(self::CURRENT_USER);
		return $this->redirect($this->generateUrl('bourse_depotpage'));
	}


	public function addArticleAction(){
		$request = $this->getRequest();
		$session = $this->get('session');
		$form = $this->formArticle();

        if( $request->getMethod() == 'POST' ){
          	$form->bindRequest($request);
            if($form->isValid() ){
            	$dep = $session->get(self::CURRENT_DEPOT) ;
            	$article = $form->getData();
            	$dep[$article->getNro()] = $article;
            	$session->set(self::CURRENT_DEPOT, $dep);
            	$session->getFlashBag()->add('addArt_success', 'Article enregistré.');
            }else{
            	$session->getFlashBag()->add('addArt_error', 'Une erreure est survenue lors de l\'enregistrement.');
            }
        }

		return $this->redirect($this->generateUrl('bourse_depotpage'));
	}


	public function deleteArticleAction($nro){
		$request = $this->getRequest();
		$session = $this->get('session');
		$form = $this->formArticle();

        $dep = $session->get(self::CURRENT_DEPOT) ;
        unset($dep[$nro]);
        $session->set(self::CURRENT_DEPOT, $dep);

		return $this->redirect($this->generateUrl('bourse_depotpage'));
	}

	public function saveDepotAction(){
		$session = $this->get('session');
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository("BourseBundle:Bourse");
		$repUser = $em->getRepository("BourseBundle:User");
        $bourse = $repository->getCurrentBourse();
        $dep = $session->get(self::CURRENT_DEPOT) ;

        if (sizeof($dep) == 0){
        	$session->getFlashBag()->add('addArt_error', 'Il n\'y a aucun article à ajouter.');
			return $this->redirect($this->generateUrl('bourse_depotpage'));
        }

        if($bourse != null) {
			$usr = $session->get(self::CURRENT_USER);
			$usr = $repUser->findOneBy(array("id" => $usr->getId()));
			foreach ($dep as $v) {
				$v->setUser($usr);
				$v->setBourse($bourse);
	 			$em->persist($v);
			}
			$em -> flush();
			$session->getFlashBag()->add('depot_terminate', 'Un nouveau dépôt a été enregistré');
			return $this->quitUserAction();
		}else{
			$session->getFlashBag()->add('addArt_error', 'Vous n\'avez aucune bourse en cours.');
			return $this->redirect($this->generateUrl('bourse_depotpage'));
		}
	}

	public function showAllDeposantAction($display){
       	$em = $this->getDoctrine()->getManager();
        $repUser = $em->getRepository("BourseBundle:User");
        $repArticle = $em->getRepository("BourseBundle:Article");
        $repBourse = $em->getRepository("BourseBundle:Bourse");
        $bourse = $repBourse->getCurrentBourse();
        $list = $repUser->findBy(array(),array("id" => "DESC"));


        foreach ($list as $v) {
        	$v->nbTotalDepoBourse = count($repArticle->findBy(array(
	            "bourse" => $bourse,
	            "user" => $v
	            )));
        	$v->nbTotalVente = count($repArticle->findBy(array(
	            "bourse" => $bourse ,
	            "sold" => true,
	            "user" => $v
	            )));
        }

        $d =  array(
            'listUsers' => $list,
            'text'=>'Liste de tous les déposants inscrits'
            );
        return ($display == "liste" ) ?
        $this->render('BourseBundle:Depot:listDeposant.html.twig', $d) :
        $this->render('BourseBundle:Depot:listDeposantTab.html.twig', $d);
    }

}