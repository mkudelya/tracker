<?php

namespace Oro\Bundle\TrackerBundle\Tests\Unit\Entity;

use Oro\Bundle\TrackerBundle\Entity\Activity;

class ActivityTest extends AbstractEntityTestCase
{
    /**
     * @var Activity
     */
    protected $entity;

    /**
     * @return string
     */
    public function getEntityFQCN()
    {
        return 'Oro\Bundle\TrackerBundle\Entity\Activity';
    }

    /**
     * @return array
     */
    public function getSetDataProvider()
    {
        $body = 'body';
        $issue = $this->getMock('Oro\Bundle\TrackerBundle\Entity\Issue');
        $user = $this->getMock('Oro\Bundle\UserBundle\Entity\User');
        $created = '2015-01-01';
        $type = 'type';
        $project = $this->getMock('Oro\Bundle\TrackerBundle\Entity\Project');

        return [
            'id' => ['id', 1, 1],
            'body'     => ['body', $body, $body],
            'issue' => ['issue', $issue, $issue],
            'user' => ['user', $user, $user],
            'created' => ['created', $created, $created],
            'type' => ['type', $type, $type],
            'project' => ['project', $project, $project],
        ];
    }

    public function testIsType()
    {
        $this->entity->setType(Activity::NEW_ISSUE_TYPE);
        $this->assertEquals(Activity::NEW_ISSUE_TYPE, $this->entity->isNewIssueType());

        $this->entity->setType(Activity::CHANGED_STATUS_ISSUE_TYPE);
        $this->assertEquals(Activity::CHANGED_STATUS_ISSUE_TYPE, $this->entity->isStatusChangedType());

        $this->entity->setType(Activity::NEW_COMMENT_ISSUE_TYPE);
        $this->assertEquals(Activity::NEW_COMMENT_ISSUE_TYPE, $this->entity->isNewCommentType());
    }

    public function testUpdatedTimestamp()
    {
        $this->entity->updatedTimestamps();
        $this->assertInstanceOf('DateTime', $this->entity->getCreated());
    }
}
