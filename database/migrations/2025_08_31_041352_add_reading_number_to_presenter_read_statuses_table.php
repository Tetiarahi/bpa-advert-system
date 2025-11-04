<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if column already exists
        if (!Schema::hasColumn('presenter_read_statuses', 'reading_number')) {
            // First, drop the existing unique constraint
            Schema::table('presenter_read_statuses', function (Blueprint $table) {
                try {
                    $table->dropUnique('presenter_content_timeslot_unique');
                } catch (\Exception) {
                    // Constraint might not exist
                }
            });

            // Add the reading_number field
            Schema::table('presenter_read_statuses', function (Blueprint $table) {
                $table->tinyInteger('reading_number')->default(1)->after('time_slot');
            });

            // Add new unique constraint that includes reading_number
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

        // Remove the reading_number column
        Schema::table('presenter_read_statuses', function (Blueprint $table) {
            $table->dropColumn('reading_number');
        });

        // Restore the old unique constraint
        Schema::table('presenter_read_statuses', function (Blueprint $table) {
            $table->unique(['presenter_id', 'content_type', 'content_id', 'time_slot'], 'presenter_content_timeslot_unique');
        });
    }
};
