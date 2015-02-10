<?php

namespace Oro\TrackerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('TrackerBundle:Default:index.html.twig');
    }
}
