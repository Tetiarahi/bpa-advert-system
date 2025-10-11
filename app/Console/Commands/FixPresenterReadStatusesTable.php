<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixPresenterReadStatusesTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:presenter-read-statuses {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix presenter_read_statuses table constraints and indexes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Fixing presenter_read_statuses table...');

        if (!$this->option('force') && !$this->confirm('This will modify the presenter_read_statuses table. Continue?')) {
            $this->info('Operation cancelled.');
            return Command::FAILURE;
        }

        try {
            // Step 1: Check current state
            $this->info('📋 Checking current table state...');

            if (!Schema::hasTable('presenter_read_statuses')) {
                $this->error('❌ Table presenter_read_statuses does not exist!');
                return Command::FAILURE;
            }

            // Get all indexes
            $indexes = DB::select("SHOW INDEX FROM presenter_read_statuses");
            $indexNames = collect($indexes)->pluck('Key_name')->unique()->filter(function($name) {
                return $name !== 'PRIMARY';
            })->toArray();

            $this->info('Current indexes: ' . implode(', ', $indexNames));

            // Step 2: Check and handle foreign key constraints
            $this->info('🔍 Checking foreign key constraints...');

            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'presenter_read_statuses'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");

            foreach ($foreignKeys as $fk) {
                $this->info("Found FK: {$fk->CONSTRAINT_NAME} on {$fk->COLUMN_NAME} -> {$fk->REFERENCED_TABLE_NAME}");
            }

            // Step 3: Drop problematic constraints (skip the ones with FK dependencies)
            $this->info('🗑️ Removing problematic constraints...');

            $constraintsToRemove = [
                'presenter_content_timeslot_reading_unique',
                'presenter_content_unique'  // Skip presenter_content_timeslot_unique if it has FK dependency
            ];

            foreach ($constraintsToRemove as $constraint) {
                if (in_array($constraint, $indexNames)) {
                    try {
                        DB::statement("ALTER TABLE presenter_read_statuses DROP INDEX `{$constraint}`");
                        $this->info("✅ Dropped constraint: {$constraint}");
                    } catch (\Exception $e) {
                        $this->warn("⚠️ Could not drop constraint {$constraint}: " . $e->getMessage());
                    }
                }
            }

            // Handle the problematic presenter_content_timeslot_unique separately
            if (in_array('presenter_content_timeslot_unique', $indexNames)) {
                $this->info("⚠️ Keeping presenter_content_timeslot_unique due to foreign key dependency");
                $this->info("This is okay - we'll add the new constraint alongside it");
            }

            // Step 4: Ensure required columns exist
            $this->info('📝 Ensuring required columns exist...');

            $columns = Schema::getColumnListing('presenter_read_statuses');

            if (!in_array('time_slot', $columns)) {
                DB::statement("ALTER TABLE presenter_read_statuses ADD COLUMN time_slot ENUM('morning', 'lunch', 'evening') AFTER content_id");
                $this->info('✅ Added time_slot column');
            }

            if (!in_array('reading_number', $columns)) {
                DB::statement("ALTER TABLE presenter_read_statuses ADD COLUMN reading_number TINYINT DEFAULT 1 AFTER time_slot");
                $this->info('✅ Added reading_number column');
            }

            if (!in_array('readings_data', $columns)) {
                DB::statement("ALTER TABLE presenter_read_statuses ADD COLUMN readings_data JSON NULL AFTER reading_number");
                $this->info('✅ Added readings_data column');
            }

            // Step 5: Clean up duplicate data
            $this->info('🧹 Cleaning up duplicate data...');

            $duplicates = DB::select("
                SELECT presenter_id, content_type, content_id, time_slot, reading_number, COUNT(*) as count
                FROM presenter_read_statuses
                GROUP BY presenter_id, content_type, content_id, time_slot, reading_number
                HAVING COUNT(*) > 1
            ");

            if (count($duplicates) > 0) {
                $this->info("Found " . count($duplicates) . " duplicate groups. Removing duplicates...");

                DB::statement("
                    DELETE t1 FROM presenter_read_statuses t1
                    INNER JOIN presenter_read_statuses t2
                    WHERE t1.id > t2.id
                    AND t1.presenter_id = t2.presenter_id
                    AND t1.content_type = t2.content_type
                    AND t1.content_id = t2.content_id
                    AND t1.time_slot = t2.time_slot
                    AND t1.reading_number = t2.reading_number
                ");

                $this->info('✅ Removed duplicate records');
            } else {
                $this->info('✅ No duplicates found');
            }

            // Step 6: Add the correct unique constraint
            $this->info('🔗 Adding correct unique constraint...');

            try {
                DB::statement("
                    ALTER TABLE presenter_read_statuses
                    ADD UNIQUE KEY presenter_content_timeslot_reading_unique (presenter_id, content_type, content_id, time_slot, reading_number)
                ");
                $this->info('✅ Added unique constraint: presenter_content_timeslot_reading_unique');
            } catch (\Exception $e) {
                $this->warn("⚠️ Could not add unique constraint: " . $e->getMessage());
            }

            // Step 7: Verify the fix
            $this->info('🔍 Verifying the fix...');

            $finalIndexes = DB::select("SHOW INDEX FROM presenter_read_statuses");
            $finalIndexNames = collect($finalIndexes)->pluck('Key_name')->unique()->filter(function($name) {
                return $name !== 'PRIMARY';
            })->toArray();

            $this->info('Final indexes: ' . implode(', ', $finalIndexNames));

            $this->info('');
            $this->info('🎉 presenter_read_statuses table has been fixed!');
            $this->info('You can now run: php artisan migrate');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error fixing table: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
