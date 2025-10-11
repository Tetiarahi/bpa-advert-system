# 📻 Program Resource for Radio Advertisement

## 📋 Overview

The Program resource has been created under the "Radio Advertisement" group to manage radio program sponsorships and advertisements. This resource handles different types of radio programs with customer assignments, scheduling, and staff management.

## ✨ Features Implemented

### **1. Database Structure**

-   ✅ **Customer Relationship**: Links to existing customers
-   ✅ **Customer Type**: Private, Local Business, GOK/NGO
-   ✅ **Radio Programs**: Nimaua Akea, News Sponsor, Karaki Sponsor, Live Sponsor
-   ✅ **Multi-Band Support**: AM/FM/Social Media with JSON storage
-   ✅ **Date Range**: Start and end dates for program duration
-   ✅ **Payment Tracking**: Payment status and amount
-   ✅ **Staff Assignment**: Responsible staff member
-   ✅ **File Attachments**: Support for documents and images

### **2. Form Interface**

```php
// Customer Information Section
- Customer selection (searchable dropdown)
- Customer type selection (Private/Local Business/GOK/NGO)

// Program Details Section
- Radio program selection (4 options)
- Broadcasting band checkboxes (AM/FM/Social Media multi-select)

// Schedule & Duration Section
- Start date picker
- End date picker (must be after start date)

// Payment & Staff Section
- Amount field with currency prefix
- Payment status toggle
- Staff assignment (searchable dropdown)
- File attachment upload
```

### **3. Table Display**

-   ✅ **Customer Column**: Searchable customer names
-   ✅ **Customer Type Badges**: Color-coded type indicators
-   ✅ **Program Badges**: Color-coded radio program types
-   ✅ **Band Display**: Shows single or multiple bands
-   ✅ **Date Columns**: Start and end dates
-   ✅ **Payment Icons**: Boolean payment status
-   ✅ **Amount**: Currency-formatted amounts
-   ✅ **Staff Column**: Responsible staff member

## 🎨 Visual Design

### **Navigation**

-   ✅ **Group**: "Radio Advertisement"
-   ✅ **Icon**: Radio icon (heroicon-o-radio)
-   ✅ **Sort Order**: 3 (after Advertisements and Gongs)

### **Color Coding**

```php
// Customer Type Colors
'Private' => 'gray'
'local_business' => 'warning' (orange)
'GOK_NGO' => 'success' (green)

// Radio Program Colors
'Nimaua Akea' => 'info' (blue)
'News Sponsor' => 'success' (green)
'Karaki Sponsor' => 'warning' (orange)
'Live Sponsor' => 'danger' (red)

// Band Colors
'AM' => 'info' (blue)
'FM' => 'warning' (orange)
Multiple bands => 'success' (green)
```

### **Form Sections**

1. **Customer Information** - Customer selection and type
2. **Program Details** - Program and band selection
3. **Schedule & Duration** - Date range selection
4. **Payment & Staff** - Financial and assignment details

## 🔧 Technical Implementation

### **Database Schema**

```sql
CREATE TABLE programs (
    id BIGINT PRIMARY KEY,
    customer_id BIGINT (foreign key to customers),
    customer_type ENUM('Private', 'local_business', 'GOK_NGO'),
    radio_program ENUM('Nimaua Akea', 'News Sponsor', 'Karaki Sponsor', 'Live Sponsor'),
    band JSON, -- Supports multiple bands
    publish_start_date DATE,
    publish_end_date DATE,
    payment_status BOOLEAN DEFAULT false,
    amount DECIMAL(15,2),
    attachment VARCHAR(255),
    staff_id BIGINT (foreign key to users),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### **Model Relationships**

```php
// Program.php
public function customer() {
    return $this->belongsTo(Customer::class);
}

public function staff() {
    return $this->belongsTo(User::class, 'staff_id');
}

// Customer.php (updated)
public function programs() {
    return $this->hasMany(Program::class);
}
```

### **Data Casting**

```php
protected $casts = [
    'publish_start_date' => 'date',
    'publish_end_date' => 'date',
    'payment_status' => 'boolean',
    'band' => 'array', // JSON array for multiple bands
];
```

## 📊 Filtering & Search

### **Available Filters**

-   ✅ **Customer Type Filter**: Filter by Private/Local Business/GOK/NGO
-   ✅ **Radio Program Filter**: Filter by specific program type
-   ✅ **Staff Filter**: Filter by assigned staff member (searchable)
-   ✅ **Payment Status Filter**: Filter by paid/unpaid status

### **Search Capabilities**

-   ✅ **Customer Search**: Search by customer name
-   ✅ **Staff Search**: Search by staff member name
-   ✅ **Sortable Columns**: All major columns are sortable

## 🎯 Radio Program Types

### **1. Nimaua Akea**

-   ✅ **Color**: Blue (info)
-   ✅ **Description**: Traditional program sponsorship
-   ✅ **Typical Duration**: 30 days
-   ✅ **Common Bands**: AM/FM

### **2. News Sponsor**

-   ✅ **Color**: Green (success)
-   ✅ **Description**: News program sponsorship
-   ✅ **Typical Duration**: 15-30 days
-   ✅ **Common Bands**: FM

### **3. Karaki Sponsor**

-   ✅ **Color**: Orange (warning)
-   ✅ **Description**: Karaki program sponsorship
-   ✅ **Typical Duration**: 14-21 days
-   ✅ **Common Bands**: AM/FM

### **4. Live Sponsor**

-   ✅ **Color**: Red (danger)
-   ✅ **Description**: Live program sponsorship
-   ✅ **Typical Duration**: 7-14 days
-   ✅ **Common Bands**: AM/FM

## 📄 View Page Features

### **Information Display**

-   ✅ **Customer Information**: Name, email, phone, type
-   ✅ **Program Details**: Program type, bands, dates
-   ✅ **Payment & Staff**: Amount, payment status, responsible staff
-   ✅ **Timestamps**: Creation and update times
-   ✅ **Attachment**: File download link if available

### **Actions Available**

-   ✅ **Edit**: Modify program details
-   ✅ **Delete**: Remove program record
-   ✅ **File Download**: Access attached files

## 🧪 Testing Results

### **Test Programs Created**

1. **Nimaua Akea** - AM band, $500, Paid
2. **News Sponsor** - FM band, $750, Unpaid
3. **Karaki Sponsor** - AM+FM bands, $1200, Paid
4. **Live Sponsor** - AM+FM bands, $2000, Unpaid

### **Functionality Verified**

-   ✅ **CRUD Operations**: Create, Read, Update, Delete
-   ✅ **Relationships**: Customer and staff relationships work
-   ✅ **Multi-Band Support**: JSON array storage and display
-   ✅ **Date Validation**: End date must be after start date
-   ✅ **File Uploads**: Attachment storage in public/programs
-   ✅ **Activity Logging**: All changes are logged

## 🚀 Usage

### **Creating a Program**

1. **Navigate** to Admin → Programs → Create
2. **Select Customer** from searchable dropdown
3. **Choose Customer Type** (Private/Local Business/GOK/NGO)
4. **Select Radio Program** (Nimaua Akea/News Sponsor/Karaki Sponsor/Live Sponsor)
5. **Choose Bands** (AM and/or FM)
6. **Set Dates** (start and end dates)
7. **Enter Amount** and set payment status
8. **Assign Staff** member
9. **Upload Attachment** (optional)
10. **Save** program

### **Managing Programs**

-   **View List**: See all programs with filters
-   **Search**: Find by customer or staff name
-   **Filter**: By customer type, program type, or payment status
-   **Edit**: Modify any program details
-   **View**: See complete program information

## 🎉 Success!

The Program resource is now fully functional with:

-   ✅ **Complete CRUD Interface**: Create, view, edit, delete programs
-   ✅ **Rich Form Sections**: Organized, user-friendly form layout
-   ✅ **Professional Table**: Color-coded, searchable, filterable
-   ✅ **Multi-Band Support**: Flexible AM/FM selection
-   ✅ **Staff Assignment**: Track responsible team members
-   ✅ **Payment Tracking**: Monitor payment status and amounts
-   ✅ **File Management**: Attachment upload and download
-   ✅ **Activity Logging**: Complete audit trail

The Program resource is now available under the "Radio Advertisement" group and ready for managing radio program sponsorships! 📻
