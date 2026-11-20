<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ExportTest extends TestCase
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
            'first_name' => 'Demo',
            'last_name' => 'Staff',
            'employee_id' => 'DEMO-100',
            'department_id' => $dept->id,
            'designation' => 'Staff Analyst',
            'hire_date' => now()->toDateString(),
            'status' => 'active',
            'salary' => 70000,
        ]);
    }

    public function test_export_employees_csv_download(): void
    {
        $response = $this->actingAs($this->admin)->get('/reports/export/employees');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('DEMO-100', $response->streamedContent());
    }

    public function test_export_payroll_csv_download(): void
    {
        $payroll = Payroll::create([
            'period_start' => now()->startOfMonth()->toDateString(),
            'period_end' => now()->endOfMonth()->toDateString(),
            'total_amount' => 70000,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->admin)->get("/reports/export/payroll?payroll_id={$payroll->id}");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_export_attendance_csv_download(): void
    {
        $response = $this->actingAs($this->admin)->get('/reports/export/attendance');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_import_employees_csv(): void
    {
        $csvContent = "username,email,department_id,employee_id,hire_date,designation,salary\n";
        $csvContent .= "importuser,import_tester@company.com,{$this->employee->department_id},IMP-999,2026-01-15,Developer,80000\n";

        $file = UploadedFile::fake()->createWithContent('employees.csv', $csvContent);

        $response = $this->actingAs($this->admin)->post('/reports/import/employees', [
            'csv_file' => $file,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('employees', ['employee_id' => 'IMP-999']);
    }
}
