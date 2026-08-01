<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class QueueMonitor extends Command
{
    protected $signature = 'queue:monitor {--retry-failed : Retry all failed jobs before printing the report}';

    protected $description = 'Report queue health: pending, processing, and failed jobs.';

    public function handle(): int
    {
        if ($this->option('retry-failed')) {
            $this->call('queue:retry', ['id' => ['all']]);
        }

        $rows = [
            ['Pending jobs', $this->countTable('jobs')],
            ['Failed jobs', $this->countTable('failed_jobs')],
            ['Scheduled jobs (cache)', $this->countTable('cache')],
            ['Sessions (active)', $this->countTable('sessions')],
        ];

        $this->table(['Metric', 'Count'], $rows);

        $failed = $this->countTable('failed_jobs');
        if ($failed > 0) {
            $this->warn("There are {$failed} failed job(s). Run `php artisan queue:retry --failed` or `php artisan queue:monitor --retry-failed` to retry them.");
        } else {
            $this->info('Queue is healthy. No failed jobs.');
        }

        return self::SUCCESS;
    }

    protected function countTable(string $table): int
    {
        try {
            return (int) DB::table($table)->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }
}