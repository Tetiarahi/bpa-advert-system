<?php

namespace App\Console\Commands;

use App\Models\ContentForm;
use App\Models\Presenter;
use Illuminate\Console\Command;

class TestSimulateTick extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-simulate-tick';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate a tick action to test tick times recording';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Simulating Tick Action...');
        $this->newLine();

        try {
            // Get a content form
            $form = ContentForm::first();
            if (!$form) {
                $this->error('❌ No content forms found');
                return 1;
            }

            // Get a presenter
            $presenter = Presenter::first();
            if (!$presenter) {
                $this->error('❌ No presenters found');
                return 1;
            }

            $this->info('📋 Before Tick:');
            $this->info("   Form ID: {$form->id}");
            $this->info("   Morning tick count: {$form->morning_tick_count}");
            $this->info("   Morning tick times: " . json_encode($form->morning_tick_times));
            $this->newLine();

            // Simulate a tick
            $timeSlot = 'morning';
            $tickCountField = $timeSlot . '_tick_count';
            $currentCount = $form->$tickCountField ?? 0;
            $newCount = $currentCount + 1;

            // Get existing tick times array
            $tickTimesField = $timeSlot . '_tick_times';
            $existingTickTimes = $form->$tickTimesField ?? [];
            if (!is_array($existingTickTimes)) {
                $existingTickTimes = [];
            }

            $this->info('📝 Existing tick times before adding:');
            $this->line("   Type: " . gettype($existingTickTimes));
            $this->line("   Count: " . count($existingTickTimes));
            $this->line("   Data: " . json_encode($existingTickTimes));
            $this->newLine();

            // Add new tick time to array
            $newTickTime = now()->toDateTimeString();
            $existingTickTimes[] = $newTickTime;

            $this->info('📝 Tick times after adding:');
            $this->line("   Type: " . gettype($existingTickTimes));
            $this->line("   Count: " . count($existingTickTimes));
            $this->line("   Data: " . json_encode($existingTickTimes));
            $this->newLine();

            // Update ContentForm with tick information
            $updateData = [
                $tickCountField => $newCount,
                $timeSlot . '_ticked_at' => now(),
                $tickTimesField => $existingTickTimes, // Store all tick times
                'presenter_id' => $presenter->id,
                'presenter_shift' => $timeSlot,
            ];

            $this->info('🔄 Updating form with data:');
            $this->line("   " . json_encode($updateData));
            $this->newLine();

            $form->update($updateData);

            // Refresh and check
            $form->refresh();

            $this->info('📋 After Tick:');
            $this->info("   Form ID: {$form->id}");
            $this->info("   Morning tick count: {$form->morning_tick_count}");
            $this->info("   Morning tick times: " . json_encode($form->morning_tick_times));
            $this->info("   Presenter ID: {$form->presenter_id}");
            $this->info("   Presenter Name: {$form->presenter->name}");
            $this->newLine();

            if ($form->morning_tick_count === $newCount && is_array($form->morning_tick_times) && count($form->morning_tick_times) > 0) {
                $this->info('✅ SUCCESS! Tick times are being recorded correctly!');
                return 0;
            } else {
                $this->error('❌ FAILED! Tick times not recorded properly');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}

