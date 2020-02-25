<?php

namespace TPG\Tests;

use TPG\Deadbolt\Exceptions\NoSuchPermissionException;

class PermissionTest extends TestCase
{
    /**
     * @test
     */
    public function a_user_can_have_permissions()
    {
        $user = $this->user();

        $user->deadbolt()->give('articles.create', 'articles.edit');

        $this->assertTrue($user->deadbolt()->has('articles.create'));
        $this->assertTrue($user->deadbolt()->has('articles.edit'));
    }

    /**
     * @test
     */
    public function permissions_can_be_made_permanent()
    {
        $user = $this->user();

        $user->deadbolt()->give('articles.create', 'articles.edit');
        $this->assertFalse($user->deadbolt()->isPermanent());

        $user->deadbolt()->makePermanent();
        $this->assertTrue($user->deadbolt()->isPermanent());
    }

    /**
     * @test
     */
    public function permissions_can_be_revoked()
    {
        $user = $this->user();

        $user->deadbolt()->give('articles.create', 'articles.edit', 'articles.delete');
        $user->deadbolt()->makePermanent();

        $user->deadbolt()->revoke('articles.delete');

        $this->assertFalse($user->deadbolt()->has('articles.delete'));
        $this->assertTrue($user->deadbolt()->has('articles.create'));

        $user->deadbolt()->makePermanent();

        $user = User::find(1);

        $this->assertFalse($user->deadbolt()->has('articles.delete'));
    }

    /**
     * @test
     */
    public function all_permissions_can_be_revoked()
    {
        $user = $this->user();

        $user->deadbolt()->give('articles.create', 'articles.edit', 'articles.delete');
        $user->deadbolt()->makePermanent();

        $user->deadbolt()->revokeAll();

        $this->assertEquals([], $user->deadbolt()->toArray());
    }

    /**
     * @test
     */
    public function it_can_check_if_a_user_has_all_permissions()
    {
        $user = $this->user();

        $user->deadbolt()->give('articles.create', 'articles.edit');

        $this->assertTrue($user->deadbolt()->hasAll('articles.create', 'articles.edit'));
        $this->assertFalse($user->deadbolt()->hasAll('articles.create', 'articles.edit', 'articles.delete'));
    }

    /**
     * @test
     */
    public function it_can_check_if_a_user_has_any_permissions()
    {
        $user = $this->user();

        $user->deadbolt()->give('articles.create');

        $this->assertTrue($user->deadbolt()->hasAny('articles.edit', 'articles.create', 'articles.delete'));
        $this->assertFalse($user->deadbolt()->hasAny('articles.edit', 'articles.delete'));
    }

    /**
     * @test
     */
    public function it_can_check_if_a_user_has_none_of_the_permissions()
    {
        $user = $this->user();

        $user->deadbolt()->give('articles.delete');

        $this->assertTrue($user->deadbolt()->hasNone('articles.edit', 'articles.create'));
        $this->assertFalse($user->deadbolt()->hasNone('articles.edit', 'articles.delete'));
    }

    /**
     * @test
     */
    public function it_can_make_a_super_user()
    {
        $user = $this->user();

        $user->deadbolt()->super()->makePermanent();

        $this->assertTrue($user->deadbolt()->hasAll(config('deadbolt.permissions')));
    }

    /**
     * @test
     */
    public function it_will_throw_an_exception_when_giving_an_undefined_permission()
    {
        $user = $this->user();

        $this->expectException(NoSuchPermissionException::class);
        $user->deadbolt()->give('articles.update');
    }

    /**
     * @test
     */
    public function user_permissions_can_be_cast_as_json()
    {
        $user = $this->user();
        $user->permissions = json_decode($user->permissions, true);
        $user->deadbolt()->super()->makePermanent();

        $user = User::find(1);
        $user->permissions = json_decode($user->permissions, true);

        $this->assertTrue($user->deadbolt()->has('articles.edit'));
        $this->assertTrue($user->deadbolt()->hasAll(config('deadbolt.permissions')));
    }
}
