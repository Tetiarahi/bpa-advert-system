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
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->enum('customer_type', ['Private', 'local_business', 'GOK_NGO'])->nullable();
            $table->foreignId('ads_category_id')->nullable()->constrained('ads_categories')->onDelete('set null');
            $table->enum('band', ['AM', 'FM', 'Uekera', 'AM-FM-Uekera'])->default('AM');
            $table->string('title');
            $table->longText('content');// Assuming 'content' is the field for advertisement details
            $table->date('issued_date'); // Assuming 'issued_date' is the field for the date of the advertisement
            $table->boolean('is_paid')->default(false);
            $table->decimal('amount', 15, 2)->nullable(); // Assuming 'amount' is the field for the advertisement cost
            $table->string('attachment')->nullable(); // Assuming 'attachment' is the field for any file associated with the advertisement
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
