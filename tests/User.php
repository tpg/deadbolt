<?php

namespace TPG\Tests;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $casts = [
        'permissions' => 'json'
    ];
}
