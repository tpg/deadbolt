<?php

declare(strict_types=1);

namespace TPG\Tests;

use TPG\Deadbolt\Drivers\Contracts\DriverInterface;

class EloquentDriver implements DriverInterface
{
    public function permissions(): array
    {
        return Permission
            ::select(['name', 'description'])
            ->pluck('description', 'name')
            ->toArray();
    }
}
