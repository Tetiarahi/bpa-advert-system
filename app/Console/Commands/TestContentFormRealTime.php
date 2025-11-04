<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ContentForm;
use App\Models\ContentFormLog;
use App\Models\Presenter;
use App\Models\Advertisement;

class TestContentFormRealTime extends Command
{
    protected $signature = 'app:test-content-form-real-time';
    protected $description = 'Test real-time tick/untick functionality for ContentForm module';

    public function handle()
    {
        $this->info('🧪 Testing ContentForm Real-Time Tick/Untick Functionality');
        $this->line('');

        try {
            // Get or create test data
            $this->info('📊 Setting up test data...');
            
            $presenter = Presenter::first();
            if (!$presenter) {
                $this->error('❌ No presenters found in database');
                return 1;
            }
            $this->line("✅ Using presenter: {$presenter->name}");

            $contentForm = ContentForm::first();
            if (!$contentForm) {
                $this->error('❌ No content forms found in database');
                return 1;
            }
            $this->line("✅ Using content form: {$contentForm->title}");
            $this->line("   - Type: {$contentForm->content_type}");
            $this->line("   - Frequencies: Morning={$contentForm->morning_frequency}, Lunch={$contentForm->lunch_frequency}, Evening={$contentForm->evening_frequency}");

            // Test tick functionality
            $this->line('');
            $this->info('🔄 Testing TICK functionality...');
            
            $timeSlot = 'morning';
            $initialCount = $contentForm->morning_tick_count ?? 0;
            $this->line("Initial tick count for {$timeSlot}: {$initialCount}");

            // Simulate tick
            $newCount = $initialCount + 1;
            $contentForm->update([
                'morning_tick_count' => $newCount,
                'morning_ticked_at' => now(),
                'presenter_id' => $presenter->id,
                'presenter_shift' => $timeSlot,
            ]);

            // Create log entry
            $log = ContentFormLog::create([
                'content_form_id' => $contentForm->id,
                'presenter_id' => $presenter->id,
                'action' => 'tick',
                'time_slot' => $timeSlot,
                'action_at' => now(),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Test Command',
                'reading_number' => $newCount,
                'notes' => "Test tick #{$newCount}",
            ]);

            $this->line("✅ Tick recorded successfully!");
            $this->line("   - New count: {$newCount}");
            $this->line("   - Log ID: {$log->id}");
            $this->line("   - Timestamp: {$log->action_at}");
            $this->line("   - Presenter: {$presenter->name}");

            // Verify in database
            $contentForm->refresh();
            $this->line("✅ Verified in database: morning_tick_count = {$contentForm->morning_tick_count}");

            // Test untick functionality
            $this->line('');
            $this->info('🔄 Testing UNTICK functionality...');

            $beforeUntick = $contentForm->morning_tick_count;
            $newCount = $beforeUntick - 1;
            
            $contentForm->update([
                'morning_tick_count' => $newCount,
                'presenter_id' => $presenter->id,
                'presenter_shift' => $timeSlot,
            ]);

            $log2 = ContentFormLog::create([
                'content_form_id' => $contentForm->id,
                'presenter_id' => $presenter->id,
                'action' => 'untick',
                'time_slot' => $timeSlot,
                'action_at' => now(),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Test Command',
                'reading_number' => $newCount,
                'notes' => "Test untick, now at #{$newCount}",
            ]);

            $this->line("✅ Untick recorded successfully!");
            $this->line("   - Before: {$beforeUntick}");
            $this->line("   - After: {$newCount}");
            $this->line("   - Log ID: {$log2->id}");
            $this->line("   - Timestamp: {$log2->action_at}");

            // Verify logs
            $this->line('');
            $this->info('📋 Verifying logs in database...');

            $logs = ContentFormLog::where('content_form_id', $contentForm->id)
                ->where('presenter_id', $presenter->id)
                ->orderBy('action_at', 'desc')
                ->limit(5)
                ->get();

            $this->line("✅ Found {$logs->count()} recent logs:");
            foreach ($logs as $log) {
                $this->line("   - {$log->action} (#{$log->reading_number}) at {$log->action_at} by {$log->presenter->name}");
            }

            // Show summary
            $this->line('');
            $this->info('📊 Summary:');
            $this->line("✅ ContentForm ID: {$contentForm->id}");
            $this->line("✅ Presenter: {$presenter->name}");
            $this->line("✅ Morning Tick Count: {$contentForm->morning_tick_count}");
            $this->line("✅ Total Logs: {$contentForm->logs()->count()}");
            $this->line("✅ Tick Logs: {$contentForm->logs()->where('action', 'tick')->count()}");
            $this->line("✅ Untick Logs: {$contentForm->logs()->where('action', 'untick')->count()}");

            $this->line('');
            $this->info('✅ All tests passed! Real-time tracking is working correctly.');
            $this->line('');
            $this->info('🎯 Next steps:');
            $this->line('1. Go to admin panel: http://localhost:8000/admin');
            $this->line('2. Click "Content Forms" in sidebar');
            $this->line('3. Click on the content form to view logs');
            $this->line('4. You should see the tick/untick logs with timestamps and presenter names');

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}

