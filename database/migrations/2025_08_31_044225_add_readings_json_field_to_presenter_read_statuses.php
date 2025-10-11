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
            // Add a JSON field to store multiple readings data
            $table->json('readings_data')->nullable()->after('reading_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presenter_read_statuses', function (Blueprint $table) {
            $table->dropColumn('readings_data');
        });
    }
};
