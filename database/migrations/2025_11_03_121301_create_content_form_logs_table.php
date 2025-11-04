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
        Schema::create('content_form_logs', function (Blueprint $table) {
            $table->id();

            // References
            $table->foreignId('content_form_id')->constrained()->cascadeOnDelete();
            $table->foreignId('presenter_id')->constrained()->cascadeOnDelete();

            // Action Information
            $table->enum('action', ['tick', 'untick']); // Action performed
            $table->enum('time_slot', ['morning', 'lunch', 'evening']); // Which shift

            // Timestamp Information
            $table->timestamp('action_at'); // When the action was performed
            $table->string('ip_address')->nullable(); // Presenter's IP address
            $table->string('user_agent')->nullable(); // Browser/device info

            // Additional Context
            $table->integer('reading_number')->nullable(); // Which reading number (1st, 2nd, etc.)
            $table->text('notes')->nullable(); // Any additional notes

            $table->timestamps();

            // Indexes
            $table->index(['content_form_id', 'presenter_id']);
            $table->index(['time_slot', 'action_at']);
            $table->index('presenter_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_form_logs');
    }
};
