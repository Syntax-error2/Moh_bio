# Leave Application to Leave Card Integration Fix

**Date:** January 13, 2026  
**Issue:** Leave applications not reflected on the leave card

---

## Problem Identified

When users created leave applications through the "Leave Application (CS Form No. 6)" interface, the entries were NOT appearing in the "Leave Card" page. The leave applications were being saved to the database but not creating corresponding leave card entries.

### Root Cause

The `createLeaveCardEntry()` function in [save_leave_application.php](save_leave_application.php) was incorrectly setting the `period_from` and `period_to` values in the leave_card table.

**What was happening:**
- The function was using the **actual leave dates** (e.g., Jan 13, 2026 - Jan 15, 2026) as the period_from and period_to values
- The leave card expects `period_from` and `period_to` to represent a **monthly period** (e.g., first day to last day of the month)
- This mismatch caused the leave card entries to be inserted but with incorrect period values, making them difficult to find or display

**Example of the bug:**
```php
// BEFORE (Incorrect):
$period_date = new DateTime($date_from);  // e.g., 2026-01-13
$period = $period_date->format('Y-m-d');  // Result: 2026-01-13
// Then this was being used as both period_from and period_to
```

---

## Solution Implemented

**File Modified:** [save_leave_application.php](save_leave_application.php)  
**Lines Changed:** 483-488

### Code Changes

**Before:**
```php
// Get the period from application date
$period_date = new DateTime($date_from);
$period = $period_date->format('Y-m-d');
```

**After:**
```php
// Get the period from application date - use the MONTH of the leave start date
$period_date = new DateTime($date_from);
// Set period_from to first day of the month
$period_from = $period_date->format('Y-m-01');
// Set period_to to last day of the month
$period_to = $period_date->format('Y-m-t');
```

### Binding Update

The parameter bindings were also updated to use the correctly formatted period values:

**Lines 553-554:**
```php
$lc_stmt->bindParam(':period_from', $period_from);  // NOW uses calculated month start
$lc_stmt->bindParam(':period_to', $period_to);      // NOW uses calculated month end
```

---

## How It Works Now

### Leave Application Flow

1. User creates a leave application for dates: **Jan 13, 2026 - Jan 15, 2026**
2. System calls `createLeaveCardEntry()` function
3. Function calculates the **monthly period**:
   - `period_from` = `2026-01-01` (first day of January)
   - `period_to` = `2026-01-31` (last day of January)
4. Function inserts leave card entry with:
   - `period_from`: 2026-01-01
   - `period_to`: 2026-01-31
   - `date_from`: 2026-01-13 (actual leave start)
   - `date_to`: 2026-01-15 (actual leave end)
   - `vl_with_pay` / `sl_with_pay`: appropriate deductions
   - `is_special_leave`: 1 or 0 based on leave type
5. Leave card entry now appears in the Leave Card table for the corresponding month

### Result

✅ Leave applications now automatically create leave card entries  
✅ Leave card entries appear in the correct monthly period  
✅ Deductions are properly reflected in leave credit balances  
✅ Special privilege leaves are correctly marked for no deductions

---

## Data Structure

The leave_card table now correctly stores:

| Column | Value | Purpose |
|--------|-------|---------|
| `period_from` | 2026-01-01 | Start of the period (month) |
| `period_to` | 2026-01-31 | End of the period (month) |
| `date_from` | 2026-01-13 | Actual leave start date |
| `date_to` | 2026-01-15 | Actual leave end date |
| `vl_with_pay` | 3.000 | Days deducted from VL |
| `sl_with_pay` | 0.000 | Days deducted from SL |
| `is_special_leave` | 1 or 0 | Whether to exclude from deductions |
| `number_of_days` | 3 | Total leave days |

---

## Testing Steps

1. **Open Leave Application page** for any personnel
2. **Create a new leave application**
   - Select leave type (e.g., "Sick Leave - Out Patient")
   - Set dates (e.g., Jan 13-15, 2026)
   - Submit the application
3. **Navigate to Leave Card** for the same personnel
4. **Verify the entry appears** with:
   - Correct period (e.g., 2026-01-01 to 2026-01-31)
   - Correct leave type in particulars
   - Correct deductions shown
   - Balance correctly calculated
5. **Test Special Privilege Leave**
   - Create a "Special Privilege Leave" application
   - Verify it appears with "Special" badge in Leave Card
   - Confirm balance does NOT deduct from credit

---

## Files Modified

1. **[save_leave_application.php](save_leave_application.php)**
   - Lines 483-488: Corrected period_from and period_to calculation
   - Lines 553-554: Updated parameter bindings to use correct variables

---

## Impact

### What Changed
- Leave applications now correctly create leave card entries with proper monthly period values
- Leave card display now properly reflects all leave applications

### What Remained the Same
- All leave application functionality works as before
- Deduction calculations remain unchanged
- Special privilege leave logic is preserved
- Delete operations properly clean up linked leave card entries

### Backward Compatibility
- Existing leave card entries are unaffected
- Only new leave card entries created from applications will use the corrected period values
- Manual leave card entries continue to work as before

---

## Notes

- The `period_from` (first day of month) and `period_to` (last day of month) values allow the leave card table to group all activities for a given month
- The separate `date_from` and `date_to` fields preserve the actual leave dates for reference
- This structure supports monthly HR reporting and leave balance tracking
- The `created_from_application` flag indicates which leave card entries were auto-generated from leave applications vs manually created
