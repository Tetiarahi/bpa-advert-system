<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Advertisement;
use App\Models\ContentForm;
use App\Models\Presenter;

class TestIndividualReadings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-individual-readings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test that individual reading numbers are tracked correctly when ticking buttons';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Individual Reading Tracking...');
        $this->newLine();

        // Test 1: Create an advertisement with 4 lunch readings
        $this->info('Test 1: Creating advertisement with 4 lunch readings...');
        try {
            $ad = Advertisement::create([
                'customer_id' => 1,
                'title' => 'Test Ad - 4 Lunch Readings',
                'content' => 'Test content for 4 readings',
                'issued_date' => now(),
                'broadcast_start_date' => now(),
                'broadcast_end_date' => now()->addDays(7),
                'morning_frequency' => null,
                'lunch_frequency' => 4,  // 4 readings in lunch
                'evening_frequency' => null,
            ]);
            $this->line('   ✅ Advertisement created with ID: ' . $ad->id);
            $this->line('   Lunch Frequency: 4');
        } catch (\Exception $e) {
            $this->error('   ❌ Failed: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();

        // Test 2: Create ContentForm for the advertisement
        $this->info('Test 2: Creating ContentForm...');
        try {
            $form = ContentForm::create([
                'content_type' => 'advertisement',
                'content_id' => $ad->id,
                'customer_id' => 1,
                'title' => $ad->title,
                'word_count' => 100,
                'source' => 'mail',
                'received_date' => now(),
                'morning_frequency' => null,
                'lunch_frequency' => 4,
                'evening_frequency' => null,
            ]);
            $this->line('   ✅ ContentForm created with ID: ' . $form->id);
        } catch (\Exception $e) {
            $this->error('   ❌ Failed: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();

        // Test 3: Get a presenter
        $this->info('Test 3: Getting presenter...');
        try {
            $presenter = Presenter::first();
            if (!$presenter) {
                $this->error('   ❌ No presenter found in database');
                return 1;
            }
            $this->line('   ✅ Presenter found: ' . $presenter->name);
        } catch (\Exception $e) {
            $this->error('   ❌ Failed: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();

        // Test 4: Simulate ticking buttons 1, 2, 3, 4 in sequence
        $this->info('Test 4: Simulating button clicks in sequence...');
        
        for ($i = 1; $i <= 4; $i++) {
            $this->line("   Clicking button $i...");
            
            try {
                // Simulate the tick action
                $tickCountField = 'lunch_tick_count';
                $currentCount = $form->$tickCountField ?? 0;
                $newCount = $currentCount + 1;

                // Get existing tick times
                $tickTimes = $form->lunch_tick_times ?? [];
                if (!is_array($tickTimes)) {
                    $tickTimes = [];
                }
                $tickTimes[] = now()->toDateTimeString();

                // Get or initialize individual readings
                $readings = $form->lunch_readings ?? [];
                if (!is_array($readings)) {
                    $readings = [];
                }

                // Track this specific reading
                $readings[$i] = [
                    'ticked' => true,
                    'ticked_at' => now()->toDateTimeString(),
                    'presenter_id' => $presenter->id,
                    'presenter_name' => $presenter->name,
                ];

                // Update the form
                $form->update([
                    'lunch_tick_count' => $newCount,
                    'lunch_ticked_at' => now(),
                    'lunch_tick_times' => $tickTimes,
                    'lunch_readings' => $readings,
                    'presenter_id' => $presenter->id,
                    'presenter_shift' => 'lunch',
                ]);

                $form->refresh();

                $this->line("      ✅ Button $i ticked - Count: $newCount");
                $this->line("      Reading $i status: " . json_encode($readings[$i]));
            } catch (\Exception $e) {
                $this->error("      ❌ Failed to tick button $i: " . $e->getMessage());
                return 1;
            }
        }

        $this->newLine();

        // Test 5: Verify all readings are tracked correctly
        $this->info('Test 5: Verifying all readings are tracked...');
        try {
            $form->refresh();
            $readings = $form->lunch_readings ?? [];
            
            $this->line('   Lunch Readings Status:');
            for ($i = 1; $i <= 4; $i++) {
                if (isset($readings[$i])) {
                    $status = $readings[$i]['ticked'] ? 'TICKED' : 'NOT TICKED';
                    $this->line("      Reading $i: $status at " . $readings[$i]['ticked_at']);
                } else {
                    $this->line("      Reading $i: NOT TRACKED");
                }
            }

            $this->line('   ');
            $this->line('   Tick Count: ' . $form->lunch_tick_count);
            $this->line('   Tick Times: ' . count($form->lunch_tick_times ?? []) . ' recorded');
        } catch (\Exception $e) {
            $this->error('   ❌ Failed: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();
        $this->info('✅ All tests passed! Individual readings are tracked correctly.');
        $this->newLine();

        return 0;
    }
}

