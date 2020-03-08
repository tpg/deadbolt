<?php

namespace TPG\Tests;

use Illuminate\Support\Arr;
use TPG\Deadbolt\Drivers\Contracts\DriverInterface;

class CustomDriver implements DriverInterface
{
    protected $permissions = [
        'test permission' => 'Testing',
    ];

    protected $groups = [
        'group' => [
            'test permission',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function permissions(...$groups): array
    {
        $permissions = $this->permissions;

        $groups = Arr::flatten($groups);

        if ($groups) {
            $permissions = [];
            foreach ($groups as $role) {
                $names[] = $this->groups[$role];
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
    public function groups(): array
    {
        return $this->groups;
    }
}
