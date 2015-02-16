<?php
namespace Oro\TrackerBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Oro\TrackerBundle\Entity\Issue;
use Oro\TrackerBundle\Entity\Comment;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class EntityListener
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->updateCollaborators($args);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->updateCollaborators($args);
    }

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
            if (!$isAssigneeCollaborator && $entity->getReporter()->getId() != $entity->getAssignee()->getId()) {
                $entity->addCollaborator($entity->getAssignee());
                $isNeedUpdate = true;
            }

            if ($isNeedUpdate) {
                $entityManager->persist($entity);
                $entityManager->flush();
            }
        }
    }
}
