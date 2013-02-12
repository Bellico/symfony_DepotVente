<?php

namespace DepotVente\BourseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BourseController extends Controller
{
    public function indexAction()
    {
        return $this->render('BourseBundle:Bourse:index.html.twig');
    }
}
