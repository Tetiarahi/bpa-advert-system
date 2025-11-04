<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AssignContentFormPermissions extends Command
{
    protected $signature = 'app:assign-content-form-permissions';
    protected $description = 'Assign ContentForm permissions to admin role';

    public function handle()
    {
        $this->info('Assigning ContentForm permissions to admin role...');

        try {
            // Get or create admin role
            $adminRole = Role::firstOrCreate(['name' => 'admin']);
            
            // Get all ContentForm permissions
            $permissions = Permission::where('name', 'like', '%content::form%')->get();
            
            if ($permissions->isEmpty()) {
                $this->warn('No ContentForm permissions found. Running shield:generate first...');
                $this->call('shield:generate', ['--all' => true]);
                $permissions = Permission::where('name', 'like', '%content::form%')->get();
            }

            // Assign permissions to admin role
            foreach ($permissions as $permission) {
                $adminRole->givePermissionTo($permission);
                $this->line("✓ Assigned: {$permission->name}");
            }

            // Also assign to super_admin role if it exists
            $superAdminRole = Role::where('name', 'super_admin')->first();
            if ($superAdminRole) {
                foreach ($permissions as $permission) {
                    $superAdminRole->givePermissionTo($permission);
                }
                $this->line('✓ Also assigned to super_admin role');
            }

            $this->info('✅ ContentForm permissions assigned successfully!');
            
            // Show summary
            $this->line('');
            $this->info('Permissions assigned:');
            foreach ($permissions as $permission) {
                $this->line("  - {$permission->name}");
            }

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}

