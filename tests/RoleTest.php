<?php

namespace TPG\Tests;

use TPG\Deadbolt\Exceptions\NoSuchRoleException;

class RoleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('deadbolt.permissions', [
            'articles.create',
            'articles.edit',
            'articles.publish',
            'articles.delete',
        ]);

        $this->app['config']->set('deadbolt.roles', [
            'writer' => [
                'articles.create',
                'articles.edit',
                'articles.delete',
            ],
            'publisher' => [
                'articles.edit',
                'articles.publish',
            ],
        ]);
    }

    /**
     * @test
     */
    public function users_can_be_assigned_permissions_by_role()
    {
        $user = $this->user();
        $user->deadbolt()->roles()->give('writer');

        $this->assertTrue($user->deadbolt()->permissions()->hasAll(config('deadbolt.roles.writer')));
    }

    /**
     * @test
     */
    public function permissions_can_be_revoked_by_role()
    {
        $user = $this->user();
        $user->deadbolt()->permissions()->super();

        $user->deadbolt()->roles()->revoke('publisher');

        $this->assertTrue($user->deadbolt()->permissions()->has('articles.create'));
        $this->assertFalse($user->deadbolt()->permissions()->has('articles.edit'));
        $this->assertFalse($user->deadbolt()->roles()->has('publisher'));
    }

    /**
     * @test
     */
    public function a_users_permissions_can_determine_the_roles()
    {
        $user = $this->user();
        $user->deadbolt()->permissions()->give('articles.edit', 'articles.publish');

        $this->assertFalse($user->deadbolt()->roles()->has('writer'));
        $this->assertTrue($user->deadbolt()->roles()->has('publisher'));
    }

    /**
     * @test
     */
    public function it_can_get_the_name_of_the_roles_by_permissions()
    {
        $user = $this->user();
        $user->deadbolt()->permissions()->give('articles.edit', 'articles.publish');

        $this->assertEquals(['publisher'], $user->deadbolt()->roles()->get());
    }

    /**
     * @test
     */
    public function it_will_throw_an_exception_if_the_role_doesnt_exist()
    {
        $user = $this->user();

        $this->expectException(NoSuchRoleException::class);

        $user->deadbolt()->roles()->give('bad role');
    }
}
