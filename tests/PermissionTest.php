<?php

namespace TPG\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use TPG\Deadbolt\Exceptions\NoSuchPermissionException;
use TPG\Deadbolt\Facades\Deadbolt;

class PermissionTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_get_an_array_of_defined_permissions()
    {
        $permissions = Deadbolt::permissions();

        $this->assertCount(0, array_diff($permissions, config('deadbolt.permissions')));
    }

    /**
     * @test
     */
    public function users_can_have_permissions()
    {
        $user = $this->user();

        Deadbolt::user($user)->give('articles.edit', 'articles.create');

        $this->assertTrue(Deadbolt::user($user)->has('articles.edit'));
        $this->assertTrue(in_array('articles.create', Deadbolt::user($user)->permissions()));
    }

    /**
     * @test
     */
    public function it_can_test_that_a_user_has_all_the_specified_permissions()
    {
        $user = $this->user();
        Deadbolt::user($user)->give('articles.edit', 'articles.create');

        $this->assertTrue(Deadbolt::user($user)->hasAll('articles.edit', 'articles.create'));
        $this->assertFalse(Deadbolt::user($user)->hasAll('articles.edit', 'articles.delete'));
    }

    /**
     * @test
     */
    public function it_can_test_that_a_user_has_any_of_the_specified_permissions()
    {
        $user = $this->user();
        Deadbolt::user($user)->give('articles.edit');

        $this->assertTrue(Deadbolt::user($user)->hasAny('articles.edit', 'articles.create'));
        $this->assertFalse(Deadbolt::user($user)->hasAny('articles.create', 'articles.delete'));
    }

    /**
     * @test
     */
    public function it_can_test_that_a_user_has_none_of_the_specified_permissions()
    {
        $user = $this->user();
        Deadbolt::user($user)->give('articles.edit');

        $this->assertTrue(Deadbolt::user($user)->hasNone('articles.create', 'articles.delete'));
        $this->assertFalse(Deadbolt::user($user)->hasNone('articles.edit', 'articles.create'));
    }

    /**
     * @test
     */
    public function it_will_throw_an_exception_if_the_permission_does_not_exist()
    {
        $user = $this->user();

        $this->expectException(NoSuchPermissionException::class);
        Deadbolt::user($user)->give('articles.change');
    }

    /**
     * @test
     */
    public function it_can_make_a_permission_set_permanent()
    {
        $user = $this->user();
        Deadbolt::user($user)->give('articles.edit', 'articles.delete')->save();

        $this->assertTrue(Deadbolt::user($user)->saved());
    }

    /**
     * @test
     */
    public function it_can_make_a_super_user()
    {
        $user = $this->user();
        Deadbolt::user($user)->super();

        $this->assertTrue(Deadbolt::user($user)->hasAll(Deadbolt::permissions()));
    }

    /**
     * @test
     */
    public function it_can_revoke_permissions_from_a_user()
    {
        $user = $this->user();
        Deadbolt::user($user)->super();

        Deadbolt::user($user)->revoke('articles.edit');

        $this->assertFalse(Deadbolt::user($user)->has('articles.edit'));
    }

    /**
     * @test
     */
    public function it_can_revoke_all_permissions_from_a_user()
    {
        $user = $this->user();
        Deadbolt::user($user)->super();

        Deadbolt::user($user)->revokeAll();

        $this->assertTrue(Deadbolt::user($user)->hasNone(Deadbolt::permissions()));
    }

    /**
     * @test
     */
    public function it_allows_a_different_column_name()
    {
        $this->app['config']->set('deadbolt.column', 'rights');

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

        Deadbolt::user($user)->give('articles.create', 'articles.edit')->save();

        $this->assertTrue(Deadbolt::user($user)->has('articles.edit'));
        $this->assertContains('articles.create', Deadbolt::user($user)->permissions());
    }
}
