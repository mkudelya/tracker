<?php
namespace Oro\TrackerBundle\Entity;

class Role
{
    protected $_roles = array(
        'ROLE_ADMINISTRATOR' => 'Administrator',
        'ROLE_MANAGER' => 'Manager',
        'ROLE_OPERATOR' => 'Operator'
    );

    public function getAvailableRoles()
    {
        return $this->_roles;
    }
}
