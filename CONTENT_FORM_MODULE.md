# Content Form Module Documentation

## Overview

The Content Form Module is a comprehensive tracking system that summarizes all activity and information from both backend and frontend for advertisements and gongs. It automatically captures metadata when content is created and tracks presenter interactions with timestamps.

## Features

### 1. **Automatic Content Metadata Capture**
- **Creation Date/Time**: Automatically recorded when advertisement or gong is created
- **Source Tracking**: Records whether content was received via Mail or Hand
- **Word Count**: Automatically calculates the number of words in content
- **Amount**: Captures the financial amount from the advertisement/gong
- **Broadcast Information**: Stores band, broadcast dates, and broadcast days
- **Shift Frequencies**: Records how many times content should be broadcast in each shift

### 2. **Presenter Interaction Tracking**
- **Tick/Untick Logging**: Records every time a presenter marks content as read/unread
- **Timestamp Recording**: Captures exact time of each interaction
- **Shift-Specific Tracking**: Separate tracking for Morning, Lunch, and Evening shifts
- **Reading Count**: Tracks how many times content has been read in each shift
- **Completion Status**: Automatically marks content as completed when all required readings are done

### 3. **Database Schema**

#### content_forms Table
```
- id: Primary key
- content_type: 'advertisement' or 'gong'
- content_id: Reference to the content
- customer_id: Reference to customer
- title: Content title or departed name
- content_summary: First 500 characters of content
- word_count: Total words in content
- source: 'mail' or 'hand'
- received_date: When content was received
- amount: Financial amount
- is_paid: Payment status
- band: JSON array of bands (AM, FM, etc.)
- broadcast_start_date: Start date of broadcast
- broadcast_end_date: End date of broadcast
- broadcast_days: JSON array of broadcast days
- morning_frequency: Required readings in morning
- lunch_frequency: Required readings in lunch
- evening_frequency: Required readings in evening
- morning_ticked_at: Last tick timestamp for morning
- lunch_ticked_at: Last tick timestamp for lunch
- evening_ticked_at: Last tick timestamp for evening
- morning_tick_count: Number of ticks in morning
- lunch_tick_count: Number of ticks in lunch
- evening_tick_count: Number of ticks in evening
- presenter_id: Assigned presenter (optional)
- presenter_shift: Assigned shift (optional)
- is_completed: Completion status
- completed_at: When all readings were completed
- created_at, updated_at: Timestamps
```

#### content_form_logs Table
```
- id: Primary key
- content_form_id: Reference to ContentForm
- presenter_id: Reference to Presenter
- action: 'tick' or 'untick'
- time_slot: 'morning', 'lunch', or 'evening'
- action_at: Exact timestamp of action
- ip_address: Presenter's IP address
- user_agent: Browser/device information
- reading_number: Which reading number (1st, 2nd, etc.)
- notes: Additional notes
- created_at, updated_at: Timestamps
```

## Models

### ContentForm Model
Located at: `app/Models/ContentForm.php`

**Key Methods:**
- `getReadingProgressAttribute()`: Returns percentage of readings completed
- `getStatusAttribute()`: Returns status (Not Started, In Progress, Completed)
- `content()`: Polymorphic relationship to Advertisement or Gong
- `logs()`: HasMany relationship to ContentFormLog

### ContentFormLog Model
Located at: `app/Models/ContentFormLog.php`

**Key Methods:**
- `scopeByTimeSlot()`: Filter logs by time slot
- `scopeByAction()`: Filter logs by action (tick/untick)
- `scopeByPresenter()`: Filter logs by presenter
- `scopeRecent()`: Filter recent logs

## Controllers

### ContentFormController
Located at: `app/Http/Controllers/ContentFormController.php`

**Key Methods:**
- `createFromContent()`: Creates ContentForm from Advertisement/Gong
- `tick()`: Records tick action and updates counts
- `untick()`: Records untick action and updates counts
- `show()`: Returns ContentForm details with logs
- `getPresenterForms()`: Returns all forms for current presenter

## API Routes

All routes are protected by `presenter.auth` middleware:

```
POST   /presenter/content-form/tick          - Record tick action
POST   /presenter/content-form/untick        - Record untick action
GET    /presenter/content-form/{id}          - Get form details
GET    /presenter/content-forms              - Get all presenter forms
```

## Observers

### AdvertisementObserver
- **created**: Automatically creates ContentForm when advertisement is created
- **updated**: Updates ContentForm when advertisement is modified
- **deleted**: Deletes associated ContentForm when advertisement is deleted

### GongObserver
- **created**: Automatically creates ContentForm when gong is created
- **updated**: Updates ContentForm when gong is modified
- **deleted**: Deletes associated ContentForm when gong is deleted

## Frontend Integration

### JavaScript Module
Located at: `public/js/content-form.js`

**Class: ContentFormManager**
- Handles tick/untick button clicks
- Sends API requests to backend
- Updates UI with response data
- Manages ContentForm lifecycle

### Usage in Dashboard
The module is automatically initialized when the presenter dashboard loads. It intercepts reading button clicks and logs them to the ContentForm system.

## Admin Panel

### Filament Resource
Located at: `app/Filament/Resources/ContentFormResource.php`

**Features:**
- View all content forms with filtering
- Filter by content type (Advertisement/Gong)
- Filter by source (Mail/Hand)
- Filter by completion status
- View detailed form information
- View associated logs

## Testing

Run the test command to verify the module:
```bash
php artisan app:test-content-form-module
```

This will test:
1. ContentForm creation
2. Data integrity
3. Tick/untick functionality
4. Log creation
5. Reading progress tracking

## Migration

The module includes automatic population of existing advertisements and gongs:
```bash
php artisan migrate
```

This will:
1. Create content_forms table
2. Create content_form_logs table
3. Populate existing advertisements and gongs with ContentForm entries

## Usage Example

### For Presenters
1. Login to presenter dashboard
2. View content for current time slot
3. Click reading buttons to mark content as read
4. System automatically records tick/untick with timestamp
5. View progress in the UI

### For Admins
1. Navigate to Content Forms in admin panel
2. View all tracked content with metadata
3. Filter by type, source, or completion status
4. Click on a form to view detailed logs
5. See all presenter interactions with timestamps

## Data Flow

```
Advertisement/Gong Created
    ↓
Observer Triggered
    ↓
ContentForm Created with Metadata
    ↓
Presenter Views Dashboard
    ↓
Presenter Clicks Reading Button
    ↓
ContentFormManager Intercepts Click
    ↓
API Request Sent (tick/untick)
    ↓
ContentFormLog Created
    ↓
ContentForm Updated with Counts
    ↓
UI Updated with Response
```

## Performance Considerations

- ContentForm entries are created automatically via observers
- Logs are indexed by content_form_id and presenter_id for fast queries
- Timestamps are recorded at the database level for accuracy
- Queries are optimized with proper indexing

## Future Enhancements

- Export ContentForm data to CSV/Excel
- Generate reports on presenter performance
- Analytics dashboard for content tracking
- Bulk operations on ContentForms
- Advanced filtering and search capabilities

