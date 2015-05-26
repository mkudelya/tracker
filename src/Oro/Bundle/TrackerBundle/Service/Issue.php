<?php

namespace Oro\Bundle\TrackerBundle\Service;

use Oro\Bundle\TrackerBundle\Entity\Issue as IssueEntity;
use Oro\Bundle\UserBundle\Entity\User;

class Issue
{
    /**
     * @param IssueEntity $issue
     * @param User $user
     * @return bool
     */
    public function isUserCollaborator(IssueEntity $issue, User $user)
    {
        return $issue->hasCollaborator($user);
    }
}
