<?php

namespace TPG\Deadbolt\Facades;

use Illuminate\Support\Facades\Facade;
use TPG\Deadbolt\Drivers\Contracts\PermissionSourceDriver;

/**
 * @method static PermissionSourceDriver driver()
 * @method static array permissions()
 */
class Deadbolt extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'deadbolt.facade';
    }
}
