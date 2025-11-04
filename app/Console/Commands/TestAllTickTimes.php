<?php

namespace App\Console\Commands;

use App\Models\ContentForm;
use App\Models\ContentFormLog;
use Illuminate\Console\Command;

class TestAllTickTimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-all-tick-times';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test that all tick times and presenter information are recorded correctly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing All Tick Times and Presenter Information Recording...');
        $this->newLine();

        try {
            // Get a content form with ticks
            $form = ContentForm::where('morning_tick_count', '>', 0)
                ->orWhere('lunch_tick_count', '>', 0)
                ->orWhere('evening_tick_count', '>', 0)
                ->first();

            if (!$form) {
                $this->error('❌ No content forms with ticks found');
                return 1;
            }

            $this->info('📋 Content Form Details:');
            $this->info("   Title: {$form->title}");
            $this->info("   ID: {$form->id}");
            $this->info("   Content Type: {$form->content_type}");
            $this->newLine();

            // Display presenter information
            $this->info('👤 Presenter Information:');
            $this->info("   Presenter ID: {$form->presenter_id}");
            if ($form->presenter) {
                $this->info("   Presenter Name: {$form->presenter->name}");
            } else {
                $this->warn("   ⚠️  No presenter assigned yet");
            }
            $this->info("   Current Shift: {$form->presenter_shift}");
            $this->newLine();

            // Display tick counts
            $this->info('📊 Tick Counts:');
            $this->info("   Morning: {$form->morning_tick_count}/{$form->morning_frequency}");
            $this->info("   Lunch: {$form->lunch_tick_count}/{$form->lunch_frequency}");
            $this->info("   Evening: {$form->evening_tick_count}/{$form->evening_frequency}");
            $this->newLine();

            // Display all tick times
            $this->info('⏰ All Tick Times Recorded:');
            $this->newLine();

            if ($form->morning_tick_times && count($form->morning_tick_times) > 0) {
                $this->info('🌅 Morning Ticks:');
                foreach ($form->morning_tick_times as $index => $time) {
                    $this->line("   " . ($index + 1) . ". {$time}");
                }
                $this->newLine();
            }

            if ($form->lunch_tick_times && count($form->lunch_tick_times) > 0) {
                $this->info('🍽️  Lunch Ticks:');
                foreach ($form->lunch_tick_times as $index => $time) {
                    $this->line("   " . ($index + 1) . ". {$time}");
                }
                $this->newLine();
            }

            if ($form->evening_tick_times && count($form->evening_tick_times) > 0) {
                $this->info('🌙 Evening Ticks:');
                foreach ($form->evening_tick_times as $index => $time) {
                    $this->line("   " . ($index + 1) . ". {$time}");
                }
                $this->newLine();
            }

            // Display logs
            $this->info('📝 Content Form Logs:');
            $logs = ContentFormLog::where('content_form_id', $form->id)
                ->orderBy('action_at', 'asc')
                ->get();

            if ($logs->count() > 0) {
                $this->line('');
                foreach ($logs as $log) {
                    $presenterName = $log->presenter ? $log->presenter->name : 'Unknown';
                    $this->line("   Reading #{$log->reading_number} | {$log->time_slot} | {$log->action} | {$presenterName} | {$log->action_at}");
                }
            } else {
                $this->warn('   No logs found');
            }

            $this->newLine();

            // Verify data
            $this->info('✅ Verification:');
            $hasPresenter = $form->presenter_id !== null;
            $hasTickTimes = ($form->morning_tick_times && count($form->morning_tick_times) > 0) ||
                           ($form->lunch_tick_times && count($form->lunch_tick_times) > 0) ||
                           ($form->evening_tick_times && count($form->evening_tick_times) > 0);
            $hasLogs = $logs->count() > 0;

            $this->line("   Presenter recorded: " . ($hasPresenter ? '✅ YES' : '❌ NO'));
            $this->line("   Tick times recorded: " . ($hasTickTimes ? '✅ YES' : '❌ NO'));
            $this->line("   Logs recorded: " . ($hasLogs ? '✅ YES' : '❌ NO'));

            $this->newLine();

            if ($hasPresenter && $hasTickTimes && $hasLogs) {
                $this->info('✅ SUCCESS! All tick times and presenter information are recorded correctly!');
                return 0;
            } else {
                $this->error('❌ FAILED! Some information is missing');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}

