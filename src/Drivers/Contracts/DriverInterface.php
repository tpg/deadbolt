<?php

namespace TPG\Deadbolt\Drivers\Contracts;

interface DriverInterface
{
    /**
     * Get an array of permission names.
     *
     * @param mixed ...$roles
     *
     * @return array
     */
    public function permissions(...$roles): array;

    /**
     * Get an array of role permissions keyed by the role names.
     *
     * @return array
     */
    public function roles(): array;
}
