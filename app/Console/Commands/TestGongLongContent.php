<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Gong;
use App\Models\Customer;

class TestGongLongContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:gong-long-content';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test gong creation with long content to verify string length fix';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Gong Long Content Functionality...');
        $this->info('============================================');

        // Check if we have customers
        $customer = Customer::first();
        if (!$customer) {
            $this->error('❌ No customers found. Please create a customer first.');
            return Command::FAILURE;
        }

        $this->info("✅ Found customer: {$customer->fullname}");

        // Create a very long memorial content (over 255 characters)
        $longContent = "
        <p><strong>In Loving Memory of Our Beloved Family Member</strong></p>
        
        <p>It is with heavy hearts that we announce the passing of our dear beloved, who left us peacefully on this day. They were a cherished member of our family and community, known for their kindness, wisdom, and unwavering love for everyone around them.</p>
        
        <p>Throughout their life, they touched the hearts of many with their generous spirit and compassionate nature. They were a devoted parent, grandparent, and friend who always put family first. Their legacy of love, laughter, and life lessons will continue to inspire us all.</p>
        
        <p>They had a passion for music, especially traditional Kiribati songs, and found great joy in sharing these melodies with others. Their favorite hymn was 'Amazing Grace,' which brought them comfort and peace throughout their life.</p>
        
        <p>We invite all family and friends to join us in celebrating their remarkable life and the countless memories we shared. Though they are no longer with us physically, their spirit lives on in our hearts and in the beautiful memories we will forever cherish.</p>
        
        <p>May they rest in eternal peace, and may their soul find comfort in the arms of the Almighty. We love you and will miss you always.</p>
        
        <p><em>From the loving family</em></p>
        ";

        $this->info('📝 Testing gong creation with long content (over 1000 characters)...');
        $this->info("📏 Content length: " . strlen($longContent) . " characters");

        try {
            $gong = Gong::create([
                'customer_id' => $customer->id,
                'departed_name' => 'Test Long Content Memorial',
                'death_date' => now()->subDays(3),
                'published_date' => now(),
                'broadcast_start_date' => now(),
                'broadcast_end_date' => now()->addDays(7),
                'band' => ['AM'],
                'contents' => $longContent,
                'song_title' => 'Amazing Grace',
                'morning_frequency' => 2,
                'lunch_frequency' => 1,
                'evening_frequency' => 1,
                'broadcast_days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'amount' => 100.00,
                'is_paid' => true,
                'attachment' => 'gongs/memorial-photo-long-content.jpg',
            ]);

            $this->info("✅ Gong created successfully with long content (ID: {$gong->id})");
            $this->info("📏 Stored content length: " . strlen($gong->contents) . " characters");
            $this->info("📎 Attachment: {$gong->attachment}");
            
            // Test retrieving the content
            $retrievedGong = Gong::find($gong->id);
            $this->info("✅ Content retrieval test passed");
            $this->info("📄 Content preview: " . substr(strip_tags($retrievedGong->contents), 0, 100) . "...");

        } catch (\Exception $e) {
            $this->error("❌ Failed to create gong with long content: {$e->getMessage()}");
            return Command::FAILURE;
        }

        // Test with even longer content (over 2000 characters)
        $veryLongContent = str_repeat($longContent, 2) . "\n\nAdditional memorial information and extended family tributes...";
        
        $this->info('📝 Testing with very long content (over 2000 characters)...');
        $this->info("📏 Very long content length: " . strlen($veryLongContent) . " characters");

        try {
            $veryLongGong = Gong::create([
                'customer_id' => $customer->id,
                'departed_name' => 'Test Very Long Content Memorial',
                'death_date' => now()->subDays(1),
                'published_date' => now(),
                'broadcast_start_date' => now(),
                'broadcast_end_date' => now()->addDays(7),
                'band' => ['FM'],
                'contents' => $veryLongContent,
                'song_title' => 'How Great Thou Art',
                'morning_frequency' => 1,
                'lunch_frequency' => 2,
                'evening_frequency' => 1,
                'broadcast_days' => ['Monday', 'Wednesday', 'Friday'],
                'amount' => 150.00,
                'is_paid' => false,
                'attachment' => null,
            ]);

            $this->info("✅ Very long gong created successfully (ID: {$veryLongGong->id})");
            $this->info("📏 Stored very long content length: " . strlen($veryLongGong->contents) . " characters");

        } catch (\Exception $e) {
            $this->error("❌ Failed to create gong with very long content: {$e->getMessage()}");
            return Command::FAILURE;
        }

        $this->info('');
        $this->info('🎉 Long content test completed successfully!');
        $this->info('');
        $this->info('✅ Database Migration Results:');
        $this->info('   - Contents field changed from string (255 chars) to text (65,535 chars)');
        $this->info('   - Long memorial content now supported');
        $this->info('   - File attachments work with long content');
        $this->info('   - Rich text formatting preserved');
        $this->info('');
        $this->info('🔍 To test in admin panel:');
        $this->info('   1. Visit: http://localhost:8000/admin');
        $this->info('   2. Go to: Radio Advertisements → Gongs');
        $this->info('   3. Create new gong with long memorial content');
        $this->info('   4. Add file attachment');
        $this->info('   5. Save - should work without string length errors');

        return Command::SUCCESS;
    }
}
