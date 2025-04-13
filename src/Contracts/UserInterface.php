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
     * @param  array<string>|string  ...$names
     *
     * @throws JsonException|NoSuchPermissionException
     */
    public function give(...$names): UserInterface;

    /**
     * Make a superuser.
     *
     * @throws JsonException|NoSuchPermissionException
     */
    public function super(): UserInterface;

    /**
     * Check if the user is super.
     */
    public function isSuper(): bool;

    /**
     * Revoke the specified permissions.
     *
     * @param  array<string>|string  $names
     *
     * @throws JsonException
     */
    public function revoke(...$names): UserInterface;

    /**
     * Revoke all permissions.
     */
    public function revokeAll(): UserInterface;

    /**
     * Sync permissions with the names provided.
     *
     * @param  array<string>|string  ...$names
     */
    public function sync(...$names): UserInterface;

    /**
     * Save the current permission set.
     */
    public function save(): UserInterface;

    /**
     * Check if the specified permission is assigned.*.
     *
     * @throws JsonException
     */
    public function has(string $permission): bool;

    /**
     * Check if all the specified permissions are assigned.
     *
     * @param  array<string>|string  $permissions
     *
     * @throws JsonException
     */
    public function hasAll(...$permissions): bool;

    /**
     * Check if any of the specified permissions are assigned.
     *
     * @param  array<string>|string  $permissions
     *
     * @throws JsonException
     */
    public function hasAny(...$permissions): bool;

    /**
     * Check if any of the specified permissions are assigned.
     *
     * @param  array<string>|string  $permissions
     *
     * @throws JsonException
     */
    public function any(...$permissions): bool;

    /**
     * Check if none of the specified permissions are assigned.
     *
     * @param  array<string>|string  ...$permissions
     *
     * @throws JsonException
     */
    public function hasNone(...$permissions): bool;

    /**
     * Check if none of the specified permissions are assigned.
     *
     * @param  array<string>|string  ...$permissions
     *
     * @throws JsonException
     */
    public function none(...$permissions): bool;

    /**
     * Get an array of permissions assigned to the user.
     *
     * @throws JsonException
     */
    public function all(): array;
}
