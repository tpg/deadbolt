<?php

namespace TPG\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use TPG\Deadbolt\Exceptions\NoSuchPermissionException;
use TPG\Deadbolt\Facades\Permissions;

class PermissionTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_get_an_array_of_defined_permissions()
    {
        $permissions = Permissions::all();

        self::assertEquals([
            'articles.create',
            'articles.edit',
            'articles.delete',
        ], $permissions);
    }

    /**
     * @test
     */
    public function it_can_describe_the_specified_permissions()
    {
        self::assertEquals(['articles.create' => 'Create Articles'], Permissions::describe('articles.create'));
        self::assertEquals(['articles.edit' => null], Permissions::describe('articles.edit'));
    }

    /**
     * @test
     */
    public function users_can_have_permissions()
    {
        $user = $this->user();

        Permissions::user($user)->give('articles.edit', 'articles.create');

        self::assertTrue(Permissions::user($user)->has('articles.edit'));
        self::assertTrue(in_array('articles.create', Permissions::user($user)->all()));
    }

    /**
     * @test
     */
    public function it_can_test_that_a_user_has_all_the_specified_permissions()
    {
        $user = $this->user();
        Permissions::user($user)->give('articles.edit', 'articles.create');

        self::assertTrue(Permissions::user($user)->hasAll('articles.edit', 'articles.create'));
        self::assertFalse(Permissions::user($user)->hasAll('articles.edit', 'articles.delete'));
    }

    /**
     * @test
     */
    public function it_can_test_that_a_user_has_any_of_the_specified_permissions()
    {
        $user = $this->user();
        Permissions::user($user)->give('articles.edit');

        self::assertTrue(Permissions::user($user)->hasAny('articles.edit', 'articles.create'));
        self::assertFalse(Permissions::user($user)->hasAny('articles.create', 'articles.delete'));
    }

    /**
     * @test
     */
    public function it_can_test_that_a_user_has_none_of_the_specified_permissions()
    {
        $user = $this->user();
        Permissions::user($user)->give('articles.edit');

        self::assertTrue(Permissions::user($user)->hasNone('articles.create', 'articles.delete'));
        self::assertFalse(Permissions::user($user)->hasNone('articles.edit', 'articles.create'));
    }

    /**
     * @test
     */
    public function it_will_throw_an_exception_if_the_permission_does_not_exist()
    {
        $user = $this->user();

        $this->expectException(NoSuchPermissionException::class);
        Permissions::user($user)->give('articles.change');
    }

    /**
     * @test
     */
    public function it_can_make_a_super_user()
    {
        $user = $this->user();
        Permissions::user($user)->super();

        self::assertTrue(Permissions::user($user)->hasAll(Permissions::all()));
    }

    /**
     * @test
     */
    public function it_can_revoke_permissions_from_a_user()
    {
        $user = $this->user();
        Permissions::user($user)->super();

        Permissions::user($user)->revoke('articles.edit');

        self::assertFalse(Permissions::user($user)->has('articles.edit'));
    }

    /**
     * @test
     */
    public function it_can_revoke_all_permissions_from_a_user()
    {
        $user = $this->user();
        Permissions::user($user)->super();

        Permissions::user($user)->revokeAll();

        self::assertTrue(Permissions::user($user)->hasNone(Permissions::all()));
    }

    /**
     * @test
     */
    public function it_can_sync_permissions()
    {
        $user = $this->user();
        Permissions::user($user)->give('articles.edit');

        Permissions::user($user)->sync('articles.create', 'articles.delete');

        self::assertTrue(Permissions::user($user)->hasAll('articles.create', 'articles.delete'));
        self::assertTrue(Permissions::user($user)->hasNone('articles.edit'));
    }

    /**
     * @test
     */
    public function it_allows_a_different_column_name()
    {
        $this->app['config']->set('permissions.column', 'rights');

        Schema::drop('users');
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->text('rights')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        $user = $this->user();

        Permissions::user($user)->give('articles.create', 'articles.edit')->save();

        self::assertTrue(Permissions::user($user)->has('articles.edit'));
        self::assertContains('articles.create', Permissions::user($user)->all());
    }

    /**
     * @test
     */
    public function it_can_assign_permissioins_to_multiple_users()
    {
        $users = $this->getUserCollection();

        Permissions::users($users)->give('articles.create');
        Permissions::user($users[1])->give('articles.edit');

        self::assertTrue(Permissions::user($users[0])->has('articles.create'));
        self::assertTrue(Permissions::user($users[1])->has('articles.create'));

        self::assertTrue(Permissions::users($users)->allHave('articles.create'));
        self::assertFalse(Permissions::users($users)->allHave('articles.edit'));
        self::assertTrue(Permissions::users($users)->anyHave('articles.create'));
    }

    protected function getUserCollection(): array
    {
        return [
            $this->user(),
            $this->user(),
        ];
    }
}
