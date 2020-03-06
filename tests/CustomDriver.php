<?php

namespace TPG\Tests;

use Illuminate\Support\Arr;
use TPG\Deadbolt\Drivers\Contracts\DriverInterface;

class CustomDriver implements DriverInterface
{
    protected $permissions = [
        'test permission' => 'Testing',
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
            $permissions = [];
            foreach ($roles as $role) {
                $names[] = $this->roles[$role];
            }

            $names = Arr::flatten($names);

            foreach ($names as $name) {
                $permissions[$name] = Arr::get($this->permissions, $name);
            }
        }


        return $permissions;
    }

    /**
     * {@inheritdoc}
     */
    public function describe(...$permissions): array
    {
        return $this->permissions;
    }

    /**
     * {@inheritdoc}
     */
    public function roles(): array
    {
        return $this->roles;
    }
}
