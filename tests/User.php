<?php

namespace TPG\Tests;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use TPG\Deadbolt\Traits\Deadbolted;

class User extends Authenticatable
{
    use Notifiable, Deadbolted;
}
