<?php

namespace TPG\Deadbolt;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use TPG\Deadbolt\Exceptions\NoSuchPermissionException;

/**
 * Class Permissions.
 */
class Permissions implements Arrayable
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
     * @var array
     */
    protected $permissions;
    /**
     * @var array
     */
    protected $definedPermissions;

    /**
     * DeadboltPermissions constructor.
     * @param Authenticatable $user
     * @param array $config
     * @param array $definedPermissions
     */
    public function __construct(Authenticatable $user, array $config, array $definedPermissions)
    {
        $this->user = $user;
        $this->config = $config;
        $this->permissions = $this->getPermissionsFromUser($user);
        $this->definedPermissions = $definedPermissions;
    }

    /**
     * Assert that the specified permissions are defined.
     *
     * @param mixed ...$permissions
     * @return bool
     * @throws NoSuchPermissionException
     */
    public function exists(...$permissions): bool
    {
        $permissions = $this->unwrap($permissions);

        foreach ($permissions as $permission) {
            if (! in_array($permission, $this->definedPermissions, true)) {
                throw new NoSuchPermissionException($permission);
            }
        }

        return true;
    }

    /**
     * Give the specified permissions to the user.
     *
     * @param mixed ...$permissions
     * @return $this
     * @throws NoSuchPermissionException
     */
    public function give(...$permissions): self
    {
        $permissions = $this->unwrap($permissions);

        $this->exists($permissions);

        $this->permissions = array_merge($this->permissions, $permissions);

        return $this;
    }

    /**
     * Revoke the specified permissions from the user.
     *
     * @param mixed ...$permissions
     * @return $this
     */
    public function revoke(...$permissions): self
    {
        $permissions = $this->unwrap($permissions);

        $this->permissions = array_filter($this->permissions, function ($permission) use ($permissions) {
            return ! in_array($permission, $permissions, true);
        });

        return $this;
    }

    /**
     * Revoke all permissions from the user.
     *
     * @return $this
     */
    public function revokeAll(): self
    {
        return $this->revoke($this->definedPermissions);
    }

    /**
     * Give all available permissions to the user.
     *
     * @return $this
     * @throws NoSuchPermissionException
     */
    public function super(): self
    {
        $this->give($this->definedPermissions);

        return $this;
    }

    /**
     * Assert that the permission set has a singular permission.
     *
     * @param string $permission
     * @return bool
     */
    public function has(string $permission): bool
    {
        return $this->hasAll([$permission]);
    }

    /**
     * Assert that all of the specified permissions exist in the permission set.
     *
     * @param mixed ...$permissions
     * @return bool
     */
    public function hasAll(...$permissions): bool
    {
        $permissions = $this->unwrap($permissions);

        foreach ($permissions as $permission) {
            if (! in_array($permission, $this->permissions, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Assert that any of the specified permissions exist in the permission set.
     *
     * @param mixed ...$permissions
     * @return bool
     */
    public function hasAny(...$permissions): bool
    {
        $permissions = $this->unwrap($permissions);

        foreach ($permissions as $permission) {
            if (in_array($permission, $this->permissions, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Assert that the permission set does not include any of the specific permissions.
     *
     * @param mixed ...$permissions
     * @return bool
     */
    public function hasNone(...$permissions): bool
    {
        return ! $this->hasAny($permissions);
    }

    /**
     * Get the permission set from the provided user object.
     *
     * @param Authenticatable $user
     * @return array
     */
    protected function getPermissionsFromUser(Authenticatable $user): array
    {
        if (is_array($user->{$this->config['column']})) {
            return $user->{$this->config['column']};
        }

        return $user->{$this->config['column']} ? json_decode($user->{$this->config['column']}, true) : [];
    }

    /**
     * Unwrap the provided permissions array.
     *
     * @param array $permissions
     * @return array
     */
    protected function unwrap(array $permissions): array
    {
        $permissions = Arr::flatten($permissions);

        return $permissions;
    }

    /**
     * Make the current permission set permanent.
     *
     * @return $this
     */
    public function makePermanent(): self
    {
        $this->user->{$this->config['column']} = json_encode($this->permissions);
        $this->user->save();

        return $this;
    }

    /**
     * Check if the current permission set is permanent.
     *
     * @return bool
     */
    public function isPermanent(): bool
    {
        return $this->getPermissionsFromUser($this->user) === $this->permissions;
    }

    /**
     * Return the assigned permissions as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return (array) $this->permissions;
    }
}
