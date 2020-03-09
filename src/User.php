<?php
declare(strict_types=1);

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
     * @var array
     */
    protected $permissions;
    /**
     * @var array
     */
    protected $groups;

    /**
     * @param Model $user
     * @param array $permissions
     * @param array $groups
     * @param array $config
     */
    public function __construct(Model $user, array $permissions, array $groups, array $config)
    {
        $this->user = $user;
        $this->config = $config;
        $this->permissions = $permissions;
        $this->groups = $groups;
    }

    /**
     * Give the specified permissions.
     *
     * @param mixed ...$names
     * @return $this
     */
    public function give(...$names): self
    {
        $permissions = array_filter($this->getPermissions(Arr::flatten($names)), function ($permission) {
            $this->exists($permission);

            return true;
        });

        $this->assignPermissions(array_merge($this->userPermissions(), $permissions), false);

        return $this;
    }

    /**
     * Merge the specified permissions with the current permissions.
     *
     * @param array $permissions
     * @param bool $permanent
     * @return $this
     */
    protected function assignPermissions(array $permissions, bool $permanent = false): self
    {
        $this->user->{$this->config['column']} = $this->permissionsAreCast() ? $permissions : json_encode($permissions);
        if ($permanent) {
            return $this->save();
        }

        return $this;
    }

    protected function permissionsAreCast(): bool
    {
        return Arr::get($this->user->getCasts(), $this->config['column']) === 'json';
    }

    /**
     * Make a super user.
     *
     * @return $this
     */
    public function super(): self
    {
        $this->give($this->permissions);

        return $this;
    }

    /**
     * Revoke the specified permissions.
     *
     * @param mixed ...$names
     * @return $this
     */
    public function revoke(...$names): self
    {
        $names = $this->getPermissions(Arr::flatten($names));

        $permissions = array_filter($this->userPermissions(), function ($permission) use ($names) {
            return ! in_array($permission, $names, true);
        });

        $this->revokeAll()->give($permissions);

        return $this;
    }

    /**
     * Revoke all permissions.
     *
     * @return $this
     */
    public function revokeAll(): self
    {
        $this->user->{$this->config['column']} = json_encode([]);

        return $this;
    }

    /**
     * Sync permissions with the names provided.
     *
     * @param mixed ...$names
     * @return $this
     */
    public function sync(...$names): self
    {
        return $this->revokeAll()->give($names);
    }

    /**
     * Save the current permission set.
     *
     * @return $this
     */
    public function save(): self
    {
        $this->user->save();

        return $this;
    }

    /**
     * Check if the current permission set is permanent.
     *
     * @return bool
     */
    public function saved(): bool
    {
        $originalPermissions = $this->user->getOriginal($this->config['column']);

        $diff = array_diff(
            $this->userPermissions(),
            $this->permissionsAreCast()
                ? $originalPermissions
                : json_decode($originalPermissions, true)
        );

        return count($diff) === 0;
    }

    /**
     * Get an array of permissions from the assignment set.
     *
     * @param array $names
     * @return array
     */
    protected function getPermissions(array $names): array
    {
        $permissions = array_map(function ($name) {
            if ($this->isGroup($name)) {
                return $this->getGroupPermissions($name);
            }

            return $name;
        }, $names);

        return Arr::flatten($permissions);
    }

    /**
     * Check if an array is defined.
     *
     * @param string $permission
     * @return bool
     * @throws NoSuchPermissionException
     */
    protected function exists(string $permission): bool
    {
        if (! $this->isPermission($permission)) {
            throw new NoSuchPermissionException($permission);
        }

        return true;
    }

    /**
     * Check if the given name is a permission.
     *
     * @param string $name
     * @return bool
     */
    protected function isPermission(string $name): bool
    {
        return in_array($name, $this->permissions, true);
    }

    /**
     * Check if the given name is a group.
     *
     * @param string $name
     * @return bool
     */
    protected function isGroup(string $name): bool
    {
        return array_key_exists($name, $this->groups);
    }

    /**
     * Get the permissions from the specified group.
     *
     * @param string $name
     * @return array
     */
    protected function getGroupPermissions(string $name): array
    {
        return Arr::get($this->groups, $name, []);
    }

    /**
     * Check if the specified permission is assigned.
     *
     * @param string $permission
     * @return bool
     */
    public function has(string $permission): bool
    {
        return in_array($permission, $this->userPermissions(), true);
    }

    /**
     * Get the permissions currently assigned to the user.
     *
     * @return array
     */
    protected function userPermissions(): array
    {
        $permissions = $this->user->{$this->config['column']} ?: [];
        if (is_array($permissions)) {
            return $permissions;
        }

        return json_decode($permissions, true) ?: [];
    }

    /**
     * Check if all the specified permissions are assigned.
     *
     * @param mixed ...$permissions
     * @return bool
     */
    public function hasAll(...$permissions): bool
    {
        $permissions = Arr::flatten($permissions);

        foreach ($permissions as $permission) {
            if (! in_array($permission, $this->userPermissions(), true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if any of the specified permissions are assigned.
     *
     * @param mixed ...$permissions
     * @return bool
     */
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

    /**
     * Check if none of the specified permissions are assigned.
     *
     * @param mixed ...$permissions
     * @return bool
     */
    public function hasNone(...$permissions): bool
    {
        return ! $this->hasAny($permissions);
    }

    /**
     * Check if the user belongs to the specified group of permissions.
     *
     * @param string $group
     * @return bool
     */
    public function is(string $group): bool
    {
        $permissions = Arr::get($this->groups, $group, []);

        return $this->hasAll($permissions);
    }

    /**
     * Get an array of permissions assigned to the user.
     *
     * @return array
     */
    public function permissions(): array
    {
        return $this->userPermissions();
    }

    /**
     * Get an array of deduced groups assigned to the user.
     *
     * @return array
     */
    public function groups(): array
    {
        return array_values(array_filter(array_keys($this->groups), function ($group) {
            return $this->hasAll($this->groups[$group]);
        }));
    }
}
