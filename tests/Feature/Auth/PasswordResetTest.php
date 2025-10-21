<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        $this->markTestSkipped('Password reset via email is disabled - system uses username-based authentication.');
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        $this->markTestSkipped('Password reset via email is disabled - system uses username-based authentication.');
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $this->markTestSkipped('Password reset via email is disabled - system uses username-based authentication.');
    }
}
