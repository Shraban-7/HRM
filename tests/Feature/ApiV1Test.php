<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\PersonalAccessToken;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiV1Test extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Employee $employee;
    protected string $plainToken = 'api-test-token-secret-xyz';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
        $this->seed(DepartmentSeeder::class);

        $this->user = User::create([
            'email' => 'api.dev@hrm.com',
            'username' => 'apidev',
            'password' => Hash::make('secret123'),
            'status' => 'active',
        ]);

        $adminRole = Role::where('slug', 'super_admin')->first();
        $this->user->roles()->attach($adminRole);

        $dept = Department::first();
        $this->employee = Employee::create([
            'user_id' => $this->user->id,
            'first_name' => 'API',
            'last_name' => 'Developer',
            'employee_id' => 'API-001',
            'department_id' => $dept->id,
            'designation' => 'Lead Integrations Engineer',
            'hire_date' => now()->toDateString(),
            'status' => 'active',
            'salary' => 90000,
        ]);

        PersonalAccessToken::create([
            'user_id' => $this->user->id,
            'name' => 'Automated Test Token',
            'token' => hash('sha256', $this->plainToken),
            'abilities' => ['*'],
        ]);
    }

    public function test_auth_token_issuance_with_valid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/token', [
            'email' => 'api.dev@hrm.com',
            'password' => 'secret123',
            'device_name' => 'CI Test Pipeline',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token',
                    'token_type',
                    'user' => ['id', 'email', 'username'],
                ],
            ]);
    }

    public function test_auth_token_issuance_rejects_invalid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/token', [
            'email' => 'api.dev@hrm.com',
            'password' => 'wrongpassword',
            'device_name' => 'CI Test Pipeline',
        ]);

        $response->assertStatus(401)
            ->assertJson(['success' => false]);
    }

    public function test_get_current_user_profile(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->plainToken}")
            ->getJson('/api/v1/me');

        $response->assertStatus(200)
            ->assertJsonPath('data.user.email', 'api.dev@hrm.com')
            ->assertJsonPath('data.employee.employee_id', 'API-001');
    }

    public function test_employee_directory_listing(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->plainToken}")
            ->getJson('/api/v1/employees');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'employee_id', 'designation'],
                ],
                'pagination' => [
                    'total',
                    'current_page',
                    'last_page',
                    'per_page',
                ],
            ]);
    }

    public function test_attendance_clock_in_and_out(): void
    {
        $clockInResponse = $this->withHeader('Authorization', "Bearer {$this->plainToken}")
            ->postJson('/api/v1/attendance/clock-in', [
                'latitude' => 37.7749,
                'longitude' => -122.4194,
            ]);

        $clockInResponse->assertStatus(200)
            ->assertJson(['success' => true]);

        $clockOutResponse = $this->withHeader('Authorization', "Bearer {$this->plainToken}")
            ->postJson('/api/v1/attendance/clock-out');

        $clockOutResponse->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_biometric_device_punch_sync(): void
    {
        $syncResponse = $this->withHeader('Authorization', "Bearer {$this->plainToken}")
            ->postJson('/api/v1/integrations/biometric-sync', [
                'device_id' => 'ZKT-EAST-LOBBY-01',
                'logs' => [
                    [
                        'employee_code' => 'API-001',
                        'timestamp' => now()->toDateTimeString(),
                        'type' => 'in',
                    ],
                ],
            ]);

        $syncResponse->assertStatus(200)
            ->assertJsonPath('processed', 1)
            ->assertJsonPath('failed', 0);
    }
}
