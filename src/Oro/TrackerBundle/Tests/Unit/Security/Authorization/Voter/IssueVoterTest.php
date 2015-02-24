<?php

namespace Oro\TrackerBundle\Tests\Unit\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Oro\TrackerBundle\Security\Authorization\Voter\IssueVoter;
use Oro\TrackerBundle\Entity\Issue;
use Oro\TrackerBundle\Entity\User;
use Oro\TrackerBundle\Entity\Role;

class IssueVoterTest extends \PHPUnit_Framework_TestCase
{
    /** @var IssueVoter */
    protected $voter;
    protected $mockUsernamePasswordToken;
    protected $mockUserEntity;
    protected $mockProjectEntity;
    protected $mockObject;

    protected function setUp()
    {
        $this->voter = new IssueVoter();

        $this->mockUsernamePasswordToken = $this
            ->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockUserEntity = $this
            ->getMockBuilder('Oro\TrackerBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockProjectEntity = $this
            ->getMockBuilder('Oro\TrackerBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockObject = $this
            ->getMockBuilder('Oro\TrackerBundle\Entity\Issue')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown()
    {
        unset($this->voter);
    }

    public function testSupportsAttributeSuccess()
    {
        $this->assertTrue($this->voter->supportsAttribute(IssueVoter::ADD_COMMENT));
        $this->assertTrue($this->voter->supportsAttribute(IssueVoter::VIEW));
        $this->assertTrue($this->voter->supportsAttribute(IssueVoter::EDIT));
    }

    public function testSupportsAttributeFailure()
    {
        $this->assertFalse($this->voter->supportsAttribute('quit'));
    }

    public function testSupportsClassSuccess()
    {
        $this->assertTrue($this->voter->supportsClass(get_class(new Issue())));
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
        $this->voter->vote($this->mockUsernamePasswordToken, new Issue(), array('edit', 'show'));
    }

    public function testAttributeSupportFailure()
    {
        $this->assertEquals(
            VoterInterface::ACCESS_ABSTAIN,
            $this->voter->vote(
                $this->mockUsernamePasswordToken,
                new Issue(),
                array('test')
            )
        );
    }

    public function testUserCheckInstanceFailure()
    {
        $this->mockUsernamePasswordToken->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue(new Issue()));

        $this->assertEquals(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote(
                $this->mockUsernamePasswordToken,
                new Issue(),
                array('edit')
            )
        );
    }

    public function testUserCheckAdministratorSuccess()
    {
        $this->mockUserEntity->expects($this->any())
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
                new Issue(),
                array('edit')
            )
        );
    }

    public function testAddCommentSuccess()
    {
        $this->mockProjectEntity->expects($this->any())
            ->method('hasMember')
            ->will($this->returnValue(true));

        $this->mockObject->expects($this->any())
            ->method('getProject')
            ->will($this->returnValue($this->mockProjectEntity));

        $this->mockUsernamePasswordToken->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($this->mockUserEntity));

        $this->assertEquals(
            VoterInterface::ACCESS_GRANTED,
            $this->voter->vote(
                $this->mockUsernamePasswordToken,
                $this->mockObject,
                array('add_comment')
            )
        );
    }

    public function testEditIssueSuccess()
    {
        $this->mockProjectEntity->expects($this->any())
            ->method('hasMember')
            ->will($this->returnValue(true));

        $this->mockObject->expects($this->any())
            ->method('getProject')
            ->will($this->returnValue($this->mockProjectEntity));

        $this->mockUsernamePasswordToken->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($this->mockUserEntity));

        $this->assertEquals(
            VoterInterface::ACCESS_GRANTED,
            $this->voter->vote(
                $this->mockUsernamePasswordToken,
                $this->mockObject,
                array('edit')
            )
        );
    }

    public function testCheckUserFailure()
    {
        $this->mockProjectEntity->expects($this->any())
            ->method('hasMember')
            ->will($this->returnValue(false));

        $this->mockObject->expects($this->any())
            ->method('getProject')
            ->will($this->returnValue($this->mockProjectEntity));

        $this->mockUsernamePasswordToken->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($this->mockUserEntity));

        $this->assertEquals(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote(
                $this->mockUsernamePasswordToken,
                $this->mockObject,
                array('edit')
            )
        );

        $this->assertEquals(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote(
                $this->mockUsernamePasswordToken,
                $this->mockObject,
                array('add_comment')
            )
        );
    }
}
