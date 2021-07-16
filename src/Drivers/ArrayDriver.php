<?php

declare(strict_types=1);

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
     * @return array
     */
    public function permissions(): array
    {
        $names = $this->getPermissionNames($this->config['permissions']);

        return $this->getDescriptions($names);
    }

    /**
     * Get the names of the permissions without descriptions.
     *
     * @param array $permissions
     * @return array
     */
    protected function getPermissionNames(array $permissions): array
    {
        return array_map(static function ($permission) use ($permissions) {
            if (is_numeric($permission)) {
                return $permissions[$permission];
            }

            return $permission;
        }, array_keys($permissions));
    }

    /**
     * Return the permission names with the descriptions.
     *
     * @param array $permissions
     * @return array
     */
    protected function getDescriptions(array $permissions): array
    {
        $res = [];
        foreach ($permissions as $name) {
            $res[$name] = Arr::get($this->config['permissions'], $name);
        }

        return $res;
    }
}
