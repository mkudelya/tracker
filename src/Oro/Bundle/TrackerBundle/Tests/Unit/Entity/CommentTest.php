<?php

namespace Oro\Bundle\TrackerBundle\Tests\Unit\Entity;

use Oro\Bundle\TrackerBundle\Entity\Comment;

class CommentTest extends AbstractEntityTestCase
{
    /**
     * @var Comment
     */
    protected $entity;

    /**
     * @return string
     */
    public function getEntityFQCN()
    {
        return 'Oro\Bundle\TrackerBundle\Entity\Comment';
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
