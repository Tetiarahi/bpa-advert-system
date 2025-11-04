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
        // Add is_read field to advertisements table
        Schema::table('advertisements', function (Blueprint $table) {
            if (!Schema::hasColumn('advertisements', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('is_paid');
            }
        });

        // Add is_read field to gongs table
        Schema::table('gongs', function (Blueprint $table) {
            if (!Schema::hasColumn('gongs', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('is_paid');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            if (Schema::hasColumn('advertisements', 'is_read')) {
                $table->dropColumn('is_read');
            }
        });

        Schema::table('gongs', function (Blueprint $table) {
            if (Schema::hasColumn('gongs', 'is_read')) {
                $table->dropColumn('is_read');
            }
        });
    }
};

