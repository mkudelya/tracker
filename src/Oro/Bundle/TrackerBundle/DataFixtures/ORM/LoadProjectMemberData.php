<?php

namespace Oro\Bundle\TrackerBundle\DataFixtures\ORM;

use Oro\Bundle\TrackerBundle\Entity\Project;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadProjectMemberData extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user1 = $this->getReference('admin-user');
        $user2 = $this->getReference('manager-user');
        $user3 = $this->getReference('operator-user');
        $project1 = $this->getReference('project1');
        $project2 = $this->getReference('project2');

        $project1->addMember($user1);
        $manager->persist($project1);

        $project2->addMember($user1);
        $project2->addMember($user2);
        $project2->addMember($user3);
        $manager->persist($project2);

        $manager->flush();
    }

    public function getDependencies()
    {
        return ['Oro\Bundle\TrackerBundle\DataFixtures\ORM\LoadProjectData'];
    }
}
