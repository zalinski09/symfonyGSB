<?php

namespace GSB\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('GSBAppBundle:Default:index.html.twig');
    }
}
