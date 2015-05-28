<?php

namespace Oro\Bundle\TrackerBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

use Oro\Bundle\TrackerBundle\Entity\Issue as IssueEntity;
use Oro\Bundle\UserBundle\Entity\User;

class IssueRepository extends EntityRepository
{
    const LIMIT = 20;

    /**
     * @param string $projectCode
     * @return mixed
     */
    public function getIssueListByProjectCode($projectCode)
    {
        $projectEntity = $this->getEntityManager()->getRepository('OroTrackerBundle:Project')->findOneByCode($projectCode);
        $issues = $this->findBy(array('project' => $projectEntity, 'parent' => null), null, self::LIMIT);

        return $issues;
    }

    /**
     * @param string $issueCode
     * @return mixed
     */
    public function getIssueSubListByIssueCode($issueCode)
    {
        $issueEntity = $this->getEntityManager()->getRepository('OroTrackerBundle:Issue')->findOneByCode($issueCode);
        $issues = $this->findBy(array('parent' => $issueEntity), null, self::LIMIT);

        return $issues;
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function getIssueListByCollaborationUser(User $user)
    {
        $issues = $this->getEntityManager()->createQuery(
            'SELECT i FROM OroTrackerBundle:Issue i JOIN i.collaborators u
            WHERE u = ?1 and i.status != ?2 and i.parent IS NULL'
        );
        $issues->setParameter(1, $user);
        $issues->setParameter(2, 'Closed');
        $issues->setMaxResults(self::LIMIT);

        return $issues->getResult();
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function getIssueListByAssigneeUser(User $user)
    {
        $issues = $this->getEntityManager()->createQuery(
            'SELECT i FROM OroTrackerBundle:Issue i
            WHERE i.assignee = ?1 and i.status != ?2 and i.parent IS NULL'
        );
        $issues->setParameter(1, $user);
        $issues->setParameter(2, 'Closed');
        $issues->setMaxResults(self::LIMIT);

        return $issues->getResult();
    }

    /**
     * @param IssueEntity $issue
     * @return mixed
     */
    public function getCollaborationListByIssue(IssueEntity $issue)
    {
        $issues = $this->getEntityManager()->createQuery(
            'SELECT u FROM OroUserBundle:User u JOIN u.issues i
            JOIN i.collaborators c WHERE i = ?1'
        );
        $issues->setParameter(1, $issue);

        return $issues->getResult();
    }

    /**
     * @param string $issueCode
     * @return mixed
     */
    public function getCommentListByIssueCode($issueCode)
    {
        $issueEntity = $this->getEntityManager()->getRepository('OroTrackerBundle:Issue')->findOneByCode($issueCode);
        $comments = $this->getEntityManager()
            ->getRepository('OroTrackerBundle:Comment')
            ->findBy(array('issue' => $issueEntity));

        return $comments;
    }
}