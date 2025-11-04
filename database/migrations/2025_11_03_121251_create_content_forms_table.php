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
        Schema::create('content_forms', function (Blueprint $table) {
            $table->id();

            // Content Reference
            $table->string('content_type'); // 'advertisement' or 'gong'
            $table->unsignedBigInteger('content_id');

            // Content Metadata
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable(); // Advertisement title or Gong departed name
            $table->text('content_summary')->nullable(); // First 500 chars of content
            $table->integer('word_count')->default(0); // Total words in content

            // Source Information
            $table->enum('source', ['mail', 'hand'])->default('hand'); // How content was received
            $table->timestamp('received_date')->nullable(); // When content was received

            // Financial Information
            $table->decimal('amount', 15, 2)->default(0); // Amount from advertisement/gong
            $table->boolean('is_paid')->default(false);

            // Broadcast Information
            $table->json('band')->nullable(); // AM, FM, etc.
            $table->date('broadcast_start_date')->nullable();
            $table->date('broadcast_end_date')->nullable();
            $table->json('broadcast_days')->nullable();

            // Presenter Shift Information
            $table->integer('morning_frequency')->default(0);
            $table->integer('lunch_frequency')->default(0);
            $table->integer('evening_frequency')->default(0);

            // Tick/Untick Tracking
            $table->timestamp('morning_ticked_at')->nullable(); // When presenter ticked during morning
            $table->timestamp('lunch_ticked_at')->nullable(); // When presenter ticked during lunch
            $table->timestamp('evening_ticked_at')->nullable(); // When presenter ticked during evening

            $table->integer('morning_tick_count')->default(0); // Number of times ticked in morning
            $table->integer('lunch_tick_count')->default(0); // Number of times ticked in lunch
            $table->integer('evening_tick_count')->default(0); // Number of times ticked in evening

            // Presenter Information
            $table->foreignId('presenter_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('presenter_shift')->nullable(); // morning, lunch, evening

            // Status
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['content_type', 'content_id']);
            $table->index(['presenter_id', 'presenter_shift']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_forms');
    }
};
