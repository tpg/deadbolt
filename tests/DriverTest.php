<?php

namespace TPG\Tests;

use TPG\Deadbolt\Facades\Permissions;

class DriverTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_use_a_custom_driver()
    {
        $this->app['config']->set('permissions.driver', CustomDriver::class);

        $this->assertEquals((new CustomDriver())->permissions(), Permissions::describe());
        $this->assertEquals(array_keys((new CustomDriver())->permissions()), Permissions::all());

        $user = $this->user();

        Permissions::user($user)->give('test permission');

        $this->assertTrue(Permissions::user($user)->has('test permission'));
    }

    /**
     * @test
     */
    public function a_custom_driver_can_be_set_inline()
    {
        $user = $this->user();

        Permissions::driver(new CustomDriver())->user($user)->give('test permission');

        $this->assertTrue(Permissions::driver(new CustomDriver())->user($user)->has('test permission'));
    }
}
