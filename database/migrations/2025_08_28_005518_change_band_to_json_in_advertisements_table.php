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
        // First, convert existing data to JSON format
        $advertisements = DB::table('advertisements')->get();

        // Add temporary column
        Schema::table('advertisements', function (Blueprint $table) {
            $table->json('band_new')->nullable()->after('band');
        });

        // Convert existing data
        foreach ($advertisements as $ad) {
            $bandArray = [$ad->band]; // Convert single value to array
            DB::table('advertisements')
                ->where('id', $ad->id)
                ->update(['band_new' => json_encode($bandArray)]);
        }

        // Drop old column and rename new one
        Schema::table('advertisements', function (Blueprint $table) {
            $table->dropColumn('band');
        });

        Schema::table('advertisements', function (Blueprint $table) {
            $table->renameColumn('band_new', 'band');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert back to enum (take first value from JSON array)
        $advertisements = DB::table('advertisements')->get();

        // Add temporary enum column
        Schema::table('advertisements', function (Blueprint $table) {
            $table->enum('band_old', ['AM', 'FM', 'Uekera', 'AM-FM-Uekera'])->default('AM')->after('band');
        });

        // Convert JSON back to single value
        foreach ($advertisements as $ad) {
            $bandArray = json_decode($ad->band, true);
            $firstBand = is_array($bandArray) && !empty($bandArray) ? $bandArray[0] : 'AM';
            DB::table('advertisements')
                ->where('id', $ad->id)
                ->update(['band_old' => $firstBand]);
        }

        // Drop JSON column and rename enum column
        Schema::table('advertisements', function (Blueprint $table) {
            $table->dropColumn('band');
        });

        Schema::table('advertisements', function (Blueprint $table) {
            $table->renameColumn('band_old', 'band');
        });
    }
};
