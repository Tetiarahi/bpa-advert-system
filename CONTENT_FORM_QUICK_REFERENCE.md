# Content Form Module - Quick Reference Guide

## 🚀 Quick Start

### Installation
```bash
# Run migrations
php artisan migrate

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Test installation
php artisan app:test-content-form-module
```

### Verify Installation
```bash
# Check routes
php artisan route:list --name=content-form

# Check database
php artisan tinker
>>> App\Models\ContentForm::count()  # Should return 27
>>> App\Models\ContentFormLog::count()  # Should return 2+
```

---

## 📊 Database Schema Quick Reference

### content_forms Table
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| content_type | string | 'advertisement' or 'gong' |
| content_id | bigint | Reference to content |
| customer_id | bigint | Reference to customer |
| title | string | Content title |
| word_count | int | Number of words |
| source | string | 'mail' or 'hand' |
| amount | decimal | Financial amount |
| morning_frequency | int | Required morning readings |
| lunch_frequency | int | Required lunch readings |
| evening_frequency | int | Required evening readings |
| morning_tick_count | int | Morning ticks done |
| lunch_tick_count | int | Lunch ticks done |
| evening_tick_count | int | Evening ticks done |
| morning_ticked_at | timestamp | Last morning tick |
| lunch_ticked_at | timestamp | Last lunch tick |
| evening_ticked_at | timestamp | Last evening tick |
| is_completed | boolean | Completion status |
| completed_at | timestamp | When completed |

### content_form_logs Table
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| content_form_id | bigint | Reference to form |
| presenter_id | bigint | Reference to presenter |
| action | string | 'tick' or 'untick' |
| time_slot | string | 'morning', 'lunch', 'evening' |
| action_at | timestamp | When action occurred |
| ip_address | string | Presenter's IP |
| user_agent | string | Browser info |
| reading_number | int | Which reading |

---

## 🔌 API Endpoints

### Tick Action
```
POST /presenter/content-form/tick
Content-Type: application/json
X-CSRF-TOKEN: {token}

{
  "content_form_id": 1,
  "time_slot": "morning"
}

Response:
{
  "success": true,
  "message": "Content marked as read",
  "progress": "1/7",
  "is_completed": false
}
```

### Untick Action
```
POST /presenter/content-form/untick
Content-Type: application/json
X-CSRF-TOKEN: {token}

{
  "content_form_id": 1,
  "time_slot": "morning"
}

Response:
{
  "success": true,
  "message": "Content unmarked",
  "progress": "0/7",
  "is_completed": false
}
```

### Get Form Details
```
GET /presenter/content-form/1
X-CSRF-TOKEN: {token}

Response:
{
  "id": 1,
  "title": "...",
  "word_count": 100,
  "amount": 500,
  "source": "mail",
  "morning_frequency": 7,
  "morning_tick_count": 3,
  "morning_ticked_at": "2025-11-03 10:30:00",
  "logs": [...]
}
```

### Get All Forms
```
GET /presenter/content-forms
X-CSRF-TOKEN: {token}

Response:
{
  "data": [
    {
      "id": 1,
      "title": "...",
      "content_type": "advertisement",
      ...
    },
    ...
  ]
}
```

---

## 🎯 Model Methods

### ContentForm Model
```php
// Get reading progress
$form->reading_progress  // Returns percentage

// Get status
$form->status  // Returns 'Not Started', 'In Progress', 'Completed'

// Get content
$form->content  // Returns Advertisement or Gong

// Get logs
$form->logs  // Returns ContentFormLog collection

// Get customer
$form->customer  // Returns Customer

// Get presenter
$form->presenter  // Returns Presenter
```

### ContentFormLog Model
```php
// Filter by time slot
ContentFormLog::byTimeSlot('morning')->get()

// Filter by action
ContentFormLog::byAction('tick')->get()

// Filter by presenter
ContentFormLog::byPresenter($presenterId)->get()

// Get recent logs
ContentFormLog::recent()->get()
```

---

## 🛠️ Common Tasks

### Get all forms for a presenter
```php
$presenter = Presenter::find($id);
$forms = ContentForm::where('presenter_id', $presenter->id)->get();
```

### Get completed forms
```php
$completed = ContentForm::where('is_completed', true)->get();
```

### Get forms by source
```php
$mailForms = ContentForm::where('source', 'mail')->get();
$handForms = ContentForm::where('source', 'hand')->get();
```

### Get forms by type
```php
$ads = ContentForm::where('content_type', 'advertisement')->get();
$gongs = ContentForm::where('content_type', 'gong')->get();
```

### Get in-progress forms
```php
$inProgress = ContentForm::where('is_completed', false)
    ->where(function ($query) {
        $query->where('morning_tick_count', '>', 0)
            ->orWhere('lunch_tick_count', '>', 0)
            ->orWhere('evening_tick_count', '>', 0);
    })
    ->get();
```

### Get logs for a form
```php
$form = ContentForm::find($id);
$logs = $form->logs()->orderBy('action_at', 'desc')->get();
```

---

## 📱 Frontend Integration

### JavaScript Usage
```javascript
// ContentFormManager is auto-initialized
window.contentFormManager

// Get form details
window.contentFormManager.getContentFormDetails(formId)

// Get all presenter forms
window.contentFormManager.getPresenterContentForms()

// Manual tick
window.contentFormManager.sendTickUntickRequest(formId, 'morning', 'tick')

// Manual untick
window.contentFormManager.sendTickUntickRequest(formId, 'morning', 'untick')
```

---

## 🔍 Troubleshooting

### ContentForms not created
```bash
php artisan migrate --path=database/migrations/2025_11_03_122451_populate_content_forms_from_existing_content.php
```

### Routes not found
```bash
php artisan route:clear
php artisan route:cache
```

### JavaScript not working
1. Check browser console (F12)
2. Verify `public/js/content-form.js` exists
3. Clear browser cache (Ctrl+Shift+Delete)
4. Check Network tab for 404 errors

### API returns 403
1. Verify presenter is logged in
2. Check CSRF token is valid
3. Verify presenter.auth middleware

### Tick/untick not recording
1. Check ContentFormLog table exists
2. Verify presenter_id is set
3. Check database permissions

---

## 📚 Documentation Files

| File | Purpose |
|------|---------|
| CONTENT_FORM_MODULE.md | Complete documentation |
| CONTENT_FORM_IMPLEMENTATION_SUMMARY.md | Implementation details |
| CONTENT_FORM_DEPLOYMENT_CHECKLIST.md | Deployment guide |
| CONTENT_FORM_FINAL_SUMMARY.md | Project summary |
| CONTENT_FORM_QUICK_REFERENCE.md | This file |

---

## ✅ Testing

```bash
# Run comprehensive tests
php artisan app:test-content-form-module

# Expected output:
# ✅ Total ContentForms: 27
# ✅ Tick/Untick Functionality: Working
# ✅ ContentFormLogs: Working
# ✅ Reading Progress: Tracking
```

---

## 🎊 Status: READY FOR PRODUCTION

All features implemented and tested. Ready for deployment.

**Last Updated:** 2025-11-03

