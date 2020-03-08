<?php

namespace TPG\Deadbolt;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use TPG\Deadbolt\Drivers\ArrayDriver;
use TPG\Deadbolt\Drivers\Contracts\DriverInterface;

/**
 * Class Deadbolt.
 */
class DeadboltService
{
    /**
     * @var array
     */
    protected $config;
    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        if ($driver = Arr::get($this->config, 'driver')) {
            $this->driver(new $driver);
        } else {
            $this->driver(new ArrayDriver($config));
        }
    }

    /**
     * Set the user.
     *
     * @param Model $model
     * @return User
     */
    public function user(Model $model): User
    {
        return new User($model, $this->permissions(), $this->groups(), $this->config);
    }

    public function users(...$users): UserCollection
    {
        $users = Arr::flatten($users);

        return new UserCollection($users, $this->permissions(), $this->groups(), $this->config);
    }

    /**
     * Set an instance of the driver.
     *
     * @param DriverInterface $driver
     * @return $this
     */
    public function driver(DriverInterface $driver): self
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * Get an array of permissions.
     *
     * @param mixed $groups
     * @return array
     */
    public function permissions(...$groups): array
    {
        return array_keys($this->driver->permissions($groups));
    }

    public function describe(...$permissions): array
    {
        $filter = Arr::flatten($permissions);

        $permissions = $this->driver->permissions();

        if (! empty($filter)) {
            $permissions = array_filter($permissions, function ($description, $permission) use ($filter) {
                return in_array($permission, $filter, true);
            }, ARRAY_FILTER_USE_BOTH);
        }

        return $permissions;
    }

    /**
     * Get an array of permissions keyed by group names.
     *
     * @return array
     */
    public function groups(): array
    {
        return $this->driver->groups();
    }
}
