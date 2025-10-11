<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "📊 Creating Essential Fake Data for BPA System\n";
echo "==============================================\n\n";

try {
    // Clear existing data safely (respecting foreign keys)
    echo "🧹 Clearing Existing Data:\n";
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    \App\Models\Customer::truncate();
    \App\Models\AdsCategory::truncate();
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
        ['name' => 'Transportation', 'description' => 'Transport, logistics, and delivery services'],
        ['name' => 'Legal Services', 'description' => 'Law firms, legal advice, and consultation'],
        ['name' => 'Construction', 'description' => 'Building, renovation, and construction services'],
    ];
    
    $createdCategories = [];
    foreach ($categories as $categoryData) {
        $category = \App\Models\AdsCategory::create($categoryData);
        $createdCategories[] = $category;
        echo "✅ Created category: {$category->name}\n";
    }

    // 2. Create Customers
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
        ['fullname' => 'Maria Gonzalez', 'Organization' => 'Gonzalez Flower Shop', 'phone' => '+1-555-0116', 'email' => 'maria.gonzalez@email.com', 'address' => '159 Rose Ave, Garden District'],
        ['fullname' => 'Thomas Wilson', 'Organization' => 'Wilson Transport Services', 'phone' => '+1-555-0117', 'email' => 'thomas.wilson@email.com', 'address' => '753 Highway Blvd, Industrial'],
        ['fullname' => 'Patricia Moore', 'Organization' => 'Moore Photography Studio', 'phone' => '+1-555-0118', 'email' => 'patricia.moore@email.com', 'address' => '951 Art Street, Creative Quarter'],
        ['fullname' => 'Kevin Thompson', 'Organization' => 'Thompson Hardware Store', 'phone' => '+1-555-0119', 'email' => 'kevin.thompson@email.com', 'address' => '357 Tool Lane, Workshop District'],
        ['fullname' => 'Linda Clark', 'Organization' => 'Clark Pet Grooming', 'phone' => '+1-555-0120', 'email' => 'linda.clark@email.com', 'address' => '246 Pet Park Way, Animal District'],
        ['fullname' => 'Mark Lewis', 'Organization' => 'Lewis Computer Repair', 'phone' => '+1-555-0121', 'email' => 'mark.lewis@email.com', 'address' => '135 Tech Street, Silicon Valley'],
        ['fullname' => 'Nancy Walker', 'Organization' => 'Walker Travel Agency', 'phone' => '+1-555-0122', 'email' => 'nancy.walker@email.com', 'address' => '789 Journey Road, Travel Hub'],
        ['fullname' => 'Steven Allen', 'Organization' => 'Allen Music Store', 'phone' => '+1-555-0123', 'email' => 'steven.allen@email.com', 'address' => '456 Melody Lane, Arts District'],
        ['fullname' => 'Carol Young', 'Organization' => 'Young Bakery', 'phone' => '+1-555-0124', 'email' => 'carol.young@email.com', 'address' => '321 Sweet Street, Bakery Row'],
        ['fullname' => 'Paul Harris', 'Organization' => 'Harris Plumbing Services', 'phone' => '+1-555-0125', 'email' => 'paul.harris@email.com', 'address' => '654 Pipe Avenue, Service District'],
    ];
    
    $createdCustomers = [];
    foreach ($customers as $customerData) {
        $customer = \App\Models\Customer::create($customerData);
        $createdCustomers[] = $customer;
        echo "✅ Created customer: {$customer->fullname} ({$customer->Organization})\n";
    }

    echo "\n📊 Data Creation Summary:\n";
    echo "=========================\n";
    echo "🏷️ Categories: " . count($createdCategories) . " created\n";
    echo "👥 Customers: " . count($createdCustomers) . " created\n";
    
    echo "\n📋 Created Categories:\n";
    echo "=====================\n";
    foreach ($createdCategories as $category) {
        echo "• {$category->name} - {$category->description}\n";
    }
    
    echo "\n👥 Sample Customers by Business Type:\n";
    echo "====================================\n";
    $businessTypes = [
        'Technology' => ['Smith Electronics', 'Lewis Computer Repair'],
        'Food & Restaurants' => ['Johnson Family Restaurant', 'King Catering Services', 'Young Bakery'],
        'Health & Medical' => ['Davis Medical Clinic', 'White Dental Practice'],
        'Automotive' => ['Brown Auto Repair'],
        'Real Estate' => ['Wilson Real Estate'],
        'Personal Services' => ['Anderson Beauty Salon', 'Clark Pet Grooming'],
        'Business Services' => ['Taylor Business Consulting'],
        'Education & Training' => ['Martinez Learning Center'],
        'Construction' => ['Garcia Construction', 'Harris Plumbing Services'],
        'Legal Services' => ['Rodriguez Law Firm'],
        'Financial Services' => ['Hall Insurance Agency'],
        'Entertainment' => ['Moore Photography Studio', 'Allen Music Store'],
        'Transportation' => ['Wilson Transport Services'],
        'Retail & Shopping' => ['Thompson Hardware Store', 'Gonzalez Flower Shop'],
        'Home & Garden' => ['Young Fitness Center'],
    ];
    
    foreach ($businessTypes as $type => $businesses) {
        echo "📂 {$type}:\n";
        foreach ($businesses as $business) {
            echo "   • {$business}\n";
        }
    }
    
    echo "\n🎯 Next Steps:\n";
    echo "==============\n";
    echo "1. Login to admin panel: http://localhost:8000/admin\n";
    echo "2. Use admin@admin.com / password\n";
    echo "3. Navigate to Advertisements section\n";
    echo "4. Create new advertisements using the customers and categories\n";
    echo "5. Navigate to Memorial Services section\n";
    echo "6. Create memorial services using the customers\n";
    echo "7. Test the presenter dashboard with real data\n";
    
    echo "\n💡 Admin Panel Features Now Available:\n";
    echo "======================================\n";
    echo "✅ Dashboard - System overview\n";
    echo "✅ Advertisements - Create ads using customers and categories\n";
    echo "✅ Memorial Services - Create memorial services\n";
    echo "✅ Customers - " . count($createdCustomers) . " customers ready to use\n";
    echo "✅ Categories - " . count($createdCategories) . " categories ready to use\n";
    echo "✅ Programs - Can be created manually\n";
    echo "✅ Activity Logs - Track all activities\n";
    
    echo "\n🎉 Essential data creation completed successfully!\n";
    echo "You now have a solid foundation to test the BPA Advertisement Management System.\n";
    echo "Create advertisements and memorial services through the admin panel using the customers and categories provided.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n";
?>
