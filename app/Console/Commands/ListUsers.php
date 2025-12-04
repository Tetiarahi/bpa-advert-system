<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class ListUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all users and their roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('👥 System Users and Roles');
        $this->info('========================');

        // List all roles
        $this->info('');
        $this->info('📋 Available Roles:');
        $roles = Role::all();
        foreach ($roles as $role) {
            $this->info("  • {$role->name}");
        }

        // List all users
        $this->info('');
        $this->info('👤 System Users:');
        $users = User::with('roles')->get();

        if ($users->count() === 0) {
            $this->warn('No users found in the system');
            return;
        }

        $headers = ['ID', 'Name', 'Email', 'Roles', 'Verified', 'Created'];
        $rows = [];

        foreach ($users as $user) {
            $roles = $user->roles->pluck('name')->join(', ') ?: 'No roles';
            $verified = $user->email_verified_at ? '✅ Yes' : '❌ No';
            $created = $user->created_at->format('M d, Y');

            $rows[] = [
                $user->id,
                $user->name,
                $user->email,
                $roles,
                $verified,
                $created
            ];
        }

        $this->table($headers, $rows);

        $this->info('');
        $this->info('🔐 Admin Panel: http://localhost:8000/admin');
    }
}
