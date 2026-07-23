# Automatic Monthly Leave Credits Accrual System

## Overview
The system automatically adds **1.25 Vacation Leave (VL)** and **1.25 Sick Leave (SL)** earned credits to all active personnel's leave cards on a monthly basis.

## How It Works

### Automatic Processing
1. **Trigger**: When an Administrator logs in to the system
2. **Timing**: Processes the **previous month's credits** 
   - Example: On October 1st (or any day in October), the system adds credits for "Month of September 2025"
3. **Frequency**: Only processes once per month (checked against database log)
4. **Personnel Coverage**: All active personnel (those without separation dates)

### Monthly Credits Added
For each personnel:
- **Particulars**: "Month of [Month Name] [Year]"
- **VL Earned**: 1.25
- **SL Earned**: 1.25
- **Remarks**: "Monthly Leave Credits"

Example entry:
```
Particulars: Month of September 2025
Vacation Leave Earned: 1.25
Sick Leave Earned: 1.25
```

## Technical Implementation

### Database Tables

#### 1. `monthly_leave_credits_log`
Tracks which months have been processed to prevent duplicates.

**Columns:**
- `id` - Auto-increment primary key
- `personnel_id` - Personnel ID
- `year` - Year processed (e.g., 2025)
- `month` - Month processed (1-12)
- `vl_earned` - VL credits added (default: 1.25)
- `sl_earned` - SL credits added (default: 1.25)
- `leave_card_id` - Reference to leave_card entry
- `processed_date` - When the credits were added
- `processed_by` - Admin who triggered the process

**Constraints:**
- Unique constraint on `(personnel_id, year, month)` prevents duplicate processing

#### 2. `leave_card` (existing table)
Monthly credits are inserted as regular entries with:
- `created_from_application` = 0 (manual entry, can be edited)
- `is_special_leave` = 0
- `period_from` and `period_to` = empty (not date-specific)

### PHP Files

#### 1. `process_monthly_leave_credits.php`
Core processing script with two main functions:

**`processMonthlyLeaveCredits($conn, $admin_id)`**
- Gets previous month's year and month
- Checks if already processed via `monthly_leave_credits_log`
- Fetches all active personnel
- Inserts leave_card entries for each personnel
- Logs processing in `monthly_leave_credits_log`
- Returns result array with status and count

**`checkAndProcessMonthlyCredits($conn, $admin_id)`**
- Wrapper function called from session.php
- Stores result in session for notification display

#### 2. `session.php` (modified)
Added automatic trigger:
```php
if ($session_access === 'Administrator') {
    require_once('process_monthly_leave_credits.php');
    checkAndProcessMonthlyCredits($conn, $session_id);
}
```

#### 3. `home.php` (modified)
Added success notification display:
- Shows alert when credits are processed
- Displays month, year, and count of personnel updated
- Auto-dismissible alert
- Notification cleared after display

## Admin Features

### Automatic Notification
When monthly credits are processed, administrators see:
```
✓ Monthly Leave Credits Processed!
Successfully processed monthly leave credits for September 2025. 150 personnel records updated.
```

### Manual Processing
Administrators can also manually trigger processing:
1. Navigate to: `process_monthly_leave_credits.php`
2. View detailed results including any errors
3. AJAX endpoint available: `process_monthly_leave_credits.php?ajax=1`

## Editing Monthly Credits

### Via Leave Card
1. Navigate to Personnel → Leave Card
2. Monthly credit entries appear as regular entries
3. Use "Add Leave Card Entry" modal to manually adjust if needed
4. Monthly credits are **fully editable** like any other leave card entry

### Important Notes
- Monthly credits are NOT linked to leave applications (`created_from_application = 0`)
- Can be edited, deleted, or adjusted manually
- Edits won't trigger any leave application sync
- Perfect for corrections or adjustments

## Processing Logic

### Month Determination
```php
$currentDate = new DateTime();
$previousMonth = clone $currentDate;
$previousMonth->modify('-1 month');

$year = $previousMonth->format('Y');
$month = $previousMonth->format('n'); // 1-12
```

### Active Personnel Query
```sql
SELECT personnel_id, lname, fname, mname 
FROM personnels 
WHERE separation_date IS NULL 
   OR separation_date = '' 
   OR separation_date = '  /  /    '
```

### Duplicate Prevention
```sql
SELECT COUNT(*) FROM monthly_leave_credits_log 
WHERE year = :year AND month = :month
```

If count > 0, skip processing for that month.

## Error Handling

### Transaction Safety
- Uses database transactions
- Rollback on any error
- Individual personnel errors logged but don't stop processing

### Error Logging
- PHP error_log for debugging
- Individual errors stored in result array
- Displayed on manual processing page

## Testing the System

### Test Scenario 1: First Run
1. Login as Administrator on October 1st
2. Should process September credits for all personnel
3. Check home.php for success notification
4. Verify leave_card entries for random personnel

### Test Scenario 2: Duplicate Prevention
1. Login again on same day
2. Should NOT reprocess September
3. No notification displayed
4. Check monthly_leave_credits_log table

### Test Scenario 3: Manual Entry
1. Go to any personnel's Leave Card
2. Click "Add Leave Card Entry"
3. Add custom credits
4. Verify entry appears in table

### Test Scenario 4: Late Login
1. Assume admin didn't login on October 1st
2. Login on October 15th
3. September credits should still be processed
4. System tracks by month, not specific date

## Maintenance

### Check Processing History
```sql
SELECT p.lname, p.fname, l.year, l.month, l.vl_earned, l.sl_earned, l.processed_date
FROM monthly_leave_credits_log l
JOIN personnels p ON l.personnel_id = p.personnel_id
WHERE l.year = 2025 AND l.month = 9
ORDER BY p.lname, p.fname;
```

### Reprocess a Month (Emergency)
```sql
-- Delete log entries for specific month
DELETE FROM monthly_leave_credits_log WHERE year = 2025 AND month = 9;

-- Delete leave_card entries (optional, if needed)
DELETE FROM leave_card 
WHERE particulars = 'Month of September 2025' 
  AND remarks = 'Monthly Leave Credits';
```

Then login as admin to retrigger automatic processing.

## Configuration

### Adjust Credit Amounts
To change the monthly credit amount from 1.25:

1. Edit `process_monthly_leave_credits.php`
2. Modify these lines:
```php
vl_earned = 1.25,    // Change to desired VL amount
sl_earned = 1.25,    // Change to desired SL amount
```

### Change Processing Schedule
Currently processes previous month. To change:

1. Edit `process_monthly_leave_credits.php`
2. Modify date calculation:
```php
$previousMonth->modify('-1 month');  // Change offset
```

## Benefits

1. **Automatic**: No manual entry needed each month
2. **Accurate**: Consistent 1.25 credits per month
3. **Safe**: Duplicate prevention built-in
4. **Auditable**: Complete processing log in database
5. **Flexible**: Credits can be edited after automatic creation
6. **Resilient**: Works even if admin logs in late in the month

## Support

For issues or questions:
1. Check PHP error logs
2. Query `monthly_leave_credits_log` table
3. Verify personnel separation dates
4. Test manual processing via direct URL access
