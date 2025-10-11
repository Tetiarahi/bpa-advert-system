<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Presenter;

class CreatePresenterAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'presenter:create {--name=} {--email=} {--password=} {--phone=} {--shift=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a presenter account for the separate presenter login system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎙️ Creating Presenter Account for Separate Login System');
        $this->info('=====================================================');

        // Get presenter details
        $name = $this->option('name') ?: $this->ask('Enter presenter name', 'New Presenter');
        $email = $this->option('email') ?: $this->ask('Enter presenter email', 'presenter@bpa.com');
        $password = $this->option('password') ?: $this->secret('Enter password (default: presenter123)') ?: 'presenter123';
        $phone = $this->option('phone') ?: $this->ask('Enter phone number', '+1234567890');
        $shift = $this->option('shift') ?: $this->choice('Select shift', ['morning', 'lunch', 'evening', 'all'], 'all');

        // Check if presenter already exists
        $existingPresenter = Presenter::where('email', $email)->first();

        if ($existingPresenter) {
            $this->warn("⚠️  Presenter with email {$email} already exists");

            if ($this->confirm('Do you want to update this presenter account?')) {
                $existingPresenter->update([
                    'name' => $name,
                    'phone' => $phone,
                    'shift' => $shift,
                    'is_active' => true,
                ]);
                $this->info('✅ Presenter account updated');
            }

            return;
        }

        // Create new presenter
        $presenter = Presenter::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
            'phone' => $phone,
            'shift' => $shift,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->info('✅ Presenter account created successfully!');
        $this->info('');
        $this->info('📋 Account Details:');
        $this->info("   Name: {$name}");
        $this->info("   Email: {$email}");
        $this->info("   Password: {$password}");
        $this->info("   Phone: {$phone}");
        $this->info("   Shift: {$shift}");
        $this->info("   Status: Active");
        $this->info('');
        $this->info('🔐 Presenter Login URL: http://localhost:8000/presenter/login');
        $this->info('🔐 Dashboard URL: http://localhost:8000/presenter/dashboard');
        $this->info('');
        $this->warn('⚠️  Please change the password after first login!');
        $this->warn('⚠️  This account can ONLY login at /presenter/login, NOT /admin');
    }
}
