<?php

namespace Oro\Bundle\TrackerBundle\Tests\Unit;

use Oro\Bundle\TrackerBundle\OroTrackerBundle;

class TrackerBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testGetParent()
    {
        $class = new OroTrackerBundle();
        $this->assertEquals('FOSUserBundle', $class->getParent());
    }
}
