<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Presenter;
use Illuminate\Support\Facades\Hash;

class FixPresenterPasswords extends Command
{
    protected $signature = 'fix:presenter-passwords';
    protected $description = 'Fix all presenter passwords to use proper hashing';

    public function handle()
    {
        $this->info('🔧 Fixing Presenter Passwords...');
        
        $presenters = Presenter::all();
        $fixed = 0;
        
        foreach ($presenters as $presenter) {
            $presenter->update([
                'password' => Hash::make('password')
            ]);
            $fixed++;
            $this->info("✅ Fixed password for: {$presenter->email}");
        }
        
        $this->info("🎉 Fixed {$fixed} presenter passwords");
        return Command::SUCCESS;
    }
}
