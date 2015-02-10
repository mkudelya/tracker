<?php
namespace Oro\TrackerBundle\Entity;

class Role
{
    protected $_roles = array(
        'administrator' => 'Administrator',
        'manager' => 'Manager',
        'operator' => 'Operator'
    );

    public function getAvailableRoles()
    {
        return $this->_roles;
    }
}
