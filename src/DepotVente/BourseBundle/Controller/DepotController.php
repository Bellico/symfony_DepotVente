<?php

namespace DepotVente\BourseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DepotController extends Controller
{
    public function indexAction()
    {
        return $this->render('BourseBundle:Depot:depot.html.twig');
    }
}
