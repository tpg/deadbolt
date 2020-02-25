<?php

namespace TPG\Deadbolt;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use TPG\Deadbolt\Exceptions\NoSuchRoleException;

class Roles
{
    /**
     * @var Authenticatable
     */
    protected $user;
    /**
     * @var array
     */
    protected $config;

    /**
     * Roles constructor.
     * @param Authenticatable $user
     * @param array $config
     */
    public function __construct(Authenticatable $user, array $config)
    {
        $this->user = $user;
        $this->config = $config;
    }

    public function exists(...$roles): bool
    {
        $roles = Arr::flatten($roles);

        foreach ($roles as $role) {
            if (!array_key_exists($role, $this->config['roles'])) {
                throw new NoSuchRoleException($role);
            }
        }

        return true;
    }

    public function give(...$roles): self
    {
        $roles = Arr::flatten($roles);

        $this->exists($roles);

        $permissions = $this->getPermissions($roles);

        $this->user->deadbolt()->permissions()->give($permissions);

        return $this;
    }

    public function revoke(...$roles): self
    {
        $roles = Arr::flatten($roles);

        $permissions = $this->getPermissions($roles);

        $this->user->deadbolt()->permissions()->revoke($permissions);

        return $this;
    }

    public function has(string $role): bool
    {
        $this->exists($role);

        $userPermissions = $this->user->deadbolt()->toArray();
        $rolePermissions = $this->config['roles'][$role];

        return !count(array_diff($userPermissions, $rolePermissions));
    }

    public function get(): array
    {
        return array_values(
            array_filter(array_keys($this->config['roles']), function ($role) {
                return $this->has($role);
            })
        );
    }

    protected function getPermissions(array $roles): array
    {
        return array_map(function ($role) {
            return $this->config['roles'][$role];
        }, $roles);
    }
}
