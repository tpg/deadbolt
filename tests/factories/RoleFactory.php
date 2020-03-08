<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use TPG\Tests\User;

$factory->define(\TPG\Tests\Role::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
    ];
});
