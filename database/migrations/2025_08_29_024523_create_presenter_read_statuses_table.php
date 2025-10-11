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
        Schema::create('presenter_read_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presenter_id')->constrained()->cascadeOnDelete();
            $table->string('content_type'); // 'advertisement' or 'gong'
            $table->unsignedBigInteger('content_id');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Unique constraint will be added in separate migration
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presenter_read_statuses');
    }
};
