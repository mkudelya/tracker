<?php

namespace Oro\Bundle\TrackerBundle\DataFixtures\ORM;

use Oro\Bundle\TrackerBundle\Entity\Issue;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadIssueData extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user1 = $this->getReference('admin-user');
        $user3 = $this->getReference('operator-user');
        $project1 = $this->getReference('project1');
        $project2 = $this->getReference('project2');

        $issue1 = new Issue();
        $issue1
            ->setReporter($user1)
            ->setAssignee($user1)
            ->setProject($project1)
            ->setSummary('Training Prepare Task')
            ->setCode('TPT1')
            ->setDescription('Prepare tracking to demo')
            ->setType('Task')
            ->setPriority(3)
            ->setStatus('Open');
        $manager->persist($issue1);

        $issue2 = new Issue();
        $issue2
            ->setReporter($user1)
            ->setAssignee($user1)
            ->setProject($project1)
            ->setSummary('Demo data story')
            ->setCode('DDS')
            ->setDescription('demo for training story')
            ->setType('story')
            ->setPriority(2)
            ->setStatus('Open');
        $manager->persist($issue2);

        $issue3 = new Issue();
        $issue3
            ->setReporter($user1)
            ->setAssignee($user1)
            ->setProject($project1)
            ->setParent($issue2)
            ->setSummary('User creating')
            ->setCode('UC1')
            ->setDescription('create users')
            ->setType('Task')
            ->setPriority(1)
            ->setStatus('In Progress');
        $manager->persist($issue3);

        $issue4 = new Issue();
        $issue4
            ->setReporter($user1)
            ->setAssignee($user1)
            ->setProject($project1)
            ->setParent($issue2)
            ->setSummary('Create permissions')
            ->setCode('CP')
            ->setDescription('Create permissions')
            ->setType('Task')
            ->setPriority(2)
            ->setStatus('Open');
        $manager->persist($issue4);

        $issue5 = new Issue();
        $issue5
            ->setReporter($user1)
            ->setAssignee($user3)
            ->setProject($project2)
            ->setSummary('Documentation')
            ->setCode('DOCS')
            ->setDescription('Reading documentation')
            ->setType('Task')
            ->setPriority(1)
            ->setStatus('Open');
        $manager->persist($issue5);

        $issue6 = new Issue();
        $issue6
            ->setReporter($user1)
            ->setAssignee($user3)
            ->setProject($project2)
            ->setSummary('Adding comments')
            ->setCode('AC1')
            ->setDescription('Adding comments to code')
            ->setType('Task')
            ->setPriority(2)
            ->setStatus('Open');
        $manager->persist($issue6);

        $manager->flush();

        $this->setReference('issue1', $issue1);
        $this->setReference('issue2', $issue2);
        $this->setReference('issue3', $issue3);
        $this->setReference('issue4', $issue4);
        $this->setReference('issue5', $issue5);
        $this->setReference('issue6', $issue6);
    }

    public function getDependencies()
    {
        return [
            'Oro\Bundle\UserBundle\DataFixtures\ORM\LoadUserData',
            'Oro\Bundle\TrackerBundle\DataFixtures\ORM\LoadProjectData'
        ];
    }
}
