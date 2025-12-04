<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupActivityLogs extends Command
{
    protected $signature = 'app:cleanup-activity-logs';
    protected $description = 'Remove activity logs for deleted models';

    public function handle()
    {
        $this->info('Cleaning up activity logs...');

        // Delete Presenter activity logs
        $presenterDeleted = DB::table('activity_log')
            ->where('subject_type', 'App\\Models\\Presenter')
            ->delete();
        
        if ($presenterDeleted > 0) {
            $this->info("✓ Deleted $presenterDeleted Presenter activity logs");
        }

        // Delete ContentForm activity logs
        $contentFormDeleted = DB::table('activity_log')
            ->where('subject_type', 'App\\Models\\ContentForm')
            ->delete();
        
        if ($contentFormDeleted > 0) {
            $this->info("✓ Deleted $contentFormDeleted ContentForm activity logs");
        }

        // Delete PresenterReadStatus activity logs
        $presenterReadStatusDeleted = DB::table('activity_log')
            ->where('subject_type', 'App\\Models\\PresenterReadStatus')
            ->delete();
        
        if ($presenterReadStatusDeleted > 0) {
            $this->info("✓ Deleted $presenterReadStatusDeleted PresenterReadStatus activity logs");
        }

        $total = $presenterDeleted + $contentFormDeleted + $presenterReadStatusDeleted;
        $this->info("✓ Total activity logs cleaned: $total");
        $this->info('Activity logs cleanup completed successfully!');
    }
}

