<?php
namespace Oro\TrackerBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class Project
{
    protected $container;
    protected $doctine;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->doctine = $container->get('doctrine');

    }

    public function getList()
    {
        $projectList = $this->getDoctrine()->getRepository('TrackerBundle:Project')->findAll();
        return $projectList;
    }

    protected function getDoctrine()
    {
        return $this->doctine;
    }
}
