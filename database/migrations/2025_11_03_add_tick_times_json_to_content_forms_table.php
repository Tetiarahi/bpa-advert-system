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
        Schema::table('content_forms', function (Blueprint $table) {
            // Add JSON columns to store all tick times for each reading
            $table->json('morning_tick_times')->nullable()->after('morning_tick_count');
            $table->json('lunch_tick_times')->nullable()->after('lunch_tick_count');
            $table->json('evening_tick_times')->nullable()->after('evening_tick_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_forms', function (Blueprint $table) {
            $table->dropColumn(['morning_tick_times', 'lunch_tick_times', 'evening_tick_times']);
        });
    }
};

