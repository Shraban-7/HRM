<?php

namespace Tests\Unit\Services;

use App\Models\Department;
use App\Models\User;
use App\Services\Employee\OrgChartService;
use Tests\TestCase;

class OrgChartServiceTest extends TestCase
{
    protected OrgChartService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OrgChartService;
    }

    public function test_build_chart_creates_nested_tree(): void
    {
        $manager = User::factory()->create();
        $parent = Department::create(['name' => 'Corporate', 'manager_id' => $manager->id]);
        $child = Department::create(['name' => 'Engineering', 'parent_id' => $parent->id]);

        $tree = $this->service->buildChart();

        $this->assertCount(1, $tree);
        $this->assertEquals('Corporate', $tree[0]['name']);
        $this->assertEquals($manager->username, $tree[0]['manager']);
        $this->assertCount(1, $tree[0]['children']);
        $this->assertEquals('Engineering', $tree[0]['children'][0]['name']);
    }

    public function test_build_chart_returns_empty_without_departments(): void
    {
        $this->assertSame([], $this->service->buildChart());
    }
}
