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
        Schema::table('advertisements', function (Blueprint $table) {
            // Add time-specific frequency fields
            $table->integer('morning_frequency')->default(0)->after('daily_frequency'); // 6AM-8AM
            $table->integer('lunch_frequency')->default(0)->after('morning_frequency'); // 12PM-2PM
            $table->integer('evening_frequency')->default(0)->after('lunch_frequency'); // 5PM-9:30PM
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->dropColumn([
                'morning_frequency',
                'lunch_frequency',
                'evening_frequency'
            ]);
        });
    }
};
