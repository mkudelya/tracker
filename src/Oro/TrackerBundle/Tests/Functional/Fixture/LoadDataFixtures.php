<?php
namespace Oro\TrackerBundle\Tests\Functional\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Oro\TrackerBundle\Entity\User;

class LoadDataFixtures extends AbstractFixture implements ContainerAwareInterface
{
    /** @var ObjectManager */
    protected $em;

    /** @var ContainerInterface */
    protected $container;

    /**
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->em = $manager;

        $this->createUser();

        $this->em->flush();
    }

    public function createUser()
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setEnabled(true);
        $user->setUsername('test');
        $user->setEmail('test@test.te');
        $user->setPlainPassword('test');

        $userManager->updateUser($user);
    }
}
