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
     * Get descriptions for the provided permission names.
     *
     * @param mixed ...$permissions
     * @return array
     */
    public function describe(...$permissions): array;

    /**
     * Get an array of role permissions keyed by the role names.
     *
     * @return array
     */
    public function roles(): array;
}
