<?php

declare(strict_types=1);

namespace TPG\Deadbolt;

use Illuminate\Support\Collection;
use TPG\Deadbolt\Contracts\UserCollectionInterface;

/**
 * Class UserCollection.
 */
class UserCollection implements UserCollectionInterface
{
    protected Collection $users;

    public function __construct(array $users, array $permissions, array $config)
    {
        $collection = collect($users);

        $this->users = $collection->map(function ($user) use ($permissions, $config) {
            return new User($user, $permissions, $config);
        });
    }

    /**
     * Give the specified permissions to the user collection.
     *
     * @param  array<string>  $names
     */
    public function give(...$names): UserCollectionInterface
    {
        $this->callOnEachUser('give', $names);

        return $this;
    }

    /**
     * Give all permissions to the user collection.
     */
    public function super(): UserCollectionInterface
    {
        $this->callOnEachUser('super');

        return $this;
    }

    /**
     * Revoke the specified permissions from the user collection.
     *
     * @param  array<string>  $names
     */
    public function revoke(...$names): UserCollectionInterface
    {
        $this->callOnEachUser('revoke', ...$names);

        return $this;
    }

    /**
     * Revoke all permissions from the user collection.
     */
    public function revokeAll(): UserCollectionInterface
    {
        $this->callOnEachUser('revokeAll');

        return $this;
    }

    /**
     * Sync the specified permissions with the user collection.
     *
     * @param  array<string>  $names
     */
    public function sync(...$names): UserCollectionInterface
    {
        $this->callOnEachUser('sync', $names);

        return $this;
    }

    /**
     * Save the user collection.
     */
    public function save(): UserCollectionInterface
    {
        $this->callOnEachUser('save');

        return $this;
    }

    protected function callOnEachUser(string $name, mixed $arguments = null): UserCollectionInterface
    {
        $this->users->each(function (User $user) use ($name, $arguments) {
            $user->{$name}($arguments);
        });

        return $this;
    }

    /**
     * Check if all the users have the specified permissions.
     *
     * @param  array<string>  $permissions
     */
    public function have(...$permissions): bool
    {
        foreach ($this->users as $user) {
            if (! $user->hasAll($permissions)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if any of the users have all the specified permissions.
     *
     * @param  array<string>  $permissions
     */
    public function any(...$permissions): bool
    {
        foreach ($this->users as $user) {
            if ($user->hasAll($permissions)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if none of the users have the specified permissions.
     */
    public function dontHave(...$permissions): bool
    {
        foreach ($this->users as $user) {
            if ($user->hasAny($permissions)) {
                return false;
            }
        }

        return true;
    }
}
