<?php

namespace Oro\Bundle\TrackerBundle\Tests\Unit\Service;

use Oro\Bundle\TrackerBundle\Service\Activity;

class ActivityTest extends \PHPUnit_Framework_TestCase
{
    protected $service;
    protected $mockContainer;
    protected $mockDoctrine;

    protected function setUp()
    {
        //container
        $this->mockContainer = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->getMock();

        //doctrine
        $this->mockDoctrine = $this->getMockBuilder('\stdClass')
            ->setMethods(array('getManager', 'createQuery', 'getResult', 'setParameter'))
            ->getMock();

        $this->mockDoctrine->expects($this->any())
            ->method('getManager')
            ->will($this->returnValue($this->mockDoctrine));

        $this->mockDoctrine->expects($this->any())
            ->method('createQuery')
            ->with($this->isType('string'))
            ->will($this->returnValue($this->mockDoctrine));

        $this->mockDoctrine->expects($this->any())
            ->method('getResult')
            ->will($this->returnValue(array('test1', 'test2')));

        $this->mockContainer->expects($this->any())
            ->method('get')
            ->with('doctrine')
            ->will($this->returnValue($this->mockDoctrine));

        $this->service = new Activity($this->mockContainer);
    }

    public function testGetActivityIssueListWhereUserIsProjectMember()
    {
        $obj = $this->getMockBuilder('Oro\Bundle\UserBundle\Entity\User')
            ->getMock();

        $this->assertCount(2, $this->service->getActivityIssueListWhereUserIsProjectMember($obj));
    }

    public function testGetActivityIssueListByUser()
    {
        $obj = $this->getMockBuilder('Oro\Bundle\UserBundle\Entity\User')
            ->getMock();

        $this->assertCount(2, $this->service->getActivityIssueListByUser($obj));
    }

    public function testGetActivityIssueListByProject()
    {
        $obj = $this->getMockBuilder('Oro\Bundle\TrackerBundle\Entity\Project')
            ->getMock();

        $this->assertCount(2, $this->service->getActivityIssueListByProject($obj));
    }

    public function testGetActivityIssueListByIssue()
    {
        $obj = $this->getMockBuilder('Oro\Bundle\TrackerBundle\Entity\Issue')
            ->getMock();

        $this->assertCount(2, $this->service->getActivityIssueListByIssue($obj));
    }
}
