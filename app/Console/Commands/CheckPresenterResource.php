<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Filament\Resources\PresenterResource;

class CheckPresenterResource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:presenter-resource';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if PresenterResource is properly configured';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking PresenterResource Configuration...');
        $this->info('================================================');
        
        // Check if class exists
        if (!class_exists(PresenterResource::class)) {
            $this->error('❌ PresenterResource class not found!');
            return Command::FAILURE;
        }
        
        $this->info('✅ PresenterResource class exists');
        
        // Check navigation properties
        $navigationIcon = PresenterResource::getNavigationIcon();
        $navigationLabel = PresenterResource::getNavigationLabel();
        $navigationGroup = PresenterResource::getNavigationGroup();
        $navigationSort = PresenterResource::getNavigationSort();
        
        $this->info("✅ Navigation Icon: {$navigationIcon}");
        $this->info("✅ Navigation Label: {$navigationLabel}");
        $this->info("✅ Navigation Group: {$navigationGroup}");
        $this->info("✅ Navigation Sort: {$navigationSort}");
        
        // Check if pages exist
        $pages = PresenterResource::getPages();
        $this->info('✅ Pages configured:');
        foreach ($pages as $name => $page) {
            $this->info("   - {$name}: {$page}");
        }
        
        // Check if files exist
        $resourceFile = app_path('Filament/Resources/PresenterResource.php');
        $pagesDir = app_path('Filament/Resources/PresenterResource/Pages');
        
        $this->info("✅ Resource file exists: " . (file_exists($resourceFile) ? 'Yes' : 'No'));
        $this->info("✅ Pages directory exists: " . (is_dir($pagesDir) ? 'Yes' : 'No'));
        
        if (is_dir($pagesDir)) {
            $pageFiles = glob($pagesDir . '/*.php');
            $this->info('✅ Page files found:');
            foreach ($pageFiles as $file) {
                $this->info('   - ' . basename($file));
            }
        }
        
        // Check relation manager
        $relationManagerFile = app_path('Filament/Resources/PresenterResource/RelationManagers/ReadStatusesRelationManager.php');
        $this->info("✅ ReadStatusesRelationManager exists: " . (file_exists($relationManagerFile) ? 'Yes' : 'No'));
        
        $this->info('');
        $this->info('🎯 Troubleshooting Tips:');
        $this->info('1. Make sure MySQL service is running');
        $this->info('2. Run: php artisan serve');
        $this->info('3. Visit: http://localhost:8000/admin');
        $this->info('4. Login with: admin@admin.com / password');
        $this->info('5. Look for "User Management" → "Presenters"');
        
        return Command::SUCCESS;
    }
}
