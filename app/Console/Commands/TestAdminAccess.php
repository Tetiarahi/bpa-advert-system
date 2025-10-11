<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class TestAdminAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:test {--email=admin@admin.com}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test admin user access and permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');
        
        $this->info('🧪 Testing Admin Access...');
        $this->info('========================');
        
        // Find admin user
        $admin = User::where('email', $email)->first();
        
        if (!$admin) {
            $this->error("❌ Admin user not found: {$email}");
            return Command::FAILURE;
        }
        
        $this->info("✅ Admin user found: {$admin->name} ({$admin->email})");
        
        // Check roles
        $roles = $admin->roles->pluck('name')->toArray();
        $this->info("✅ Roles: " . implode(', ', $roles));
        
        // Check permissions
        $permissions = $admin->getAllPermissions()->count();
        $this->info("✅ Total permissions: {$permissions}");
        
        // Check specific permissions
        $criticalPermissions = [
            'view_any_user',
            'view_any_advertisement',
            'view_any_customer',
            'view_any_gong',
        ];
        
        $this->info('');
        $this->info('🔍 Checking critical permissions:');
        foreach ($criticalPermissions as $permission) {
            $hasPermission = $admin->can($permission);
            $status = $hasPermission ? '✅' : '❌';
            $this->info("  {$status} {$permission}");
        }
        
        // Check Filament Shield
        $this->info('');
        $this->info('🛡️ Filament Shield Status:');
        $this->info('  ✅ Super admin enabled: ' . (config('filament-shield.super_admin.enabled') ? 'Yes' : 'No'));
        $this->info('  ✅ Super admin name: ' . config('filament-shield.super_admin.name'));
        
        // Check if user is super admin
        $isSuperAdmin = $admin->hasRole('super_admin');
        $this->info("  ✅ User is super admin: " . ($isSuperAdmin ? 'Yes' : 'No'));
        
        $this->info('');
        if ($isSuperAdmin && $permissions > 0) {
            $this->info('🎉 Admin access looks good! Try logging in at /admin');
        } else {
            $this->error('❌ Admin access may have issues. Run: php artisan db:seed --class=ProductionAdminSeeder');
        }
        
        return Command::SUCCESS;
    }
}
