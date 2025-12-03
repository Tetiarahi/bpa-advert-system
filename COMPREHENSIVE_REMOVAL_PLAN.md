# Comprehensive Frontend & Presenter Removal Plan

## Overview
Complete removal of:
1. **Frontend UI** (Presenter Dashboard)
2. **ContentForm Module** (Tracking system)
3. **Presenter User Management** (Authentication & User Management)

---

## Files to Remove

### 1. Frontend Views (2 files)
```
resources/views/presenter/dashboard.blade.php
resources/views/presenter/login.blade.php
```

### 2. Frontend Controllers (2 files)
```
app/Http/Controllers/PresenterAuthController.php
app/Http/Controllers/PresenterDashboardController.php
```

### 3. Frontend Middleware (2 files)
```
app/Http/Middleware/PresenterAuth.php
app/Http/Middleware/PresenterActivityLogger.php
```

### 4. Frontend Assets (2 files)
```
public/js/content-form.js
public/css/custom.css
```

### 5. ContentForm Module (11 files)

**Models:**
```
app/Models/ContentForm.php
app/Models/ContentFormLog.php
```

**Controller:**
```
app/Http/Controllers/ContentFormController.php
```

**Observers:**
```
app/Observers/AdvertisementObserver.php
app/Observers/GongObserver.php
```

**Filament Resources:**
```
app/Filament/Resources/ContentFormResource.php
app/Filament/Resources/ContentFormResource/Pages/ListContentForms.php
app/Filament/Resources/ContentFormResource/Pages/CreateContentForm.php
app/Filament/Resources/ContentFormResource/Pages/EditContentForm.php
```

**Test Command:**
```
app/Console/Commands/TestContentFormModule.php
```

### 6. Presenter User Management (5 files)

**Model:**
```
app/Models/Presenter.php
```

**Filament Resource:**
```
app/Filament/Resources/PresenterResource.php
app/Filament/Resources/PresenterResource/Pages/ListPresenters.php
app/Filament/Resources/PresenterResource/Pages/CreatePresenter.php
app/Filament/Resources/PresenterResource/Pages/EditPresenter.php
```

### 7. Presenter-Related Models (1 file)
```
app/Models/PresenterReadStatus.php
```

### 8. Routes (from routes/web.php)
Remove all presenter routes:
- Presenter login routes
- Presenter dashboard routes
- Presenter content form routes

### 9. Migrations (7 files)
```
database/migrations/2025_08_29_024452_create_presenters_table.php
database/migrations/2025_08_29_024523_create_presenter_read_statuses_table.php
database/migrations/2025_08_29_061203_add_time_slot_to_presenter_read_statuses_table.php
database/migrations/2025_01_02_000000_fix_presenter_read_statuses_constraints.php
database/migrations/2025_11_03_121251_create_content_forms_table.php
database/migrations/2025_11_03_121301_create_content_form_logs_table.php
database/migrations/2025_11_03_122451_populate_content_forms_from_existing_content.php
```

### 10. Documentation Files (10+ files)
```
PRESENTER_DASHBOARD_FILTERING_COMPLETE.md
CHANGES_SUMMARY.md
INDIVIDUAL_READINGS_TRACKING_FIX.md
INDIVIDUAL_READINGS_IMPLEMENTATION_SUMMARY.md
INDIVIDUAL_READINGS_QUICK_REFERENCE.md
NULLABLE_FREQUENCIES_IMPLEMENTATION.md
NULLABLE_FREQUENCIES_QUICK_REFERENCE.md
CONTENT_FORM_*.md (all documentation files)
CONTENT_FORM_WHERE_TO_FIND.md
```

### 11. Test Commands (2 files)
```
app/Console/Commands/TestPresenterDashboardFiltering.php
app/Console/Commands/TestIndividualReadings.php
```

---

## What to Keep

✅ **Advertisement Model & Controller** - Core business logic
✅ **Gong (Memorial) Model & Controller** - Core business logic
✅ **Customer Model & Controller** - Core business logic
✅ **AdsCategory Model & Controller** - Core business logic
✅ **Program Model & Controller** - Core business logic
✅ **Filament Admin Panel** - Admin interface for managing content
✅ **Database Tables** - advertisements, gongs, customers, etc.
✅ **PDF Generation** - For printing advertisements/gongs

---

## Database Cleanup

After removing migrations, you'll need to:
1. Drop `presenters` table
2. Drop `presenter_read_statuses` table
3. Drop `content_forms` table
4. Drop `content_form_logs` table

Or create a rollback migration to handle this.

---

## Summary

**Total Files to Remove: ~40 files**
- Frontend Views: 2
- Controllers: 3
- Middleware: 2
- Frontend Assets: 2
- ContentForm Module: 11
- Presenter Management: 5
- Models: 2
- Migrations: 7
- Documentation: 10+
- Test Commands: 2

**Result:** Pure backend API system with Filament admin panel for managing advertisements, gongs, and customers.

---

## Next Steps

1. Confirm removal plan
2. Remove files in order
3. Update routes/web.php
4. Create rollback migration for database tables
5. Test that admin panel still works
6. Verify no broken imports or references

