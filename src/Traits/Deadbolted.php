<?php

namespace TPG\Deadbolt\Traits;

use TPG\Deadbolt\Deadbolt;

trait Deadbolted
{
    protected $deadbolt;

    public function deadbolt(): Deadbolt
    {
        return $this->deadbolt ?: $this->deadbolt = new Deadbolt($this, config('deadbolt'));
    }
}
