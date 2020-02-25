<?php

return [

    // Specify the column where permissions are stored on model table
    'column' => 'permissions',


    'permissions' => [
        // You can specify how the defined permissions are sourced. Available options are "array", "database", "json"
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

        // if "driver" is "json", specify the connection details here
        'json' => [
            'endpoint' => env('DEADBOLT_JSON_ENDPOINT'),
            'cache' => 3600,
            'query' => [
                // Specify any additional query parameters
            ],
            'headers' => [
                // Specify any additional headers to send with the request. Ex: 'Authorization'
            ],
        ]
    ],

];
