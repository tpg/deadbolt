<?php

declare(strict_types=1);

namespace TPG\Deadbolt\Contracts;

use Illuminate\Database\Eloquent\Model;
use JsonException;
use TPG\Deadbolt\Exceptions\NoSuchPermissionException;
use TPG\Deadbolt\User;

interface UserInterface
{
    public function __construct(Model $user, array $permissions, array $config);

    /**
     * Give the specified permissions.
     *
     * @param  mixed  ...$names
     * @return UserInterface
     *
     * @throws JsonException|NoSuchPermissionException
     */
    public function give(...$names): UserInterface;

    /**
     * Make a super user.
     *
     * @return UserInterface
     *
     * @throws JsonException|NoSuchPermissionException
     */
    public function super(): UserInterface;

    /**
     * Revoke the specified permissions.
     *
     * @param  mixed  ...$names
     * @return UserInterface
     *
     * @throws JsonException
     */
    public function revoke(...$names): UserInterface;

    /**
     * Revoke all permissions.
     *
     * @return UserInterface
     */
    public function revokeAll(): UserInterface;

    /**
     * Sync permissions with the names provided.
     *
     * @param  mixed  ...$names
     * @return UserInterface
     */
    public function sync(...$names): UserInterface;

    /**
     * Save the current permission set.
     *
     * @return UserInterface
     */
    public function save(): UserInterface;

    /**
     * Check if the specified permission is assigned.
     *
     * @param  string  $permission
     * @return bool
     *
     * @throws JsonException
     */
    public function has(string $permission): bool;

    /**
     * Check if all the specified permissions are assigned.
     *
     * @param  mixed  ...$permissions
     * @return bool
     *
     * @throws JsonException
     */
    public function hasAll(...$permissions): bool;

    /**
     * Check if any of the specified permissions are assigned.
     *
     * @param  mixed  ...$permissions
     * @return bool
     *
     * @throws JsonException
     */
    public function hasAny(...$permissions): bool;

    /**
     * Check if none of the specified permissions are assigned.
     *
     * @param  mixed  ...$permissions
     * @return bool
     *
     * @throws JsonException
     */
    public function hasNone(...$permissions): bool;

    /**
     * Get an array of permissions assigned to the user.
     *
     * @return array
     *
     * @throws JsonException
     */
    public function all(): array;
}
