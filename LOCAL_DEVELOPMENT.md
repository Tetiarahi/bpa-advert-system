# 🏠 BPA Advert Management System - Local Development

## 🚀 Quick Start

### **Prerequisites**
- ✅ **PHP 8.1+** installed
- ✅ **Composer** installed
- ✅ **Node.js & NPM** (optional, for frontend assets)

### **1. Install Dependencies**
```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies (optional)
npm install
```

### **2. Environment Setup**
```bash
# Copy environment file (already configured for local)
# .env is already set up for SQLite local development

# Generate application key if needed
php artisan key:generate
```

### **3. Database Setup**
```bash
# Run migrations
php artisan migrate

# Seed the database (optional)
php artisan db:seed
```

### **4. Create Admin User**
```bash
# Create admin user for Filament
php artisan make:filament-user
```

### **5. Start Development Server**
```bash
# Start Laravel development server
php artisan serve

# Your app will be available at:
# http://localhost:8000
```

## 🎯 Access Points

### **Main Application**
- **Home**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/admin

### **Development Tools**
- **Telescope** (if installed): http://localhost:8000/telescope
- **Horizon** (if installed): http://localhost:8000/horizon

## 🔧 Local Configuration

### **Database**
- **Type**: SQLite (file-based, no server needed)
- **Location**: `database/database.sqlite`
- **Automatic**: Created when you run migrations

### **Environment**
- **APP_ENV**: local
- **APP_DEBUG**: true
- **APP_URL**: http://localhost:8000

### **Features Enabled**
- ✅ **Customer Management**
- ✅ **Advertisement Management**
- ✅ **Gong Memorial Management**
- ✅ **Category Management**
- ✅ **Activity Logging**
- ✅ **Revenue Tracking**
- ✅ **Dashboard Widgets**

## 🛠️ Development Commands

### **Database**
```bash
# Fresh migration (reset database)
php artisan migrate:fresh

# Fresh migration with seeding
php artisan migrate:fresh --seed

# Create new migration
php artisan make:migration create_example_table
```

### **Filament**
```bash
# Create new Filament resource
php artisan make:filament-resource ExampleResource

# Create new Filament page
php artisan make:filament-page ExamplePage

# Create new Filament widget
php artisan make:filament-widget ExampleWidget
```

### **Cache & Optimization**
```bash
# Clear all caches
php artisan optimize:clear

# Cache for better performance
php artisan optimize

# Clear specific caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 📁 Project Structure

```
BPA-Advert-Management-System/
├── 📁 app/
│   ├── 📁 Filament/
│   │   ├── 📁 Resources/     # Admin panel resources
│   │   └── 📁 Widgets/       # Dashboard widgets
│   └── 📁 Models/            # Eloquent models
├── 📁 database/
│   ├── 📁 migrations/        # Database migrations
│   └── 📁 seeders/          # Database seeders
├── 📁 public/               # Web-accessible files
├── 📁 resources/
│   └── 📁 views/            # Blade templates
├── 📁 routes/               # Route definitions
├── 📄 .env                  # Environment configuration
└── 📄 artisan              # Laravel command-line tool
```

## 🎨 Customization

### **Adding New Features**
1. **Create Model**: `php artisan make:model ExampleModel -m`
2. **Create Resource**: `php artisan make:filament-resource ExampleResource`
3. **Run Migration**: `php artisan migrate`

### **Modifying Existing Features**
- **Models**: Located in `app/Models/`
- **Resources**: Located in `app/Filament/Resources/`
- **Widgets**: Located in `app/Filament/Widgets/`

## 🧪 Testing

### **Run Tests**
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter ExampleTest
```

### **Create Tests**
```bash
# Create feature test
php artisan make:test ExampleFeatureTest

# Create unit test
php artisan make:test ExampleUnitTest --unit
```

## 🔍 Debugging

### **Enable Debug Mode**
- Already enabled in `.env` with `APP_DEBUG=true`

### **View Logs**
- **Location**: `storage/logs/laravel.log`
- **Real-time**: `tail -f storage/logs/laravel.log`

### **Database Queries**
- Use Laravel Telescope for query debugging
- Enable query logging in your code

## 📦 Building for Production

### **Optimize for Production**
```bash
# Install production dependencies only
composer install --optimize-autoloader --no-dev

# Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build frontend assets
npm run build
```

## 🆘 Troubleshooting

### **Common Issues**

**1. Permission Errors**
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
```

**2. Database Issues**
```bash
# Reset database
php artisan migrate:fresh --seed
```

**3. Cache Issues**
```bash
# Clear all caches
php artisan optimize:clear
```

**4. Composer Issues**
```bash
# Update dependencies
composer update

# Dump autoload
composer dump-autoload
```

## 🎉 You're Ready!

Your BPA Advert Management System is now set up for local development:

1. **Run**: `php artisan serve`
2. **Visit**: http://localhost:8000
3. **Admin**: http://localhost:8000/admin
4. **Start coding!** 🚀

Happy coding! 🎯
