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
        // Make frequencies nullable in advertisements table
        Schema::table('advertisements', function (Blueprint $table) {
            $table->integer('morning_frequency')->nullable()->change();
            $table->integer('lunch_frequency')->nullable()->change();
            $table->integer('evening_frequency')->nullable()->change();
        });

        // Make frequencies nullable in gongs table
        Schema::table('gongs', function (Blueprint $table) {
            $table->integer('morning_frequency')->nullable()->change();
            $table->integer('lunch_frequency')->nullable()->change();
            $table->integer('evening_frequency')->nullable()->change();
        });

        // Make frequencies nullable in content_forms table
        Schema::table('content_forms', function (Blueprint $table) {
            $table->integer('morning_frequency')->nullable()->change();
            $table->integer('lunch_frequency')->nullable()->change();
            $table->integer('evening_frequency')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert frequencies to not nullable in advertisements table
        Schema::table('advertisements', function (Blueprint $table) {
            $table->integer('morning_frequency')->default(0)->change();
            $table->integer('lunch_frequency')->default(0)->change();
            $table->integer('evening_frequency')->default(0)->change();
        });

        // Revert frequencies to not nullable in gongs table
        Schema::table('gongs', function (Blueprint $table) {
            $table->integer('morning_frequency')->default(0)->change();
            $table->integer('lunch_frequency')->default(0)->change();
            $table->integer('evening_frequency')->default(0)->change();
        });

        // Revert frequencies to not nullable in content_forms table
        Schema::table('content_forms', function (Blueprint $table) {
            $table->integer('morning_frequency')->default(0)->change();
            $table->integer('lunch_frequency')->default(0)->change();
            $table->integer('evening_frequency')->default(0)->change();
        });
    }
};

