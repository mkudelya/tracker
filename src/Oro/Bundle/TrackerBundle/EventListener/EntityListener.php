<?php

namespace Oro\Bundle\TrackerBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

use Oro\Bundle\TrackerBundle\Entity\Issue;
use Oro\Bundle\TrackerBundle\Entity\Activity;
use Oro\Bundle\TrackerBundle\Entity\Comment;
use Oro\Bundle\UserBundle\Entity\User;

class EntityListener
{
    /**
     * @var Container
     */
    private $container;

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
        $this->addToActivity($args, true);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->updateCollaborators($args);
        $this->addToActivity($args, false);
    }

    /**
     * @param LifecycleEventArgs $args
     * @param bool $isNewEntity
     */
    public function addToActivity(LifecycleEventArgs $args, $isNewEntity)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        $token = $this->container->get('security.context')->getToken();

        if ($token) {
            $currentUser = $token->getUser();
        } else {
            return;
        }

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
                $this->activityEmailNotification($activityEntity);
            } else {
                $uow = $entityManager->getUnitOfWork();
                $uow->computeChangeSets();
                $changeset = $uow->getEntityChangeSet($entity);

                if (is_array($changeset) && isset($changeset['status'])) {
                    $activityEntity->setType(Activity::CHANGED_STATUS_ISSUE_TYPE);
                    $activityEntity->setBody($changeset['status'][1]);
                    $entityManager->persist($activityEntity);
                    $entityManager->flush();
                    $this->activityEmailNotification($activityEntity);
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
            $this->activityEmailNotification($activityEntity);
        }
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

    /**
     * @param Activity $activity
     */
    public function activityEmailNotification(Activity $activity)
    {
        $issue = $activity->getIssue();
        $collaborators = $issue->getCollaborators();

        if ($collaborators->count()) {
            foreach ($collaborators as $user) {
                $message = \Swift_Message::newInstance()
                    ->setSubject('Notification task - "'.$issue->getSummary().'"')
                    ->setFrom('robot@localhost')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->container->get('templating')->render(
                            'OroTrackerBundle:Activity:email.html.twig',
                            array('activity' => $activity)
                        ),
                        'text/html'
                    );

                $this->container->get('mailer')->send($message);
            }
        }
    }
}
