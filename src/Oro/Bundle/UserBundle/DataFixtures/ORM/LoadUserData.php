<?php

namespace Oro\Bundle\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements
    FixtureInterface,
    ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        $adminUser = $userManager->createUser();
        $adminUser->setUsername('admin');
        $adminUser->setEmail('mkudelya@gmail.com');
        $adminUser->setPlainPassword('test');
        $adminUser->setFullName('Mike Kudelya');
        $adminUser->setEnabled(true);
        $adminUser->addRole('ROLE_ADMINISTRATOR');
        $adminUser->setTimezone('America/Los_Angeles');
        $userManager->updateUser($adminUser);

        $managerUser = $userManager->createUser();
        $managerUser->setUsername('manager');
        $managerUser->setEmail('manager@gmail.com');
        $managerUser->setPlainPassword('test');
        $managerUser->setFullName('Manager');
        $managerUser->setEnabled(true);
        $managerUser->addRole('ROLE_MANAGER');
        $managerUser->setTimezone('America/Los_Angeles');
        $userManager->updateUser($managerUser);

        $operatorUser = $userManager->createUser();
        $operatorUser->setUsername('operator');
        $operatorUser->setEmail('operator@gmail.com');
        $operatorUser->setPlainPassword('test');
        $operatorUser->setFullName('Operator');
        $operatorUser->setEnabled(true);
        $operatorUser->addRole('ROLE_OPERATOR');
        $operatorUser->setTimezone('America/Los_Angeles');
        $userManager->updateUser($operatorUser);

        $this->addReference('admin-user', $adminUser);
        $this->addReference('manager-user', $managerUser);
        $this->addReference('operator-user', $operatorUser);
    }
}
