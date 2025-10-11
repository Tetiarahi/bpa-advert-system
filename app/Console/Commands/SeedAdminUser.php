<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\AdminPermissionsSeeder;

class SeedAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:seed
                            {--email=admin@admin.com : Admin email address}
                            {--password=password : Admin password}
                            {--name=Super Administrator : Admin name}
                            {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin user with all permissions (safe for production)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔐 Creating Admin User with All Permissions...');
        $this->info('===============================================');

        $email = $this->option('email');
        $password = $this->option('password');
        $name = $this->option('name');

        $this->info("📧 Email: {$email}");
        $this->info("👤 Name: {$name}");
        $this->info("🔑 Password: " . str_repeat('*', strlen($password)));
        $this->info('');

        if (!$this->option('force') && !$this->confirm('Do you want to proceed with creating/updating the admin user?')) {
            $this->info('❌ Operation cancelled.');
            return Command::FAILURE;
        }

        try {
            // Run the admin permissions seeder
            $seeder = new AdminPermissionsSeeder();
            $seeder->setCommand($this);
            $seeder->run();

            $this->info('');
            $this->info('✅ Admin user created/updated successfully!');
            $this->info('');
            $this->info('🎯 Next Steps:');
            $this->info("1. Visit: " . config('app.url') . "/admin");
            $this->info("2. Login with: {$email} / {$password}");
            $this->info('3. Change the password after login for security');
            $this->info('4. Test all admin features');
            $this->info('');
            $this->info('🔒 Security Note: Please change the default password immediately!');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Failed to create admin user: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());

            $this->info('');
            $this->info('🔧 Troubleshooting:');
            $this->info('1. Check database connection');
            $this->info('2. Run: php artisan migrate');
            $this->info('3. Check permissions table exists');
            $this->info('4. Try: php artisan permission:cache-reset');

            return Command::FAILURE;
        }
    }
}
