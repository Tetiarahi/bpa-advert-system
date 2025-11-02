# 🚀 BPA Advert Management System - Deployment Guide

## 📋 Pre-Deployment Checklist

Before deploying to production, ensure you have:
- ✅ PHP 8.1+ installed
- ✅ MySQL 5.7+ or MariaDB 10.3+ running
- ✅ Composer installed
- ✅ Node.js and NPM installed
- ✅ SSH access to your server
- ✅ Domain name configured (optional)

---

## 🔧 Step-by-Step Deployment Instructions

### **Step 1: Clone Repository**
```bash
cd /path/to/your/deployment/directory
git clone https://github.com/tetmkamatie/bpa-advert-system.git
cd bpa-advert-system
```

### **Step 2: Install Dependencies**
```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node dependencies (if needed for assets)
npm install
npm run build
```

### **Step 3: Environment Configuration**
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

**Edit `.env` file with your production settings:**
```env
APP_NAME="BPA Advert Management System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=your-database-host
DB_PORT=3306
DB_DATABASE=advert_management
DB_USERNAME=your-db-username
DB_PASSWORD=your-db-password

# Timezone (Kiribati)
APP_TIMEZONE=Pacific/Tarawa

# Mail Configuration (optional)
MAIL_MAILER=smtp
MAIL_HOST=your-mail-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
```

### **Step 4: Database Setup**
```bash
# Run all migrations
php artisan migrate --force

# Seed admin user
php artisan db:seed --class=ProductionAdminSeeder

# Seed presenter accounts (optional)
php artisan db:seed --class=PresenterSeeder
```

### **Step 5: Storage and Permissions**
```bash
# Create storage symlink
php artisan storage:link

# Generate permissions and policies
php artisan shield:generate --all

# Set proper file permissions
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/app/public
```

### **Step 6: Cache Optimization**
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Generate optimized caches for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **Step 7: Verify Installation**
```bash
# Run comprehensive tests
php artisan test:all-functionality

# Check if everything is working
php artisan tinker
# Type: exit to quit
```

---

## 🎯 Post-Deployment Verification

### **1. Check Admin Panel Access**
- Navigate to: `https://your-domain.com/admin`
- Login with: `admin@admin.com` / `password`
- Verify all resources are accessible

### **2. Check Presenter Dashboard**
- Navigate to: `https://your-domain.com/presenter/login`
- Login with test account: `john.morning@bpa.com` / `password`
- Verify content displays correctly

### **3. Test File Uploads**
- Create a new advertisement with file attachment
- Create a new gong with file attachment
- Verify files are stored and accessible

### **4. Test PDF Export**
- Go to an advertisement
- Click "Export to PDF"
- Verify PDF generates correctly with logo

### **5. Check Activity Logging**
- Go to Admin Panel → Activity Logs
- Verify login/logout activities are recorded
- Check IP addresses and session durations

---

## 🔐 Security Hardening

### **1. Update Admin Credentials**
```bash
php artisan tinker
# In tinker:
$user = App\Models\User::where('email', 'admin@admin.com')->first();
$user->update(['password' => bcrypt('your-new-secure-password')]);
exit
```

### **2. Update Presenter Passwords**
```bash
php artisan tinker
# In tinker:
App\Models\Presenter::all()->each(function($p) {
    $p->update(['password' => bcrypt('your-new-secure-password')]);
});
exit
```

### **3. Enable HTTPS**
- Configure SSL certificate (Let's Encrypt recommended)
- Update `APP_URL` in `.env` to use `https://`
- Configure web server to redirect HTTP to HTTPS

### **4. Set Up Firewall Rules**
- Restrict admin panel access to specific IPs (optional)
- Enable rate limiting on login endpoints
- Configure DDoS protection

---

## 📊 Maintenance Commands

### **Regular Backups**
```bash
# Backup database
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql

# Backup files
tar -czf storage_backup_$(date +%Y%m%d).tar.gz storage/app/public
```

### **Monitor Logs**
```bash
# View application logs
tail -f storage/logs/laravel.log

# View activity logs
php artisan tinker
# In tinker:
Spatie\Activitylog\Models\Activity::latest()->limit(50)->get();
exit
```

### **Clear Old Logs**
```bash
# Clear logs older than 14 days
php artisan logs:clear
```

### **Database Maintenance**
```bash
# Optimize database
php artisan db:optimize

# Check database integrity
php artisan db:check
```

---

## 🆘 Troubleshooting

### **Issue: "Storage link not found"**
```bash
php artisan storage:link
```

### **Issue: "Permission denied" errors**
```bash
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/app/public
```

### **Issue: "Class not found" errors**
```bash
composer dump-autoload
php artisan cache:clear
```

### **Issue: "Database connection failed"**
- Verify database credentials in `.env`
- Check MySQL service is running
- Verify database exists

### **Issue: "Logo not showing in PDF"**
- Ensure `public/images/bpa-logo.png` exists
- Check file permissions: `chmod 644 public/images/bpa-logo.png`
- Verify path in PDF templates

---

## 📞 Support & Contact

For issues or questions:
- **Developer**: Tetiarahi Mathew
- **Email**: tetmkamatie@gmail.com
- **Organization**: BPA Radio Station

---

**Last Updated**: October 31, 2025
**Version**: 1.0.0

