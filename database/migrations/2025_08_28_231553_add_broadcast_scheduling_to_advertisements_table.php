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
        Schema::table('advertisements', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('advertisements', 'broadcast_start_date')) {
                $table->date('broadcast_start_date')->nullable()->after('issued_date');
            }
            if (!Schema::hasColumn('advertisements', 'broadcast_end_date')) {
                $table->date('broadcast_end_date')->nullable()->after('broadcast_start_date');
            }
            if (!Schema::hasColumn('advertisements', 'broadcast_times')) {
                $table->json('broadcast_times')->nullable()->after('broadcast_end_date');
            }
            if (!Schema::hasColumn('advertisements', 'daily_frequency')) {
                $table->integer('daily_frequency')->default(1)->after('broadcast_times');
            }
            if (!Schema::hasColumn('advertisements', 'broadcast_days')) {
                $table->json('broadcast_days')->nullable()->after('daily_frequency');
            }
            if (!Schema::hasColumn('advertisements', 'broadcast_notes')) {
                $table->text('broadcast_notes')->nullable()->after('broadcast_days');
            }
        });

        // Copy issued_date to broadcast_start_date and set default end date
        DB::table('advertisements')->update([
            'broadcast_start_date' => DB::raw('issued_date'),
            'broadcast_end_date' => DB::raw('DATE_ADD(issued_date, INTERVAL 7 DAY)'),
            'broadcast_times' => json_encode(['Morning']),
            'broadcast_days' => json_encode(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->dropColumn([
                'broadcast_start_date',
                'broadcast_end_date',
                'broadcast_times',
                'daily_frequency',
                'broadcast_days',
                'broadcast_notes'
            ]);
        });
    }
};
