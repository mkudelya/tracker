<?php

namespace Oro\Bundle\TrackerBundle\DataFixtures\ORM;

use Oro\Bundle\TrackerBundle\Entity\Activity;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadActivityData extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user1 = $this->getReference('admin-user');
        $user2 = $this->getReference('manager-user');
        $project1 = $this->getReference('project1');
        $project2 = $this->getReference('project2');
        $issue1 = $this->getReference('issue1');
        $issue2 = $this->getReference('issue2');
        $issue3 = $this->getReference('issue3');
        $issue4 = $this->getReference('issue4');
        $issue5 = $this->getReference('issue5');
        $issue6 = $this->getReference('issue6');

        $activity1 = new Activity();
        $activity1
            ->setIssue($issue1)
            ->setUser($user1)
            ->setProject($project1)
            ->setBody('')
            ->setType(1);

        $activity2 = new Activity();
        $activity2
            ->setIssue($issue2)
            ->setUser($user1)
            ->setProject($project1)
            ->setBody('')
            ->setType(1);

        $activity3 = new Activity();
        $activity3
            ->setIssue($issue3)
            ->setUser($user1)
            ->setProject($project1)
            ->setBody('')
            ->setType(1);

        $activity4 = new Activity();
        $activity4
            ->setIssue($issue4)
            ->setUser($user1)
            ->setProject($project1)
            ->setBody('')
            ->setType(1);

        $activity5 = new Activity();
        $activity5
            ->setIssue($issue1)
            ->setUser($user2)
            ->setProject($project1)
            ->setBody('')
            ->setType(3);

        $activity6 = new Activity();
        $activity6
            ->setIssue($issue4)
            ->setUser($user2)
            ->setProject($project1)
            ->setBody('')
            ->setType(3);

        $activity7 = new Activity();
        $activity7
            ->setIssue($issue2)
            ->setUser($user2)
            ->setProject($project1)
            ->setBody('')
            ->setType(3);

        $activity8 = new Activity();
        $activity8
            ->setIssue($issue3)
            ->setUser($user2)
            ->setProject($project1)
            ->setBody('In progress')
            ->setType(2);

        $activity9 = new Activity();
        $activity9
            ->setIssue($issue5)
            ->setUser($user1)
            ->setProject($project2)
            ->setBody('')
            ->setType(1);

        $activity10 = new Activity();
        $activity10
            ->setIssue($issue5)
            ->setUser($user1)
            ->setProject($project2)
            ->setBody('')
            ->setType(3);

        $activity11 = new Activity();
        $activity11
            ->setIssue($issue6)
            ->setUser($user1)
            ->setProject($project2)
            ->setBody('')
            ->setType(1);

        $manager->persist($activity1);
        $manager->persist($activity2);
        $manager->persist($activity3);
        $manager->persist($activity4);
        $manager->persist($activity5);
        $manager->persist($activity6);
        $manager->persist($activity7);
        $manager->persist($activity8);
        $manager->persist($activity9);
        $manager->persist($activity10);
        $manager->persist($activity11);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            'Oro\Bundle\TrackerBundle\DataFixtures\ORM\LoadProjectData',
            'Oro\Bundle\TrackerBundle\DataFixtures\ORM\LoadIssueData'
        ];
    }
}
