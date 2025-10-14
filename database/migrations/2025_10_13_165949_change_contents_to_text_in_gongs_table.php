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
            // Change contents from string to text to allow longer memorial content
            $table->text('contents')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gongs', function (Blueprint $table) {
            // Revert back to string (this may truncate data if content is longer than 255 chars)
            $table->string('contents')->nullable()->change();
        });
    }
};
