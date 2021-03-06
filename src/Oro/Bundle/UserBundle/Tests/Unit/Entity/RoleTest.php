<?php

namespace Oro\Bundle\TrackerBundle\Tests\Unit\Entity;

use Oro\Bundle\UserBundle\Model\Role;

class RoleTest extends \PHPUnit_Framework_TestCase
{
    public function testRoles()
    {
        $roles = (new Role())->getAvailableRoles();

        $expectedRoles = array(
            Role::ROLE_ADMINISTRATOR => 'Administrator',
            Role::ROLE_MANAGER => 'Manager',
            Role::ROLE_OPERATOR => 'Operator'
        );

        $this->assertEquals($expectedRoles, $roles);
    }
}
