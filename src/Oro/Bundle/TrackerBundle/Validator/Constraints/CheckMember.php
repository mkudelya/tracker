<?php

namespace Oro\Bundle\TrackerBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class CheckMember extends Constraint
{
    public $message = 'You can\'t uncheck user "%user%" because user is assigned in "%task%" task';

    public function validatedBy()
    {
        return 'check_member';
    }
}
