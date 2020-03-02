<?php

namespace TPG\Tests;

use Illuminate\Support\Arr;
use TPG\Deadbolt\Drivers\Contracts\DriverInterface;

class CustomDriver implements DriverInterface
{
    protected $permissions = [
        'test permission',
    ];

    protected $roles = [
        'role' => [
            'test permission',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function permissions(...$roles): array
    {
        $permissions = $this->permissions;

        $roles = Arr::flatten($roles);

        if ($roles) {
            foreach ($roles as $role) {
                $permissions[] = $this->roles[$role];
            }

            $permissions = Arr::flatten($permissions);
        }

        return $permissions;
    }

    /**
     * {@inheritdoc}
     */
    public function roles(): array
    {
        return $this->roles;
    }
}
