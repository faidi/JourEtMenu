<?php

namespace JourEtMenu\JourEtMenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('JourEtMenuBundle:Default:index.html.twig', array('name' => $name));
    }
}
