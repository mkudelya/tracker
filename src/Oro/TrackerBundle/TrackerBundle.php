<?php

namespace Oro\TrackerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TrackerBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
