<?php
namespace Oro\TrackerBundle\Service;

use Oro\TrackerBundle\Entity\Activity as ActivityEntity;
use Oro\TrackerBundle\Entity\User;
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
        $issues = $manager->createQuery('select a from Oro\TrackerBundle\Entity\Activity a JOIN a.project p JOIN p.users u WHERE u.id = ?1');
        $issues->setParameter(1, $user->getId());
        return $issues->getResult();
    }

    protected function getDoctrine()
    {
        return $this->doctine;
    }
}
