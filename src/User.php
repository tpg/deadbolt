<?php

namespace TPG\Deadbolt;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use TPG\Deadbolt\Exceptions\NoSuchPermissionException;

class User
{
    /**
     * @var Model
     */
    private $user;
    /**
     * @var array
     */
    private $config;

    /**
     * User constructor.
     * @param Model $user
     * @param array $config
     */
    public function __construct(Model $user, array $config)
    {
        $this->user = $user;
        $this->config = $config;
    }

    public function give(...$names): self
    {
        $permissions = array_filter($this->getPermissions(Arr::flatten($names)), function ($permission) {
            $this->exists($permission);

            return true;
        });

        $this->mergePermissions($permissions, false);

        return $this;
    }

    protected function mergePermissions(array $permissions, bool $permanent = false): self
    {
        $this->user->permissions = json_encode($permissions);
        if ($permanent) {
            return $this->save();
        }

        return $this;
    }

    public function super(): self
    {
        $this->give($this->config['permissions']);

        return $this;
    }

    public function revoke(...$names): self
    {
        $names = $this->getPermissions(Arr::flatten($names));

        $permissions = array_filter($this->userPermissions(), function ($permission) use ($names) {

            return ! in_array($permission, $names, true);

        });

        $this->revokeAll()->give($permissions);

        return $this;
    }

    public function revokeAll(): self
    {
        $this->user->permissions = json_encode([]);

        return $this;
    }

    public function save(): self
    {
        $this->user->save();

        return $this;
    }

    public function saved(): bool
    {
        $diff = array_diff(
            $this->userPermissions(),
            json_decode($this->user->getOriginal('permissions'), true)
        );

        return count($diff) === 0;
    }

    protected function getPermissions(array $names): array
    {
        $permissions = array_map(function ($name) {
            if ($this->isRole($name)) {
                return $this->getRolePermissions($name);
            }
            return $name;
        }, $names);

        return Arr::flatten($permissions);
    }

    protected function exists(string $permission): bool
    {
        if (!$this->isPermission($permission)) {
            throw new NoSuchPermissionException($permission);
        }

        return true;
    }

    protected function isPermission(string $name): bool
    {
        return in_array($name, $this->config['permissions'], true);
    }

    protected function isRole(string $name): bool
    {
        return array_key_exists($name, $this->config['roles']);
    }

    protected function getRolePermissions(string $name): array
    {

        return Arr::get($this->config, 'roles.' . $name, []);
    }

    public function has(string $permission): bool
    {
        return in_array($permission, $this->userPermissions(), true);
    }

    protected function userPermissions(): array
    {
        return json_decode($this->user->permissions, true) ?: [];
    }

    public function hasAll(...$permissions): bool
    {
        $permissions = Arr::flatten($permissions);

        foreach ($permissions as $permission) {
            if (!in_array($permission, $this->userPermissions(), true)) {
                return false;
            }
        }

        return true;
    }

    public function hasAny(...$permissions): bool
    {
        $permissions = Arr::flatten($permissions);

        foreach ($permissions as $permission) {
            if (in_array($permission, $this->userPermissions(), true)) {
                return true;
            }
        }

        return false;
    }

    public function hasNone(...$permissions): bool
    {
        return !$this->hasAny($permissions);
    }

    public function is($role): bool
    {
        $permissions = Arr::get($this->config, 'roles.' . $role, []);

        return $this->hasAll($permissions);
    }

    public function permissions(): array
    {
        return $this->userPermissions();
    }

    public function roles(): array
    {
        return array_values(array_filter(array_keys($this->config['roles']), function ($role) {
            return $this->hasAll($this->config['roles'][$role]);
        }));
    }
}
