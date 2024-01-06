<?php

namespace TPG\Deadbolt\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use TPG\Deadbolt\Tests\Models\Role;
use TPG\Deadbolt\Traits\HasPermissions;

class User extends Authenticatable
{
    use Notifiable, HasPermissions, HasFactory;

//    protected $casts = [
//        'permissions' => 'json',
//    ];
//
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
}
