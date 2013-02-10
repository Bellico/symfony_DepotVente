<?php

namespace DepotVente\BourseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    public function indexAction()
    {
        return $this->render('BourseBundle:Home:index.html.twig');
    }
}
