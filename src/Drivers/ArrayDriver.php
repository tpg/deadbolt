<?php

declare(strict_types=1);

namespace TPG\Deadbolt\Drivers;

use Illuminate\Support\Arr;
use TPG\Deadbolt\Drivers\Contracts\DriverInterface;

class ArrayDriver implements DriverInterface
{
    public function __construct(protected array $config)
    {
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

    protected function getPermissionNames(array $permissions): array
    {
        return array_map(static function ($permission) use ($permissions) {
            if (is_numeric($permission)) {
                return $permissions[$permission];
            }

            return $permission;
        }, array_keys($permissions));
    }

    protected function getDescriptions(array $permissions): array
    {
        $res = [];
        foreach ($permissions as $name) {
            $res[$name] = Arr::get($this->config['permissions'], $name);
        }

        return $res;
    }
}
