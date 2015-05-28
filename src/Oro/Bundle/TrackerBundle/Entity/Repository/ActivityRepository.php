<?php

namespace Oro\Bundle\TrackerBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\TrackerBundle\Entity\Project;
use Oro\Bundle\TrackerBundle\Entity\Issue as IssueEntity;

class ActivityRepository extends EntityRepository
{
    const LIMIT = 10;

    /**
     * @param User $user
     * @return mixed
     */
    public function getActivityIssueListWhereUserIsProjectMember(User $user)
    {
        $activities = $this->getEntityManager()->createQuery(
            'SELECT a FROM OroTrackerBundle:Activity a JOIN a.project p
            JOIN p.members u WHERE u = ?1 ORDER BY a.created DESC'
        );
        $activities->setParameter(1, $user);
        $activities->setMaxResults(self::LIMIT);
        return $activities->getResult();
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function getActivityIssueListByUser(User $user)
    {
        $activities = $this->getEntityManager()->createQuery(
            'SELECT a FROM OroTrackerBundle:Activity a WHERE a.user = ?1
            ORDER BY a.created DESC'
        );
        $activities->setParameter(1, $user);
        $activities->setMaxResults(self::LIMIT);
        return $activities->getResult();
    }

    /**
     * @param Project $project
     * @return mixed
     */
    public function getActivityIssueListByProject(Project $project)
    {
        $activities = $this->getEntityManager()->createQuery(
            'SELECT a FROM OroTrackerBundle:Activity a JOIN a.project p
            WHERE p = ?1 ORDER BY a.created DESC'
        );
        $activities->setParameter(1, $project);
        $activities->setMaxResults(self::LIMIT);
        return $activities->getResult();
    }

    /**
     * @param IssueEntity $issue
     * @return mixed
     */
    public function getActivityIssueListByIssue(IssueEntity $issue)
    {
        $activities = $this->getEntityManager()->createQuery(
            'SELECT a FROM OroTrackerBundle:Activity a JOIN a.issue i
            WHERE i.id = ?1 or i.parent = ?1 ORDER BY a.created DESC'
        );
        $activities->setParameter(1, $issue);
        $activities->setMaxResults(self::LIMIT);
        return $activities->getResult();
    }
}