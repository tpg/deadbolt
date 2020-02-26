<?php

namespace TPG\Deadbolt\Facades;

use Illuminate\Support\Facades\Facade;
use TPG\Deadbolt\User;

/**
 * @method static User user($user)
 */
class Deadbolt extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'deadbolt.facade';
    }
}
