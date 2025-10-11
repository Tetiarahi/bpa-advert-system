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
        Schema::table('gongs', function (Blueprint $table) {
            // Add specific broadcast times for each time slot
            $table->json('morning_times')->nullable()->after('morning_frequency'); // e.g., ["05:30", "06:30", "07:30", "08:30"]
            $table->json('lunch_times')->nullable()->after('lunch_frequency');     // e.g., ["11:30", "12:30", "13:30", "14:30"]
            $table->json('evening_times')->nullable()->after('evening_frequency'); // e.g., ["16:30", "17:30", "18:30", "19:30"]
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gongs', function (Blueprint $table) {
            $table->dropColumn([
                'morning_times',
                'lunch_times',
                'evening_times'
            ]);
        });
    }
};
