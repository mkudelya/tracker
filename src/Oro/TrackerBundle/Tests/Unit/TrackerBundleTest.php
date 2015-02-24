<?php

namespace Oro\TrackerBundle\Tests\Unit;

use Oro\TrackerBundle\TrackerBundle;

class TrackerBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testGetParent()
    {
        $class = new TrackerBundle();
        $this->assertEquals('FOSUserBundle', $class->getParent());
    }
}
