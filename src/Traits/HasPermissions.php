<?php

declare(strict_types=1);

namespace TPG\Deadbolt\Traits;

use TPG\Deadbolt\Facades\Deadbolt;
use TPG\Deadbolt\User as DeadboltUser;

trait HasPermissions
{
    public function permissions(): DeadboltUser
    {
        return Deadbolt::user($this);
    }
}
