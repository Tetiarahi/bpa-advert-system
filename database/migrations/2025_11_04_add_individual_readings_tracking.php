<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add JSON columns to track which specific reading numbers have been ticked
     * for each time slot (morning, lunch, evening).
     * 
     * Example structure:
     * morning_readings: {
     *   "1": {"ticked": true, "ticked_at": "2025-11-04 10:30:00", "presenter_id": 1},
     *   "2": {"ticked": true, "ticked_at": "2025-11-04 10:35:00", "presenter_id": 1},
     *   "3": {"ticked": false},
     *   "4": {"ticked": false}
     * }
     */
    public function up(): void
    {
        Schema::table('content_forms', function (Blueprint $table) {
            // Add JSON columns to track individual reading status for each time slot
            $table->json('morning_readings')->nullable()->after('morning_tick_times');
            $table->json('lunch_readings')->nullable()->after('lunch_tick_times');
            $table->json('evening_readings')->nullable()->after('evening_tick_times');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_forms', function (Blueprint $table) {
            $table->dropColumn(['morning_readings', 'lunch_readings', 'evening_readings']);
        });
    }
};

