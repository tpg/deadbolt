<?php

namespace TPG\Deadbolt\Drivers;

use Illuminate\Support\Arr;
use TPG\Deadbolt\Drivers\Contracts\DriverInterface;

class ArrayDriver implements DriverInterface
{
    /**
     * @var array
     */
    protected $config;


    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get an array of permission names.
     *
     * @param mixed ...$roles
     *
     * @return array
     */
    public function permissions(...$roles): array
    {
        $roles = Arr::flatten($roles);

        if (count($roles)) {
            $permissions = array_map(function ($role) {
                return Arr::get($this->config, 'roles.'.$role);
            }, $roles);

            return Arr::flatten($permissions);
        }

        return $this->config['permissions'];
    }

    /**
     * Get an array of role permissions keyed by the role names.
     *
     * @return array
     */
    public function roles(): array
    {
        return $this->config['roles'];
    }
}
