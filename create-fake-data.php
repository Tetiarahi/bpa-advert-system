<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "📊 Creating Fake Data for BPA Advertisement Management System\n";
echo "=============================================================\n\n";

try {
    // Clear existing data safely (respecting foreign keys)
    echo "🧹 Clearing Existing Data:\n";
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    \App\Models\Advertisement::truncate();
    \App\Models\Gong::truncate();
    \App\Models\Customer::truncate();
    \App\Models\AdsCategory::truncate();
    \App\Models\Program::truncate();
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    echo "✅ Existing data cleared\n\n";

    // 1. Create Advertisement Categories
    echo "🏷️ Creating Advertisement Categories:\n";

    $categories = [
        ['name' => 'Business Services', 'description' => 'Professional and business services'],
        ['name' => 'Retail & Shopping', 'description' => 'Stores, shops, and retail businesses'],
        ['name' => 'Food & Restaurants', 'description' => 'Restaurants, cafes, and food services'],
        ['name' => 'Health & Medical', 'description' => 'Healthcare services and medical facilities'],
        ['name' => 'Education & Training', 'description' => 'Schools, courses, and educational services'],
        ['name' => 'Real Estate', 'description' => 'Property sales, rentals, and real estate services'],
        ['name' => 'Automotive', 'description' => 'Car sales, repairs, and automotive services'],
        ['name' => 'Entertainment', 'description' => 'Events, shows, and entertainment services'],
        ['name' => 'Technology', 'description' => 'IT services, electronics, and technology'],
        ['name' => 'Personal Services', 'description' => 'Beauty, fitness, and personal care services'],
        ['name' => 'Home & Garden', 'description' => 'Home improvement, gardening, and household services'],
        ['name' => 'Financial Services', 'description' => 'Banking, insurance, and financial services'],
    ];

    $createdCategories = [];
    foreach ($categories as $categoryData) {
        $category = \App\Models\AdsCategory::create($categoryData);
        $createdCategories[] = $category;
        echo "✅ Created category: {$category->name}\n";
    }

    // 2. Create Radio Programs (skip for now as it needs customers first)
    echo "\n📺 Skipping Radio Programs for now (will create after customers):\n";
    $createdPrograms = [];

    // 3. Create Customers
    echo "\n👥 Creating Customers:\n";

    $customers = [
        ['fullname' => 'John Smith', 'Organization' => 'Smith Electronics', 'phone' => '+1-555-0101', 'email' => 'john.smith@email.com', 'address' => '123 Main St, Downtown'],
        ['fullname' => 'Sarah Johnson', 'Organization' => 'Johnson Family Restaurant', 'phone' => '+1-555-0102', 'email' => 'sarah.johnson@email.com', 'address' => '456 Oak Ave, Midtown'],
        ['fullname' => 'Michael Brown', 'Organization' => 'Brown Auto Repair', 'phone' => '+1-555-0103', 'email' => 'michael.brown@email.com', 'address' => '789 Pine Rd, Uptown'],
        ['fullname' => 'Emily Davis', 'Organization' => 'Davis Medical Clinic', 'phone' => '+1-555-0104', 'email' => 'emily.davis@email.com', 'address' => '321 Elm St, Westside'],
        ['fullname' => 'David Wilson', 'Organization' => 'Wilson Real Estate', 'phone' => '+1-555-0105', 'email' => 'david.wilson@email.com', 'address' => '654 Maple Dr, Eastside'],
        ['fullname' => 'Lisa Anderson', 'Organization' => 'Anderson Beauty Salon', 'phone' => '+1-555-0106', 'email' => 'lisa.anderson@email.com', 'address' => '987 Cedar Ln, Northside'],
        ['fullname' => 'Robert Taylor', 'Organization' => 'Taylor Business Consulting', 'phone' => '+1-555-0107', 'email' => 'robert.taylor@email.com', 'address' => '147 Birch St, Southside'],
        ['fullname' => 'Jennifer Martinez', 'Organization' => 'Martinez Learning Center', 'phone' => '+1-555-0108', 'email' => 'jennifer.martinez@email.com', 'address' => '258 Spruce Ave, Central'],
        ['fullname' => 'William Garcia', 'Organization' => 'Garcia Construction', 'phone' => '+1-555-0109', 'email' => 'william.garcia@email.com', 'address' => '369 Willow Rd, Riverside'],
        ['fullname' => 'Amanda Rodriguez', 'Organization' => 'Rodriguez Law Firm', 'phone' => '+1-555-0110', 'email' => 'amanda.rodriguez@email.com', 'address' => '741 Poplar Dr, Hillside'],
        ['fullname' => 'James Lee', 'Organization' => 'Lee Family Services', 'phone' => '+1-555-0111', 'email' => 'james.lee@email.com', 'address' => '852 Ash St, Lakeside'],
        ['fullname' => 'Michelle White', 'Organization' => 'White Dental Practice', 'phone' => '+1-555-0112', 'email' => 'michelle.white@email.com', 'address' => '963 Hickory Ave, Parkside'],
        ['fullname' => 'Christopher Hall', 'Organization' => 'Hall Insurance Agency', 'phone' => '+1-555-0113', 'email' => 'christopher.hall@email.com', 'address' => '159 Walnut Ln, Seaside'],
        ['fullname' => 'Jessica Young', 'Organization' => 'Young Fitness Center', 'phone' => '+1-555-0114', 'email' => 'jessica.young@email.com', 'address' => '357 Cherry Rd, Mountainside'],
        ['fullname' => 'Daniel King', 'Organization' => 'King Catering Services', 'phone' => '+1-555-0115', 'email' => 'daniel.king@email.com', 'address' => '468 Peach Dr, Countryside'],
    ];

    $createdCustomers = [];
    foreach ($customers as $customerData) {
        $customer = \App\Models\Customer::create($customerData);
        $createdCustomers[] = $customer;
        echo "✅ Created customer: {$customer->fullname}\n";
    }

    // Skip Radio Programs for now (complex structure)
    echo "\n📺 Skipping Radio Programs (can be created manually in admin panel)\n";
    $createdPrograms = [];

    echo "\n📢 Creating Advertisements:\n";

    $advertisements = [
        [
            'title' => 'Grand Opening - Smith\'s Electronics',
            'content' => 'Join us for the grand opening of Smith\'s Electronics! 50% off all items this weekend. Latest smartphones, laptops, and gadgets. Visit us at 123 Main St.',
            'customer_id' => $createdCustomers[0]->id,
            'ads_category_id' => $createdCategories[8]->id, // Technology
            'issued_date' => now(),
            'morning_frequency' => 2,
            'lunch_frequency' => 1,
            'evening_frequency' => 3,
            'band' => ['AM', 'FM'],
            'broadcast_start_date' => now(),
            'broadcast_end_date' => now()->addDays(7),
            'is_paid' => true,
        ],
        [
            'title' => 'Johnson\'s Family Restaurant - New Menu',
            'content' => 'Try our new seasonal menu at Johnson\'s Family Restaurant! Fresh ingredients, local produce, and family recipes. Open daily 7AM-10PM. Call for reservations.',
            'customer_id' => $createdCustomers[1]->id,
            'ads_category_id' => $createdCategories[2]->id, // Food & Restaurants
            'morning_frequency' => 1,
            'lunch_frequency' => 3,
            'evening_frequency' => 2,
            'band' => ['FM'],
            'broadcast_start_date' => now(),
            'broadcast_end_date' => now()->addDays(14),
            'is_paid' => true,
        ],
        [
            'title' => 'Brown\'s Auto Repair - Expert Service',
            'content' => 'Professional auto repair services at Brown\'s Garage. 20 years experience, certified mechanics, fair prices. Oil changes, brake service, engine repair.',
            'customer_id' => $createdCustomers[2]->id,
            'ads_category_id' => $createdCategories[6]->id, // Automotive
            'morning_frequency' => 1,
            'lunch_frequency' => 0,
            'evening_frequency' => 2,
            'band' => ['AM'],
            'broadcast_start_date' => now(),
            'broadcast_end_date' => now()->addDays(30),
            'is_paid' => true,
        ],
        [
            'title' => 'Davis Medical Clinic - Health Checkups',
            'content' => 'Schedule your annual health checkup at Davis Medical Clinic. Experienced doctors, modern equipment, comprehensive care. Insurance accepted.',
            'customer_id' => $createdCustomers[3]->id,
            'ads_category_id' => $createdCategories[3]->id, // Health & Medical
            'morning_frequency' => 1,
            'lunch_frequency' => 1,
            'evening_frequency' => 1,
            'band' => ['FM', 'Uekera'],
            'broadcast_start_date' => now(),
            'broadcast_end_date' => now()->addDays(21),
            'is_paid' => true,
        ],
        [
            'title' => 'Wilson Real Estate - Dream Homes',
            'content' => 'Find your dream home with Wilson Real Estate. Residential and commercial properties, experienced agents, competitive rates. Call today for consultation.',
            'customer_id' => $createdCustomers[4]->id,
            'ads_category_id' => $createdCategories[5]->id, // Real Estate
            'morning_frequency' => 2,
            'lunch_frequency' => 1,
            'evening_frequency' => 2,
            'band' => ['AM', 'FM'],
            'broadcast_start_date' => now(),
            'broadcast_end_date' => now()->addDays(45),
            'is_paid' => true,
        ],
        [
            'title' => 'Anderson Beauty Salon - Makeover Special',
            'content' => 'Transform your look at Anderson Beauty Salon! Hair styling, coloring, manicures, facials. Book your makeover package today. New client discount available.',
            'customer_id' => $createdCustomers[5]->id,
            'ads_category_id' => $createdCategories[9]->id, // Personal Services
            'morning_frequency' => 0,
            'lunch_frequency' => 2,
            'evening_frequency' => 1,
            'band' => ['FM'],
            'broadcast_start_date' => now(),
            'broadcast_end_date' => now()->addDays(10),
            'is_paid' => false,
        ],
        [
            'title' => 'Taylor\'s Business Consulting',
            'content' => 'Grow your business with Taylor\'s Consulting Services. Strategic planning, financial analysis, marketing solutions. Free initial consultation for new clients.',
            'customer_id' => $createdCustomers[6]->id,
            'ads_category_id' => $createdCategories[0]->id, // Business Services
            'morning_frequency' => 1,
            'lunch_frequency' => 2,
            'evening_frequency' => 0,
            'band' => ['AM'],
            'broadcast_start_date' => now()->addDays(2),
            'broadcast_end_date' => now()->addDays(16),
            'is_paid' => true,
        ],
        [
            'title' => 'Martinez Learning Center - Tutoring',
            'content' => 'Improve your grades with Martinez Learning Center. Math, science, English tutoring. Experienced teachers, small groups, proven results.',
            'customer_id' => $createdCustomers[7]->id,
            'ads_category_id' => $createdCategories[4]->id, // Education & Training
            'morning_frequency' => 1,
            'lunch_frequency' => 0,
            'evening_frequency' => 2,
            'band' => ['FM', 'Uekera'],
            'broadcast_start_date' => now(),
            'broadcast_end_date' => now()->addDays(28),
            'is_paid' => true,
        ],
    ];

    $createdAds = [];
    foreach ($advertisements as $adData) {
        $ad = \App\Models\Advertisement::create($adData);
        $createdAds[] = $ad;
        echo "✅ Created advertisement: {$ad->title}\n";
    }

    echo "\n🕊️ Creating Memorial Services (Gongs):\n";

    $gongs = [
        [
            'departed_name' => 'Margaret Thompson',
            'contents' => 'In loving memory of Margaret Thompson, beloved mother and grandmother. She will be remembered for her kindness, wisdom, and the love she shared with everyone.',
            'song_title' => 'Amazing Grace',
            'customer_id' => $createdCustomers[8]->id,
            'morning_frequency' => 1,
            'lunch_frequency' => 0,
            'evening_frequency' => 1,
            'band' => ['AM', 'FM'],
            'broadcast_start_date' => now(),
            'broadcast_end_date' => now()->addDays(3),
            'is_paid' => true,
        ],
        [
            'departed_name' => 'Robert Johnson Sr.',
            'contents' => 'We remember Robert Johnson Sr., a dedicated father, husband, and community leader. His legacy of service and compassion will live on in our hearts.',
            'song_title' => 'How Great Thou Art',
            'customer_id' => $createdCustomers[9]->id,
            'morning_frequency' => 1,
            'lunch_frequency' => 1,
            'evening_frequency' => 1,
            'band' => ['FM'],
            'broadcast_start_date' => now(),
            'broadcast_end_date' => now()->addDays(5),
            'is_paid' => true,
        ],
        [
            'departed_name' => 'Eleanor Williams',
            'contents' => 'Eleanor Williams, age 78, passed peacefully surrounded by family. A teacher for 40 years, she touched countless lives with her dedication to education.',
            'song_title' => 'Blessed Assurance',
            'customer_id' => $createdCustomers[10]->id,
            'morning_frequency' => 0,
            'lunch_frequency' => 1,
            'evening_frequency' => 1,
            'band' => ['AM'],
            'broadcast_start_date' => now()->addDays(1),
            'broadcast_end_date' => now()->addDays(4),
            'is_paid' => true,
        ],
        [
            'departed_name' => 'Frank Miller',
            'contents' => 'Frank Miller, veteran and loving grandfather, has joined his beloved wife in eternal rest. His stories and laughter will be deeply missed by all who knew him.',
            'song_title' => 'Taps',
            'customer_id' => $createdCustomers[11]->id,
            'morning_frequency' => 1,
            'lunch_frequency' => 0,
            'evening_frequency' => 2,
            'band' => ['AM', 'FM', 'Uekera'],
            'broadcast_start_date' => now(),
            'broadcast_end_date' => now()->addDays(7),
            'is_paid' => true,
        ],
        [
            'departed_name' => 'Dorothy Chen',
            'contents' => 'Dorothy Chen, beloved community volunteer and mother of three, will be remembered for her generous spirit and dedication to helping others in need.',
            'song_title' => 'In the Garden',
            'customer_id' => $createdCustomers[12]->id,
            'morning_frequency' => 1,
            'lunch_frequency' => 1,
            'evening_frequency' => 0,
            'band' => ['FM'],
            'broadcast_start_date' => now()->addDays(2),
            'broadcast_end_date' => now()->addDays(5),
            'is_paid' => false,
        ],
    ];

    $createdGongs = [];
    foreach ($gongs as $gongData) {
        $gong = \App\Models\Gong::create($gongData);
        $createdGongs[] = $gong;
        echo "✅ Created memorial service: {$gong->departed_name}\n";
    }

    echo "\n📊 Data Creation Summary:\n";
    echo "=========================\n";
    echo "🏷️ Categories: " . count($createdCategories) . " created\n";
    echo "📺 Programs: " . count($createdPrograms) . " created\n";
    echo "👥 Customers: " . count($createdCustomers) . " created\n";
    echo "📢 Advertisements: " . count($createdAds) . " created\n";
    echo "🕊️ Memorial Services: " . count($createdGongs) . " created\n";

    echo "\n🎯 Data Distribution:\n";
    echo "=====================\n";
    echo "📢 Paid Advertisements: " . collect($createdAds)->where('is_paid', true)->count() . "\n";
    echo "📢 Unpaid Advertisements: " . collect($createdAds)->where('is_paid', false)->count() . "\n";
    echo "🕊️ Paid Memorial Services: " . collect($createdGongs)->where('is_paid', true)->count() . "\n";
    echo "🕊️ Unpaid Memorial Services: " . collect($createdGongs)->where('is_paid', false)->count() . "\n";

    echo "\n📅 Broadcast Schedule:\n";
    echo "======================\n";
    echo "🌅 Morning Broadcasts: " . (collect($createdAds)->sum('morning_frequency') + collect($createdGongs)->sum('morning_frequency')) . " times\n";
    echo "🍽️ Lunch Broadcasts: " . (collect($createdAds)->sum('lunch_frequency') + collect($createdGongs)->sum('lunch_frequency')) . " times\n";
    echo "🌆 Evening Broadcasts: " . (collect($createdAds)->sum('evening_frequency') + collect($createdGongs)->sum('evening_frequency')) . " times\n";

    echo "\n🎉 Fake data creation completed successfully!\n";
    echo "You can now test the admin panel and presenter dashboard with realistic data.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n";
?>
