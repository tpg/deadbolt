<?php

declare(strict_types=1);

namespace TPG\Deadbolt\Drivers\Contracts;

interface DriverInterface
{
    /**
     * Get an array of permission names.
     *
     * @param mixed ...$groups
     *
     * @return array
     */
    public function permissions(...$groups): array;

    /**
     * Get an array of role permissions keyed by the role names.
     *
     * @param bool $describe
     * @return array
     */
    public function groups(bool $describe = false): array;
}
