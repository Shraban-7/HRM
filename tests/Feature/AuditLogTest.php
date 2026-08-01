<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    protected function loginAsAdmin(): void
    {
        $user = User::create([
            'email' => 'audit@test.com',
            'username' => 'audit_admin',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        $role = Role::create(['name' => 'HR Admin', 'slug' => 'hr_admin']);
        $user->roles()->attach($role);

        $this->session([
            'user_id' => $user->id,
            'user_roles' => ['hr_admin'],
            'user_permissions' => ['audit.view'],
        ]);
    }

    public function test_can_view_audit_logs(): void
    {
        $this->loginAsAdmin();
        $user = User::factory()->create();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'employee.hired',
            'resource' => 'employee',
            'resource_id' => '1',
            'ip_address' => '127.0.0.1',
            'created_at' => now(),
        ]);

        $response = $this->get(route('audit.index'));

        $response->assertStatus(200);
        $response->assertSee('employee.hired');
    }
}
