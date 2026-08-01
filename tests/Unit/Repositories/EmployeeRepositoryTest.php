<?php

namespace Tests\Unit\Repositories;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Repositories\Cache\CachedEmployeeRepository;
use App\Repositories\Eloquent\EmployeeRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class EmployeeRepositoryTest extends TestCase
{
    protected EmployeeRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EmployeeRepository;
    }

    public function test_find_all_paginates_employees(): void
    {
        Employee::factory()->count(5)->create();

        $page = $this->repository->findAll([], 15);

        $this->assertCount(5, $page);
        $this->assertInstanceOf(LengthAwarePaginator::class, $page);
    }

    public function test_find_all_filters_by_status(): void
    {
        Employee::factory()->active()->count(3)->create();
        Employee::factory()->terminated()->count(2)->create();

        $page = $this->repository->findAll(['status' => 'terminated'], 15);

        $this->assertCount(2, $page);
    }

    public function test_find_by_user_id_returns_employee(): void
    {
        $user = User::factory()->create();
        Employee::factory()->create(['user_id' => $user->id]);

        $employee = $this->repository->findByUserId($user->id);

        $this->assertNotNull($employee);
        $this->assertEquals($user->id, $employee->user_id);
    }

    public function test_cached_repository_caches_find_by_id(): void
    {
        $employee = Employee::factory()->create();
        Cache::shouldReceive('get')->with('employees:version', 1)->andReturn(1);
        Cache::shouldReceive('remember')
            ->twice()
            ->andReturn($employee);

        $cached = new CachedEmployeeRepository($this->repository);

        $this->assertEquals($employee->id, $cached->findById($employee->id)?->id);
        $this->assertEquals($employee->id, $cached->findById($employee->id)?->id);
    }

    public function test_cached_repository_flushes_cache_on_create(): void
    {
        $department = Department::create(['name' => 'Tech']);
        $user = User::factory()->create();

        Cache::shouldReceive('increment')->once()->with('employees:version');

        $cached = new CachedEmployeeRepository($this->repository);
        $cached->create([
            'user_id' => $user->id,
            'department_id' => $department->id,
            'employee_id' => 'EMP-C-1',
            'hire_date' => '2026-08-01',
            'designation' => 'Engineer',
            'salary' => 50000,
            'status' => 'active',
            'employment_type' => 'full_time',
        ]);
    }

    public function test_delete_terminates_employee(): void
    {
        $user = User::factory()->create();
        $department = Department::create(['name' => 'Ops']);
        $employee = Employee::factory()->create(['user_id' => $user->id, 'department_id' => $department->id, 'status' => 'active']);

        $this->repository->delete($employee);

        $this->assertDatabaseHas('employees', ['id' => $employee->id, 'status' => 'terminated']);
    }
}
