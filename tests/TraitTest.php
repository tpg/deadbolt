<?php

declare(strict_types=1);

namespace TPG\Tests;

class TraitTest extends TestCase
{
    /**
     * @test
     **/
    public function it_can_get_all_permissions()
    {
        $user = $this->user();

        $user->permissions()->give('articles.edit');

        self::assertSame(['articles.edit'], $user->permissions()->all());
    }
}
