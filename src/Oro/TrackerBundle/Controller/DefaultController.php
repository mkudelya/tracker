<?php

namespace Oro\TrackerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('TrackerBundle:Default:index.html.twig', array('name' => $name));
    }
}
