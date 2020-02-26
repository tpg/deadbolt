<?php

namespace TPG\Deadbolt\Exceptions;

class NoSuchPermissionException extends \Exception
{
    /**
     * @param string $permission
     */
    public function __construct(string $permission)
    {
        $message = 'The permission '.$permission.' does not exist';
        parent::__construct($message, 1);
    }
}
