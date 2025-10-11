<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting Database Seeding...');
        $this->command->info('================================');

        try {
            // Seed admin user with all permissions first
            $this->command->info('📝 Running AdminPermissionsSeeder...');
            $this->call(AdminPermissionsSeeder::class);
            $this->command->info('✅ AdminPermissionsSeeder completed successfully');

        } catch (\Exception $e) {
            $this->command->error('❌ AdminPermissionsSeeder failed: ' . $e->getMessage());
            $this->command->error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }

        try {
            // Create test user
            $this->command->info('📝 Creating test user...');
            $testUser = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
            $this->command->info("✅ Created test user: {$testUser->email}");

        } catch (\Exception $e) {
            $this->command->error('❌ Test user creation failed: ' . $e->getMessage());
            // Don't throw here, test user is not critical
        }

        $this->command->info('');
        $this->command->info('🎉 Database seeding completed successfully!');
        $this->command->info('');
        $this->command->info('🔑 Admin Login Credentials:');
        $this->command->info('   Email: admin@admin.com');
        $this->command->info('   Password: password');
        $this->command->info('');
        $this->command->info('🌐 Access the admin panel at: /admin');
    }
}
