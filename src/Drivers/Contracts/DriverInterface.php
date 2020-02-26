<?php

namespace TPG\Deadbolt\Drivers\Contracts;

interface DriverInterface
{
    public function __construct(array $config);

    public function permissions(...$roles): array;
    public function roles(): array;
}
