<?php

namespace TPG\Deadbolt\Facades;

use Illuminate\Support\Facades\Facade;
use TPG\Deadbolt\Drivers\Contracts\DriverInterface;
use TPG\Deadbolt\User;
use TPG\Deadbolt\UserCollection;

/**
 * @method static User user($user)
 * @method static UserCollection users($users)
 * @method static array permissions(...$roles)
 * @method static array describe(...$permissions)
 * @method static array groups()
 * @method static \TPG\Deadbolt\DeadboltService driver(DriverInterface $driver)
 */
class Deadbolt extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'deadbolt.facade';
    }
}
