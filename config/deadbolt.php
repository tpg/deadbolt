<?php

return [

    'column' => 'permissions',

    'permissions' => [
        /*
         * // Define your permissions here...
         * 'articles.create' => 'Create articles',
         * 'articles.edit',
         * 'articles.delete',
         * // ...
         */

    ],

    'groups' => [
        /*
         * // Define your groups of permissions...
         * 'writer' => [
         *     'articles.create',
         *     'articles.edit',
         * ],
         * // ...
         */
    ],

    // Where do you store your permissions? By default this will be the included `ArrayDriver`.
    // Provide the class path of the driver you want to use instead
    // 'driver' => \App\Drivers\DatabaseDriver::class,
    'driver' => null,

];
