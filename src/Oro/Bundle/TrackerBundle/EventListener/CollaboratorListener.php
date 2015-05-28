<?php

namespace Oro\Bundle\TrackerBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

use Oro\Bundle\TrackerBundle\Entity\Issue;
use Oro\Bundle\TrackerBundle\Entity\Comment;

class CollaboratorListener
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->updateCollaborators($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->updateCollaborators($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function updateCollaborators(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if ($entity instanceof Issue) {
            $isNeedUpdate = false;
            $isReporterCollaborator = $this
                ->container
                ->get('issue')
                ->isUserCollaborator($entity, $entity->getReporter());

            $isAssigneeCollaborator = $this
                ->container
                ->get('issue')
                ->isUserCollaborator($entity, $entity->getAssignee());

            if (!$isReporterCollaborator) {
                $entity->addCollaborator($entity->getReporter());
                $isNeedUpdate = true;
            }

            //also prevent duplicates users<-->issues
            if (!$isAssigneeCollaborator && $entity->getReporter()->getId() !== $entity->getAssignee()->getId()) {
                $entity->addCollaborator($entity->getAssignee());
                $isNeedUpdate = true;
            }

            if ($isNeedUpdate) {
                $entityManager->persist($entity);
                $entityManager->flush();
            }
        } elseif ($entity instanceof Comment) {
            $isUserCollaborator = $this
                ->container
                ->get('issue')
                ->isUserCollaborator($entity->getIssue(), $entity->getUser());

            if (!$isUserCollaborator) {
                $entity->getIssue()->addCollaborator($entity->getUser());
                $entityManager->persist($entity);
                $entityManager->flush();
            }
        }
    }
}
