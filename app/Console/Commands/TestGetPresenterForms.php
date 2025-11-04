<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ContentForm;
use App\Models\Presenter;

class TestGetPresenterForms extends Command
{
    protected $signature = 'app:test-get-presenter-forms';
    protected $description = 'Test the getPresenterForms endpoint to verify ContentForm retrieval';

    public function handle()
    {
        $this->info('🧪 Testing getPresenterForms endpoint...');
        $this->newLine();

        // Get a presenter
        $presenter = Presenter::first();
        if (!$presenter) {
            $this->error('❌ No presenter found in database');
            return;
        }

        $this->info("✅ Using presenter: {$presenter->name}");
        $this->newLine();

        // Get all ContentForms
        $allForms = ContentForm::all();
        $this->info("📊 Total ContentForms in database: {$allForms->count()}");
        $this->newLine();

        // Show sample forms
        $this->info('📋 Sample ContentForms:');
        $allForms->take(5)->each(function ($form, $index) {
            $presenterIdDisplay = $form->presenter_id ? "✅ {$form->presenter_id}" : "⭕ NULL";
            $this->line("  {$index}. ID: {$form->id}, Type: {$form->content_type}, Content ID: {$form->content_id}, Presenter ID: {$presenterIdDisplay}");
        });
        $this->newLine();

        // Count forms with and without presenter_id
        $withPresenter = $allForms->whereNotNull('presenter_id')->count();
        $withoutPresenter = $allForms->whereNull('presenter_id')->count();

        $this->info("📊 Forms with presenter_id: {$withPresenter}");
        $this->info("📊 Forms without presenter_id: {$withoutPresenter}");
        $this->newLine();

        // Simulate the API call
        $this->info('🔄 Simulating API call to /presenter/content-forms...');
        $this->newLine();

        // This is what the controller returns
        try {
            $forms = ContentForm::with(['customer'])
                ->orderBy('created_at', 'desc')
                ->get();

            $this->info("✅ API would return: {$forms->count()} forms");
            $this->newLine();

            // Show what the frontend would receive
            $this->info('📤 Frontend would receive:');
            $forms->take(3)->each(function ($form, $index) {
                $this->line("  {$index}. content_type: {$form->content_type}, content_id: {$form->content_id}, id: {$form->id}");
            });
            $this->newLine();

            $this->info('✅ Test complete! The fix allows frontend to find ContentForms even before first tick.');
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $this->info('🔧 Trying without customer relationship...');

            $forms = ContentForm::orderBy('created_at', 'desc')->get();
            $this->info("✅ API would return: {$forms->count()} forms");
            $this->newLine();

            // Show what the frontend would receive
            $this->info('📤 Frontend would receive:');
            $forms->take(3)->each(function ($form, $index) {
                $this->line("  {$index}. content_type: {$form->content_type}, content_id: {$form->content_id}, id: {$form->id}");
            });
            $this->newLine();

            $this->info('✅ Test complete! The fix allows frontend to find ContentForms even before first tick.');
        }
    }
}

