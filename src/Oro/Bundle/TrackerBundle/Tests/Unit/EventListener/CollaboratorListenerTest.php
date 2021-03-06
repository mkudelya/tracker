<?php

namespace Oro\Bundle\TrackerBundle\Tests\Unit\EventListener;

use Oro\Bundle\TrackerBundle\EventListener\CollaboratorListener;

class CollaboratorListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CollaboratorListener
     */
    protected $listener;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockEntityManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockIssueEntity;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockCommentEntity;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockAssigneeEntity;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockReporterEntity;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockIssueService;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockContainer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockArgs;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockUsernamePasswordToken;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSecurityContext;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockActivityEntity;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockUnitOfWork;

    protected function setUp()
    {
        $this->mockIssueEntity = $this->getMockBuilder('Oro\Bundle\TrackerBundle\Entity\Issue')->getMock();
        $this->mockCommentEntity = $this->getMockBuilder('Oro\Bundle\TrackerBundle\Entity\Comment')->getMock();
        $this->mockAssigneeEntity = $this->getMockBuilder('Oro\Bundle\UserBundle\Entity\User')->getMock();
        $this->mockReporterEntity = $this->getMockBuilder('Oro\Bundle\UserBundle\Entity\User')->getMock();
        $this->mockUnitOfWork = $this->getMockBuilder('Doctrine\ORM\UnitOfWork')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockSecurityContext = $this
            ->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockUsernamePasswordToken = $this
            ->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockIssueService =
            $this->getMockBuilder('Oro\Bundle\TrackerBundle\Service\Issue')
                ->disableOriginalConstructor()
                ->getMock('Oro\Bundle\TrackerBundle\Service\Issue');

        //Security context
        $this->mockSecurityContext->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($this->mockUsernamePasswordToken));

        //Unit Of Work
        $this->mockUnitOfWork->expects($this->any())
            ->method('getEntityChangeSet')
            ->will($this->returnValue(array('status' => 1)));

        //Authentication User
        $this->mockUsernamePasswordToken->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($this->mockAssigneeEntity));

        //container
        $this->mockContainer = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->getMock();

        //comment
        $this->mockCommentEntity->expects($this->any())
            ->method('getIssue')
            ->will($this->returnValue($this->mockIssueEntity));
        $this->mockCommentEntity->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($this->mockReporterEntity));

        //issue
        $this->mockIssueEntity->expects($this->any())
            ->method('getReporter')
            ->will($this->returnValue($this->mockReporterEntity));
        $this->mockIssueEntity->expects($this->any())
            ->method('getAssignee')
            ->will($this->returnValue($this->mockAssigneeEntity));
        //entity manager
        $this->mockEntityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        //LifecycleEventArgs
        $this->mockArgs = $this->getMockBuilder('Doctrine\ORM\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockArgs->expects($this->any())
            ->method('getEntityManager')
            ->will($this->returnValue($this->mockEntityManager));

        $this->listener = new CollaboratorListener($this->mockContainer);
    }

    public function testAddReporterAndAssigneeToCollaboratorsSuccess()
    {
        //special set different id to add assignee user to collaborators too
        $this->mockReporterEntity->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(11111));

        $this->mockArgs->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue($this->mockIssueEntity));

        $this->mockContainer->expects($this->any())
            ->method('get')
            ->with('issue')
            ->will($this->returnValue($this->mockIssueService));

        //main conditionals
        $this->mockIssueService->expects($this->any())
            ->method('isUserCollaborator')
            ->will($this->returnValue(false));

        $this->mockIssueEntity->expects($this->exactly(2))
            ->method('addCollaborator')
            ->with($this->isType('object'));

        $this->mockEntityManager->expects($this->exactly(1))
            ->method('flush');

        $this->listener->updateCollaborators($this->mockArgs);
    }

    public function testAddReporterAndAssigneeToCollaboratorsFailure()
    {
        //special set different id to add assignee user to collaborators too
        $this->mockReporterEntity->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(11111));

        $this->mockArgs->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue($this->mockIssueEntity));

        $this->mockContainer->expects($this->any())
            ->method('get')
            ->with('issue')
            ->will($this->returnValue($this->mockIssueService));

        //main conditionals
        $this->mockIssueService->expects($this->any())
            ->method('isUserCollaborator')
            ->will($this->returnValue(true));

        $this->mockIssueEntity->expects($this->never())
            ->method('addCollaborator');

        $this->mockEntityManager->expects($this->never())
            ->method('flush');

        $this->listener->updateCollaborators($this->mockArgs);
    }

    public function testAddUserAfterCommentToCollaboratorsSuccess()
    {
        $this->mockArgs->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue($this->mockCommentEntity));

        $this->mockContainer->expects($this->any())
            ->method('get')
            ->with('issue')
            ->will($this->returnValue($this->mockIssueService));

        //main conditionals
        $this->mockIssueService->expects($this->any())
            ->method('isUserCollaborator')
            ->will($this->returnValue(false));

        $this->mockIssueEntity->expects($this->once())
            ->method('addCollaborator')
            ->with($this->mockReporterEntity);

        $this->mockEntityManager->expects($this->once())
            ->method('flush');

        $this->listener->updateCollaborators($this->mockArgs);
    }
}
