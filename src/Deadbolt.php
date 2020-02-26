<?php

namespace TPG\Deadbolt;

use Illuminate\Database\Eloquent\Model;
use TPG\Deadbolt\Drivers\ArrayDriver;
use TPG\Deadbolt\Drivers\Contracts\DriverInterface;

/**
 * Class Deadbolt.
 */
class Deadbolt
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
        $this->driver = new ArrayDriver($config);
    }

    /**
     * Set the user.
     *
     * @param Model $model
     * @return User
     */
    public function user(Model $model): User
    {
        return new User($model, $this->permissions(), $this->config);
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
     * @return array
     */
    public function permissions(): array
    {
        return $this->driver->permissions();
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
