<?php

declare(strict_types=1);

namespace TPG\Deadbolt\Exceptions;

use Exception;

class NoSuchPermissionException extends Exception
{
    public function __construct(string $permission)
    {
        $message = 'The permission '.$permission.' does not exist';
        parent::__construct($message, 1);
    }
}
