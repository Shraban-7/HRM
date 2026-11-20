<?php

namespace Tests\Feature;

use App\Models\AssetCategory;
use App\Models\Department;
use App\Models\DocumentCategory;
use App\Models\Employee;
use App\Models\ExpenseCategory;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommercialModulesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
        $this->seed(DepartmentSeeder::class);

        $this->admin = User::factory()->create();
        $superAdminRole = Role::where('slug', 'super_admin')->first();
        $this->admin->roles()->attach($superAdminRole);

        $dept = Department::first();
        $this->employee = Employee::create([
            'user_id' => $this->admin->id,
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'employee_id' => 'EMP-001',
            'department_id' => $dept->id,
            'designation' => 'Executive',
            'hire_date' => now()->toDateString(),
            'status' => 'active',
            'salary' => 95000,
        ]);
    }

    public function test_onboarding_module_renders_and_functions(): void
    {
        $response = $this->actingAs($this->admin)->get('/onboarding');
        $response->assertStatus(200);
        $response->assertSee('Employee Onboarding');
    }

    public function test_exit_module_allows_submitting_resignation(): void
    {
        $indexResponse = $this->actingAs($this->admin)->get('/exit');
        $indexResponse->assertStatus(200);

        $submitResponse = $this->actingAs($this->admin)->post('/exit', [
            'employee_id' => $this->employee->id,
            'resignation_date' => now()->toDateString(),
            'last_working_day' => now()->addDays(30)->toDateString(),
            'reason' => 'Career advancement and relocated.',
        ]);

        $submitResponse->assertRedirect();
        $this->assertDatabaseHas('exit_requests', [
            'employee_id' => $this->employee->id,
            'status' => 'pending',
        ]);
    }

    public function test_asset_management_creates_and_lists_hardware(): void
    {
        $cat = AssetCategory::create(['name' => 'Laptops', 'code' => 'LAPTOPS']);

        $createResponse = $this->actingAs($this->admin)->post('/assets', [
            'asset_category_id' => $cat->id,
            'name' => 'MacBook Pro M3 Max 16"',
            'asset_tag' => 'AST-MBP-001',
            'serial_number' => 'SNM3M99882',
            'purchase_date' => now()->toDateString(),
            'cost' => 3499.00,
            'status' => 'available',
        ]);

        $createResponse->assertRedirect();
        $this->assertDatabaseHas('assets', ['asset_tag' => 'AST-MBP-001']);

        $indexResponse = $this->actingAs($this->admin)->get('/assets');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('MacBook Pro M3 Max');
    }

    public function test_digital_documents_repository(): void
    {
        DocumentCategory::create(['name' => 'Identity Proofs']);

        $response = $this->actingAs($this->admin)->get('/documents');
        $response->assertStatus(200);
        $response->assertSee('Digital Documents');
    }

    public function test_expenses_and_receipt_claims(): void
    {
        $cat = ExpenseCategory::create(['name' => 'Client Entertainment', 'code' => 'ENT', 'max_limit' => 2000]);

        $submitResponse = $this->actingAs($this->admin)->post('/expenses', [
            'employee_id' => $this->employee->id,
            'expense_category_id' => $cat->id,
            'amount' => 185.50,
            'claim_date' => now()->toDateString(),
            'description' => 'Client Q1 Strategy Dinner',
        ]);

        $submitResponse->assertRedirect();
        $this->assertDatabaseHas('expenses', [
            'employee_id' => $this->employee->id,
            'amount' => 185.50,
            'status' => 'pending',
        ]);

        $indexResponse = $this->actingAs($this->admin)->get('/expenses');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Client Q1 Strategy Dinner');
    }

    public function test_loans_and_salary_advances(): void
    {
        $submitResponse = $this->actingAs($this->admin)->post('/loans', [
            'employee_id' => $this->employee->id,
            'loan_amount' => 1500.00,
            'interest_rate' => 0.0,
            'term_months' => 3,
        ]);

        $submitResponse->assertRedirect();
        $this->assertDatabaseHas('loans', [
            'employee_id' => $this->employee->id,
            'loan_amount' => 1500.00,
            'status' => 'pending',
        ]);

        $indexResponse = $this->actingAs($this->admin)->get('/loans');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('1,500');
    }

    public function test_helpdesk_ticket_lifecycle(): void
    {
        $createResponse = $this->actingAs($this->admin)->post('/helpdesk', [
            'employee_id' => $this->employee->id,
            'department' => 'IT Support',
            'priority' => 'high',
            'subject' => 'VPN Authentication Gateway Timeout',
            'description' => 'Cannot connect to US-East VPN tunnel since morning update.',
        ]);

        $createResponse->assertRedirect();
        $ticket = Ticket::where('subject', 'VPN Authentication Gateway Timeout')->first();
        $this->assertNotNull($ticket);

        $showResponse = $this->actingAs($this->admin)->get("/helpdesk/{$ticket->id}");
        $showResponse->assertStatus(200);
        $showResponse->assertSee('VPN Authentication Gateway Timeout');

        $commentResponse = $this->actingAs($this->admin)->post("/helpdesk/{$ticket->id}/comments", [
            'comment' => 'Network team has cycled the certificate, please retry.',
            'status' => 'resolved',
        ]);

        $commentResponse->assertRedirect();
        $this->assertDatabaseHas('ticket_comments', [
            'ticket_id' => $ticket->id,
            'comment' => 'Network team has cycled the certificate, please retry.',
        ]);
        $this->assertEquals('resolved', $ticket->fresh()->status);
    }
}
