<?php
namespace Oro\TrackerBundle\Tests\Functional\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Oro\TrackerBundle\Entity\Role;

class LoadDataFixtures extends AbstractFixture implements ContainerAwareInterface
{
    const ADMIN_USERNAME = 'test';
    const ADMIN_PASSWORD = 'test';

    /** @var ObjectManager */
    protected $em;

    /** @var ContainerInterface */
    protected $container;

    protected $adminUser;

    /**
     * @param ContainerInterface|null $container A ContainerInterface instance or null
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
        $this->adminUser->setEmail('test@test.te');
        $this->adminUser->setPlainPassword(self::ADMIN_PASSWORD);
        $this->em->persist($this->adminUser);
    }

    public function getAdminUser()
    {
        return $this->adminUser;
    }
}
