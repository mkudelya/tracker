<?php

namespace Oro\TrackerBundle\Tests\Unit\Entity;

use Oro\TrackerBundle\Entity\Role;

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
