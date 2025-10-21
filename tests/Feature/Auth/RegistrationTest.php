<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->markTestSkipped('Public registration is intentionally disabled - only HR can create users via admin panel.');
    }

    public function test_new_users_can_register(): void
    {
        $this->markTestSkipped('Public registration is intentionally disabled - only HR can create users via admin panel.');
    }
}
