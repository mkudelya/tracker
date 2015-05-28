<?php

namespace Oro\Bundle\UserBundle\Model;

class Role
{
    const ROLE_ADMINISTRATOR = 'ROLE_ADMINISTRATOR';
    const ROLE_MANAGER = 'ROLE_MANAGER';
    const ROLE_OPERATOR = 'ROLE_OPERATOR';

    protected static $roles = array(
        self::ROLE_ADMINISTRATOR => 'Administrator',
        self::ROLE_MANAGER => 'Manager',
        self::ROLE_OPERATOR => 'Operator'
    );

    /**
     * @return array
     */
    public function getAvailableRoles()
    {
        return self::$roles;
    }
}
