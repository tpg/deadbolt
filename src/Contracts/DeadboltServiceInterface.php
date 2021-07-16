<?php

declare(strict_types=1);

namespace TPG\Deadbolt\Contracts;

use Illuminate\Database\Eloquent\Model;
use TPG\Deadbolt\Drivers\Contracts\DriverInterface;
use TPG\Deadbolt\User;
use TPG\Deadbolt\UserCollection;

interface DeadboltServiceInterface
{
    /**
     * @param array $config
     */
    public function __construct(array $config);

    /**
     * A user.
     *
     * @param Model $model
     * @return User
     */
    public function user(Model $model): User;

    /**
     * A collection of users.
     *
     * @param ...$users
     * @return UserCollection
     */
    public function users(...$users): UserCollection;

    /**
     * Set permissions the driver.
     *
     * @param DriverInterface $driver
     * @return DeadboltServiceInterface
     */
    public function driver(DriverInterface $driver): DeadboltServiceInterface;

    /**
     * Get an array of permissions.
     *
     * @return array
     */
    public function all(): array;

    /**
     * Get the permission descriptions.
     *
     * @param ...$permissions
     * @return array
     */
    public function describe(...$permissions): array;
}
