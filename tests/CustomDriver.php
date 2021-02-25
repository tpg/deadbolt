<?php

namespace TPG\Tests;

use TPG\Deadbolt\Drivers\Contracts\DriverInterface;

class CustomDriver implements DriverInterface
{
    protected $permissions = [
        'test permission' => 'Testing',
    ];

    /**
     * {@inheritdoc}
     */
    public function permissions(): array
    {
        return $this->permissions;
    }

    /**
     * {@inheritdoc}
     */
    public function describe(...$permissions): array
    {
        return $this->permissions;
    }
}
