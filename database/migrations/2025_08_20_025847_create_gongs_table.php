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
        Schema::create('gongs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string(column: 'departed_name');
            $table->string('death_date')->nullable(); // Add death date column
            $table->string('published_date'); // Add death place column
            $table->enum('band', ['AM', 'FM', 'Both'])->default('AM');
            $table->string('contents')->nullable();
            $table->string('song_title');
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('attachment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gongs');
    }
};
