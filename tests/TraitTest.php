<?php

declare(strict_types=1);

namespace TPG\Deadbolt\Tests;

use PHPUnit\Framework\Attributes\Test;

class TraitTest extends TestCase
{
    #[Test]
    public function it_can_get_all_permissions(): void
    {
        $user = $this->user();

        $user->permissions()->give('articles.edit');

        self::assertSame(['articles.edit'], $user->permissions()->all());
    }

    #[Test]
    public function it_can_get_permission_descriptions(): void
    {
        $user = $this->user();

        $user->permissions()->give('articles.create');

        self::assertSame(['articles.create' => 'Create Articles'], $user->permissions()->describe());
    }
}
