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
        // Check if time_slot column exists, if not add it
        if (!Schema::hasColumn('presenter_read_statuses', 'time_slot')) {
            Schema::table('presenter_read_statuses', function (Blueprint $table) {
                $table->enum('time_slot', ['morning', 'lunch', 'evening'])->after('content_id');
            });
        }

        // Check if reading_number column exists, if not add it
        if (!Schema::hasColumn('presenter_read_statuses', 'reading_number')) {
            Schema::table('presenter_read_statuses', function (Blueprint $table) {
                $table->tinyInteger('reading_number')->default(1)->after('time_slot');
            });
        }

        // Check if the old unique constraint exists and drop it
        $indexes = DB::select("SHOW INDEX FROM presenter_read_statuses WHERE Key_name = 'presenter_content_timeslot_unique'");
        if (!empty($indexes)) {
            Schema::table('presenter_read_statuses', function (Blueprint $table) {
                $table->dropUnique('presenter_content_timeslot_unique');
            });
        }

        // Check if the new unique constraint exists, if not add it
        $newIndexes = DB::select("SHOW INDEX FROM presenter_read_statuses WHERE Key_name = 'presenter_content_timeslot_reading_unique'");
        if (empty($newIndexes)) {
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
        // Drop the new unique constraint if it exists
        $newIndexes = DB::select("SHOW INDEX FROM presenter_read_statuses WHERE Key_name = 'presenter_content_timeslot_reading_unique'");
        if (!empty($newIndexes)) {
            Schema::table('presenter_read_statuses', function (Blueprint $table) {
                $table->dropUnique('presenter_content_timeslot_reading_unique');
            });
        }

        // Remove the reading_number column if it exists
        if (Schema::hasColumn('presenter_read_statuses', 'reading_number')) {
            Schema::table('presenter_read_statuses', function (Blueprint $table) {
                $table->dropColumn('reading_number');
            });
        }

        // Restore the old unique constraint if it doesn't exist
        $oldIndexes = DB::select("SHOW INDEX FROM presenter_read_statuses WHERE Key_name = 'presenter_content_timeslot_unique'");
        if (empty($oldIndexes)) {
            Schema::table('presenter_read_statuses', function (Blueprint $table) {
                $table->unique(['presenter_id', 'content_type', 'content_id', 'time_slot'], 'presenter_content_timeslot_unique');
            });
        }
    }
};
