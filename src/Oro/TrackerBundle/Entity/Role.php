<?php
namespace Oro\TrackerBundle\Entity;

class Role
{
    const ROLE_ADMINISTRATOR = 'ROLE_ADMINISTRATOR';
    const ROLE_MANAGER = 'ROLE_MANAGER';
    const ROLE_OPERATOR = 'ROLE_OPERATOR';

    protected $roles = array(
        self::ROLE_ADMINISTRATOR => 'Administrator',
        self::ROLE_MANAGER => 'Manager',
        self::ROLE_OPERATOR => 'Operator'
    );

    public function getAvailableRoles()
    {
        return $this->roles;
    }
}
