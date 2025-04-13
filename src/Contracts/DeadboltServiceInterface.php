<?php

declare(strict_types=1);

namespace TPG\Deadbolt\Contracts;

use Illuminate\Database\Eloquent\Model;
use TPG\Deadbolt\Drivers\Contracts\DriverInterface;
use TPG\Deadbolt\User;
use TPG\Deadbolt\UserCollection;

interface DeadboltServiceInterface
{
    public function __construct(array $config);

    /**
     * Specify the user to  manipulate permissions for.
     */
    public function user(Model $model): User;

    /**
     * A collection of users to manipulate permissions for.
     *     *
     * @param  array<Model>  $users
     */
    public function users(...$users): UserCollection;

    /**
     * Set the driver used to access permissions.
     */
    public function driver(DriverInterface $driver): DeadboltServiceInterface;

    /**
     * Get an array of permissions.
     */
    public function all(): array;

    /**
     * Get the permission descriptions.
     *
     * @param  array<string>  $permissions
     */
    public function describe(...$permissions): array;
}
