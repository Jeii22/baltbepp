<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RecaptchaRegistrationTest extends TestCase
{
    #[Test]
    public function registration_fails_without_recaptcha_token(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'tester123@gmail.com',
            'password' => 'Secret123!',
            'password_confirmation' => 'Secret123!',
            'terms' => 1,
            // missing recaptcha_token
        ]);

        $response->assertSessionHasErrors('recaptcha');
    }

    #[Test]
    public function registration_fails_with_low_score_recaptcha(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.1,
                'action' => 'register',
            ], 200),
        ]);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'tester456@gmail.com',
            'password' => 'Secret123!',
            'password_confirmation' => 'Secret123!',
            'terms' => 1,
            'recaptcha_token' => 'dummy',
        ]);

        $response->assertSessionHasErrors('recaptcha');
    }

    #[Test]
    public function registration_succeeds_with_valid_recaptcha(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
                'action' => 'register',
            ], 200),
        ]);

        $response = $this->post('/register', [
            'name' => 'Good User',
            'email' => 'gooduser1@gmail.com',
            'password' => 'Secret123!',
            'password_confirmation' => 'Secret123!',
            'terms' => 1,
            'recaptcha_token' => 'dummy',
        ]);

        $response->assertSessionDoesntHaveErrors();
        $this->assertAuthenticated();
    }
}
