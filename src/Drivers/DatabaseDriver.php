<?php

namespace TPG\Deadbolt\Drivers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use TPG\Deadbolt\Drivers\Contracts\PermissionSourceDriver;

class DatabaseDriver implements PermissionSourceDriver
{
    /**
     * @var array
     */
    private $config;

    /**
     * DatabaseDriver constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function get()
    {
        return Cache::remember('deadbolt.permissions', $this->config['cache'], function () {
            return $this->getPermissionsFromDatabase();
        });
    }

    protected function getPermissionsFromDatabase(): array
    {
        return DB::connection($this->connection())
            ->table($this->config['table'])
            ->get($this->config['column'])
            ->pluck($this->config['column'])->toArray();
    }

    protected function connection(): string
    {
        if ($this->config['connection'] === 'default') {
            return app('config')->get('database.' . $this->config['connection']);
        }
        return $this->config['connnection'];
    }
}
