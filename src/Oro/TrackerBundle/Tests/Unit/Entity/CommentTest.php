<?php

namespace Oro\TrackerBundle\Tests\Unit\Entity;

use Oro\TrackerBundle\Entity\Comment;

class CommentTest extends AbstractEntityTestCase
{
    /**
     * @var Comment
     */
    protected $entity;

    public function getEntityFQCN()
    {
        return 'Oro\TrackerBundle\Entity\Comment';
    }

    public function getSetDataProvider()
    {
        $body = 'body';
        $issue = $this->getMock('Oro\TrackerBundle\Entity\Issue');
        $user = $this->getMock('Oro\TrackerBundle\Entity\User');
        $created = '2015-01-01';

        return [
            'id' => ['id', 1, 1],
            'body'     => ['body', $body, $body],
            'issue' => ['issue', $issue, $issue],
            'user' => ['user', $user, $user],
            'created' => ['created', $created, $created],
        ];
    }

    public function testUpdatedTimestamp()
    {
        $this->entity->updatedTimestamps();
        $this->assertInstanceOf('DateTime', $this->entity->getCreated());
    }
}
