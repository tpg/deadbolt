<?php

namespace TPG\Deadbolt\Facades;

use Illuminate\Support\Facades\Facade;
use TPG\Deadbolt\Drivers\Contracts\DriverInterface;
use TPG\Deadbolt\User;

/**
 * @method static User user($user)
 * @method static array permissions(...$roles)
 * @method static array roles()
 * @method static \TPG\Deadbolt\DeadboltService driver(DriverInterface $driver)
 */
class Deadbolt extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'deadbolt.facade';
    }
}
