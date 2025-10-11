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
            $table->unique(['presenter_id', 'content_type', 'content_id'], 'presenter_content_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presenter_read_statuses', function (Blueprint $table) {
            $table->dropUnique('presenter_content_unique');
        });
    }
};
