<?php

declare(strict_types=1);

namespace TPG\Deadbolt;

use Illuminate\Support\Collection;

class UserCollection
{
    /**
     * @var Collection
     */
    protected $users;

    public function __construct($users, array $permissions, array $config)
    {
        $users = collect($users);
        $this->users = $users->map(function ($user) use ($permissions, $config) {
            return new User($user, $permissions, $config);
        });
    }

    public function give(...$names): self
    {
        $this->callOnEachUser('give', $names);

        return $this;
    }

    public function super(): self
    {
        $this->callOnEachUser('super');

        return $this;
    }

    public function revoke(...$names): self
    {
        $this->callOnEachUser('revoke', $names);

        return $this;
    }

    public function revokeAll(): self
    {
        $this->callOnEachUser('revokeAll');

        return $this;
    }

    public function sync(...$names): self
    {
        $this->callOnEachUser('sync', $names);

        return $this;
    }

    public function save(): self
    {
        $this->callOnEachUser('save');

        return $this;
    }

    protected function callOnEachUser($name, $arguments = null): self
    {
        $this->users->each(function (User $user) use ($name, $arguments) {
            $user->{$name}($arguments);
        });

        return $this;
    }

    public function allHave(...$permissions): bool
    {
        foreach ($this->users as $user) {
            if (! $user->hasAll($permissions)) {
                return false;
            }
        }

        return true;
    }

    public function anyHave(...$permissions): bool
    {
        foreach ($this->users as $user) {
            if ($user->hasAll($permissions)) {
                return true;
            }
        }

        return false;
    }

    public function has(string $permission): bool
    {
        return $this->anyHave($permission);
    }

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
