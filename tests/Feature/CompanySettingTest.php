<?php

namespace Tests\Feature;

use App\Models\CompanySetting;
use App\Models\Holiday;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanySettingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);

        $this->admin = User::factory()->create();
        $superAdminRole = Role::where('slug', 'super_admin')->first();
        $this->admin->roles()->attach($superAdminRole);

        // Attach audit.view permission
        $auditPerm = Permission::where('slug', 'audit.view')->first();
        if ($auditPerm && ! $superAdminRole->permissions()->where('permission_id', $auditPerm->id)->exists()) {
            $superAdminRole->permissions()->attach($auditPerm->id);
        }
    }

    public function test_super_admin_can_access_settings_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/settings');

        $response->assertStatus(200);
        $response->assertSee('System Settings');
    }

    public function test_super_admin_can_update_company_settings(): void
    {
        $response = $this->actingAs($this->admin)->put('/settings', [
            'company_name' => 'Acme Global Enterprises',
            'company_email' => 'contact@acme-global.com',
            'company_phone' => '+1 555 0192',
            'currency_code' => 'EUR',
            'currency_symbol' => '€',
            'timezone' => 'UTC',
            'date_format' => 'Y-m-d',
        ]);

        $response->assertRedirect();
        $this->assertEquals('Acme Global Enterprises', CompanySetting::get('company_name'));
        $this->assertEquals('EUR', CompanySetting::get('currency_code'));
    }

    public function test_super_admin_can_create_holiday(): void
    {
        $response = $this->actingAs($this->admin)->post('/settings/holidays', [
            'name' => 'Independence Day',
            'date' => '2026-07-04',
            'is_recurring' => 1,
            'description' => 'National Holiday celebration',
        ]);

        $response->assertRedirect();
        $this->assertNotNull(Holiday::where('name', 'Independence Day')->first());
    }

    public function test_super_admin_can_view_and_update_role_permissions(): void
    {
        $viewResponse = $this->actingAs($this->admin)->get('/settings/roles');
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Role-Based Access Control');

        $employeeRole = Role::where('slug', 'employee')->first();

        $updateResponse = $this->actingAs($this->admin)->put("/settings/roles/{$employeeRole->id}", [
            'name' => $employeeRole->name,
            'description' => 'Updated regular employee role description',
            'permissions' => [],
        ]);

        $updateResponse->assertRedirect();
    }
}
