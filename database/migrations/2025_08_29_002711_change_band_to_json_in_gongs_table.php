<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, add a temporary column
        Schema::table('gongs', function (Blueprint $table) {
            $table->json('band_new')->nullable()->after('band');
        });

        // Update existing records to use JSON format in the new column
        $gongs = DB::table('gongs')->get();
        foreach ($gongs as $gong) {
            DB::table('gongs')
                ->where('id', $gong->id)
                ->update(['band_new' => json_encode([$gong->band])]);
        }

        // Drop the old column and rename the new one
        Schema::table('gongs', function (Blueprint $table) {
            $table->dropColumn('band');
        });

        Schema::table('gongs', function (Blueprint $table) {
            $table->renameColumn('band_new', 'band');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gongs', function (Blueprint $table) {
            // Revert back to enum (this is a simplified revert)
            $table->string('band')->change();
        });
    }
};
