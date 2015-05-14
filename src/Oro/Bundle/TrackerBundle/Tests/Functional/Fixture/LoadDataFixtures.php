<?php

namespace Oro\Bundle\TrackerBundle\Tests\Functional\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Oro\Bundle\UserBundle\Entity\Role;

class LoadDataFixtures extends AbstractFixture implements ContainerAwareInterface
{
    const ADMIN_USERNAME = 'test';
    const ADMIN_PASSWORD = 'test';

    /**
     * @var ObjectManager
     */
    protected $em;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Oro\Bundle\UserBundle\Entity\User
     */
    protected $adminUser;

    /**
     * @param ContainerInterface $container
     * @return $this
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->em = $manager;
        $this->createAdminUser();
        $this->em->flush();
    }

    public function createAdminUser()
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $this->adminUser = $userManager->createUser();
        $this->adminUser->addRole(Role::ROLE_ADMINISTRATOR);
        $this->adminUser->setEnabled(true);
        $this->adminUser->setUsername(self::ADMIN_USERNAME);
        $this->adminUser->setFullName('Admin');
        $this->adminUser->setTimezone('America/Los_Angeles');
        $this->adminUser->setEmail('test@test.te');
        $this->adminUser->setPlainPassword(self::ADMIN_PASSWORD);
        $this->em->persist($this->adminUser);
    }

    /**
     * @return \Oro\Bundle\UserBundle\Entity\User
     */
    public function getAdminUser()
    {
        return $this->adminUser;
    }
}
