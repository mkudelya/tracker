<?php

namespace Oro\TrackerBundle\Tests\Unit\EventListener;

use Oro\TrackerBundle\EventListener\EntityListener;
use Doctrine\Common\Collections\ArrayCollection;

class EntityListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $listener;
    protected $mockEntityManager;
    protected $mockIssueEntity;
    protected $mockCommentEntity;
    protected $mockAssigneeEntity;
    protected $mockReporterEntity;
    protected $mockIssueService;
    protected $mockContainer;
    protected $mockArgs;
    protected $mockUsernamePasswordToken;
    protected $mockSecurityContext;
    protected $mockActivityEntity;
    protected $mockUnitOfWork;

    protected function setUp()
    {
        $this->mockIssueEntity = $this->getMockBuilder('Oro\TrackerBundle\Entity\Issue')->getMock();
        $this->mockCommentEntity = $this->getMockBuilder('Oro\TrackerBundle\Entity\Comment')->getMock();
        $this->mockAssigneeEntity = $this->getMockBuilder('Oro\TrackerBundle\Entity\User')->getMock();
        $this->mockReporterEntity = $this->getMockBuilder('Oro\TrackerBundle\Entity\User')->getMock();
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
            $this->getMockBuilder('Oro\TrackerBundle\Service\Issue')
            ->disableOriginalConstructor()
            ->getMock('Oro\TrackerBundle\Service\Issue');

        $this->mockAuthenticationToken = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token')
            ->disableOriginalConstructor()
            ->getMock();

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

        $this->listener = new EntityListener($this->mockContainer);
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

    public function testAddNewIssueToActivitySuccess()
    {
        $this->mockArgs->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue($this->mockIssueEntity));

        $this->mockContainer->expects($this->any())
            ->method('get')
            ->with('security.context')
            ->will($this->returnValue($this->mockSecurityContext));

        $this->mockActivityEntity = $this->getMockBuilder('Oro\TrackerBundle\Entity\Activity')->getMock();
        $this->mockActivityEntity->expects($this->any())
            ->method('getIssue')
            ->will($this->returnValue($this->mockIssueEntity));

        $this->mockIssueEntity->expects($this->any())
            ->method('getCollaborators')
            ->will($this->returnValue(new ArrayCollection()));

        $this->mockEntityManager->expects($this->once())
            ->method('flush');

        $this->listener->addToActivity($this->mockArgs, true);
    }

    public function testUpdateStatusIssueToActivitySuccess()
    {
        $this->mockArgs->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue($this->mockIssueEntity));

        $this->mockContainer->expects($this->any())
            ->method('get')
            ->with('security.context')
            ->will($this->returnValue($this->mockSecurityContext));

        $this->mockActivityEntity = $this->getMockBuilder('Oro\TrackerBundle\Entity\Activity')->getMock();
        $this->mockActivityEntity->expects($this->any())
            ->method('getIssue')
            ->will($this->returnValue($this->mockIssueEntity));

        $this->mockEntityManager->expects($this->any())
            ->method('getUnitOfWork')
            ->will($this->returnValue($this->mockUnitOfWork));

        $this->mockIssueEntity->expects($this->any())
            ->method('getCollaborators')
            ->will($this->returnValue(new ArrayCollection()));

        $this->mockEntityManager->expects($this->once())
            ->method('flush');

        $this->listener->addToActivity($this->mockArgs, false);
    }

    public function testAddCommentToActivitySuccess()
    {
        $this->mockArgs->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue($this->mockCommentEntity));

        $this->mockContainer->expects($this->any())
            ->method('get')
            ->with('security.context')
            ->will($this->returnValue($this->mockSecurityContext));

        $this->mockIssueEntity->expects($this->any())
            ->method('getCollaborators')
            ->will($this->returnValue(new ArrayCollection()));

        $this->mockEntityManager->expects($this->once())
            ->method('flush');

        $this->listener->addToActivity($this->mockArgs, true);
    }

    public function testActivityEmailNotificationSuccess()
    {
        $mailer = $this->getMockBuilder('\stdClass')->setMethods(array('send'))->getMock();
        $mailer->expects($this->any())
            ->method('send')
            ->will($this->returnValue(true));

        $renderer = $this->getMockBuilder('\stdClass')->setMethods(array('render'))->getMock();
        $renderer->expects($this->any())
        ->method('render')
        ->will($this->returnValue('body'));

        $this->mockContainer->expects($this->at(0))
            ->method('get')
            ->with('templating')
            ->will($this->returnValue($renderer));

        $this->mockContainer->expects($this->at(1))
            ->method('get')
            ->with('mailer')
            ->will($this->returnValue($mailer));

        $this->mockActivityEntity = $this->getMockBuilder('Oro\TrackerBundle\Entity\Activity')->getMock();
        $this->mockActivityEntity->expects($this->any())
            ->method('getIssue')
            ->will($this->returnValue($this->mockIssueEntity));

        $collaborators = new ArrayCollection();
        $collaborators->add($this->mockReporterEntity);

        $this->mockIssueEntity->expects($this->any())
            ->method('getCollaborators')
            ->will($this->returnValue($collaborators));

        $this->listener->activityEmailNotification($this->mockActivityEntity);
    }
}
