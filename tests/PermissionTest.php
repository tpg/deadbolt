<?php

namespace TPG\Deadbolt\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use TPG\Deadbolt\Exceptions\NoSuchPermissionException;
use TPG\Deadbolt\Facades\Deadbolt;

class PermissionTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_get_an_array_of_defined_permissions(): void
    {
        $permissions = Deadbolt::all();

        self::assertEquals([
            'articles.create',
            'articles.edit',
            'articles.delete',
        ], $permissions);
    }

    /**
     * @test
     */
    public function it_can_describe_the_specified_permissions(): void
    {
        self::assertEquals(['articles.create' => 'Create Articles'], Deadbolt::describe('articles.create'));
        self::assertEquals(['articles.edit' => null], Deadbolt::describe('articles.edit'));
    }

    /**
     * @test
     */
    public function users_can_have_permissions(): void
    {
        $user = $this->user();

        Deadbolt::user($user)->give('articles.edit', 'articles.create');

        self::assertTrue(Deadbolt::user($user)->has('articles.edit'));
        self::assertContains('articles.create', Deadbolt::user($user)->all());
    }

    /**
     * @test
     */
    public function it_can_test_that_a_user_has_all_the_specified_permissions(): void
    {
        $user = $this->user();
        Deadbolt::user($user)->give('articles.edit', 'articles.create');

        self::assertTrue(Deadbolt::user($user)->hasAll('articles.edit', 'articles.create'));
        self::assertFalse(Deadbolt::user($user)->hasAll('articles.edit', 'articles.delete'));
    }

    /**
     * @test
     */
    public function it_can_test_that_a_user_has_any_of_the_specified_permissions(): void
    {
        $user = $this->user();
        Deadbolt::user($user)->give('articles.edit');

        self::assertTrue(Deadbolt::user($user)->hasAny('articles.edit', 'articles.create'));
        self::assertFalse(Deadbolt::user($user)->hasAny('articles.create', 'articles.delete'));
    }

    /**
     * @test
     */
    public function it_can_test_that_a_user_has_none_of_the_specified_permissions(): void
    {
        $user = $this->user();
        Deadbolt::user($user)->give('articles.edit');

        self::assertTrue(Deadbolt::user($user)->hasNone('articles.create', 'articles.delete'));
        self::assertFalse(Deadbolt::user($user)->hasNone('articles.edit', 'articles.create'));
    }

    /**
     * @test
     */
    public function it_will_throw_an_exception_if_the_permission_does_not_exist(): void
    {
        $user = $this->user();

        $this->expectException(NoSuchPermissionException::class);
        Deadbolt::user($user)->give('articles.change');
    }

    /**
     * @test
     */
    public function it_can_make_a_super_user(): void
    {
        $user = $this->user();
        Deadbolt::user($user)->super();

        self::assertTrue(Deadbolt::user($user)->hasAll(...Deadbolt::all()));
    }

    /**
     * @test
     */
    public function it_can_revoke_permissions_from_a_user(): void
    {
        $user = $this->user();
        Deadbolt::user($user)->super();

        Deadbolt::user($user)->revoke('articles.edit');

        self::assertFalse(Deadbolt::user($user)->has('articles.edit'));
    }

    /**
     * @test
     */
    public function it_can_revoke_all_permissions_from_a_user(): void
    {
        $user = $this->user();
        Deadbolt::user($user)->super();

        Deadbolt::user($user)->revokeAll();

        self::assertTrue(Deadbolt::user($user)->hasNone(...Deadbolt::all()));
    }

    /**
     * @test
     */
    public function it_can_sync_permissions(): void
    {
        $user = $this->user();
        Deadbolt::user($user)->give('articles.edit');

        Deadbolt::user($user)->sync('articles.create', 'articles.delete');

        self::assertTrue(Deadbolt::user($user)->hasAll('articles.create', 'articles.delete'));
        self::assertTrue(Deadbolt::user($user)->hasNone('articles.edit'));
    }

    /**
     * @test
     */
    public function it_allows_a_different_column_name(): void
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

        self::assertTrue(Deadbolt::user($user)->has('articles.edit'));
        self::assertContains('articles.create', Deadbolt::user($user)->all());
    }

    /**
     * @test
     */
    public function it_can_assign_permissioins_to_multiple_users(): void
    {
        $users = $this->getUserCollection();

        Deadbolt::users($users)->give('articles.create');
        Deadbolt::user($users[1])->give('articles.edit');

        self::assertTrue(Deadbolt::user($users[0])->has('articles.create'));
        self::assertTrue(Deadbolt::user($users[1])->has('articles.create'));

        self::assertTrue(Deadbolt::users($users)->has('articles.create'));
        self::assertTrue(Deadbolt::users($users)->allHave('articles.create'));
        self::assertFalse(Deadbolt::users($users)->allHave('articles.edit'));
        self::assertTrue(Deadbolt::users($users)->anyHave('articles.create'));
    }

    /**
     * @test
     **/
    public function it_can_make_multiple_users_super(): void
    {
        $users = $this->getUserCollection();

        Deadbolt::users($users)->super();

        self::assertTrue(Deadbolt::user($users[0])->isSuper());
        self::assertTrue(Deadbolt::user($users[1])->isSuper());
    }

    /**
     * @test
     **/
    public function it_can_revoke_permissions_from_users(): void
    {
        $users = $this->getUserCollection();

        Deadbolt::users($users)->super();

        Deadbolt::users($users)->revoke('articles.create');

        self::assertFalse(Deadbolt::user($users[0])->has('articles.create'));
        self::assertFalse(Deadbolt::user($users[1])->has('articles.create'));

        Deadbolt::users($users)->revokeAll();

        self::assertFalse(Deadbolt::user($users[0])->hasAny(Deadbolt::all()));
    }

    protected function getUserCollection(): array
    {
        return [
            $this->user(),
            $this->user(),
        ];
    }
}
