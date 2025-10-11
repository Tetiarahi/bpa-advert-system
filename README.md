# 📻 BPA Advert Management System

A comprehensive advertisement management system built with Laravel and Filament for BPA Radio Station in Kiribati.

## ✨ Features

### 🎯 Core Functionality
- **Advertisement Management**: Create, edit, and manage radio advertisements with time-specific broadcasting
- **Customer Management**: Comprehensive customer database with contact information
- **Gong Management**: Handle memorial announcements and tributes with file attachments
- **Program Management**: Manage radio programs and scheduling
- **Multi-band Support**: Handle both AM and FM broadcasts with flexible scheduling

### 👥 User Management
- **Admin Panel**: Full-featured Filament admin interface with role-based permissions
- **Presenter Dashboard**: Dedicated interface for radio presenters with real-time content
- **Activity Logging**: Comprehensive tracking of all system activities and user actions
- **Authentication**: Secure login/logout with session tracking

### 📊 Advanced Features
- **PDF Export**: Generate professional PDF reports for advertisements and gongs
- **File Attachments**: Upload and manage images and documents
- **Time-specific Broadcasting**: Schedule content for Morning, Lunch, and Evening slots
- **Reading Tracking**: Track presenter reading activities with detailed analytics
- **Responsive Design**: Mobile-friendly interface for all user types

### 🔒 Security & Monitoring
- **Role-based Permissions**: Granular access control using Spatie Laravel Permission
- **Activity Logging**: Track login/logout times, session duration, and IP addresses
- **Audit Trail**: Complete history of all data changes and user activities
- **Secure File Storage**: Organized file management with proper access controls

## 🛠️ Technology Stack

- **Backend**: Laravel 11.x
- **Admin Panel**: Filament 3.x
- **Database**: MySQL 8.0+
- **PDF Generation**: DomPDF
- **Authentication**: Laravel Sanctum
- **Permissions**: Spatie Laravel Permission
- **Activity Logging**: Spatie Laravel Activitylog
- **File Storage**: Laravel Storage with public disk
- **Frontend**: Blade templates with Tailwind CSS

## 🚀 Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL 5.7+ or MariaDB 10.3+
- Node.js and NPM (for asset compilation)

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/tetmkamatie/BPA-Advert-Management-System.git
   cd BPA-Advert-Management-System
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database in `.env`**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=advert_management
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   
   APP_TIMEZONE=Pacific/Tarawa
   ```

5. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed --class=ProductionAdminSeeder
   php artisan db:seed --class=PresenterSeeder
   ```

6. **Storage and permissions**
   ```bash
   php artisan storage:link
   php artisan shield:generate --all
   ```

7. **Start development server**
   ```bash
   php artisan serve
   ```

## 🔑 Default Access Credentials

### Admin Panel
- **URL**: `http://localhost:8000/admin`
- **Email**: `admin@admin.com`
- **Password**: `password`
- **Permissions**: Full system access

### Presenter Dashboard
- **URL**: `http://localhost:8000/presenter/login`
- **Test Accounts**:
  - Morning Shift: `john.morning@example.com` / `password`
  - Lunch Shift: `sarah.lunch@example.com` / `password`
  - Evening Shift: `mike.evening@example.com` / `password`

## 📁 Project Structure

```
app/
├── Filament/Resources/          # Admin panel resources
├── Http/Controllers/            # Application controllers
├── Models/                      # Eloquent models
├── Policies/                    # Authorization policies
├── Console/Commands/            # Artisan commands
└── Services/                    # Business logic services

resources/
├── views/presenter/             # Presenter dashboard views
└── views/pdf/                   # PDF templates

database/
├── migrations/                  # Database migrations
└── seeders/                     # Database seeders

public/
├── images/                      # Static images (logos, etc.)
└── storage/                     # Symlink to storage/app/public
```

## 🎯 Key Features Breakdown

### Advertisement Management
- Create and manage radio advertisements
- Time-specific frequency settings (Morning, Lunch, Evening)
- Multi-band broadcasting (AM/FM)
- Customer association and billing
- PDF export functionality

### Presenter Dashboard
- Real-time content display based on current time slot
- Mark advertisements and gongs as read
- Track reading progress and frequency
- Responsive design for various devices

### Activity Logging
- User login/logout tracking with session duration
- IP address and browser information
- Complete audit trail of all data changes
- Presenter activity monitoring

### File Management
- Secure file uploads for advertisements and gongs
- Organized storage in dedicated directories
- Support for images and PDF documents
- Public access URLs for uploaded files

## 🔧 Configuration

### Timezone Settings
The system is configured for Kiribati timezone (`Pacific/Tarawa`). Update in `.env`:
```env
APP_TIMEZONE=Pacific/Tarawa
```

### File Storage
Files are stored in `storage/app/public/` with organized subdirectories:
- `ads/` - Advertisement attachments
- `gongs/` - Memorial attachments

### Broadcasting Schedule
- **Morning**: 6:00 AM - 8:00 AM
- **Lunch**: 12:00 PM - 2:00 PM  
- **Evening**: 6:00 PM - 8:00 PM

## 🧪 Testing

### Run Test Commands
```bash
# Test presenter activity logging
php artisan test:presenter-activity-log

# Test gong attachment functionality
php artisan test:gong-attachment

# Test admin access and permissions
php artisan admin:test
```

### Manual Testing
1. Access admin panel and create test data
2. Login to presenter dashboard and interact with content
3. Check activity logs for proper tracking
4. Test file uploads in gong and advertisement forms

## 📈 Monitoring & Analytics

### Activity Logs
- Access via Admin Panel → Activity Logs
- Filter by log type (presenter_auth, presenter, advertisement, etc.)
- View detailed session information and user activities

### Presenter Analytics
- Track reading frequencies and completion rates
- Monitor login patterns and session durations
- Generate reports on presenter activity

## 🔒 Security Features

- Role-based access control with granular permissions
- Session management with automatic timeout
- IP address tracking for security monitoring
- Secure file upload with type validation
- CSRF protection on all forms
- SQL injection prevention through Eloquent ORM

## 🤝 Contributing

This is a proprietary system developed specifically for BPA Radio Station. For feature requests or bug reports, please contact the development team.

## 📄 License

This project is proprietary software developed for BPA Radio Station, Kiribati. All rights reserved.

## 📞 Support

For technical support or questions about the system, please contact:
- **Developer**: Tetiarahi Mathew
- **Email**: tetmkamatie@gmail.com
- **Organization**: BPA Radio Station

---

**Built with ❤️ for BPA Radio Station, Kiribati** 🇰🇮
