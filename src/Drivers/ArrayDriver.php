<?php

namespace TPG\Deadbolt\Drivers;

use TPG\Deadbolt\Drivers\Contracts\PermissionSourceDriver;

class ArrayDriver implements PermissionSourceDriver
{
    /**
     * @var array
     */
    private $config;

    /**
     * ArrayDriver constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function get()
    {
        return $this->config;
    }
}
