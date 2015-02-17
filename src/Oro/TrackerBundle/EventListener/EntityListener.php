<?php
namespace Oro\TrackerBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Oro\TrackerBundle\Entity\Issue;
use Oro\TrackerBundle\Entity\Activity;
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
        $this->addToActivity($args, true);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->updateCollaborators($args);
        $this->addToActivity($args, false);
    }

    public function addToActivity(LifecycleEventArgs $args, $isNewEntity)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();
        $currentUser = $this->container->get('security.context')->getToken()->getUser();

        if ($entity instanceof Issue) {
            $activityEntity = new Activity();
            $activityEntity->setIssue($entity);
            $activityEntity->setUser($currentUser);
            $activityEntity->setProject($entity->getProject());

            if ($isNewEntity) {
                $activityEntity->setType(Activity::NEW_ISSUE_TYPE);
                $activityEntity->setBody('');
                $entityManager->persist($activityEntity);
                $entityManager->flush();
            } else {
                $uow = $entityManager->getUnitOfWork();
                $uow->computeChangeSets();
                $changeset = $uow->getEntityChangeSet($entity);

                if (is_array($changeset) && isset($changeset['status'])) {
                    $activityEntity->setType(Activity::CHANGED_STATUS_ISSUE_TYPE);
                    $activityEntity->setBody($changeset['status'][1]);
                    $entityManager->persist($activityEntity);
                    $entityManager->flush();
                }
            }
        } elseif ($entity instanceof Comment && $isNewEntity) {
            $activityEntity = new Activity();
            $activityEntity->setIssue($entity->getIssue());
            $activityEntity->setUser($currentUser);
            $activityEntity->setProject($entity->getIssue()->getProject());
            $activityEntity->setType(Activity::NEW_COMMENT_ISSUE_TYPE);
            $activityEntity->setBody('');
            $entityManager->persist($activityEntity);
            $entityManager->flush();
        }
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
