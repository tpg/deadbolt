<?php

namespace TPG\Deadbolt\Exceptions;

class NoSuchRoleException extends \Exception
{
    public function __construct(string $role)
    {
        $message = 'The role '.$role.' does not exist';
        parent::__construct($message, 1);
    }
}
