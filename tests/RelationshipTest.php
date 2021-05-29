<?php

namespace TPG\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use TPG\Deadbolt\Facades\Deadbolt;

class RelationshipTest extends TestCase
{
    protected function setupDatabase(): void
    {
        parent::setupDatabase(); // TODO: Change the autogenerated stub

        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('permissions')->nullable();
            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('user_id');
        });
    }

    /**
     * @test
     */
    public function it_can_test_related_roles()
    {
        $user = $this->user();

        $writer = Role::create([
            'name' => 'writer',
        ]);

        $publisher = Role::create([
            'name' => 'publisher',
        ]);

        Deadbolt::user($writer)->give('articles.create', 'articles.edit', 'articles.delete')->save();
        Deadbolt::user($publisher)->give('articles.edit')->save();

        $user->roles()->attach([$writer->id, $publisher->id]);

        $user->load('roles');

        $this->assertTrue(Deadbolt::users($user->roles)->anyHave('articles.edit'));
        $this->assertFalse(Deadbolt::users($user->roles)->allHave('articles.delete'));
    }
}
