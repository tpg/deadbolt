<?php

namespace TPG\Deadbolt\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use TPG\Deadbolt\DeadboltServiceProvider;
use TPG\Deadbolt\Facades\Deadbolt;
use TPG\Deadbolt\Tests\Models\User;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setupDatabase();

        $this->setPermissions();
    }

    protected function setupDatabase(): void
    {
        Schema::create('users', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->text('permissions')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    protected function setPermissions(): void
    {
        $this->app['config']->set('deadbolt.permissions', [
            'articles.create' => 'Create Articles',
            'articles.edit',
            'articles.delete',
        ]);
    }

    protected function getPackageProviders($app): array
    {
        return [
            DeadboltServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Deadbolt' => Deadbolt::class,
        ];
    }

    protected function user(): User
    {
        return User::factory()->create();
    }
}
