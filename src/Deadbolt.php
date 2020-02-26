<?php

namespace TPG\Deadbolt;

use Illuminate\Database\Eloquent\Model;
use TPG\Deadbolt\Drivers\ArrayDriver;
use TPG\Deadbolt\Drivers\Contracts\DriverInterface;

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
     * Deadbolt constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->driver = new ArrayDriver($config);
    }

    public function user(Model $model): User
    {
        return new User($model, $this->permissions(), $this->config);
    }

    public function driver(DriverInterface $driver): self
    {
        $this->driver = $driver;

        return $this;
    }

    public function permissions(): array
    {
        return $this->driver->get();
    }

    public function roles(): array
    {
        return $this->config['roles'];
    }
}
