<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;

class RecaptchaLoginTest extends TestCase
{

    #[Test]
    public function login_fails_without_recaptcha_token(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret123',
            // no recaptcha_token
        ]);

        $response->assertSessionHasErrors('recaptcha');
    }

    #[Test]
    public function login_fails_with_low_score_recaptcha(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
        ]);

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.1,
            ], 200),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret123',
            'recaptcha_token' => 'dummy',
        ]);

        $response->assertSessionHasErrors('recaptcha');
    }

    #[Test]
    public function login_succeeds_with_valid_recaptcha(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
        ]);

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
            ], 200),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret123',
            'recaptcha_token' => 'dummy',
        ]);

        $response->assertSessionDoesntHaveErrors();
        $this->assertAuthenticatedAs($user);
    }
}
