<?php

namespace Oro\TrackerBundle\Tests\EventListener;

use Oro\TrackerBundle\Service\Issue as IssueService;
use Oro\TrackerBundle\EventListener\EntityListener;

class EntityListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $listener;
    protected $mockEntityManager;
    protected $mockIssueEntity;
    protected $mockAssigneeEntity;
    protected $mockReporterEntity;
    protected $mockIssueService;
    protected $mockContainer;
    protected $mockArgs;

    protected function setUp()
    {
        $this->mockIssueEntity = $this
            ->getMockBuilder('Oro\TrackerBundle\Entity\Issue')
            ->setMethods(['addCollaborator'])
            ->getMock();
        $this->mockAssigneeEntity = $this->getMockBuilder('Oro\TrackerBundle\Entity\User')->getMock();
        $this->mockReporterEntity = $this->getMockBuilder('Oro\TrackerBundle\Entity\User')->getMock();
        $this->mockIssueService = $this->getMockBuilder('Oro\TrackerBundle\Service\Issue')
            //->setMethods(['isUserCollaborator'])
            ->disableOriginalConstructor()
            ->getMock();

//        $this->mockIssueService
//            ->expects($this->any())
//            ->method('isUserCollaborator')
//            ->with($this->mockIssueEntity, $this->mockReporterEntity)
//            ->will($this->returnValue(false));


        $this->mockContainer = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->mockContainer->expects($this->any())
            ->method('get')
            ->with('issue')
            ->will($this->returnValue($this->mockIssueService));

        $this->mockIssueEntity->expects($this->any())
            ->method('getReporter')
            ->will($this->returnValue($this->mockReporterEntity));
        $this->mockIssueEntity->expects($this->any())
            ->method('getAssignee')
            ->will($this->returnValue($this->mockAssigneeEntity));

        $this->mockEntityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockArgs = $this->getMockBuilder('Doctrine\ORM\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockArgs->expects($this->any())
            ->method('getEntityManager')
            ->will($this->returnValue($this->mockEntityManager));

        $this->listener = new EntityListener($this->mockContainer);
    }

    public function testPostPersistAddReporterAndAssigneeToCollaborators()
    {
        //special set different id to add assignee user to collaborators too
        $this->mockReporterEntity->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(11111));

        $this->mockArgs->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue($this->mockIssueEntity));

        //main conditionals
//        $this->mockIssueEntity->expects($this->once())
//            ->method('addCollaborator')
//            ->with($this->mockReporterEntity);
//


        $this->listener->postPersist($this->mockArgs);
    }
}
