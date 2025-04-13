<?php

namespace TPG\Deadbolt\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use TPG\Deadbolt\Traits\HasPermissions;

class User extends Authenticatable
{
    use HasFactory, HasPermissions, Notifiable;

    //    protected $casts = [
    //        'permissions' => 'json',
    //    ];
    //
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
}
