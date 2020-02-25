<?php

namespace TPG\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use TPG\Deadbolt\Facades\Deadbolt;

class DatabaseDriverTest extends TestCase
{
    protected $permissions = [
        'articles.create',
        'articles.edit',
        'articles.delete',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupPermissionsTable();

        $this->app['config']->set('deadbolt.permissions.driver', 'database');
    }

    /**
     * @test
     */
    public function permissions_can_be_sourced_from_a_database_table(): void
    {
        $permissions = Deadbolt::permissions();

        $this->assertContains('articles.create', $permissions);
        $this->assertCount(3, $permissions);
    }

    protected function setupPermissionsTable(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('title')->nullable();
        });

        DB::table('permissions')->insert(array_map(function ($permission) {
            return [
                'name' => $permission,
            ];
        }, $this->permissions));
    }
}
