<?php

namespace Oro\Bundle\TrackerBundle\Tests\Unit\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

use Oro\Bundle\TrackerBundle\Security\Authorization\Voter\CommentVoter;
use Oro\Bundle\TrackerBundle\Entity\Comment;
use Oro\Bundle\TrackerBundle\Entity\User;
use Oro\Bundle\TrackerBundle\Entity\Role;

class CommentVoterTest extends \PHPUnit_Framework_TestCase
{
    /** @var CommentVoter */
    protected $voter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockUsernamePasswordToken;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockUserEntity;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockObject;

    protected function setUp()
    {
        $this->voter = new CommentVoter();

        $this->mockUsernamePasswordToken = $this
            ->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockUserEntity = $this
            ->getMockBuilder('Oro\Bundle\TrackerBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockObject = $this
            ->getMockBuilder('Oro\Bundle\TrackerBundle\Entity\Comment')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown()
    {
        unset($this->voter);
    }

    public function testSupportsAttributeSuccess()
    {
        $this->assertTrue($this->voter->supportsAttribute(CommentVoter::ADD));
        $this->assertTrue($this->voter->supportsAttribute(CommentVoter::DELETE));
        $this->assertTrue($this->voter->supportsAttribute(CommentVoter::EDIT));
    }

    public function testSupportsAttributeFailure()
    {
        $this->assertFalse($this->voter->supportsAttribute('quit'));
    }

    public function testSupportsClassSuccess()
    {
        $this->assertTrue($this->voter->supportsClass(get_class(new Comment())));
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
     * @expectedExceptionMessage Only one attribute is allowed for DELETE or EDIT
     */
    public function testAttributeCountThrowException()
    {
        $this->voter->vote($this->mockUsernamePasswordToken, new Comment(), array('edit', 'show'));
    }

    public function testAttributeSupportFailure()
    {
        $this->assertEquals(
            VoterInterface::ACCESS_ABSTAIN,
            $this->voter->vote(
                $this->mockUsernamePasswordToken,
                new Comment(),
                array('test')
            )
        );
    }

    public function testUserCheckInstanceFailure()
    {
        $this->mockUsernamePasswordToken->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue(new Comment()));

        $this->assertEquals(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote(
                $this->mockUsernamePasswordToken,
                new Comment(),
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
                new Comment(),
                array('edit')
            )
        );
    }

    public function testUserCheckSuccess()
    {
        $this->mockUserEntity->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(111));

        $this->mockObject->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($this->mockUserEntity));

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

    public function testUserCheckFailure()
    {
        $mockNonGrantedUserEntity = $this
            ->getMockBuilder('Oro\Bundle\TrackerBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $mockNonGrantedUserEntity->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(222));

        $this->mockUserEntity->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(111));

        $this->mockObject->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($mockNonGrantedUserEntity));

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
    }
}
