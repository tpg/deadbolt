<?php

namespace TPG\Deadbolt\Tests\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use TPG\Deadbolt\Tests\Models\Role;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
