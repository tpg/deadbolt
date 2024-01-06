<?php

declare(strict_types=1);

namespace TPG\Deadbolt\Tests;

use TPG\Deadbolt\Drivers\Contracts\DriverInterface;
use TPG\Deadbolt\Tests\Models\Permission;

class EloquentDriver implements DriverInterface
{
    public function permissions(): array
    {
        return Permission::select(['name', 'description'])
            ->pluck('description', 'name')
            ->toArray();
    }
}
