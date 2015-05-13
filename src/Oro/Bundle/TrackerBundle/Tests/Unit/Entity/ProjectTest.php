<?php

namespace Oro\Bundle\TrackerBundle\Tests\Unit\Entity;

use Oro\Bundle\TrackerBundle\Entity\Project;

class ProjectTest extends AbstractEntityTestCase
{
    /**
     * @var Project
     */
    protected $entity;

    /**
     * @return string
     */
    public function getEntityFQCN()
    {
        return 'Oro\Bundle\TrackerBundle\Entity\Project';
    }

    /**
     * @return array
     */
    public function getSetDataProvider()
    {
        $label = 'label';
        $summary = 'summary';
        $code = 'code _ 5 !';
        $expectedCode = 'CODE_5';

        return [
            'id' => ['id', 1, 1],
            'label'     => ['label', $label, $label],
            'summary' => ['summary', $summary, $summary],
            'code' => ['code', $code, $expectedCode],
        ];
    }

    public function testIssues()
    {
        $mock = $this->getMockBuilder('Oro\Bundle\TrackerBundle\Entity\Issue')->getMock();
        $mock
            ->expects($this->exactly(1))
            ->method('getId')
            ->will($this->returnValue(self::TEST_ID));

        $this->assertCount(0, $this->entity->getIssues());
        $this->assertEquals($this->entity, $this->entity->addIssue($mock));
        $this->assertEquals(self::TEST_ID, $this->entity->getIssues()->get(0)->getId());
        $this->entity->removeIssue($mock);
        $this->assertCount(0, $this->entity->getIssues());
    }

    public function testActivity()
    {
        $mock = $this->getMockBuilder('Oro\Bundle\TrackerBundle\Entity\Activity')->getMock();
        $mock
            ->expects($this->exactly(1))
            ->method('getId')
            ->will($this->returnValue(self::TEST_ID));

        $this->assertCount(0, $this->entity->getActivities());
        $this->assertEquals($this->entity, $this->entity->addActivity($mock));
        $this->assertEquals(self::TEST_ID, $this->entity->getActivities()->get(0)->getId());
        $this->entity->removeActivity($mock);
        $this->assertCount(0, $this->entity->getActivities());
    }

    public function testMembers()
    {
        $mock = $this->getMockBuilder('Oro\Bundle\TrackerBundle\Entity\User')->getMock();
        $mock
            ->expects($this->exactly(3))
            ->method('getId')
            ->will($this->returnValue(self::TEST_ID));

        $this->assertCount(0, $this->entity->getMembers());
        $this->assertEquals($this->entity, $this->entity->addMember($mock));
        $this->assertEquals(self::TEST_ID, $this->entity->getMembers()->get(0)->getId());
        $this->assertEquals(true, $this->entity->hasMember($mock));
        $this->entity->removeMember($mock);
        $this->assertCount(0, $this->entity->getMembers());
        $this->assertEquals(false, $this->entity->hasMember($mock));
    }
}
