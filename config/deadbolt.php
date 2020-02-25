<?php

return [

    // Specify the column where permissions are stored on model table
    'column' => 'permissions',

    'permissions' => [
        // You can specify how the defined permissions are sourced. Available options are "array", "database".
        'driver' => 'array',

        'array' => [
            // Define an array of permissions
        ],

        // if "driver" is "database", specify the connection details here
        'database' => [

            'connection' => 'default',
            'table' => 'permissions',
            'column' => 'name',
            'cache' => 3600,

        ],
    ],

];
