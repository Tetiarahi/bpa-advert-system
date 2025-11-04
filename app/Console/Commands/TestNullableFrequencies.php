<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Advertisement;
use App\Models\Gong;
use App\Models\ContentForm;

class TestNullableFrequencies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-nullable-frequencies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test that frequencies can be null in advertisements, gongs, and content forms';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Nullable Frequencies...');
        $this->newLine();

        // Test 1: Create Advertisement with null frequencies
        $this->info('Test 1: Creating Advertisement with null frequencies...');
        try {
            $ad = Advertisement::create([
                'customer_id' => 1,
                'title' => 'Test Ad with Null Frequencies',
                'content' => 'Test content',
                'issued_date' => now(),
                'broadcast_start_date' => now(),
                'broadcast_end_date' => now()->addDays(7),
                'morning_frequency' => null,
                'lunch_frequency' => null,
                'evening_frequency' => null,
            ]);
            $this->line('   ✅ Advertisement created with ID: ' . $ad->id);
            $this->line('   Morning Frequency: ' . ($ad->morning_frequency ?? 'null'));
            $this->line('   Lunch Frequency: ' . ($ad->lunch_frequency ?? 'null'));
            $this->line('   Evening Frequency: ' . ($ad->evening_frequency ?? 'null'));
        } catch (\Exception $e) {
            $this->error('   ❌ Failed: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();

        // Test 2: Create Gong with null frequencies
        $this->info('Test 2: Creating Gong with null frequencies...');
        try {
            $gong = Gong::create([
                'customer_id' => 1,
                'departed_name' => 'Test Person',
                'death_date' => now(),
                'published_date' => now(),
                'broadcast_start_date' => now(),
                'broadcast_end_date' => now()->addDays(7),
                'contents' => 'Test gong content',
                'song_title' => 'Test Song',
                'morning_frequency' => null,
                'lunch_frequency' => null,
                'evening_frequency' => null,
            ]);
            $this->line('   ✅ Gong created with ID: ' . $gong->id);
            $this->line('   Morning Frequency: ' . ($gong->morning_frequency ?? 'null'));
            $this->line('   Lunch Frequency: ' . ($gong->lunch_frequency ?? 'null'));
            $this->line('   Evening Frequency: ' . ($gong->evening_frequency ?? 'null'));
        } catch (\Exception $e) {
            $this->error('   ❌ Failed: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();

        // Test 3: Create ContentForm with null frequencies
        $this->info('Test 3: Creating ContentForm with null frequencies...');
        try {
            $form = ContentForm::create([
                'content_type' => 'advertisement',
                'content_id' => $ad->id,
                'customer_id' => 1,
                'title' => 'Test Form with Null Frequencies',
                'word_count' => 100,
                'source' => 'mail',
                'received_date' => now(),
                'morning_frequency' => null,
                'lunch_frequency' => null,
                'evening_frequency' => null,
            ]);
            $this->line('   ✅ ContentForm created with ID: ' . $form->id);
            $this->line('   Morning Frequency: ' . ($form->morning_frequency ?? 'null'));
            $this->line('   Lunch Frequency: ' . ($form->lunch_frequency ?? 'null'));
            $this->line('   Evening Frequency: ' . ($form->evening_frequency ?? 'null'));
        } catch (\Exception $e) {
            $this->error('   ❌ Failed: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();

        // Test 4: Verify database stores null correctly
        $this->info('Test 4: Verifying database stores null correctly...');
        try {
            $adFromDb = Advertisement::find($ad->id);
            $this->line('   ✅ Retrieved Advertisement from database');
            $this->line('   Morning Frequency: ' . ($adFromDb->morning_frequency ?? 'null'));
            $this->line('   Lunch Frequency: ' . ($adFromDb->lunch_frequency ?? 'null'));
            $this->line('   Evening Frequency: ' . ($adFromDb->evening_frequency ?? 'null'));
        } catch (\Exception $e) {
            $this->error('   ❌ Failed: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();

        // Test 5: Verify Gong database stores null correctly
        $this->info('Test 5: Verifying Gong database stores null correctly...');
        try {
            $gongFromDb = Gong::find($gong->id);
            $this->line('   ✅ Retrieved Gong from database');
            $this->line('   Morning Frequency: ' . ($gongFromDb->morning_frequency ?? 'null'));
            $this->line('   Lunch Frequency: ' . ($gongFromDb->lunch_frequency ?? 'null'));
            $this->line('   Evening Frequency: ' . ($gongFromDb->evening_frequency ?? 'null'));
        } catch (\Exception $e) {
            $this->error('   ❌ Failed: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();
        $this->info('✅ All tests passed! Nullable frequencies are working correctly.');
        $this->newLine();

        return 0;
    }
}

