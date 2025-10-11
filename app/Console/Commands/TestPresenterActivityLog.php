<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Presenter;
use Spatie\Activitylog\Models\Activity;

class TestPresenterActivityLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:presenter-activity-log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test presenter activity logging functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Presenter Activity Log Functionality...');
        $this->info('================================================');

        // Find a test presenter
        $presenter = Presenter::where('email', 'john.morning@example.com')->first();

        if (!$presenter) {
            $this->error('❌ Test presenter not found. Please run: php artisan db:seed --class=PresenterSeeder');
            return Command::FAILURE;
        }

        $this->info("✅ Found test presenter: {$presenter->name} ({$presenter->email})");

        // Test manual activity logging
        $this->info('📝 Creating test login activity...');

        activity('presenter_auth')
            ->causedBy($presenter)
            ->performedOn($presenter)
            ->withProperties([
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Test Browser)',
                'login_time' => now()->toDateTimeString(),
                'shift' => $presenter->shift,
            ])
            ->log('Presenter logged in');

        $this->info('📝 Creating test logout activity...');

        activity('presenter_auth')
            ->causedBy($presenter)
            ->performedOn($presenter)
            ->withProperties([
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Test Browser)',
                'logout_time' => now()->toDateTimeString(),
                'session_duration_minutes' => 45,
                'shift' => $presenter->shift,
            ])
            ->log('Presenter logged out');

        // Test presenter model changes
        $this->info('📝 Creating test presenter update activity...');

        $presenter->update(['phone' => '123-456-7890']);

        // Check recent activities
        $recentActivities = Activity::where('log_name', 'presenter_auth')
            ->orWhere('log_name', 'presenter')
            ->latest()
            ->take(5)
            ->get();

        $this->info('');
        $this->info('📊 Recent Presenter Activities:');
        $this->info('================================');

        foreach ($recentActivities as $activity) {
            $this->info("🔸 {$activity->log_name}: {$activity->description}");
            $causerName = $activity->causer ? $activity->causer->name : 'System';
            $this->info("   👤 User: {$causerName}");
            $this->info("   📅 Time: {$activity->created_at->format('Y-m-d H:i:s')}");

            if ($activity->log_name === 'presenter_auth') {
                $props = $activity->properties;
                $ipAddress = isset($props['ip_address']) ? $props['ip_address'] : 'N/A';
                $shift = isset($props['shift']) ? $props['shift'] : 'N/A';
                $this->info("   🌐 IP: {$ipAddress}");
                $this->info("   ⏰ Shift: {$shift}");
                if (isset($props['session_duration_minutes'])) {
                    $this->info("   ⏱️  Duration: {$props['session_duration_minutes']} minutes");
                }
            }
            $this->info('');
        }

        $this->info('🎉 Activity logging test completed successfully!');
        $this->info('');
        $this->info('🔍 To view activities in admin panel:');
        $this->info('   1. Visit: http://localhost:8000/admin');
        $this->info('   2. Login: admin@admin.com / password');
        $this->info('   3. Go to: Activity Logs');
        $this->info('   4. Filter by: "Presenter Authentication"');

        return Command::SUCCESS;
    }
}
