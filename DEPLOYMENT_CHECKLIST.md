# ✅ Deployment Checklist - Quick Reference

## 🚀 Essential Commands to Run on Deployment

Copy and paste these commands in order:

```bash
# 1. Install dependencies
composer install --optimize-autoloader --no-dev

# 2. Generate application key
php artisan key:generate

# 3. Run database migrations
php artisan migrate --force

# 4. Seed admin user
php artisan db:seed --class=ProductionAdminSeeder

# 5. Seed presenter accounts (optional)
php artisan db:seed --class=PresenterSeeder

# 6. Create storage symlink
php artisan storage:link

# 7. Generate permissions and policies
php artisan shield:generate --all

# 8. Set file permissions
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/app/public

# 9. Clear and cache configuration
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 10. Generate optimized caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 11. Run verification tests
php artisan test:all-functionality
```

---

## 📝 Configuration Checklist

Before running the commands above, ensure:

- [ ] `.env` file is created and configured
- [ ] Database credentials are correct
- [ ] `APP_URL` is set to your domain
- [ ] `APP_TIMEZONE=Pacific/Tarawa` is set
- [ ] `APP_ENV=production` is set
- [ ] `APP_DEBUG=false` is set
- [ ] Database exists and is accessible
- [ ] MySQL service is running

---

## 🔑 Default Credentials

### Admin Panel
- **URL**: `https://your-domain.com/admin`
- **Email**: `admin@admin.com`
- **Password**: `password` (⚠️ Change immediately!)

### Presenter Dashboard
- **URL**: `https://your-domain.com/presenter/login`
- **Test Accounts**: Various accounts with `password` (⚠️ Change immediately!)

---

## ✨ Post-Deployment Tasks

After deployment, verify:

- [ ] Admin panel is accessible
- [ ] Presenter dashboard is accessible
- [ ] File uploads work correctly
- [ ] PDF export works correctly
- [ ] Activity logging is working
- [ ] All resources are visible in admin panel
- [ ] Database is properly seeded
- [ ] Storage symlink is created
- [ ] File permissions are correct
- [ ] HTTPS is enabled (if applicable)

---

## 🔐 Security Tasks

After deployment, complete:

- [ ] Change admin password
- [ ] Change all presenter passwords
- [ ] Enable HTTPS/SSL
- [ ] Configure firewall rules
- [ ] Set up automated backups
- [ ] Configure email notifications
- [ ] Review and adjust permissions
- [ ] Set up monitoring/logging

---

## 📊 Monitoring Commands

After deployment, use these for monitoring:

```bash
# View application logs
tail -f storage/logs/laravel.log

# Check recent activity logs
php artisan tinker
Spatie\Activitylog\Models\Activity::latest()->limit(20)->get();
exit

# Verify database connection
php artisan tinker
DB::connection()->getPdo();
exit

# Check storage space
du -sh storage/app/public

# List all users
php artisan tinker
App\Models\User::all(['id', 'name', 'email']);
exit
```

---

## 🆘 Emergency Commands

If something goes wrong:

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Regenerate autoloader
composer dump-autoload

# Reset permissions
php artisan shield:generate --all

# Rollback last migration
php artisan migrate:rollback

# Check application status
php artisan tinker
# Type: exit to quit
```

---

## 📞 Support

For issues, contact:
- **Email**: tetmkamatie@gmail.com
- **Organization**: BPA Radio Station

---

**Version**: 1.0.0 | **Last Updated**: October 31, 2025

