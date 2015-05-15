<?php

namespace Oro\Bundle\TrackerBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;

class CheckMemberValidator extends ConstraintValidator
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param object $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $isValid = true;

        if (!($value instanceof PersistentCollection)) {
            return $isValid;
        }

        /** @var $value PersistentCollection */
        /** @var $project \Oro\Bundle\TrackerBundle\Entity\Project */
        $project = $value->getOwner();
        $currentProjectIssues = $project->getIssues();
        $currentProjectMembers = [];

        if ($value->count()) {
            foreach ($value as $user) {
                $currentProjectMembers[] = $user->getId();
            }
        }

        if ($currentProjectIssues->count()) {
            foreach ($currentProjectIssues as $issue) {
                if (!in_array($issue->getAssignee()->getId(), $currentProjectMembers)) {
                    $isValid = false;
                    break;
                }
            }
        }

        if ($isValid === false) {
            $this->context->addViolation(
                $constraint->message,
                array(
                    '%user%' => $issue->getAssignee()->getUsername(),
                    '%task%' => $issue->getSummary()
                )
            );
        }
    }
}
