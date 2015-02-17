<?php
namespace Oro\TrackerBundle\Service;

use Oro\TrackerBundle\Entity\Activity as ActivityEntity;
use Oro\TrackerBundle\Entity\User;
use Oro\TrackerBundle\Entity\Project;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class Activity
{
    protected $container;
    protected $doctine;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->doctine = $container->get('doctrine');
    }

    public function getActivityIssueListByUser(User $user)
    {
        $manager = $this->getDoctrine()->getManager();
        $activities = $manager->createQuery('select a from Oro\TrackerBundle\Entity\Activity a JOIN a.project p JOIN p.users u WHERE u = ?1');
        $activities->setParameter(1, $user);
        return $activities->getResult();
    }

    public function getActivityIssueListByProject(Project $project)
    {
        $manager = $this->getDoctrine()->getManager();
        $activities = $manager->createQuery('select a from Oro\TrackerBundle\Entity\Activity a JOIN a.project p WHERE p = ?1');
        $activities->setParameter(1, $project);
        return $activities->getResult();
    }

    protected function getDoctrine()
    {
        return $this->doctine;
    }
}
