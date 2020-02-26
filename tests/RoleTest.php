<?php

namespace TPG\Tests;

use TPG\Deadbolt\Facades\Deadbolt;

class RoleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('deadbolt.permissions', [
            'articles.create',
            'articles.edit',
            'articles.delete',
            'articles.publish',
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
    public function it_can_get_an_array_of_roles()
    {
        $roles = Deadbolt::roles();

        $diff = array_diff(array_keys($roles), array_keys(config('deadbolt.roles')));

        $this->assertCount(0, $diff);
    }

    /**
     * @test
     */
    public function it_can_assign_permissions_by_giving_a_role()
    {
        $user = $this->user();

        Deadbolt::user($user)->give('publisher');

        $this->assertTrue(Deadbolt::user($user)->hasAll('articles.edit', 'articles.publish'));
        $this->assertFalse(Deadbolt::user($user)->has('articles.create'));
    }

    /**
     * @test
     */
    public function it_can_give_more_than_one_role()
    {
        $user = $this->user();

        Deadbolt::user($user)->give('publisher', 'writer');

        $this->assertTrue(Deadbolt::user($user)
            ->hasAll('articles.create', 'articles.edit', 'articles.delete', 'articles.publish'));
    }

    /**
     * @test
     */
    public function it_can_combine_permissions_and_roles()
    {
        $user = $this->user();
        Deadbolt::user($user)->give('publisher', 'articles.delete');

        $this->assertTrue(Deadbolt::user($user)->hasAll('articles.edit', 'articles.publish', 'articles.delete'));
        $this->assertTrue(Deadbolt::user($user)->hasNone('articles.create'));
    }

    /**
     * @test
     */
    public function it_can_revoke_permissions_by_role()
    {
        $user = $this->user();
        Deadbolt::user($user)->super();

        Deadbolt::user($user)->revoke('publisher');

        $this->assertTrue(Deadbolt::user($user)->hasNone('articles.edit', 'articles.publish'));
        $this->assertTrue(Deadbolt::user($user)->has('articles.create'));
    }

    /**
     * @test
     */
    public function it_can_get_the_users_roles()
    {
        $user = $this->user();

        $this->assertEquals([], Deadbolt::user($user)->roles());

        Deadbolt::user($user)->give('publisher');

        $this->assertEquals(['publisher'], Deadbolt::user($user)->roles());
    }

    /**
     * @test
     */
    public function it_can_check_if_a_user_has_a_specified_role()
    {
        $user = $this->user();

        Deadbolt::user($user)->give('publisher');

        $this->assertTrue(Deadbolt::user($user)->is('publisher'));
        $this->assertFalse(Deadbolt::user($user)->is('writer'));
    }
}
