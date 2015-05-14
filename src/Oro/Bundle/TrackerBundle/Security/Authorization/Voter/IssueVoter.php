<?php

namespace Oro\Bundle\TrackerBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Oro\Bundle\TrackerBundle\Entity\Issue;
use Oro\Bundle\UserBundle\Entity\Role;

class IssueVoter implements VoterInterface
{
    const ADD_COMMENT = 'add_comment';
    const VIEW = 'view';
    const EDIT = 'edit';

    /**
     * @param string $attribute
     * @return bool
     */
    public function supportsAttribute($attribute)
    {
        return in_array(
            $attribute,
            array(
                self::ADD_COMMENT,
                self::VIEW,
                self::EDIT
            )
        );
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        $supportedClass = 'Oro\Bundle\TrackerBundle\Entity\Issue';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param TokenInterface $token
     * @param Issue $object
     * @param array $attributes
     * @return int
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if (!$this->supportsClass(get_class($object))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if (1 !== count($attributes)) {
            throw new \InvalidArgumentException(
                'Only one attribute is allowed for VIEW or EDIT'
            );
        }

        $attribute = $attributes[0];
        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return VoterInterface::ACCESS_DENIED;
        }

        if ($user->hasRole(Role::ROLE_ADMINISTRATOR) || $user->hasRole(Role::ROLE_MANAGER)) {
            return VoterInterface::ACCESS_GRANTED;
        }

        switch($attribute) {
            case self::ADD_COMMENT:
            case self::VIEW:
            case self::EDIT:
                if ($object->getProject()->hasMember($user)) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
