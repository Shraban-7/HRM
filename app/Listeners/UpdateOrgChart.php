<?php

namespace App\Listeners;

use App\Events\EmployeeHired;
use App\Events\EmployeeTerminated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class UpdateOrgChart implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(EmployeeHired|EmployeeTerminated $event): void
    {
        // Clear the organization chart cache so it regenerates on next view
        Cache::forget('org_chart_data');
        Cache::forget('org_chart_tree');
    }
}