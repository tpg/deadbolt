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
    public function __construct(protected Model $user, protected array $permissions, protected array $config) {}

    /**
     * Give the specified permissions.
     *
     * @param  array<string>|string  $names
     *
     * @throws JsonException|NoSuchPermissionException
     */
    public function give(...$names): UserInterface
    {
        $permissions = array_filter(Arr::flatten($names), fn ($permission) => $this->findPermissionOrFail($permission));

        $this->assignPermissions(array_merge($this->userPermissions(), $permissions));

        return $this->save();
    }

    /**
     * Merge the specified permissions with the current permissions.
     *
     * @throws JsonException
     */
    protected function assignPermissions(array $permissions): UserInterface
    {
        $this->user->{$this->config['column']} = $this->permissionsAreCast()
            ? $permissions
            : json_encode($permissions, JSON_THROW_ON_ERROR);

        return $this->save();
    }

    /**
     * Check if the "permissions" field has already been cast on the model.
     */
    protected function permissionsAreCast(): bool
    {
        return Arr::get($this->user->getCasts(), $this->config['column']) === 'json';
    }

    /**
     * Make a super-user.
     *
     * @throws JsonException|NoSuchPermissionException
     */
    public function super(): UserInterface
    {
        return $this->give(...$this->permissions);
    }

    /**
     * Check if the user is super.
     *
     * @throws JsonException
     */
    public function isSuper(): bool
    {
        return $this->hasAll(...$this->permissions);
    }

    /**
     * Revoke the specified permissions.
     *
     * @param  array<string>|string  ...$names
     *
     * @throws JsonException|NoSuchPermissionException
     */
    public function revoke(...$names): UserInterface
    {
        return $this->sync(
            ...array_diff($this->userPermissions(), $names)
        );
    }

    /**
     * Revoke all permissions.
     *
     * @throws JsonException
     */
    public function revokeAll(): UserInterface
    {
        $this->user->{$this->config['column']} = json_encode([], JSON_THROW_ON_ERROR);

        return $this->save();
    }

    /**
     * Sync permissions with the names provided.
     *
     * @param  array<string>|string  $names
     *
     * @throws JsonException
     * @throws NoSuchPermissionException
     */
    public function sync(...$names): UserInterface
    {
        return $this->revokeAll()->give($names);
    }

    /**
     * Save the current permission set.
     */
    public function save(): UserInterface
    {
        $this->user->save();

        return $this;
    }

    /**
     * @throws NoSuchPermissionException
     */
    protected function findPermissionOrFail(string $permission): bool
    {
        if (! $this->isPermission($permission)) {
            throw new NoSuchPermissionException($permission);
        }

        return true;
    }

    protected function isPermission(string $name): bool
    {
        return in_array($name, $this->permissions, true);
    }

    /**
     * Check if the specified permission is assigned.
     *
     *
     * @throws JsonException
     */
    public function has(string $permission): bool
    {
        return in_array($permission, $this->userPermissions(), true);
    }

    /**
     * @throws JsonException
     */
    protected function userPermissions(): array
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
     * @param  array<string>|string  $permissions
     *
     * @throws JsonException
     */
    public function hasAll(...$permissions): bool
    {
        foreach (Arr::flatten($permissions) as $permission) {
            if (! in_array($permission, $this->userPermissions(), true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if any of the specified permissions are assigned.
     *
     * @param  array<string>|string  ...$permissions
     *
     * @throws JsonException
     */
    public function hasAny(...$permissions): bool
    {
        foreach (Arr::flatten($permissions) as $permission) {
            if (in_array($permission, $this->userPermissions(), true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if any of the specified permissions are assigned.
     *
     * @param  array<string>|string  ...$permissions
     *
     * @throws JsonException
     */
    public function any(...$permissions): bool
    {
        return $this->hasAny(...$permissions);
    }

    /**
     * Check if none of the specified permissions are assigned.
     *
     * @param  array<string>|string  $permissions
     *
     * @throws JsonException
     */
    public function hasNone(...$permissions): bool
    {
        return ! $this->hasAny($permissions);
    }

    /**
     * Check if none of the specified permissions are assigned.
     *
     * @param  array<string>|string  $permissions
     *
     * @throws JsonException
     */
    public function none(...$permissions): bool
    {
        return $this->hasNone(...$permissions);
    }

    /**
     * Get an array of permissions assigned to the user.
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
     * @throws JsonException
     */
    public function describe(): array
    {
        return Deadbolt::describe($this->all());
    }
}
