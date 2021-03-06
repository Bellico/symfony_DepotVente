<?php

namespace DepotVente\BourseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use DepotVente\BourseBundle\Entity\Article;
use DepotVente\BourseBundle\Entity\Bourse;
use DepotVente\BourseBundle\Entity\User;
use DepotVente\BourseBundle\Form\BourseHandler;
use DepotVente\BourseBundle\Form\BourseType;

class HomeController extends Controller
{
    public function indexAction()
    {

        $repository = $this->getDoctrine()->getRepository("BourseBundle:Bourse");
        $bourse = $repository->getCurrentBourse();

        $form = $this->createForm(new BourseType, new Bourse());
        return $this->render('BourseBundle:Home:index.html.twig',array('bourse' => $bourse,'form' => $form->createView() ));
    }

    public function createBourseAction(){
        $em = $this->getDoctrine()->getManager();
        $repBourse = $em->getRepository("BourseBundle:Bourse");
        $bourse = $repBourse->getCurrentBourse();
        if($bourse != null ) {$bourse->setOpen(false);}

        $bourse = new Bourse();
        $form = $this->createForm(new BourseType, $bourse);
        $formHandler = new BourseHandler($form, $this->get('request'), $this->getDoctrine()->getEntityManager());
        $formHandler->process();
        return $this->redirect($this->generateUrl('bourse_homepage'));
    }

    public function bourseTestAction(){
        $em = $this->getDoctrine()->getManager();

        $repBourse = $em->getRepository("BourseBundle:Bourse");
        $c_bourse = $repBourse->getCurrentBourse();
        if($c_bourse != null ) {$c_bourse->setOpen(false);}

        $bourse = new Bourse();
        $bourse->setName("Bourse de Test")->setDescription("Ceci est une simple bourse pour tester sur le theme des Fruits")->setLieu("Nancy")->setTheme("Fruits");
        $em -> persist($bourse);

        $usr1 = new User();
        $usr1->setName("Martin")->setFirstName("Franck")->setAddress("12 rue des Nancy")->setTel("0669696964")->setMail("franck@mail.fr");
        $em -> persist($usr1);

        $usr2 = new User();
        $usr2->setName("Belluco")->setFirstName("Luca")->setAddress("13 rue des Ponts")->setTel("0669696964")->setMail("luca@mail.fr");
        $em -> persist($usr2);

        $usr3 = new User();
        $usr3->setName("Lecompte")->setFirstName("Mathieu")->setAddress("25 rue des Brouette")->setTel("0669696964")->setMail("mathieu@mail.fr");
        $em -> persist($usr3);

        $atc1 = new Article();
        $atc1->setName("Pomme rouge")->setDescription("Une belle pomme rouge")->setPrice("2.85")->setValidate(true)->setUser($usr1)->setBourse($bourse);
        $em -> persist($atc1);
        sleep(1);

        $atc2 = new Article();
        $atc2->setName("Fraise")->setDescription("Une fraise pas comme les autres")->setPrice("5")->setValidate(true)->setUser($usr1)->setBourse($bourse);
        $em -> persist($atc2);
        sleep(1);

        $atc3 = new Article();
        $atc3->setName("Cerise")->setDescription("Ca donne faim")->setPrice("2.3")->setValidate(true)->setUser($usr2)->setBourse($bourse);
        $em -> persist($atc3);
        sleep(1);

        $atc4 = new Article();
        $atc4->setName("Banane")->setDescription("Bon pour la santé")->setPrice("5")->setValidate(true)->setUser($usr2)->setBourse($bourse);
        $em -> persist($atc4);
        sleep(1);

        $atc5 = new Article();
        $atc5->setName("Kiwi")->setDescription("Kiwi mure et juteux pas cher")->setPrice("1.22")->setValidate(true)->setUser($usr3)->setBourse($bourse);
        $em -> persist($atc5);

        $em -> flush();
        return $this->redirect($this->generateUrl('bourse_homepage'));
    }
}
