<?php

declare(strict_types=1);

namespace TPG\Deadbolt\Drivers\Contracts;

interface DriverInterface
{
    /**
     * Get an array of permission names.
     */
    public function permissions(): array;
}
