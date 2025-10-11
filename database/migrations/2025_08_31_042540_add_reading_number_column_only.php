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
        Schema::table('presenter_read_statuses', function (Blueprint $table) {
            // Only add reading_number column if it doesn't exist
            if (!Schema::hasColumn('presenter_read_statuses', 'reading_number')) {
                $table->tinyInteger('reading_number')->default(1)->after('time_slot');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presenter_read_statuses', function (Blueprint $table) {
            if (Schema::hasColumn('presenter_read_statuses', 'reading_number')) {
                $table->dropColumn('reading_number');
            }
        });
    }
};
