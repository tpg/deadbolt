<?php

return [

    /*
    |--------------------------------------------------------------------------
    | The name of the permissions column
    |--------------------------------------------------------------------------
    |
    | This is the name of the column where permissions are stored. By default
    | this is set to "permissions", but it can be anything you like.
    |
    */
    'column' => 'permissions',

    /*
    |--------------------------------------------------------------------------
    | Defined permissions
    |--------------------------------------------------------------------------
    |
    | Only the permissions defined in this array are permitted. This gives
    | you a single source of truth and avoids errors. Permission names can
    | be anything you like and you can provide a description.
    |
    */
    'permissions' => [
        // 'articles.create' => 'Create new articles',
    ],

    /*
    |--------------------------------------------------------------------------
    | Permission driver
    |--------------------------------------------------------------------------
    |
    | The permission driver feature allows you to specify how permissions
    | are sourced. By default, the provided "ArrayDriver" will source
    | permissions from this file, however you can provide your own custom
    | driver to source permissions from a database, or an API endpoint.
    |
    | A default DatabaseDriver is also provided. See the documentation for
    | details on it's use.
    |
    */
    'driver' => TPG\Deadbolt\Drivers\ArrayDriver::class,
    // 'driver' => TPG\Deadbolt\Drivers\DatabaseDriver::class,

];
