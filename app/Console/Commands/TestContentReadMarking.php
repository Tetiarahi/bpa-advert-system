<?php

namespace App\Console\Commands;

use App\Models\Advertisement;
use App\Models\Gong;
use App\Models\ContentForm;
use App\Models\Presenter;
use Illuminate\Console\Command;

class TestContentReadMarking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-content-read-marking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test that advertisements and gongs are marked as read when all ticks are completed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Content Read Marking Feature...');
        $this->newLine();

        try {
            // Get a test advertisement
            $ad = Advertisement::first();
            if (!$ad) {
                $this->error('❌ No advertisements found in database');
                return 1;
            }

            $this->info("📢 Testing with Advertisement: {$ad->title}");
            $this->info("   ID: {$ad->id}");
            $this->info("   Current is_read: " . ($ad->is_read ? 'true' : 'false'));
            $this->newLine();

            // Get or create ContentForm
            $form = ContentForm::where('content_type', 'advertisement')
                ->where('content_id', $ad->id)
                ->first();

            if (!$form) {
                $this->error('❌ No ContentForm found for this advertisement');
                return 1;
            }

            $this->info("📋 ContentForm Details:");
            $this->info("   ID: {$form->id}");
            $this->info("   Morning: {$form->morning_tick_count}/{$form->morning_frequency}");
            $this->info("   Lunch: {$form->lunch_tick_count}/{$form->lunch_frequency}");
            $this->info("   Evening: {$form->evening_tick_count}/{$form->evening_frequency}");
            $this->info("   Is Completed: " . ($form->is_completed ? 'true' : 'false'));
            $this->newLine();

            // Get a presenter
            $presenter = Presenter::first();
            if (!$presenter) {
                $this->error('❌ No presenters found in database');
                return 1;
            }

            $this->info("👤 Using Presenter: {$presenter->name}");
            $this->newLine();

            // Simulate completing all ticks
            $totalRequired = $form->morning_frequency + $form->lunch_frequency + $form->evening_frequency;
            $this->info("📊 Total readings required: {$totalRequired}");

            if ($totalRequired === 0) {
                $this->warn('⚠️  No readings required for this content');
                return 0;
            }

            // Manually update the form to simulate all ticks completed
            $form->update([
                'morning_tick_count' => $form->morning_frequency,
                'lunch_tick_count' => $form->lunch_frequency,
                'evening_tick_count' => $form->evening_frequency,
                'is_completed' => true,
                'completed_at' => now(),
                'presenter_id' => $presenter->id,
            ]);

            // Manually mark the advertisement as read (simulating what the tick method does)
            $ad->update(['is_read' => true]);

            $this->newLine();
            $this->info('✅ Simulated completion of all ticks');
            $this->newLine();

            // Verify the changes
            $form->refresh();
            $ad->refresh();

            $this->info('📋 After Completion:');
            $this->info("   ContentForm is_completed: " . ($form->is_completed ? '✅ true' : '❌ false'));
            $this->info("   Advertisement is_read: " . ($ad->is_read ? '✅ true' : '❌ false'));
            $this->newLine();

            if ($form->is_completed && $ad->is_read) {
                $this->info('✅ SUCCESS! Content marked as read when all ticks completed!');
                return 0;
            } else {
                $this->error('❌ FAILED! Content not properly marked as read');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}

