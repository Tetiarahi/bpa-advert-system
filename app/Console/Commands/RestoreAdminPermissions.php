<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\AdminPermissionsSeeder;

class RestoreAdminPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:restore-permissions 
                            {--email=admin@admin.com : Admin email address}
                            {--password=password : Admin password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore all permissions and roles for the admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Restoring Admin Permissions and Roles...');
        $this->info('==========================================');

        try {
            // Run the admin permissions seeder
            $seeder = new AdminPermissionsSeeder();
            $seeder->setCommand($this);
            $seeder->run();

            $this->info('');
            $this->info('✅ Admin permissions restored successfully!');
            $this->info('');
            $this->info('🎯 Next Steps:');
            $this->info('1. Visit: http://localhost:8000/admin');
            $this->info('2. Login with: admin@admin.com / password');
            $this->info('3. Change the password after login');
            $this->info('4. Test all admin features');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Failed to restore admin permissions:');
            $this->error($e->getMessage());
            
            $this->info('');
            $this->info('🔧 Try these troubleshooting steps:');
            $this->info('1. php artisan migrate');
            $this->info('2. php artisan shield:generate --all');
            $this->info('3. php artisan admin:restore-permissions');

            return Command::FAILURE;
        }
    }
}
