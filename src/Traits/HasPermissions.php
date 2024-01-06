<?php

declare(strict_types=1);

namespace TPG\Deadbolt\Traits;

use Illuminate\Database\Eloquent\Builder;
use TPG\Deadbolt\Facades\Deadbolt;
use TPG\Deadbolt\User as DeadboltUser;

trait HasPermissions
{
    public function permissions(): DeadboltUser
    {
        return Deadbolt::user($this);
    }

    public function scopeWithPermissions(Builder $builder, ...$permissions): Builder
    {
        return $builder->whereJsonContains('permissions', $permissions);
    }
}
