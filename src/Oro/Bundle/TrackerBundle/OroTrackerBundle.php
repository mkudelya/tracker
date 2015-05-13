<?php

namespace Oro\Bundle\TrackerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class OroTrackerBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
