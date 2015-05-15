<?php

namespace Oro\Bundle\TrackerBundle\DataFixtures\ORM;

use Oro\Bundle\TrackerBundle\Entity\Comment;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCommentData extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user1 = $this->getReference('admin-user');
        $user2 = $this->getReference('manager-user');
        $issue1 = $this->getReference('issue1');
        $issue2 = $this->getReference('issue2');
        $issue4 = $this->getReference('issue4');
        $issue5 = $this->getReference('issue5');

        $comment1 = new Comment();
        $comment1
            ->setUser($user2)
            ->setIssue($issue1)
            ->setBody('Please, create database schema');

        $comment2 = new Comment();
        $comment2
            ->setUser($user2)
            ->setIssue($issue4)
            ->setBody('This task isn\'t necessary');

        $comment3 = new Comment();
        $comment3
            ->setUser($user2)
            ->setIssue($issue2)
            ->setBody('Please start UC1');

        $comment4 = new Comment();
        $comment4
            ->setUser($user1)
            ->setIssue($issue5)
            ->setBody('This task is open!');

        $manager->persist($comment1);
        $manager->persist($comment2);
        $manager->persist($comment3);
        $manager->persist($comment4);

        $manager->flush();
    }

    public function getDependencies()
    {
        return ['Oro\Bundle\TrackerBundle\DataFixtures\ORM\LoadIssueData'];
    }
}
