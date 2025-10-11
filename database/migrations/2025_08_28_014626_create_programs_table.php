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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->enum('customer_type', ['Private', 'local_business', 'GOK_NGO'])->nullable();
            $table->enum('radio_program', ['Nimaua Akea', 'News Sponsor', 'Karaki Sponsor', 'Live Sponsor']);
            $table->json('band'); // Support multiple bands (AM/FM)
            $table->date('publish_start_date');
            $table->date('publish_end_date');
            $table->boolean('payment_status')->default(false);
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('attachment')->nullable();
            $table->foreignId('staff_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
