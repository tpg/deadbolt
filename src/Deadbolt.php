<?php

namespace TPG\Deadbolt;

use Illuminate\Database\Eloquent\Model;

class Deadbolt
{
    /**
     * @var array
     */
    protected $config;

    /**
     * Deadbolt constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function user(Model $model): User
    {
        return new User($model, $this->config);
    }

    public function permissions(): array
    {
        return $this->config['permissions'];
    }

    public function roles(): array
    {
        return $this->config['roles'];
    }
}
