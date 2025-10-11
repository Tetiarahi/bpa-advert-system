<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class AdminOnlySeeder extends Seeder
{
    /**
     * Run the database seeder.
     * This seeder ONLY creates the admin user and permissions - no sample data.
     */
    public function run(): void
    {
        $this->command->info('🔐 Creating Admin User Only (Production Safe)...');

        try {
            // Create or update admin user
            $admin = User::updateOrCreate(
                ['email' => 'admin@admin.com'],
                [
                    'name' => 'Super Administrator',
                    'email' => 'admin@admin.com',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            $this->command->info("✅ Admin user created/updated: {$admin->email}");

            // Create super_admin role if it doesn't exist
            $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
            $this->command->info("✅ Super admin role created/updated");

            // Define essential permissions
            $permissions = [
                // Core admin permissions
                'view_any_user', 'create_user', 'update_user', 'delete_user',
                'view_any_role', 'create_role', 'update_role', 'delete_role',
                
                // Advertisement permissions
                'view_any_advertisement', 'create_advertisement', 'update_advertisement', 'delete_advertisement',
                
                // Customer permissions
                'view_any_customer', 'create_customer', 'update_customer', 'delete_customer',
                
                // Gong permissions
                'view_any_gong', 'create_gong', 'update_gong', 'delete_gong',
                
                // Program permissions
                'view_any_program', 'create_program', 'update_program', 'delete_program',
                
                // Category permissions
                'view_any_ads::category', 'create_ads::category', 'update_ads::category', 'delete_ads::category',
                
                // Activity log permissions
                'view_any_activity::log',
                
                // Widget permissions
                'widget_AdvertisementsThisMonthWidget',
                'widget_RecentAdvertisementsTableWidget',
                'widget_RecentGongsTableWidget',
                'widget_RecentActivityWidget',
            ];

            // Create permissions if they don't exist
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission]);
            }

            $this->command->info("✅ Created/updated " . count($permissions) . " essential permissions");

            // Assign all permissions to super_admin role
            $superAdminRole->syncPermissions($permissions);
            $this->command->info("✅ Assigned permissions to super_admin role");

            // Assign super_admin role to admin user
            $admin->assignRole($superAdminRole);
            $this->command->info("✅ Assigned super_admin role to admin user");

            // Clear permission cache
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            $this->command->info("✅ Permission cache cleared");

            $this->command->info('');
            $this->command->info('🎉 Admin setup complete!');
            $this->command->info('📧 Email: admin@admin.com');
            $this->command->info('🔑 Password: password');
            $this->command->info('🌐 Admin Panel: /admin');
            $this->command->info('');
            $this->command->warn('⚠️  IMPORTANT: Change the password after first login!');

        } catch (\Exception $e) {
            $this->command->error('❌ Error creating admin user: ' . $e->getMessage());
            throw $e;
        }
    }
}
