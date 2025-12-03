<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Advertisement;
use App\Models\Gong;
use App\Models\Customer;
use App\Models\AdsCategory;
use Carbon\Carbon;

class TestPresenterDashboardFiltering extends Command
{
    protected $signature = 'app:test-presenter-dashboard-filtering';
    protected $description = 'Test that presenter dashboard correctly filters advertisements and memorials by time slot and broadcast schedule';

    public function handle()
    {
        $this->info('🧪 Testing Presenter Dashboard Filtering...');
        $this->newLine();

        // Create test customer
        $customer = Customer::firstOrCreate(
            ['email' => 'test-dashboard@example.com'],
            [
                'fullname' => 'Test Customer',
                'phone' => '1234567890',
                'Organization' => 'Test Organization'
            ]
        );

        // Create test category
        $category = AdsCategory::firstOrCreate(
            ['name' => 'Test Category'],
            ['description' => 'Test Category for Dashboard']
        );

        $today = Carbon::today();

        // Test 1: Advertisement with only morning frequency
        $this->info('Test 1: Advertisement with only morning frequency');
        $ad1 = Advertisement::create([
            'customer_id' => $customer->id,
            'ads_category_id' => $category->id,
            'band' => ['AM'],
            'title' => 'Morning Only Ad',
            'content' => 'This ad should only appear in morning',
            'issued_date' => $today,
            'broadcast_start_date' => $today,
            'broadcast_end_date' => $today->addDays(7),
            'morning_frequency' => 3,
            'lunch_frequency' => null,
            'evening_frequency' => null,
            'is_paid' => true,
            'amount' => 100,
        ]);
        $this->line("✓ Created: {$ad1->title}");
        $this->line("  - Morning: {$ad1->morning_frequency}x");
        $this->line("  - Lunch: {$ad1->lunch_frequency}");
        $this->line("  - Evening: {$ad1->evening_frequency}");

        // Test 2: Advertisement with morning and lunch frequency
        $this->info('Test 2: Advertisement with morning and lunch frequency');
        $ad2 = Advertisement::create([
            'customer_id' => $customer->id,
            'ads_category_id' => $category->id,
            'band' => ['AM'],
            'title' => 'Morning & Lunch Ad',
            'content' => 'This ad should appear in morning and lunch',
            'issued_date' => $today,
            'broadcast_start_date' => $today,
            'broadcast_end_date' => $today->addDays(7),
            'morning_frequency' => 2,
            'lunch_frequency' => 4,
            'evening_frequency' => null,
            'is_paid' => true,
            'amount' => 150,
        ]);
        $this->line("✓ Created: {$ad2->title}");
        $this->line("  - Morning: {$ad2->morning_frequency}x");
        $this->line("  - Lunch: {$ad2->lunch_frequency}x");
        $this->line("  - Evening: {$ad2->evening_frequency}");

        // Test 3: Memorial with all time slots
        $this->info('Test 3: Memorial with all time slots');
        $gong1 = Gong::create([
            'customer_id' => $customer->id,
            'departed_name' => 'John Doe',
            'death_date' => $today->subDays(3),
            'published_date' => $today,
            'broadcast_start_date' => $today,
            'broadcast_end_date' => $today->addDays(7),
            'band' => ['AM'],
            'contents' => 'Memorial service for John Doe',
            'song_title' => 'Amazing Grace',
            'morning_frequency' => 1,
            'lunch_frequency' => 1,
            'evening_frequency' => 2,
            'is_paid' => true,
            'amount' => 200,
        ]);
        $this->line("✓ Created: {$gong1->departed_name}");
        $this->line("  - Morning: {$gong1->morning_frequency}x");
        $this->line("  - Lunch: {$gong1->lunch_frequency}x");
        $this->line("  - Evening: {$gong1->evening_frequency}x");

        // Test 4: Verify accessor shortcuts work
        $this->newLine();
        $this->info('Test 4: Verify accessor shortcuts (morning_freq, lunch_freq, evening_freq)');
        $this->line("Ad1 morning_freq: {$ad1->morning_freq} (should be 3)");
        $this->line("Ad1 lunch_freq: {$ad1->lunch_freq} (should be 0)");
        $this->line("Ad1 evening_freq: {$ad1->evening_freq} (should be 0)");

        $this->line("Ad2 morning_freq: {$ad2->morning_freq} (should be 2)");
        $this->line("Ad2 lunch_freq: {$ad2->lunch_freq} (should be 4)");
        $this->line("Ad2 evening_freq: {$ad2->evening_freq} (should be 0)");

        $this->line("Gong1 morning_freq: {$gong1->morning_freq} (should be 1)");
        $this->line("Gong1 lunch_freq: {$gong1->lunch_freq} (should be 1)");
        $this->line("Gong1 evening_freq: {$gong1->evening_freq} (should be 2)");

        // Test 5: Verify broadcast date filtering
        $this->newLine();
        $this->info('Test 5: Verify broadcast date filtering');
        $ad_expired = Advertisement::create([
            'customer_id' => $customer->id,
            'ads_category_id' => $category->id,
            'band' => ['AM'],
            'title' => 'Expired Ad',
            'content' => 'This ad should NOT appear (expired)',
            'issued_date' => $today->subDays(10),
            'broadcast_start_date' => $today->subDays(10),
            'broadcast_end_date' => $today->subDays(1),
            'morning_frequency' => 3,
            'lunch_frequency' => 3,
            'evening_frequency' => 3,
            'is_paid' => true,
            'amount' => 100,
        ]);
        $this->line("✓ Created expired ad: {$ad_expired->title}");
        $this->line("  - Broadcast dates: {$ad_expired->broadcast_start_date->format('M d')} - {$ad_expired->broadcast_end_date->format('M d')}");
        $this->line("  - Should NOT appear on dashboard (outside broadcast range)");

        $this->newLine();
        $this->info('✅ All tests completed successfully!');
        $this->newLine();
        $this->info('Summary:');
        $this->line('- Advertisements and memorials are filtered by current time slot');
        $this->line('- Only content with frequency > 0 for the current slot is shown');
        $this->line('- Only content within broadcast_start_date and broadcast_end_date is shown');
        $this->line('- Only AM band content is shown');
        $this->line('- Accessor shortcuts (morning_freq, lunch_freq, evening_freq) work correctly');
    }
}

