<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class ProductionAdminSeeder extends Seeder
{
    /**
     * Run the database seeder for production deployment.
     * This seeder is safe to run multiple times and only creates admin user.
     */
    public function run(): void
    {
        $this->command->info('🚀 Production Admin Setup...');

        // Clear permission cache first
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

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

        // Create super_admin role (this is critical for Filament Shield)
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $this->command->info("✅ Super admin role created/updated");

        // Also create panel_user role (required by Filament Shield)
        $panelUserRole = Role::firstOrCreate(['name' => 'panel_user']);
        $this->command->info("✅ Panel user role created/updated");

        // Complete permissions for Filament admin access
        $permissions = [
            // Activity Log permissions
            'view_activity::log',
            'view_any_activity::log',
            'create_activity::log',
            'update_activity::log',
            'restore_activity::log',
            'restore_any_activity::log',
            'replicate_activity::log',
            'reorder_activity::log',
            'delete_activity::log',
            'delete_any_activity::log',
            'force_delete_activity::log',
            'force_delete_any_activity::log',

            // Ads Category permissions
            'view_ads::category',
            'view_any_ads::category',
            'create_ads::category',
            'update_ads::category',
            'restore_ads::category',
            'restore_any_ads::category',
            'replicate_ads::category',
            'reorder_ads::category',
            'delete_ads::category',
            'delete_any_ads::category',
            'force_delete_ads::category',
            'force_delete_any_ads::category',

            // Advertisement permissions
            'view_advertisement',
            'view_any_advertisement',
            'create_advertisement',
            'update_advertisement',
            'restore_advertisement',
            'restore_any_advertisement',
            'replicate_advertisement',
            'reorder_advertisement',
            'delete_advertisement',
            'delete_any_advertisement',
            'force_delete_advertisement',
            'force_delete_any_advertisement',

            // Customer permissions
            'view_customer',
            'view_any_customer',
            'create_customer',
            'update_customer',
            'restore_customer',
            'restore_any_customer',
            'replicate_customer',
            'reorder_customer',
            'delete_customer',
            'delete_any_customer',
            'force_delete_customer',
            'force_delete_any_customer',

            // Gong permissions
            'view_gong',
            'view_any_gong',
            'create_gong',
            'update_gong',
            'restore_gong',
            'restore_any_gong',
            'replicate_gong',
            'reorder_gong',
            'delete_gong',
            'delete_any_gong',
            'force_delete_gong',
            'force_delete_any_gong',

            // Program permissions
            'view_program',
            'view_any_program',
            'create_program',
            'update_program',
            'restore_program',
            'restore_any_program',
            'replicate_program',
            'reorder_program',
            'delete_program',
            'delete_any_program',
            'force_delete_program',
            'force_delete_any_program',

            // Role permissions
            'view_role',
            'view_any_role',
            'create_role',
            'update_role',
            'delete_role',
            'delete_any_role',

            // User permissions
            'view_user',
            'view_any_user',
            'create_user',
            'update_user',
            'restore_user',
            'restore_any_user',
            'replicate_user',
            'reorder_user',
            'delete_user',
            'delete_any_user',
            'force_delete_user',
            'force_delete_any_user',

            // Widget permissions
            'widget_AdvertisementsThisMonthWidget',
            'widget_RecentAdvertisementsTableWidget',
            'widget_RecentGongsTableWidget',
            'widget_RecentActivityWidget',
        ];

        // Create permissions
        $createdCount = 0;
        foreach ($permissions as $permission) {
            $perm = Permission::firstOrCreate(['name' => $permission]);
            if ($perm->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        $this->command->info("✅ Created/updated " . count($permissions) . " permissions ({$createdCount} new)");

        // Assign permissions and role
        $superAdminRole->syncPermissions($permissions);
        $this->command->info("✅ Assigned " . count($permissions) . " permissions to super_admin role");

        // Assign both super_admin and panel_user roles to admin user
        $admin->syncRoles(['super_admin', 'panel_user']);
        $this->command->info("✅ Assigned super_admin and panel_user roles to admin user");

        // Verify the setup
        $userPermissions = $admin->getAllPermissions()->count();
        $userRoles = $admin->roles->pluck('name')->toArray();

        $this->command->info("✅ Admin user has {$userPermissions} permissions");
        $this->command->info("✅ Admin user roles: " . implode(', ', $userRoles));

        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->command->info("✅ Permission cache cleared");

        $this->command->info('');
        $this->command->info('🎉 Production Admin Setup Complete!');
        $this->command->info('📧 Email: admin@admin.com');
        $this->command->info('🔑 Password: password');
        $this->command->info('🌐 Admin Panel: /admin');
        $this->command->info('');
        $this->command->warn('⚠️  IMPORTANT: Change password after login!');

        // Test admin access
        $this->command->info('');
        $this->command->info('🧪 Testing admin access...');

        // Check if user can access admin panel
        $canAccessPanel = $admin->hasRole('super_admin') || $admin->hasRole('panel_user');
        $this->command->info($canAccessPanel ? '✅ Admin can access panel' : '❌ Admin cannot access panel');

        // Check Filament Shield configuration
        $shieldEnabled = config('filament-shield.super_admin.enabled', false);
        $this->command->info($shieldEnabled ? '✅ Filament Shield super_admin enabled' : '❌ Filament Shield super_admin disabled');

        // Additional troubleshooting info
        $this->command->info('');
        $this->command->info('🔧 If still getting forbidden errors:');
        $this->command->info('1. Run: php artisan shield:generate --all');
        $this->command->info('2. Run: php artisan cache:clear');
        $this->command->info('3. Run: php artisan config:clear');
        $this->command->info('4. Check file permissions on server');
        $this->command->info('5. Check .env: APP_URL matches your domain');
        $this->command->info('6. Try: php artisan route:list | grep admin');
    }
}
