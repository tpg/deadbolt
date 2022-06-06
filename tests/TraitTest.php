<?php

declare(strict_types=1);

namespace TPG\Tests;

class TraitTest extends TestCase
{
    /**
     * @test
     **/
    public function it_can_get_all_permissions(): void
    {
        $user = $this->user();

        $user->permissions()->give('articles.edit');

        self::assertSame(['articles.edit'], $user->permissions()->all());
    }

    /**
     * @test
     **/
    public function it_can_get_permission_descriptions(): void
    {
        $user = $this->user();

        $user->permissions()->give('articles.create');

        self::assertSame(['articles.create' => 'Create Articles'], $user->permissions()->describe());
    }
}
