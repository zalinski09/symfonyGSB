<?php

namespace GSB\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('GSBAccountBundle:Default:index.html.twig');
    }
}
