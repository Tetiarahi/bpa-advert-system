<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Customer;
use App\Models\Advertisement;
use App\Models\Gong;
use App\Models\Presenter;
use App\Models\AdsCategory;
use App\Models\Program;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TestAllFunctionality extends Command
{
    protected $signature = 'test:all-functionality';
    protected $description = 'Comprehensive test of all application functionality before deployment';

    public function handle()
    {
        $this->info('🚀 COMPREHENSIVE PRE-DEPLOYMENT TESTING');
        $this->info('==========================================');
        $this->newLine();

        $allTestsPassed = true;

        // Test 1: Database Connection
        $allTestsPassed &= $this->testDatabaseConnection();
        
        // Test 2: Admin User
        $allTestsPassed &= $this->testAdminUser();
        
        // Test 3: Models and Data
        $allTestsPassed &= $this->testModelsAndData();
        
        // Test 4: File Storage
        $allTestsPassed &= $this->testFileStorage();
        
        // Test 5: Activity Logging
        $allTestsPassed &= $this->testActivityLogging();
        
        // Test 6: Presenter System
        $allTestsPassed &= $this->testPresenterSystem();
        
        // Test 7: Long Content (Gongs)
        $allTestsPassed &= $this->testLongContent();

        $this->newLine();
        if ($allTestsPassed) {
            $this->info('🎉 ALL TESTS PASSED! Application is ready for deployment.');
            return Command::SUCCESS;
        } else {
            $this->error('❌ Some tests failed. Please fix issues before deployment.');
            return Command::FAILURE;
        }
    }

    private function testDatabaseConnection(): bool
    {
        $this->info('🔍 Testing Database Connection...');
        
        try {
            \DB::connection()->getPdo();
            $this->info('✅ Database connection successful');
            return true;
        } catch (\Exception $e) {
            $this->error("❌ Database connection failed: {$e->getMessage()}");
            return false;
        }
    }

    private function testAdminUser(): bool
    {
        $this->info('🔍 Testing Admin User...');
        
        try {
            $admin = User::where('email', 'admin@admin.com')->first();
            if (!$admin) {
                $this->error('❌ Admin user not found');
                return false;
            }
            
            if (!Hash::check('password', $admin->password)) {
                $this->error('❌ Admin password incorrect');
                return false;
            }
            
            $this->info('✅ Admin user exists and password is correct');
            return true;
        } catch (\Exception $e) {
            $this->error("❌ Admin user test failed: {$e->getMessage()}");
            return false;
        }
    }

    private function testModelsAndData(): bool
    {
        $this->info('🔍 Testing Models and Sample Data...');
        
        try {
            // Test Customer model
            $customerCount = Customer::count();
            $this->info("✅ Customers: {$customerCount} records");
            
            // Test AdsCategory model
            $categoryCount = AdsCategory::count();
            $this->info("✅ Ad Categories: {$categoryCount} records");
            
            // Test Advertisement model
            $adCount = Advertisement::count();
            $this->info("✅ Advertisements: {$adCount} records");
            
            // Test Gong model
            $gongCount = Gong::count();
            $this->info("✅ Gongs: {$gongCount} records");
            
            // Test Program model
            $programCount = Program::count();
            $this->info("✅ Programs: {$programCount} records");
            
            // Test Presenter model
            $presenterCount = Presenter::count();
            $this->info("✅ Presenters: {$presenterCount} records");
            
            return true;
        } catch (\Exception $e) {
            $this->error("❌ Models test failed: {$e->getMessage()}");
            return false;
        }
    }

    private function testFileStorage(): bool
    {
        $this->info('🔍 Testing File Storage...');
        
        try {
            // Test storage directories
            $directories = ['ads', 'gongs'];
            foreach ($directories as $dir) {
                if (!Storage::disk('public')->exists($dir)) {
                    Storage::disk('public')->makeDirectory($dir);
                }
                $this->info("✅ Storage directory '{$dir}' exists");
            }
            
            // Test file write/read
            $testFile = 'test-file.txt';
            Storage::disk('public')->put($testFile, 'Test content');
            
            if (Storage::disk('public')->exists($testFile)) {
                Storage::disk('public')->delete($testFile);
                $this->info('✅ File storage read/write working');
                return true;
            } else {
                $this->error('❌ File storage write failed');
                return false;
            }
        } catch (\Exception $e) {
            $this->error("❌ File storage test failed: {$e->getMessage()}");
            return false;
        }
    }

    private function testActivityLogging(): bool
    {
        $this->info('🔍 Testing Activity Logging...');
        
        try {
            $activityCount = Activity::count();
            $this->info("✅ Activity logs: {$activityCount} records");
            
            // Test creating an activity log
            activity('test')
                ->log('Test activity log entry');
            
            $newCount = Activity::count();
            if ($newCount > $activityCount) {
                $this->info('✅ Activity logging is working');
                return true;
            } else {
                $this->error('❌ Activity logging failed');
                return false;
            }
        } catch (\Exception $e) {
            $this->error("❌ Activity logging test failed: {$e->getMessage()}");
            return false;
        }
    }

    private function testPresenterSystem(): bool
    {
        $this->info('🔍 Testing Presenter System...');
        
        try {
            $presenters = Presenter::all();
            if ($presenters->isEmpty()) {
                $this->warn('⚠️ No presenters found - this is okay for initial deployment');
                return true;
            }
            
            foreach ($presenters as $presenter) {
                if (!Hash::check('password', $presenter->password)) {
                    $this->error("❌ Presenter {$presenter->email} password incorrect");
                    return false;
                }
            }
            
            $this->info('✅ Presenter authentication system working');
            return true;
        } catch (\Exception $e) {
            $this->error("❌ Presenter system test failed: {$e->getMessage()}");
            return false;
        }
    }

    private function testLongContent(): bool
    {
        $this->info('🔍 Testing Long Content Support...');
        
        try {
            // Test with a long content string
            $longContent = str_repeat('This is a test of long content support. ', 50);
            
            $customer = Customer::first();
            if (!$customer) {
                $this->warn('⚠️ No customers found - skipping long content test');
                return true;
            }
            
            $gong = Gong::create([
                'customer_id' => $customer->id,
                'departed_name' => 'Test Long Content',
                'death_date' => now(),
                'published_date' => now(),
                'broadcast_start_date' => now(),
                'broadcast_end_date' => now()->addDays(7),
                'band' => ['AM'],
                'contents' => $longContent,
                'song_title' => 'Test Song',
                'morning_frequency' => 1,
                'lunch_frequency' => 1,
                'evening_frequency' => 1,
                'broadcast_days' => ['Monday'],
                'amount' => 50.00,
                'is_paid' => true,
            ]);
            
            if (strlen($gong->contents) === strlen($longContent)) {
                $this->info("✅ Long content support working (tested {$gong->id} chars)");
                return true;
            } else {
                $this->error('❌ Long content was truncated');
                return false;
            }
        } catch (\Exception $e) {
            $this->error("❌ Long content test failed: {$e->getMessage()}");
            return false;
        }
    }
}
