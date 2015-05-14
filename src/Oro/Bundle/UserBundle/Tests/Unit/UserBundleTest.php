<?php

namespace Oro\Bundle\UserBundle\Tests\Unit;

use Oro\Bundle\UserBundle\OroUserBundle;

class UserBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testGetParent()
    {
        $class = new OroUserBundle();
        $this->assertEquals('FOSUserBundle', $class->getParent());
    }
}
