<?php

namespace TPG\Deadbolt\Drivers\Contracts;

interface PermissionSourceDriver
{
    public function __construct(array $config);

    public function get();
}
