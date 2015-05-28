<?php

namespace Oro\Bundle\TrackerBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

use Oro\Bundle\TrackerBundle\Entity\Issue;
use Oro\Bundle\TrackerBundle\Entity\Activity;
use Oro\Bundle\TrackerBundle\Entity\Comment;

class ActivityListener
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var string
     */
    private $fromEmail;

    /**
     * @param Container $container
     */
    public function __construct(Container $container, $fromEmail)
    {
        $this->container = $container;
        $this->fromEmail = $fromEmail;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->addToActivity($args, true);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
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
                    ->setFrom($this->fromEmail)
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
