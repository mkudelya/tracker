<?php

namespace Oro\Bundle\TrackerBundle\DataFixtures\ORM;

use Oro\Bundle\TrackerBundle\Entity\Project;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadProjectData extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $project1 = new Project();
        $project1
            ->setLabel('Training Project Label')
            ->setSummary('Training Tracking Project')
            ->setCode('TTP');
        $manager->persist($project1);

        $project2 = new Project();
        $project2
            ->setLabel('Oro training')
            ->setSummary('Oro training project')
            ->setCode('OROTRAINING');
        $manager->persist($project2);

        $manager->flush();

        $this->addReference('project1', $project1);
        $this->addReference('project2', $project2);
    }

    public function getDependencies()
    {
        return [
            'Oro\Bundle\UserBundle\DataFixtures\ORM\LoadUserData'
        ];
    }
}
