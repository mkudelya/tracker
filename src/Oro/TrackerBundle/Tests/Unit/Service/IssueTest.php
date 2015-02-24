<?php

namespace Oro\TrackerBundle\Tests\Unit\Service;

use Oro\TrackerBundle\Service\Issue;

class IssueTest extends \PHPUnit_Framework_TestCase
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
            ->setMethods(array(
                'getManager', 'createQuery', 'getResult', 'setParameter', 'getRepository', 'findOneByCode', 'findBy'
            ))
            ->getMock();

        $this->mockDoctrine->expects($this->any())
            ->method('getManager')
            ->will($this->returnValue($this->mockDoctrine));

        $this->mockDoctrine->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($this->mockDoctrine));

        $this->mockDoctrine->expects($this->any())
            ->method('findOneByCode')
            ->will($this->returnValue(new \stdClass()));

        $this->mockDoctrine->expects($this->any())
            ->method('createQuery')
            ->with($this->isType('string'))
            ->will($this->returnValue($this->mockDoctrine));

        $this->mockDoctrine->expects($this->any())
            ->method('getResult')
            ->will($this->returnValue(array('test1', 'test2')));

        $this->mockDoctrine->expects($this->any())
            ->method('findBy')
            ->will($this->returnValue(array('test1', 'test2')));

        $this->mockContainer->expects($this->any())
            ->method('get')
            ->with('doctrine')
            ->will($this->returnValue($this->mockDoctrine));

        $this->service = new Issue($this->mockContainer);
    }

    public function testGetIssueListByProjectCode()
    {
        $this->assertCount(2, $this->service->getIssueListByProjectCode('test'));
    }

    public function testGetIssueSubListByIssueCode()
    {
        $this->assertCount(2, $this->service->getIssueSubListByIssueCode('test'));
    }

    public function testGetActivityIssueListByUser()
    {
        $obj = $this->getMockBuilder('Oro\TrackerBundle\Entity\User')
            ->getMock();

        $this->assertCount(2, $this->service->getIssueListByCollaborationUser($obj));
    }

    public function testGetIssueListByAssigneeUser()
    {
        $obj = $this->getMockBuilder('Oro\TrackerBundle\Entity\User')
            ->getMock();

        $this->assertCount(2, $this->service->getIssueListByAssigneeUser($obj));
    }

    public function testGetCollaborationListByIssue()
    {
        $obj = $this->getMockBuilder('Oro\TrackerBundle\Entity\Issue')
            ->getMock();

        $this->assertCount(2, $this->service->getCollaborationListByIssue($obj));
    }

    public function testGetCommentListByIssueCode()
    {
        $this->assertCount(2, $this->service->getCommentListByIssueCode('test'));
    }

    public function testIsUserCollaborator()
    {
        $issue = $this->getMockBuilder('Oro\TrackerBundle\Entity\Issue')
            ->getMock();

        $user = $this->getMockBuilder('Oro\TrackerBundle\Entity\User')
            ->getMock();

        $issue->expects($this->any())
            ->method('hasCollaborator')
            ->will($this->returnValue(true));

        $this->assertTrue($this->service->isUserCollaborator($issue, $user));
    }
}
