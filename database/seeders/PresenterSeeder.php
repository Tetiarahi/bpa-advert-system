<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Presenter;
use Illuminate\Support\Facades\Hash;

class PresenterSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        $presenters = [
            [
                'name' => 'John Morning',
                'email' => 'john.morning@bpa.com',
                'password' => Hash::make('password'),
                'phone' => '+61 400 123 456',
                'shift' => 'morning',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Sarah Lunch',
                'email' => 'sarah.lunch@bpa.com',
                'password' => Hash::make('password'),
                'phone' => '+61 400 234 567',
                'shift' => 'lunch',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Mike Evening',
                'email' => 'mike.evening@bpa.com',
                'password' => Hash::make('password'),
                'phone' => '+61 400 345 678',
                'shift' => 'evening',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Lisa All-Shifts',
                'email' => 'lisa.allshifts@bpa.com',
                'password' => Hash::make('password'),
                'phone' => '+61 400 456 789',
                'shift' => 'all',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'David Inactive',
                'email' => 'david.inactive@bpa.com',
                'password' => Hash::make('password'),
                'phone' => '+61 400 567 890',
                'shift' => 'morning',
                'is_active' => false,
                'email_verified_at' => null,
            ],
        ];

        foreach ($presenters as $presenterData) {
            Presenter::updateOrCreate(
                ['email' => $presenterData['email']],
                $presenterData
            );
        }

        $this->command->info('✅ Sample presenters created successfully!');
        $this->command->info('📧 All presenter passwords are: password');
    }
}
