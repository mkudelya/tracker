<?php

namespace Oro\TrackerBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class Project
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected $doctine;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->doctine = $container->get('doctrine');
    }

    /**
     * @return mixed
     */
    public function getList()
    {
        $projectList = $this->getDoctrine()->getRepository('TrackerBundle:Project')->findAll();
        return $projectList;
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected function getDoctrine()
    {
        return $this->doctine;
    }
}
