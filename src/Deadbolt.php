<?php

namespace TPG\Deadbolt;

use TPG\Deadbolt\Drivers\ArrayDriver;
use TPG\Deadbolt\Drivers\Contracts\PermissionSourceDriver;
use TPG\Deadbolt\Drivers\DatabaseDriver;

class Deadbolt
{
    /**
     * @var array
     */
    private $config;
    /**
     * @var array
     */
    protected $drivers = [
        'array' => ArrayDriver::class,
        'database' => DatabaseDriver::class,
    ];
    /**
     * @var PermissionSourceDriver
     */
    protected $driver;

    /**
     * Deadbolt constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        $this->setDriver();
    }

    protected function setDriver(): void
    {
        $driverName = $this->config['permissions']['driver'];
        $driverConfig = $this->config['permissions'][$driverName];
        $this->driver = new $this->drivers[$driverName]($driverConfig);
    }

    public function driver(): PermissionSourceDriver
    {
        return $this->driver;
    }

    public function permissions()
    {
        return $this->driver()->get();
    }
}
