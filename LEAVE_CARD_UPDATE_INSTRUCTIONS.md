# Leave Card Feature Update Instructions

## Overview
This update adds two major features to the Leave Card system:

1. **Special Leave Functionality**: Allows marking leave entries as "Special Leave" which prevents leave credit deductions
2. **Comprehensive Data Summary Card**: Displays visual statistics of leave balances and usage above the leave card table

---

## STEP 1: Database Update (REQUIRED)

### Execute this SQL in phpMyAdmin or MySQL console:

```sql
ALTER TABLE `leave_card` 
ADD COLUMN `is_special_leave` TINYINT(1) NOT NULL DEFAULT 0 
COMMENT 'Special leave indicator - no leave credit deductions' 
AFTER `remarks`;
```

### How to run the SQL:
1. Open **phpMyAdmin** (http://localhost/phpmyadmin)
2. Select your database (likely `moh_hrms`)
3. Click on the **SQL** tab
4. Paste the SQL command above
5. Click **Go**
6. Verify success message appears

---

## STEP 2: Features Implemented

### Feature 1: Special Leave Checkbox

**What it does:**
- When adding or editing a leave card entry, you can check "Special Leave / No leave credit deductions"
- When checked, the backend automatically sets VL With Pay and SL With Pay to 0 (no deductions from leave credits)
- Users can still enter values in the "With Pay" fields, but these will be overridden to 0 by the backend
- Special leave entries are highlighted in green in the table with a badge

**How to use:**
1. Click "New Leave Card Entry" or edit existing entry
2. Check the "Special Leave / No leave credit deductions" checkbox
3. Notice the information message explaining that With Pay deductions won't apply
4. Enter all details as normal (Earned, With Pay, Without Pay, etc.)
5. Save the entry - backend will automatically set With Pay values to 0
6. The entry will appear with a green background and "Special" badge in the table

**Visual Indicators:**
- Green row background for special leave entries
- Green "Special" badge next to particulars
- Information notice explaining no deductions will be made
- With Pay fields remain enabled for data entry

---

### Feature 2: Comprehensive Leave Summary Cards

**What's displayed:**

#### Vacation Leave Summary (Blue Card)
- **Total Earned**: All VL credits earned
- **Current Balance**: Available VL (Earned - Used with Pay)
- **Used (w/ Pay)**: Total VL used with pay
- **Used (w/o Pay)**: Total VL used without pay

#### Sick Leave Summary (Cyan Card)
- **Total Earned**: All SL credits earned
- **Current Balance**: Available SL (Earned - Used with Pay)
- **Used (w/ Pay)**: Total SL used with pay
- **Used (w/o Pay)**: Total SL used without pay

#### Additional Statistics (Gray Card)
- **Total Entries**: Number of leave card records
- **Special Leaves**: Count of special leave entries
- **Total Balance**: Combined VL + SL balance
- **Total Used**: Combined VL + SL used

---

## STEP 3: Files Modified

### Backend Files:
1. **save_add_leave_card_entry.php**
   - Added `is_special_leave` field handling
   - Implements logic to force with_pay = 0 when special leave is checked
   - Updated INSERT and UPDATE queries

### Frontend Files:
2. **leave_card.php**
   - Added comprehensive summary statistics cards (before table)
   - Added visual indicators for special leave entries (green background + badge)
   - Updated edit modal to show checkbox state correctly
   - Added JavaScript to disable/enable with_pay fields based on checkbox
   - Enhanced DataTable display

### Database Files:
3. **db/alter_leave_card_add_special_leave.sql**
   - SQL script to add the `is_special_leave` column

---

## STEP 4: Testing Checklist

### Test Special Leave Feature:
- [ ] Add new entry with special leave checked
- [ ] Enter values in with_pay fields and verify they can be edited
- [ ] Save entry and verify backend sets with_pay to 0 (no deductions)
- [ ] Verify entry appears with green background and "Special" badge
- [ ] Edit the entry and verify checkbox state is preserved
- [ ] Uncheck special leave and verify with_pay values are now applied normally
- [ ] Verify special leave count in summary card

### Test Summary Cards:
- [ ] Verify all earned values display correctly
- [ ] Verify current balances calculate correctly (Earned - Used with Pay)
- [ ] Verify used values display correctly
- [ ] Verify total entries count matches table rows
- [ ] Verify special leaves count is accurate
- [ ] Add/edit/delete entries and verify summary updates

### Test Existing Functionality:
- [ ] Add normal leave entry (without special leave)
- [ ] Edit existing entries
- [ ] Delete entries
- [ ] Verify table sorting and filtering
- [ ] Verify balance calculations in table are correct
- [ ] Check responsive design on mobile/tablet

---

## STEP 5: Rollback Instructions (If Needed)

If you need to remove this feature:

```sql
-- Remove the is_special_leave column
ALTER TABLE `leave_card` DROP COLUMN `is_special_leave`;
```

Then restore previous versions of:
- save_add_leave_card_entry.php
- leave_card.php

---

## Technical Notes

### Special Leave Logic:
```php
// In save_add_leave_card_entry.php
$is_special_leave = isset($_POST['is_special_leave']) ? 1 : 0;

if ($is_special_leave) {
    $vl_with_pay = 0;  // No VL deduction
    $sl_with_pay = 0;  // No SL deduction
} else {
    // Use posted values
}
```

### Balance Calculation:
```php
// Running balance in table
$vl_bal += $lc_row['vl_earned'];
$vl_bal = $vl_bal - $lc_row['vl_with_pay'];

// Summary statistics
$vl_current_balance = $vl_total_earned - $vl_total_with_pay;
```

---

## Support

If you encounter any issues:
1. Check browser console for JavaScript errors
2. Check PHP error log for backend errors
3. Verify database column was added successfully
4. Clear browser cache and refresh page
5. Verify all files were updated correctly

---

**Implementation Date**: October 24, 2025  
**Version**: 2.0  
**Status**: Ready for Production
