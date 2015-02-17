<?php
namespace Oro\TrackerBundle\Service;

use Oro\TrackerBundle\Entity\Issue as IssueEntity;
use Oro\TrackerBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class Issue
{
    protected $container;
    protected $doctine;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->doctine = $container->get('doctrine');

    }

    public function isUserCollaborator(IssueEntity $issue, User $user)
    {
        return $issue->hasCollaborator($user);
    }

    public function getIssueListByProjectCode($projectCode)
    {
        $projectEntity = $this->getDoctrine()->getRepository('TrackerBundle:Project')->findOneByCode($projectCode);
        $issues = $this->getDoctrine()
            ->getRepository('TrackerBundle:Issue')
            ->findBy(array('project' => $projectEntity, 'parent' => null));

        return $issues;
    }

    public function getIssueSubListByIssueCode($issueCode)
    {
        $manager = $this->getDoctrine()->getManager();
        $issueEntity = $manager->getRepository('TrackerBundle:Issue')->findOneByCode($issueCode);

        $issues = $this->getDoctrine()
            ->getRepository('TrackerBundle:Issue')
            ->findBy(array('parent' => $issueEntity));

        return $issues;
    }

    public function getIssueListByCollaborationUser(User $user)
    {
        $manager = $this->getDoctrine()->getManager();
        $issues = $manager->createQuery('select i from Oro\TrackerBundle\Entity\Issue i JOIN i.collaborators u WHERE u = ?1 and i.status != ?2 and i.parent IS NULL');
        $issues->setParameter(1, $user);
        $issues->setParameter(2, 'closed');

        return $issues->getResult();
    }

    public function getIssueListByAssigneeUser(User $user)
    {
        $manager = $this->getDoctrine()->getManager();
        $issues = $manager->createQuery('select i from Oro\TrackerBundle\Entity\Issue i WHERE i.assignee = ?1 and i.status != ?2 and i.parent IS NULL');
        $issues->setParameter(1, $user);
        $issues->setParameter(2, 'closed');

        return $issues->getResult();
    }

    public function getCollaborationListByIssue(IssueEntity $issue)
    {
        $manager = $this->getDoctrine()->getManager();
        $issues = $manager->createQuery('select u from Oro\TrackerBundle\Entity\User u JOIN u.issues i JOIN i.collaborators c WHERE i = ?1');
        $issues->setParameter(1, $issue);

        return $issues->getResult();
    }

    public function getCommentListByIssueCode($issueCode)
    {
        $issueEntity = $this->getDoctrine()->getRepository('TrackerBundle:Issue')->findOneByCode($issueCode);
        $comments = $this->getDoctrine()
            ->getRepository('TrackerBundle:Comment')
            ->findBy(array('issue' => $issueEntity));

        return $comments;
    }

    protected function getDoctrine()
    {
        return $this->doctine;
    }
}
