<?php

declare(strict_types=1);

namespace TPG\Deadbolt\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use TPG\Deadbolt\Facades\Deadbolt;
use TPG\Deadbolt\Tests\Models\Permission;

class EloquentDriverTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('deadbolt.driver', EloquentDriver::class);

        Permission::create(['name' => 'articles.create', 'description' => 'Create Articles']);
        Permission::create(['name' => 'articles.edit', 'description' => 'Edit Articles']);
        Permission::create(['name' => 'articles.delete', 'description' => 'Delete Articles']);
    }

    protected function setupDatabase(): void
    {
        parent::setupDatabase();

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description');
            $table->timestamps();
        });

        Schema::create('permission_user', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('user_id');
        });
    }

    /**
     * @test
     **/
    public function it_can_return_an_array_of_permissions(): void
    {
        $permissions = Deadbolt::all();
        self::assertSame([
            'articles.create',
            'articles.edit',
            'articles.delete',
        ], $permissions);
    }

    /**
     * @test
     **/
    public function it_can_fetch_a_description(): void
    {
        self::assertSame('Create Articles', Arr::first(Deadbolt::describe('articles.create')));
    }
}
