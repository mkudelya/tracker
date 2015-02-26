<?php

namespace Oro\TrackerBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

use Oro\TrackerBundle\Entity\Issue as IssueEntity;
use Oro\TrackerBundle\Entity\User;

class Issue
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
     * @param IssueEntity $issue
     * @param User $user
     * @return bool
     */
    public function isUserCollaborator(IssueEntity $issue, User $user)
    {
        return $issue->hasCollaborator($user);
    }

    /**
     * @param string $projectCode
     * @return mixed
     */
    public function getIssueListByProjectCode($projectCode)
    {
        $projectEntity = $this->getDoctrine()->getRepository('TrackerBundle:Project')->findOneByCode($projectCode);
        $issues = $this->getDoctrine()
            ->getRepository('TrackerBundle:Issue')
            ->findBy(array('project' => $projectEntity, 'parent' => null));

        return $issues;
    }

    /**
     * @param string $issueCode
     * @return mixed
     */
    public function getIssueSubListByIssueCode($issueCode)
    {
        $manager = $this->getDoctrine()->getManager();
        $issueEntity = $manager->getRepository('TrackerBundle:Issue')->findOneByCode($issueCode);

        $issues = $this->getDoctrine()
            ->getRepository('TrackerBundle:Issue')
            ->findBy(array('parent' => $issueEntity));

        return $issues;
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function getIssueListByCollaborationUser(User $user)
    {
        $manager = $this->getDoctrine()->getManager();
        $issues = $manager->createQuery('select i from Oro\TrackerBundle\Entity\Issue i JOIN i.collaborators u
        WHERE u = ?1 and i.status != ?2 and i.parent IS NULL');
        $issues->setParameter(1, $user);
        $issues->setParameter(2, 'Closed');

        return $issues->getResult();
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function getIssueListByAssigneeUser(User $user)
    {
        $manager = $this->getDoctrine()->getManager();
        $issues = $manager->createQuery('select i from Oro\TrackerBundle\Entity\Issue i
        WHERE i.assignee = ?1 and i.status != ?2 and i.parent IS NULL');
        $issues->setParameter(1, $user);
        $issues->setParameter(2, 'Closed');

        return $issues->getResult();
    }

    /**
     * @param IssueEntity $issue
     * @return mixed
     */
    public function getCollaborationListByIssue(IssueEntity $issue)
    {
        $manager = $this->getDoctrine()->getManager();
        $issues = $manager->createQuery('select u from Oro\TrackerBundle\Entity\User u JOIN u.issues i
        JOIN i.collaborators c WHERE i = ?1');
        $issues->setParameter(1, $issue);

        return $issues->getResult();
    }

    /**
     * @param string $issueCode
     * @return mixed
     */
    public function getCommentListByIssueCode($issueCode)
    {
        $issueEntity = $this->getDoctrine()->getRepository('TrackerBundle:Issue')->findOneByCode($issueCode);
        $comments = $this->getDoctrine()
            ->getRepository('TrackerBundle:Comment')
            ->findBy(array('issue' => $issueEntity));

        return $comments;
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected function getDoctrine()
    {
        return $this->doctine;
    }
}
