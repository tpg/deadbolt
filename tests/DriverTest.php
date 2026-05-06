<?php

namespace TPG\Deadbolt\Tests;

use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;
use TPG\Deadbolt\Facades\Deadbolt;

class DriverTest extends TestCase
{
    #[Test]
    public function it_can_use_a_custom_driver(): void
    {
        Config::set('deadbolt.driver', CustomDriver::class);

        $this->assertEquals((new CustomDriver)->permissions(), Deadbolt::describe());
        $this->assertEquals(array_keys((new CustomDriver)->permissions()), Deadbolt::all());

        $user = $this->user();

        Deadbolt::user($user)->give('test permission');

        $this->assertTrue(Deadbolt::user($user)->has('test permission'));
    }

    #[Test]
    public function a_custom_driver_can_be_set_inline(): void
    {
        $user = $this->user();

        Deadbolt::driver(new CustomDriver)->user($user)->give('test permission');

        $this->assertTrue(Deadbolt::driver(new CustomDriver)->user($user)->has('test permission'));
    }
}
