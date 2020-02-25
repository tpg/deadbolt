<?php

namespace TPG\Deadbolt\Exceptions;

use Throwable;

class NoSuchPermissionException extends \Exception
{
    public function __construct(string $permission)
    {
        $message = 'The permission ' . $permission . ' does not exist';
        parent::__construct($message, 1);
    }
}
