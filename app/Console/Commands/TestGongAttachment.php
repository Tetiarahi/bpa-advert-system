<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Gong;
use App\Models\Customer;

class TestGongAttachment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:gong-attachment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test gong creation with attachment functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Gong Attachment Functionality...');
        $this->info('==========================================');

        // Check if we have customers
        $customer = Customer::first();
        if (!$customer) {
            $this->error('❌ No customers found. Please create a customer first.');
            return Command::FAILURE;
        }

        $this->info("✅ Found customer: {$customer->fullname}");

        // Test creating a gong without attachment
        $this->info('📝 Testing gong creation without attachment...');
        
        try {
            $gong = Gong::create([
                'customer_id' => $customer->id,
                'departed_name' => 'Test Memorial Person',
                'death_date' => now()->subDays(7),
                'published_date' => now(),
                'broadcast_start_date' => now(),
                'broadcast_end_date' => now()->addDays(7),
                'band' => ['AM'],
                'contents' => 'Test memorial content for attachment testing',
                'song_title' => 'Amazing Grace',
                'morning_frequency' => 2,
                'lunch_frequency' => 1,
                'evening_frequency' => 1,
                'broadcast_days' => ['Monday', 'Tuesday', 'Wednesday'],
                'amount' => 50.00,
                'is_paid' => true,
                'attachment' => null, // No attachment
            ]);

            $this->info("✅ Gong created successfully without attachment (ID: {$gong->id})");
        } catch (\Exception $e) {
            $this->error("❌ Failed to create gong without attachment: {$e->getMessage()}");
            return Command::FAILURE;
        }

        // Test creating a gong with attachment path
        $this->info('📝 Testing gong creation with attachment path...');
        
        try {
            $gongWithAttachment = Gong::create([
                'customer_id' => $customer->id,
                'departed_name' => 'Test Memorial Person With Photo',
                'death_date' => now()->subDays(5),
                'published_date' => now(),
                'broadcast_start_date' => now(),
                'broadcast_end_date' => now()->addDays(7),
                'band' => ['AM'],
                'contents' => 'Test memorial content with attachment',
                'song_title' => 'How Great Thou Art',
                'morning_frequency' => 1,
                'lunch_frequency' => 1,
                'evening_frequency' => 1,
                'broadcast_days' => ['Monday', 'Wednesday', 'Friday'],
                'amount' => 75.00,
                'is_paid' => true,
                'attachment' => 'gongs/test-memorial-photo.jpg', // Test attachment path
            ]);

            $this->info("✅ Gong created successfully with attachment (ID: {$gongWithAttachment->id})");
            $this->info("   📎 Attachment: {$gongWithAttachment->attachment}");
        } catch (\Exception $e) {
            $this->error("❌ Failed to create gong with attachment: {$e->getMessage()}");
            return Command::FAILURE;
        }

        // Test retrieving and displaying gongs
        $this->info('📊 Testing gong retrieval...');
        
        $recentGongs = Gong::latest()->take(3)->get();
        
        foreach ($recentGongs as $gong) {
            $this->info("🔸 Gong ID: {$gong->id}");
            $this->info("   👤 Departed: {$gong->departed_name}");
            $this->info("   📎 Attachment: " . ($gong->attachment ? $gong->attachment : 'None'));
            $this->info("   💰 Amount: \${$gong->amount}");
            $this->info('');
        }

        // Test attachment field type
        $this->info('🔍 Testing attachment field type...');
        $testGong = Gong::latest()->first();
        if ($testGong) {
            $attachmentType = gettype($testGong->attachment);
            $this->info("✅ Attachment field type: {$attachmentType}");
            
            if ($testGong->attachment) {
                $this->info("✅ Attachment value: {$testGong->attachment}");
                $this->info("✅ Storage URL: " . asset('storage/' . $testGong->attachment));
            }
        }

        $this->info('🎉 Gong attachment test completed successfully!');
        $this->info('');
        $this->info('🔍 To test file upload in admin panel:');
        $this->info('   1. Visit: http://localhost:8000/admin');
        $this->info('   2. Login: admin@admin.com / password');
        $this->info('   3. Go to: Radio Advertisements → Gongs');
        $this->info('   4. Click: New Gong');
        $this->info('   5. Fill form and upload a file in Attachments section');

        return Command::SUCCESS;
    }
}
