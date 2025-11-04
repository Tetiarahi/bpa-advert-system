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
        if (!Schema::hasColumn('presenter_read_statuses', 'time_slot')) {
            Schema::table('presenter_read_statuses', function (Blueprint $table) {
                // Add time slot column to track read status per time slot
                $table->enum('time_slot', ['morning', 'lunch', 'evening'])->after('content_id');
            });

            // Drop the old unique constraint in a separate statement
            Schema::table('presenter_read_statuses', function (Blueprint $table) {
                if (Schema::hasColumn('presenter_read_statuses', 'time_slot')) {
                    try {
                        $table->dropUnique('presenter_content_unique');
                    } catch (\Exception) {
                        // Constraint might not exist
                    }
                }
            });

            // Add new unique constraint including time_slot
            Schema::table('presenter_read_statuses', function (Blueprint $table) {
                $table->unique(['presenter_id', 'content_type', 'content_id', 'time_slot'], 'presenter_content_timeslot_unique');
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
            $table->dropUnique('presenter_content_timeslot_unique');
        });

        // Remove time slot column
        Schema::table('presenter_read_statuses', function (Blueprint $table) {
            $table->dropColumn('time_slot');
        });

        // Restore old unique constraint
        Schema::table('presenter_read_statuses', function (Blueprint $table) {
            $table->unique(['presenter_id', 'content_type', 'content_id'], 'presenter_content_unique');
        });
    }
};
