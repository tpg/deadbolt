<?php

namespace TPG\Deadbolt\Traits;

use TPG\Deadbolt\Permissions;

trait Deadbolted
{
    protected $deadbolt;

    public function deadbolt(): Permissions
    {
        return $this->deadbolt ?: $this->deadbolt = new Permissions($this, config('deadbolt'));
    }
}
