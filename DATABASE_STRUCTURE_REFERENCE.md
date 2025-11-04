# Database Structure Reference

## content_forms Table

### Columns

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| content_type | string | 'advertisement' or 'gong' |
| content_id | bigint | ID of the advertisement or gong |
| customer_id | bigint | Customer who submitted the content |
| title | string | Title of the content |
| content_summary | text | Summary of content |
| word_count | integer | Number of words |
| source | string | 'mail' or 'hand' |
| received_date | datetime | When content was received |
| amount | decimal | Amount paid |
| is_paid | boolean | Whether payment is complete |
| band | json | Array of bands |
| broadcast_start_date | date | Start date of broadcast |
| broadcast_end_date | date | End date of broadcast |
| broadcast_days | json | Days to broadcast |
| morning_frequency | integer | Number of morning readings required |
| lunch_frequency | integer | Number of lunch readings required |
| evening_frequency | integer | Number of evening readings required |
| **morning_ticked_at** | datetime | Last time morning was ticked |
| **lunch_ticked_at** | datetime | Last time lunch was ticked |
| **evening_ticked_at** | datetime | Last time evening was ticked |
| **morning_tick_count** | integer | Number of morning ticks |
| **lunch_tick_count** | integer | Number of lunch ticks |
| **evening_tick_count** | integer | Number of evening ticks |
| **morning_tick_times** | json | Array of all morning tick timestamps |
| **lunch_tick_times** | json | Array of all lunch tick timestamps |
| **evening_tick_times** | json | Array of all evening tick timestamps |
| **presenter_id** | bigint | ID of presenter who ticked |
| **presenter_shift** | string | Current shift of presenter |
| **is_completed** | boolean | Whether all readings are done |
| **completed_at** | datetime | When all readings were completed |
| created_at | datetime | Record creation time |
| updated_at | datetime | Record update time |

### Example Data

```json
{
  "id": 1,
  "content_type": "advertisement",
  "content_id": 5,
  "customer_id": 2,
  "title": "Summer Sale Announcement",
  "word_count": 150,
  "source": "mail",
  "amount": 500.00,
  "is_paid": true,
  "morning_frequency": 3,
  "lunch_frequency": 2,
  "evening_frequency": 2,
  "morning_tick_count": 3,
  "lunch_tick_count": 2,
  "evening_tick_count": 2,
  "morning_tick_times": [
    "2025-11-04 13:45:53",
    "2025-11-04 14:30:12",
    "2025-11-04 15:22:45"
  ],
  "lunch_tick_times": [
    "2025-11-04 11:15:30",
    "2025-11-04 12:45:22"
  ],
  "evening_tick_times": [
    "2025-11-04 16:30:12",
    "2025-11-04 17:45:33"
  ],
  "presenter_id": 1,
  "presenter_shift": "morning",
  "is_completed": true,
  "completed_at": "2025-11-04 17:45:33"
}
```

## content_form_logs Table

### Columns

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| content_form_id | bigint | Foreign key to content_forms |
| presenter_id | bigint | Foreign key to presenters |
| action | string | 'tick' or 'untick' |
| time_slot | string | 'morning', 'lunch', or 'evening' |
| action_at | datetime | When the action occurred |
| ip_address | string | IP address of the presenter |
| user_agent | string | Browser/device information |
| reading_number | integer | Which reading (1, 2, 3, etc.) |
| notes | text | Additional notes |
| created_at | datetime | Record creation time |
| updated_at | datetime | Record update time |

### Example Data

```json
{
  "id": 1,
  "content_form_id": 1,
  "presenter_id": 1,
  "action": "tick",
  "time_slot": "morning",
  "action_at": "2025-11-04 13:45:53",
  "ip_address": "192.168.1.100",
  "user_agent": "Mozilla/5.0...",
  "reading_number": 1,
  "notes": "Presenter Sarah Johnson ticked reading #1 for morning shift"
}
```

## Relationships

### ContentForm
```php
// Belongs to
- Customer (customer_id)
- Presenter (presenter_id)

// Has many
- ContentFormLog (content_form_id)

// Polymorphic
- Advertisement or Gong (content_type, content_id)
```

### ContentFormLog
```php
// Belongs to
- ContentForm (content_form_id)
- Presenter (presenter_id)
```

### Presenter
```php
// Has many
- ContentForm (presenter_id)
- ContentFormLog (presenter_id)
```

## JSON Array Structure

### morning_tick_times
```json
[
  "2025-11-04 13:45:53",
  "2025-11-04 14:30:12",
  "2025-11-04 15:22:45"
]
```

### lunch_tick_times
```json
[
  "2025-11-04 11:15:30",
  "2025-11-04 12:45:22"
]
```

### evening_tick_times
```json
[
  "2025-11-04 16:30:12",
  "2025-11-04 17:45:33"
]
```

## Queries

### Get all tick times for a content form
```php
$form = ContentForm::find(1);
$allMorningTicks = $form->morning_tick_times; // Array
$allLunchTicks = $form->lunch_tick_times;     // Array
$allEveningTicks = $form->evening_tick_times; // Array
```

### Get all logs for a content form
```php
$form = ContentForm::find(1);
$logs = $form->logs()->orderBy('action_at', 'asc')->get();

foreach ($logs as $log) {
    echo $log->reading_number . " | " . $log->time_slot . " | " . $log->presenter->name . " | " . $log->action_at;
}
```

### Get logs by presenter
```php
$logs = ContentFormLog::where('presenter_id', 1)->get();
```

### Get logs by time slot
```php
$logs = ContentFormLog::where('time_slot', 'morning')->get();
```

### Get logs by action
```php
$ticks = ContentFormLog::where('action', 'tick')->get();
$unticks = ContentFormLog::where('action', 'untick')->get();
```

### Get completed forms
```php
$completed = ContentForm::where('is_completed', true)->get();
```

### Get forms by presenter
```php
$forms = ContentForm::where('presenter_id', 1)->get();
```

## Indexes

### Recommended Indexes
```sql
-- For faster queries
CREATE INDEX idx_content_forms_presenter_id ON content_forms(presenter_id);
CREATE INDEX idx_content_forms_content_type_id ON content_forms(content_type, content_id);
CREATE INDEX idx_content_forms_is_completed ON content_forms(is_completed);
CREATE INDEX idx_content_form_logs_presenter_id ON content_form_logs(presenter_id);
CREATE INDEX idx_content_form_logs_content_form_id ON content_form_logs(content_form_id);
CREATE INDEX idx_content_form_logs_action_at ON content_form_logs(action_at);
```

## Storage Considerations

### JSON Column Size
- Each timestamp: ~19 bytes
- Array of 10 timestamps: ~200 bytes
- Three arrays (morning, lunch, evening): ~600 bytes per form

### Estimated Storage
- 1000 content forms × 600 bytes = 600 KB
- 10000 content forms × 600 bytes = 6 MB

## Performance Notes

- JSON arrays are efficient for small datasets (< 100 items)
- Queries on JSON arrays are slower than indexed columns
- For large-scale analytics, consider denormalizing to separate table
- ContentFormLog table provides indexed access to individual ticks

## Backup Considerations

- JSON data is stored as text in MySQL
- Standard database backups work fine
- JSON arrays are human-readable in backups
- No special backup procedures needed

