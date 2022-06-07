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
    /**
     * @var Collection
     */
    protected $users;

    /**
     * @param  array  $users
     * @param  array  $permissions
     * @param  array  $config
     */
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
     * @param ...$names
     * @return UserCollectionInterface
     */
    public function give(...$names): UserCollectionInterface
    {
        $this->callOnEachUser('give', $names);

        return $this;
    }

    /**
     * Give all permissions to the user collection.
     *
     * @return UserCollectionInterface
     */
    public function super(): UserCollectionInterface
    {
        $this->callOnEachUser('super');

        return $this;
    }

    /**
     * Revoke the specified permissions from the user collection.
     *
     * @param ...$names
     * @return UserCollectionInterface
     */
    public function revoke(...$names): UserCollectionInterface
    {
        $this->callOnEachUser('revoke', $names);

        return $this;
    }

    /**
     * Revoke all permissions from the user collection.
     *
     * @return UserCollectionInterface
     */
    public function revokeAll(): UserCollectionInterface
    {
        $this->callOnEachUser('revokeAll');

        return $this;
    }

    /**
     * Sync the specified permissions with the user collection.
     *
     * @param ...$names
     * @return UserCollectionInterface
     */
    public function sync(...$names): UserCollectionInterface
    {
        $this->callOnEachUser('sync', $names);

        return $this;
    }

    /**
     * Save the user collection.
     *
     * @return UserCollectionInterface
     */
    public function save(): UserCollectionInterface
    {
        $this->callOnEachUser('save');

        return $this;
    }

    /**
     * Call the.
     *
     * @param  string  $name
     * @param  mixed|null  $arguments
     * @return UserCollectionInterface
     */
    protected function callOnEachUser(string $name, $arguments = null): UserCollectionInterface
    {
        $this->users->each(function (User $user) use ($name, $arguments) {
            $user->{$name}($arguments);
        });

        return $this;
    }

    /**
     * Check if all the users have the specified permissions.
     *
     * @param ...$permissions
     * @return bool
     */
    public function allHave(...$permissions): bool
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
     * @param ...$permissions
     * @return bool
     */
    public function anyHave(...$permissions): bool
    {
        foreach ($this->users as $user) {
            if ($user->hasAll($permissions)) {
                return true;
            }
        }

        return false;
    }

    public function has($permission): bool
    {
        return $this->anyHave($permission);
    }

    /**
     * Check if none of the users have the specified permissions.
     *
     * @param ...$permissions
     * @return bool
     */
    public function noneHave(...$permissions): bool
    {
        foreach ($this->users as $user) {
            if ($user->hasAny($permissions)) {
                return false;
            }
        }

        return true;
    }
}
