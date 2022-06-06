<?php

declare(strict_types=1);

namespace TPG\Deadbolt;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use JsonException;
use TPG\Deadbolt\Contracts\UserInterface;
use TPG\Deadbolt\Exceptions\NoSuchPermissionException;
use TPG\Deadbolt\Facades\Deadbolt;

class User implements UserInterface
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
     * @param  Model  $user
     * @param  array  $permissions
     * @param  array  $config
     */
    public function __construct(Model $user, array $permissions, array $config)
    {
        $this->user = $user;
        $this->config = $config;
        $this->permissions = $permissions;
    }

    /**
     * Give the specified permissions.
     *
     * @param  mixed  ...$names
     * @return UserInterface
     *
     * @throws JsonException|NoSuchPermissionException
     */
    public function give(...$names): UserInterface
    {
        $permissions = array_filter(Arr::flatten($names), function ($permission) {
            $this->exists($permission);

            return true;
        });

        $this->assignPermissions(array_merge($this->userPermissions(), $permissions));

        return $this->save();
    }

    /**
     * Merge the specified permissions with the current permissions.
     *
     * @param  array  $permissions
     * @return UserInterface
     */
    protected function assignPermissions(array $permissions): UserInterface
    {
        $this->user->{$this->config['column']} = $this->permissionsAreCast() ? $permissions : json_encode($permissions);

        return $this->save();
    }

    /**
     * Check if the "permissions" field has already been cast on the model.
     *
     * @return bool
     */
    protected function permissionsAreCast(): bool
    {
        return Arr::get($this->user->getCasts(), $this->config['column']) === 'json';
    }

    /**
     * Make a super-user.
     *
     * @return UserInterface
     *
     * @throws JsonException|NoSuchPermissionException
     */
    public function super(): UserInterface
    {
        return $this->give($this->permissions);
    }

    /**
     * Revoke the specified permissions.
     *
     * @param  mixed  ...$names
     * @return UserInterface
     *
     * @throws JsonException
     */
    public function revoke(...$names): UserInterface
    {
        return $this->sync(
            array_diff($this->userPermissions(), $names)
        );
    }

    /**
     * Revoke all permissions.
     *
     * @return UserInterface
     */
    public function revokeAll(): UserInterface
    {
        $this->user->{$this->config['column']} = json_encode([]);

        return $this->save();
    }

    /**
     * Sync permissions with the names provided.
     *
     * @param  mixed  ...$names
     * @return UserInterface
     */
    public function sync(...$names): UserInterface
    {
        return $this->revokeAll()->give($names);
    }

    /**
     * Save the current permission set.
     *
     * @return UserInterface
     */
    public function save(): UserInterface
    {
        $this->user->save();

        return $this;
    }

    /**
     * Check if an array is defined.
     *
     * @param  string  $permission
     * @return bool
     *
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
     * @param  string  $name
     * @return bool
     */
    protected function isPermission(string $name): bool
    {
        return in_array($name, $this->permissions, true);
    }

    /**
     * Check if the specified permission is assigned.
     *
     * @param  string  $permission
     * @return bool
     *
     * @throws JsonException
     */
    public function has(string $permission): bool
    {
        return in_array($permission, $this->userPermissions(), true);
    }

    /**
     * Get the permissions currently assigned to the user.
     *
     * @return array
     *
     * @throws JsonException
     */
    protected function userPermissions(bool $refresh = false): array
    {
        $permissions = $this->user->getAttributeValue($this->config['column']) ?: [];
        if (is_array($permissions)) {
            return $permissions;
        }

        return json_decode($permissions, true, 512, JSON_THROW_ON_ERROR) ?: [];
    }

    /**
     * Check if all the specified permissions are assigned.
     *
     * @param  mixed  ...$permissions
     * @return bool
     *
     * @throws JsonException
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
     * @param  mixed  ...$permissions
     * @return bool
     *
     * @throws JsonException
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
     * @param  mixed  ...$permissions
     * @return bool
     *
     * @throws JsonException
     */
    public function hasNone(...$permissions): bool
    {
        return ! $this->hasAny($permissions);
    }

    /**
     * Get an array of permissions assigned to the user.
     *
     * @return array
     *
     * @throws JsonException
     */
    public function all(): array
    {
        return $this->userPermissions();
    }

    /**
     * Get an array of permissions with descriptions.
     *
     * @return array
     *
     * @throws JsonException
     */
    public function describe(): array
    {
        return Deadbolt::describe($this->all());
    }
}
