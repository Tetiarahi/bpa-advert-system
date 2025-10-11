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
        Schema::table('gongs', function (Blueprint $table) {
            // Add broadcast date fields
            $table->date('broadcast_start_date')->nullable()->after('published_date');
            $table->date('broadcast_end_date')->nullable()->after('broadcast_start_date');

            // Add time-specific frequency fields
            $table->integer('morning_frequency')->default(0)->after('broadcast_end_date'); // 6AM-8AM
            $table->integer('lunch_frequency')->default(0)->after('morning_frequency'); // 12PM-2PM
            $table->integer('evening_frequency')->default(0)->after('lunch_frequency'); // 5PM-9:30PM

            // Add broadcast scheduling fields
            $table->json('broadcast_days')->nullable()->after('evening_frequency'); // Days of the week
            $table->text('broadcast_notes')->nullable()->after('broadcast_days'); // Special instructions
        });

        // Copy published_date to broadcast_start_date and set default end date for existing records
        DB::table('gongs')->update([
            'broadcast_start_date' => DB::raw('published_date'),
            'broadcast_end_date' => DB::raw('published_date'),
            'morning_frequency' => 1,
            'broadcast_days' => json_encode(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gongs', function (Blueprint $table) {
            $table->dropColumn([
                'broadcast_start_date',
                'broadcast_end_date',
                'morning_frequency',
                'lunch_frequency',
                'evening_frequency',
                'broadcast_days',
                'broadcast_notes'
            ]);
        });
    }
};
