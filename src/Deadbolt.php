<?php

namespace TPG\Deadbolt;

use Illuminate\Contracts\Auth\Authenticatable;

class Deadbolt
{
    /**
     * @var Permissions
     */
    protected $permissions;
    /**
     * @var Authenticatable
     */
    protected $user;
    /**
     * @var array
     */
    protected $config;
    /**
     * @var Roles
     */
    protected $roles;

    /**
     * Deadbolt constructor.
     * @param Authenticatable $user
     * @param array $config
     */
    public function __construct(Authenticatable $user, array $config)
    {
        $this->user = $user;
        $this->config = $config;
    }

    public function permissions(): Permissions
    {
        return $this->permissions ?: $this->permissions = new Permissions($this->user, $this->config);
    }

    public function roles(): Roles
    {
        return $this->roles ?: $this->roles = new Roles($this->user, $this->config);
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this->permissions(), $name)) {
            return $this->permissions->{$name}(...$arguments);
        }

        throw new \BadMethodCallException('Call to undefined method '.$name);
    }
}
