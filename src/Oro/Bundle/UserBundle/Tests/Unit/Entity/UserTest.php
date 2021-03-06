<?php

namespace Oro\Bundle\UserBundle\Tests\Unit\Entity;

use Oro\Bundle\UserBundle\Entity\User;

class UserTest extends AbstractEntityTestCase
{
    /**
     * @var User
     */
    protected $entity;

    /**
     * @return string
     */
    public function getEntityFQCN()
    {
        return 'Oro\Bundle\UserBundle\Entity\User';
    }

    /**
     * @return array
     */
    public function getSetDataProvider()
    {
        $fullname = 'full';
        $avatar = 'avatar';
        $timezone = 'America/Los_Angeles';

        return [
            'id' => ['id', 1, 1],
            'fullname'     => ['fullname', $fullname, $fullname],
            'avatar' => ['avatar', $avatar, $avatar],
            'timezone' => ['timezone', $timezone, $timezone],
        ];
    }

    public function testAvatar()
    {
        $avatarFileMock =  $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
            ->disableOriginalConstructor()
            ->getMock();
        $this->entity->setAvatarFile($avatarFileMock);
        $this->assertEquals($avatarFileMock, $this->entity->getAvatarFile());

        $this->entity->setAvatar('1.jpg');
        $this->entity->setAvatarFile($avatarFileMock);
        $this->assertEquals(null, $this->entity->getAvatar());

        $this->entity->upload();
        $this->assertNotEmpty($this->entity->getAvatar());

        $this->assertContains($this->entity->getAvatar(), $this->entity->getAbsoluteAvatarPath());
        $this->assertContains($this->entity->getAvatar(), $this->entity->getWebAvatarPath());
    }

    public function testProject()
    {
        $mock = $this->getMockBuilder('Oro\Bundle\TrackerBundle\Entity\Project')->getMock();
        $mock
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(self::TEST_ID));

        $this->assertCount(0, $this->entity->getProjects());
        $this->assertEquals($this->entity, $this->entity->addProject($mock));
        $this->assertEquals(self::TEST_ID, $this->entity->getProjects()->get(0)->getId());
        $this->entity->removeProject($mock);
        $this->assertCount(0, $this->entity->getProjects());
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
}
