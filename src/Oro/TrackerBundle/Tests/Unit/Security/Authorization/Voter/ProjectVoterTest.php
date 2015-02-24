<?php

namespace Oro\TrackerBundle\Tests\Unit\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Oro\TrackerBundle\Security\Authorization\Voter\ProjectVoter;
use Oro\TrackerBundle\Entity\Project;
use Oro\TrackerBundle\Entity\User;
use Oro\TrackerBundle\Entity\Role;

class ProjectVoterTest extends \PHPUnit_Framework_TestCase
{
    /** @var ProjectVoter */
    protected $voter;
    protected $mockUsernamePasswordToken;
    protected $mockUserEntity;
    protected $mockObject;

    protected function setUp()
    {
        $this->voter = new ProjectVoter();

        $this->mockUsernamePasswordToken = $this
            ->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockUserEntity = $this
            ->getMockBuilder('Oro\TrackerBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockObject = $this
            ->getMockBuilder('Oro\TrackerBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown()
    {
        unset($this->voter);
    }

    public function testSupportsAttributeSuccess()
    {
        $this->assertTrue($this->voter->supportsAttribute(ProjectVoter::ADD_ISSUE));
        $this->assertTrue($this->voter->supportsAttribute(ProjectVoter::VIEW));
        $this->assertTrue($this->voter->supportsAttribute(ProjectVoter::EDIT));
    }

    public function testSupportsAttributeFailure()
    {
        $this->assertFalse($this->voter->supportsAttribute('quit'));
    }

    public function testSupportsClassSuccess()
    {
        $this->assertTrue($this->voter->supportsClass(get_class(new Project())));
    }

    public function testSupportsClassFailure()
    {
        $this->assertFalse($this->voter->supportsClass(get_class(new User())));
    }

    public function testSupportsClassVoteFailure()
    {
        $this->assertEquals(
            VoterInterface::ACCESS_ABSTAIN,
            $this->voter->vote(
                $this->mockUsernamePasswordToken,
                new User(),
                array('edit')
            )
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Only one attribute is allowed for VIEW or EDIT
     */
    public function testAttributeCountThrowException()
    {
        $this->voter->vote($this->mockUsernamePasswordToken, new Project(), array('edit', 'show'));
    }

    public function testAttributeSupportFailure()
    {
        $this->assertEquals(
            VoterInterface::ACCESS_ABSTAIN,
            $this->voter->vote(
                $this->mockUsernamePasswordToken,
                new Project(),
                array('test')
            )
        );
    }

    public function testUserCheckInstanceFailure()
    {
        $this->mockUsernamePasswordToken->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue(new Project()));

        $this->assertEquals(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote(
                $this->mockUsernamePasswordToken,
                new Project(),
                array('edit')
            )
        );
    }

    public function testUserCheckAdministratorSuccess()
    {
        $this->mockUserEntity->expects($this->at(0))
            ->method('hasRole')
            ->with(Role::ROLE_ADMINISTRATOR)
            ->will($this->returnValue(true));

        $this->mockUsernamePasswordToken->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($this->mockUserEntity));

        $this->assertEquals(
            VoterInterface::ACCESS_GRANTED,
            $this->voter->vote(
                $this->mockUsernamePasswordToken,
                new Project(),
                array('edit')
            )
        );
    }

    public function testUserCheckManagerSuccess()
    {
        $this->mockUserEntity->expects($this->at(1))
            ->method('hasRole')
            ->with(Role::ROLE_MANAGER)
            ->will($this->returnValue(true));

        $this->mockUsernamePasswordToken->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($this->mockUserEntity));

        $this->assertEquals(
            VoterInterface::ACCESS_GRANTED,
            $this->voter->vote(
                $this->mockUsernamePasswordToken,
                new Project(),
                array('edit')
            )
        );
    }

    public function testAddIssueSuccess()
    {
        $this->mockObject->expects($this->any())
            ->method('hasMember')
            ->will($this->returnValue(true));

        $this->mockUsernamePasswordToken->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($this->mockUserEntity));

        $this->assertEquals(
            VoterInterface::ACCESS_GRANTED,
            $this->voter->vote(
                $this->mockUsernamePasswordToken,
                $this->mockObject,
                array('add_issue')
            )
        );
    }

    public function testCheckUserFailure()
    {
        $this->mockObject->expects($this->any())
            ->method('hasMember')
            ->will($this->returnValue(false));

        $this->mockUsernamePasswordToken->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($this->mockUserEntity));

        $this->assertEquals(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote(
                $this->mockUsernamePasswordToken,
                $this->mockObject,
                array('add_issue')
            )
        );
    }
}
