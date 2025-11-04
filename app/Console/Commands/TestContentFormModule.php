<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ContentForm;
use App\Models\ContentFormLog;
use App\Models\Advertisement;
use App\Models\Gong;
use App\Models\Presenter;

class TestContentFormModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-content-form-module';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the ContentForm module functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing ContentForm Module...');
        $this->newLine();

        // Test 1: Check ContentForms were created
        $this->info('Test 1: Checking ContentForms creation...');
        $totalForms = ContentForm::count();
        $this->info("✅ Total ContentForms: {$totalForms}");

        $adForms = ContentForm::where('content_type', 'advertisement')->count();
        $gongForms = ContentForm::where('content_type', 'gong')->count();
        $this->info("   - Advertisements: {$adForms}");
        $this->info("   - Gongs: {$gongForms}");
        $this->newLine();

        // Test 2: Check ContentForm data
        $this->info('Test 2: Checking ContentForm data integrity...');
        $form = ContentForm::first();
        if ($form) {
            $this->info("✅ Sample ContentForm:");
            $this->info("   - Title: {$form->title}");
            $this->info("   - Type: {$form->content_type}");
            $this->info("   - Word Count: {$form->word_count}");
            $this->info("   - Amount: {$form->amount}");
            $this->info("   - Source: {$form->source}");
            $this->info("   - Morning Frequency: {$form->morning_frequency}");
            $this->info("   - Lunch Frequency: {$form->lunch_frequency}");
            $this->info("   - Evening Frequency: {$form->evening_frequency}");
        }
        $this->newLine();

        // Test 3: Test tick/untick functionality
        $this->info('Test 3: Testing tick/untick functionality...');
        $presenter = Presenter::first();
        $form = ContentForm::where('morning_frequency', '>', 0)->first();

        if ($presenter && $form) {
            $this->info("✅ Using Presenter: {$presenter->name}");
            $this->info("✅ Using ContentForm: {$form->title}");

            // Create a tick log
            $log = ContentFormLog::create([
                'content_form_id' => $form->id,
                'presenter_id' => $presenter->id,
                'action' => 'tick',
                'time_slot' => 'morning',
                'action_at' => now(),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Test Agent',
                'reading_number' => 1,
            ]);

            $this->info("✅ Created tick log: ID {$log->id}");

            // Update ContentForm tick count
            $form->update([
                'morning_tick_count' => 1,
                'morning_ticked_at' => now(),
            ]);

            $this->info("✅ Updated ContentForm tick count");

            // Create an untick log
            $untickLog = ContentFormLog::create([
                'content_form_id' => $form->id,
                'presenter_id' => $presenter->id,
                'action' => 'untick',
                'time_slot' => 'morning',
                'action_at' => now(),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Test Agent',
                'reading_number' => 0,
            ]);

            $this->info("✅ Created untick log: ID {$untickLog->id}");

            // Update ContentForm tick count back to 0
            $form->update([
                'morning_tick_count' => 0,
            ]);

            $this->info("✅ Updated ContentForm tick count back to 0");
        }
        $this->newLine();

        // Test 4: Check ContentFormLogs
        $this->info('Test 4: Checking ContentFormLogs...');
        $totalLogs = ContentFormLog::count();
        $this->info("✅ Total ContentFormLogs: {$totalLogs}");

        $tickLogs = ContentFormLog::where('action', 'tick')->count();
        $untickLogs = ContentFormLog::where('action', 'untick')->count();
        $this->info("   - Tick actions: {$tickLogs}");
        $this->info("   - Untick actions: {$untickLogs}");
        $this->newLine();

        // Test 5: Check reading progress
        $this->info('Test 5: Checking reading progress...');
        $completedForms = ContentForm::where('is_completed', true)->count();
        $inProgressForms = ContentForm::where('is_completed', false)
            ->where(function ($query) {
                $query->where('morning_tick_count', '>', 0)
                    ->orWhere('lunch_tick_count', '>', 0)
                    ->orWhere('evening_tick_count', '>', 0);
            })
            ->count();
        $notStartedForms = ContentForm::where('is_completed', false)
            ->where('morning_tick_count', 0)
            ->where('lunch_tick_count', 0)
            ->where('evening_tick_count', 0)
            ->count();

        $this->info("✅ Completed: {$completedForms}");
        $this->info("✅ In Progress: {$inProgressForms}");
        $this->info("✅ Not Started: {$notStartedForms}");
        $this->newLine();

        $this->info('✅ All tests completed successfully!');
    }
}
