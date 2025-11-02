# ⚡ Quick Commands Reference

## 🚀 Deployment Commands (Run in Order)

```bash
# 1. Install dependencies
composer install --optimize-autoloader --no-dev

# 2. Generate key
php artisan key:generate

# 3. Run migrations
php artisan migrate --force

# 4. Seed admin
php artisan db:seed --class=ProductionAdminSeeder

# 5. Seed presenters (optional)
php artisan db:seed --class=PresenterSeeder

# 6. Create storage link
php artisan storage:link

# 7. Generate permissions
php artisan shield:generate --all

# 8. Set permissions
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/app/public

# 9. Clear caches
php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear

# 10. Generate optimized caches
php artisan config:cache && php artisan route:cache && php artisan view:cache

# 11. Verify installation
php artisan test:all-functionality
```

---

## 🔧 Maintenance Commands

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Regenerate autoloader
composer dump-autoload

# Optimize database
php artisan db:optimize

# Clear old logs
php artisan logs:clear

# Check application status
php artisan tinker
# Type: exit to quit
```

---

## 👤 User Management Commands

```bash
# Change admin password
php artisan tinker
$user = App\Models\User::where('email', 'admin@admin.com')->first();
$user->update(['password' => bcrypt('new-password')]);
exit

# Change all presenter passwords
php artisan tinker
App\Models\Presenter::all()->each(function($p) {
    $p->update(['password' => bcrypt('new-password')]);
});
exit

# List all users
php artisan tinker
App\Models\User::all(['id', 'name', 'email']);
exit

# List all presenters
php artisan tinker
App\Models\Presenter::all(['id', 'name', 'email']);
exit
```

---

## 📊 Monitoring Commands

```bash
# View application logs
tail -f storage/logs/laravel.log

# View recent activity logs
php artisan tinker
Spatie\Activitylog\Models\Activity::latest()->limit(50)->get();
exit

# Check database connection
php artisan tinker
DB::connection()->getPdo();
exit

# Count records
php artisan tinker
echo "Users: " . App\Models\User::count() . "\n";
echo "Presenters: " . App\Models\Presenter::count() . "\n";
echo "Advertisements: " . App\Models\Advertisement::count() . "\n";
echo "Gongs: " . App\Models\Gong::count() . "\n";
echo "Customers: " . App\Models\Customer::count() . "\n";
exit
```

---

## 🔄 Database Commands

```bash
# Run migrations
php artisan migrate

# Run migrations with force (production)
php artisan migrate --force

# Rollback last migration
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset

# Refresh database (warning: deletes all data)
php artisan migrate:refresh --seed

# Check migration status
php artisan migrate:status

# Seed database
php artisan db:seed

# Seed specific seeder
php artisan db:seed --class=ProductionAdminSeeder
```

---

## 📁 File & Storage Commands

```bash
# Create storage symlink
php artisan storage:link

# Remove storage symlink
rm public/storage

# Check storage space
du -sh storage/app/public

# List uploaded files
ls -la storage/app/public/ads/
ls -la storage/app/public/gongs/

# Clear storage
rm -rf storage/app/public/ads/*
rm -rf storage/app/public/gongs/*
```

---

## 🔐 Security Commands

```bash
# Generate permissions and policies
php artisan shield:generate --all

# Restore admin permissions
php artisan restore:admin-permissions

# Fix presenter passwords
php artisan fix:presenter-passwords

# Create presenter account
php artisan create:presenter-account
```

---

## 🧪 Testing Commands

```bash
# Run all functionality tests
php artisan test:all-functionality

# Run Laravel tests
php artisan test

# Run specific test
php artisan test tests/Feature/YourTest.php
```

---

## 🚀 Development Commands

```bash
# Start development server
php artisan serve

# Start with specific port
php artisan serve --port=8001

# Build assets
npm run build

# Watch assets
npm run dev

# Tinker (interactive shell)
php artisan tinker
```

---

## 📋 Useful Artisan Commands

```bash
# List all commands
php artisan list

# Get help for command
php artisan help migrate

# Check Laravel version
php artisan --version

# Show application info
php artisan about

# Generate application key
php artisan key:generate

# Publish vendor assets
php artisan vendor:publish
```

---

## 🆘 Emergency Commands

```bash
# Clear everything
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload

# Reset permissions
php artisan shield:generate --all

# Check application health
php artisan tinker
DB::connection()->getPdo();
exit

# View recent errors
tail -f storage/logs/laravel.log
```

---

## 📝 Notes

-   Replace `new-password` with your actual password
-   Use `--force` flag for production commands
-   Always backup database before running destructive commands
-   Test commands in development first
-   Monitor logs after deployment

---

**Last Updated**: October 31, 2025
