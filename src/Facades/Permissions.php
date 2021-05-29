<?php

declare(strict_types=1);

namespace TPG\Deadbolt\Facades;

use Illuminate\Support\Facades\Facade;
use TPG\Deadbolt\Drivers\Contracts\DriverInterface;
use TPG\Deadbolt\User;
use TPG\Deadbolt\UserCollection;

/**
 * @method static User user($user)
 * @method static UserCollection users($users)
 * @method static array all(...$roles)
 * @method static array describe(...$permissions)
 * @method static array groups()
 * @method static \TPG\Deadbolt\DeadboltService driver(DriverInterface $driver)
 */
class Permissions extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'deadbolt.facade';
    }
}
