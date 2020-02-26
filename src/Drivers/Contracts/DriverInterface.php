<?php

namespace TPG\Deadbolt\Drivers\Contracts;

interface DriverInterface
{
    public function __construct(array $config);

    public function get(...$roles);
}
