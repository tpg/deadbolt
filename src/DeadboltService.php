<?php

declare(strict_types=1);

namespace TPG\Deadbolt;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use TPG\Deadbolt\Contracts\DeadboltServiceInterface;
use TPG\Deadbolt\Drivers\ArrayDriver;
use TPG\Deadbolt\Drivers\Contracts\DriverInterface;

class DeadboltService implements DeadboltServiceInterface
{
    protected array $config;

    protected DriverInterface $driver;

    public function __construct(array $config)
    {
        $this->config = $config;

        $configuredDriver = Arr::get($this->config, 'driver');

        $this->driver($configuredDriver
            ? new $configuredDriver($config)
            : new ArrayDriver($config)
        );
    }

    /**
     * Specify the user to  manipulate permissions for.
     */
    public function user(Model $model): User
    {
        return new User($model, $this->all(), $this->config);
    }

    /**
     * A collection of users to manipulate permissions for.
     *
     * @param  array<Model>  $users
     */
    public function users(...$users): UserCollection
    {
        return new UserCollection(Arr::flatten($users), $this->all(), $this->config);
    }

    /**
     * Set the driver used to access permissions.
     */
    public function driver(DriverInterface $driver): DeadboltServiceInterface
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * Get an array of permissions.
     */
    public function all(): array
    {
        return array_keys($this->driver->permissions());
    }

    /**
     * Get the permission descriptions.
     *
     * @param  array<string>|string  $permissions
     */
    public function describe(...$permissions): array
    {
        $filter = Arr::flatten($permissions);

        if (! empty($filter)) {
            return array_filter(
                $this->driver->permissions(),
                static fn ($description, $permission) => in_array($permission, $filter, true),
                ARRAY_FILTER_USE_BOTH
            );
        }

        return $this->driver->permissions();
    }
}
