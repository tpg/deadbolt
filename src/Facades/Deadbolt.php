<?php

declare(strict_types=1);

namespace TPG\Deadbolt\Facades;

use Illuminate\Support\Facades\Facade;
use TPG\Deadbolt\DeadboltService;
use TPG\Deadbolt\Drivers\Contracts\DriverInterface;
use TPG\Deadbolt\User;
use TPG\Deadbolt\UserCollection;

/**
 * @method static User user($user)
 * @method static UserCollection users($users)
 * @method static array all()
 * @method static array describe(...$permissions)
 * @method static array groups()
 * @method static DeadboltService driver(DriverInterface $driver)
 */
class Deadbolt extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'deadbolt.facade';
    }
}
