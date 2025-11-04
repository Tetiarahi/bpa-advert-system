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
        // First, we need to remove any duplicate entries that might exist
        // Keep only the first entry for each presenter/content/timeslot combination
        try {
            DB::statement("
                DELETE t1 FROM presenter_read_statuses t1
                INNER JOIN presenter_read_statuses t2
                WHERE t1.id > t2.id
                AND t1.presenter_id = t2.presenter_id
                AND t1.content_type = t2.content_type
                AND t1.content_id = t2.content_id
                AND t1.time_slot = t2.time_slot
            ");
        } catch (\Exception) {
            // No duplicates to delete
        }

        // Drop the old unique constraint if it exists
        try {
            Schema::table('presenter_read_statuses', function (Blueprint $table) {
                $table->dropUnique('presenter_content_timeslot_unique');
            });
        } catch (\Exception) {
            // Constraint might not exist
        }

        // Add the new unique constraint that includes reading_number if it doesn't exist
        $indexes = DB::select("SHOW INDEX FROM presenter_read_statuses WHERE Key_name = 'presenter_content_timeslot_reading_unique'");
        if (empty($indexes)) {
            Schema::table('presenter_read_statuses', function (Blueprint $table) {
                $table->unique(['presenter_id', 'content_type', 'content_id', 'time_slot', 'reading_number'], 'presenter_content_timeslot_reading_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new unique constraint
        Schema::table('presenter_read_statuses', function (Blueprint $table) {
            $table->dropUnique('presenter_content_timeslot_reading_unique');
        });

        // Restore the old unique constraint
        Schema::table('presenter_read_statuses', function (Blueprint $table) {
            $table->unique(['presenter_id', 'content_type', 'content_id', 'time_slot'], 'presenter_content_timeslot_unique');
        });
    }
};
