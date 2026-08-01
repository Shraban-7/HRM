<?php

namespace Tests\Feature;

use App\Models\JobPost;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    protected function createUser(array $overrides = []): User
    {
        return User::create(array_merge([
            'email' => 'sec@test.com',
            'username' => 'sec_user',
            'password' => Hash::make('password'),
            'status' => 'active',
        ], $overrides));
    }

    public function test_security_headers_are_present_on_responses(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_login_is_rate_limited_after_six_attempts(): void
    {
        $this->createUser();

        for ($i = 0; $i < 6; $i++) {
            $this->post('/login', [
                'identifier' => 'sec@test.com',
                'password' => 'wrongpassword',
            ]);
        }

        $response = $this->post('/login', [
            'identifier' => 'sec@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(429);
    }

    public function test_resume_upload_rejects_malicious_extension(): void
    {
        $job = JobPost::create(['title' => 'Engineer', 'description' => 'Role', 'status' => 'published']);

        $response = $this->post(route('careers.apply', $job), [
            'name' => 'Eve Hacker',
            'email' => 'eve@example.com',
            'phone' => '555666',
            'resume' => UploadedFile::fake()->create('resume.php', 100, 'application/octet-stream'),
        ]);

        $response->assertSessionHasErrors('resume');
        $this->assertDatabaseMissing('applicants', ['email' => 'eve@example.com']);
    }

    public function test_password_is_stored_as_bcrypt_hash(): void
    {
        $user = $this->createUser();

        $this->assertNotEquals('password', $user->password);
        $this->assertTrue(Hash::isHashed($user->password));
        $this->assertTrue(Hash::check('password', $user->password));
    }

    public function test_unauthenticated_json_request_returns_401(): void
    {
        $response = $this->getJson('/dashboard');

        $response->assertStatus(401);
    }

    public function test_unknown_route_renders_custom_404_page(): void
    {
        $response = $this->get('/this-route-does-not-exist');

        $response->assertStatus(404);
        $response->assertSee('Page Not Found');
        $response->assertSee('does not exist');
    }
}
