<?php

namespace Oro\Bundle\TrackerBundle\Tests\Unit\Entity;

use Oro\Bundle\TrackerBundle\Entity\Issue;

class IssueTest extends AbstractEntityTestCase
{
    /**
     * @var Issue
     */
    protected $entity;

    /**
     * @return string
     */
    public function getEntityFQCN()
    {
        return 'Oro\Bundle\TrackerBundle\Entity\Issue';
    }

    /**
     * @return array
     */
    public function getSetDataProvider()
    {
        $summary = 'summary';
        $code = 'code _ 5 !';
        $expectedCode = 'CODE_5';
        $description = 'description';
        $type = 'type';
        $priority = 6;
        $status = 'open';
        $resolution = 'done';
        $created = '2015-01-01';
        $updated = '2015-01-01';
        $parent = $this->getMock('Oro\Bundle\TrackerBundle\Entity\Issue');
        $project = $this->getMock('Oro\Bundle\TrackerBundle\Entity\Project');
        $reporter = $this->getMock('Oro\Bundle\TrackerBundle\Entity\User');
        $assignee = $this->getMock('Oro\Bundle\TrackerBundle\Entity\User');

        return [
            'id' => ['id', 1, 1],
            'summary'     => ['summary', $summary, $summary],
            'code' => ['code', $code, $expectedCode],
            'description' => ['description', $description, $description],
            'type' => ['type', $type, $type],
            'priority' => ['priority', $priority, $priority],
            'status' => ['status', $status, $status],
            'resolution' => ['resolution', $resolution, $resolution],
            'created' => ['created', $created, $created],
            'updated' => ['updated', $updated, $updated],
            'parent' => ['parent', $parent, $parent],
            'project' => ['project', $project, $project],
            'reporter' => ['reporter', $reporter, $reporter],
            'assignee' => ['assignee', $assignee, $assignee],
        ];
    }

    public function testCollaborators()
    {
        $mock = $this->getMockBuilder('Oro\Bundle\TrackerBundle\Entity\User')->getMock();
        $mock
            ->expects($this->exactly(3))
            ->method('getId')
            ->will($this->returnValue(self::TEST_ID));

        $this->assertCount(0, $this->entity->getCollaborators());
        $this->assertEquals($this->entity, $this->entity->addCollaborator($mock));
        $this->assertEquals(self::TEST_ID, $this->entity->getCollaborators()->get(0)->getId());
        $this->assertEquals(true, $this->entity->hasCollaborator($mock));
        $this->entity->removeCollaborator($mock);
        $this->assertCount(0, $this->entity->getCollaborators());
        $this->assertEquals(false, $this->entity->hasCollaborator($mock));
    }

    public function testChildren()
    {
        $mock = $this->getMockBuilder('Oro\Bundle\TrackerBundle\Entity\Issue')->getMock();
        $mock
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(self::TEST_ID));

        $this->assertCount(0, $this->entity->getChildren());
        $this->assertEquals($this->entity, $this->entity->addChild($mock));
        $this->assertEquals(self::TEST_ID, $this->entity->getChildren()->get(0)->getId());
        $this->entity->removeChild($mock);
        $this->assertCount(0, $this->entity->getChildren());
    }

    public function testComments()
    {
        $mock = $this->getMockBuilder('Oro\Bundle\TrackerBundle\Entity\Comment')->getMock();
        $mock
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(self::TEST_ID));

        $this->assertCount(0, $this->entity->getComments());
        $this->assertEquals($this->entity, $this->entity->addComment($mock));
        $this->assertEquals(self::TEST_ID, $this->entity->getComments()->get(0)->getId());
        $this->entity->removeComment($mock);
        $this->assertCount(0, $this->entity->getComments());
    }

    public function testActivity()
    {
        $mock = $this->getMockBuilder('Oro\Bundle\TrackerBundle\Entity\Activity')->getMock();
        $mock
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(self::TEST_ID));

        $this->assertCount(0, $this->entity->getActivities());
        $this->assertEquals($this->entity, $this->entity->addActivity($mock));
        $this->assertEquals(self::TEST_ID, $this->entity->getActivities()->get(0)->getId());
        $this->entity->removeActivity($mock);
        $this->assertCount(0, $this->entity->getActivities());
    }

    public function testToString()
    {
        $this->entity->setCode('test');
        $this->assertEquals('TEST', $this->entity);
    }

    public function testUpdatedTimestamp()
    {
        $this->entity->updatedTimestamps();
        $this->assertInstanceOf('DateTime', $this->entity->getCreated());
    }
}
