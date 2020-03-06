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
        return new User($model, $this->permissions(), $this->roles(), $this->config);
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
     * @param mixed $roles
     * @return array
     */
    public function permissions(...$roles): array
    {
        return $this->driver->permissions($roles);
    }

    public function describe(...$permissions): array
    {
        return $this->driver->describe($permissions);
    }

    /**
     * Get an array of role permissions keyed by role names.
     *
     * @return array
     */
    public function roles(): array
    {
        return $this->driver->roles();
    }
}
