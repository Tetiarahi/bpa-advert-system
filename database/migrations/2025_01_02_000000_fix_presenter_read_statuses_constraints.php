<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, let's check what constraints and indexes exist
        $this->info('Checking existing constraints and indexes...');
        
        // Get all indexes on the table
        $indexes = DB::select("SHOW INDEX FROM presenter_read_statuses");
        $indexNames = collect($indexes)->pluck('Key_name')->unique()->toArray();
        
        $this->info('Existing indexes: ' . implode(', ', $indexNames));
        
        // Check for foreign key constraints
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'presenter_read_statuses' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        foreach ($foreignKeys as $fk) {
            $this->info("Foreign key: {$fk->CONSTRAINT_NAME} on {$fk->COLUMN_NAME} -> {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}");
        }
        
        // Drop all unique constraints that might exist (in the correct order)
        $constraintsToRemove = [
            'presenter_content_timeslot_reading_unique',
            'presenter_content_timeslot_unique', 
            'presenter_content_unique'
        ];
        
        foreach ($constraintsToRemove as $constraint) {
            if (in_array($constraint, $indexNames)) {
                try {
                    Schema::table('presenter_read_statuses', function (Blueprint $table) use ($constraint) {
                        $table->dropUnique($constraint);
                    });
                    $this->info("Dropped constraint: {$constraint}");
                } catch (\Exception $e) {
                    $this->info("Could not drop constraint {$constraint}: " . $e->getMessage());
                }
            }
        }
        
        // Ensure all required columns exist
        if (!Schema::hasColumn('presenter_read_statuses', 'time_slot')) {
            Schema::table('presenter_read_statuses', function (Blueprint $table) {
                $table->enum('time_slot', ['morning', 'lunch', 'evening'])->after('content_id');
            });
            $this->info('Added time_slot column');
        }
        
        if (!Schema::hasColumn('presenter_read_statuses', 'reading_number')) {
            Schema::table('presenter_read_statuses', function (Blueprint $table) {
                $table->tinyInteger('reading_number')->default(1)->after('time_slot');
            });
            $this->info('Added reading_number column');
        }
        
        if (!Schema::hasColumn('presenter_read_statuses', 'readings_data')) {
            Schema::table('presenter_read_statuses', function (Blueprint $table) {
                $table->json('readings_data')->nullable()->after('reading_number');
            });
            $this->info('Added readings_data column');
        }
        
        // Clean up any duplicate data before adding the unique constraint
        $this->info('Cleaning up duplicate data...');
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
        
        // Add the final unique constraint
        try {
            Schema::table('presenter_read_statuses', function (Blueprint $table) {
                $table->unique(['presenter_id', 'content_type', 'content_id', 'time_slot', 'reading_number'], 'presenter_content_timeslot_reading_unique');
            });
            $this->info('Added final unique constraint');
        } catch (\Exception $e) {
            $this->info('Could not add unique constraint: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the unique constraint
        Schema::table('presenter_read_statuses', function (Blueprint $table) {
            $table->dropUnique('presenter_content_timeslot_reading_unique');
        });
    }
    
    private function info($message)
    {
        if (app()->runningInConsole()) {
            echo $message . "\n";
        }
    }
};
