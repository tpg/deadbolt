<?php

declare(strict_types=1);

namespace TPG\Deadbolt\Contracts;

interface UserCollectionInterface
{
    /**
     * @param  array  $users
     * @param  array  $permissions
     * @param  array  $config
     */
    public function __construct(array $users, array $permissions, array $config);

    /**
     * Give the specified permissions to the user collection.
     *
     * @param  array<string>  $names
     */
    public function give(...$names): UserCollectionInterface;

    /**
     * Give all permissions to the user collection.
     */
    public function super(): UserCollectionInterface;

    /**
     * Revoke the specified permissions from the user collection.
     *
     * @param  array<string>  $names
     */
    public function revoke(...$names): UserCollectionInterface;

    /**
     * Revoke all permissions from the user collection.
     */
    public function revokeAll(): UserCollectionInterface;

    /**
     * Sync the specified permissions with the user collection.
     *
     * @param  array<string>  $names
     */
    public function sync(...$names): UserCollectionInterface;

    /**
     * Save the user collection.
     */
    public function save(): UserCollectionInterface;

    /**
     * Check if all the users have all the specified permissions.
     *
     * @param  array<string>  $permissions
     */
    public function have(...$permissions): bool;

    /**
     * Check if all the users have at least one of the specified permissions.
     *
     * @param  array<string>  $permissions
     */
    public function any(...$permissions): bool;

    /**
     * Check if all the users have none of the specified permissions.
     *
     * @param  array<string>  $permissions
     */
    public function dontHave(...$permissions): bool;
}
