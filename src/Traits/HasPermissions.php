<?php

declare(strict_types=1);

namespace TPG\Deadbolt\Traits;

use TPG\Deadbolt\Facades\Permissions;
use TPG\Deadbolt\User as DeadboltUser;

trait HasPermissions
{
    public function permissions(): DeadboltUser
    {
        return Permissions::user($this);
    }
}
