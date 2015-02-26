<?php

namespace Oro\TrackerBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

use Oro\TrackerBundle\Entity\User;
use Oro\TrackerBundle\Entity\Project;
use Oro\TrackerBundle\Entity\Issue as IssueEntity;

class Activity
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
     * @param User $user
     * @return mixed
     */
    public function getActivityIssueListWhereUserIsProjectMember(User $user)
    {
        $manager = $this->getDoctrine()->getManager();
        $activities = $manager->createQuery('select a from Oro\TrackerBundle\Entity\Activity a JOIN a.project p
        JOIN p.members u WHERE u = ?1 ORDER BY a.created DESC');
        $activities->setParameter(1, $user);
        return $activities->getResult();
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function getActivityIssueListByUser(User $user)
    {
        $manager = $this->getDoctrine()->getManager();
        $activities = $manager->createQuery('select a from Oro\TrackerBundle\Entity\Activity a WHERE a.user = ?1
        ORDER BY a.created DESC');
        $activities->setParameter(1, $user);
        return $activities->getResult();
    }

    /**
     * @param Project $project
     * @return mixed
     */
    public function getActivityIssueListByProject(Project $project)
    {
        $manager = $this->getDoctrine()->getManager();
        $activities = $manager->createQuery('select a from Oro\TrackerBundle\Entity\Activity a JOIN a.project p
        WHERE p = ?1 ORDER BY a.created DESC');
        $activities->setParameter(1, $project);
        return $activities->getResult();
    }

    /**
     * @param IssueEntity $issue
     * @return mixed
     */
    public function getActivityIssueListByIssue(IssueEntity $issue)
    {
        $manager = $this->getDoctrine()->getManager();
        $activities = $manager->createQuery('select a from Oro\TrackerBundle\Entity\Activity a JOIN a.issue i
        WHERE i.id = ?1 or i.parent = ?1 ORDER BY a.created DESC');
        $activities->setParameter(1, $issue);
        return $activities->getResult();
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected function getDoctrine()
    {
        return $this->doctine;
    }
}
