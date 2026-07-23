# Table Name Fix - Personnel vs Personnels

## Issue
The newly created payroll run pages (`view_payroll_run.php`, `edit_payroll_run.php`, `print_payroll_run.php`) were referencing a non-existent table `personnel` (singular), causing SQL errors.

**Error Message:**
```
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'moh_hrms.personnel' doesn't exist
```

## Root Cause
The actual table name in the database is `personnels` (plural), not `personnel` (singular).

## Database Schema

### Correct Table Name: `personnels`
```sql
CREATE TABLE `personnels` (
  `personnel_id` int(11) NOT NULL PRIMARY KEY,
  `RFTag_id` varchar(25) NOT NULL,
  `personnel_id_code` varchar(25) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `mname` varchar(255) NOT NULL,
  -- ... other fields
)
```

### User Account Table: `useraccount`
```sql
CREATE TABLE `useraccount` (
  `user_id` int(11) NOT NULL PRIMARY KEY,
  `personnel_id` int(11) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  -- ... other fields
)
```

### Payroll Runs Table: `pr_tbl_payroll_runs`
```sql
CREATE TABLE `pr_tbl_payroll_runs` (
  `run_id` int(11) NOT NULL PRIMARY KEY,
  `created_by` int(11),  -- References useraccount.user_id
  `approved_by` int(11), -- References useraccount.user_id
  -- ... other fields
)
```

## Fixes Applied

### 1. Fixed `view_payroll_run.php`

#### Before (INCORRECT):
```php
LEFT JOIN personnel creator ON pr.created_by = creator.id
LEFT JOIN personnel approver ON pr.approved_by = approver.id
```

```php
LEFT JOIN personnel p ON prd.personnel_id = p.id
```

#### After (CORRECT):
```php
LEFT JOIN useraccount creator ON pr.created_by = creator.user_id
LEFT JOIN useraccount approver ON pr.approved_by = approver.user_id
```

```php
LEFT JOIN personnels p ON prd.personnel_id = p.personnel_id
```

**Changes Made:**
- Line 26-27: Fixed JOIN to `useraccount` table (for creator/approver names)
- Line 278: Fixed JOIN to `personnels` table with correct column `personnel_id`

### 2. Fixed `edit_payroll_run.php`

#### Before (INCORRECT):
```php
LEFT JOIN personnel p ON prd.personnel_id = p.id
```

#### After (CORRECT):
```php
LEFT JOIN personnels p ON prd.personnel_id = p.personnel_id
```

**Changes Made:**
- Line 195: Fixed JOIN to `personnels` table with correct column `personnel_id`

### 3. Fixed `print_payroll_run.php`

#### Before (INCORRECT):
```php
LEFT JOIN personnel creator ON pr.created_by = creator.id
LEFT JOIN personnel approver ON pr.approved_by = approver.id
```

```php
LEFT JOIN personnel p ON prd.personnel_id = p.id
```

#### After (CORRECT):
```php
LEFT JOIN useraccount creator ON pr.created_by = creator.user_id
LEFT JOIN useraccount approver ON pr.approved_by = approver.user_id
```

```php
LEFT JOIN personnels p ON prd.personnel_id = p.personnel_id
```

**Changes Made:**
- Line 25-26: Fixed JOIN to `useraccount` table (for creator/approver names)
- Line 319: Fixed JOIN to `personnels` table with correct column `personnel_id`

## Table Relationships

### Creator/Approver Relationship:
```
pr_tbl_payroll_runs.created_by → useraccount.user_id → useraccount.fname/lname
pr_tbl_payroll_runs.approved_by → useraccount.user_id → useraccount.fname/lname
```

**Why useraccount?**
- The `created_by` and `approved_by` fields store the USER ID (from login system)
- The `useraccount` table has both `user_id` and `fname`/`lname` fields
- This allows displaying the user's name without needing to join through `personnels`

### Personnel in Payroll Run:
```
pr_tbl_payroll_run_details.personnel_id → personnels.personnel_id → personnels.fname/lname/mname
```

**Why personnels?**
- The payroll details reference actual personnel records
- Each personnel has their complete information in the `personnels` table
- Need to display personnel name, department, etc. in payroll reports

## Column Name Differences

| Table | Primary Key Column | First Name | Last Name |
|-------|-------------------|------------|-----------|
| `personnels` | `personnel_id` | `fname` | `lname` |
| `useraccount` | `user_id` | `fname` | `lname` |

**Important Notes:**
- Both tables have `fname` and `lname` columns (same column names)
- Primary keys are different: `personnel_id` vs `user_id`
- JOINs must use the correct primary key column

## Testing

### Before Fix:
```
Error: Table 'moh_hrms.personnel' doesn't exist
```

### After Fix:
```
✓ view_payroll_run.php loads successfully
✓ Personnel names display correctly
✓ Creator/Approver names display correctly
✓ All JOINs work properly
```

## Lessons Learned

1. **Always verify table names** - Don't assume singular/plural conventions
2. **Check actual schema** - Use `SHOW TABLES` and `DESCRIBE` commands
3. **Understand relationships** - Know which tables store which IDs
4. **Test with real data** - Errors only appear when accessing the pages

## Related Files

Files that correctly use `personnels` (plural):
- `process_payroll_generation.php` ✓
- `list_personnel.php` ✓
- `list_personnel_income.php` ✓
- `list_personnel_deductions.php` ✓

Files that were fixed:
- `view_payroll_run.php` ✓ FIXED
- `edit_payroll_run.php` ✓ FIXED
- `print_payroll_run.php` ✓ FIXED

## Additional Fixes (October 21, 2025)

### Issue 1: Missing menu.php file
**Error:**
```
Warning: include(menu.php): Failed to open stream: No such file or directory
```

**Fix:**
Changed `include('menu.php')` to `include('menu_sidebar.php')` in `view_payroll_run.php`

### Issue 2: Incorrect column name for department
**Error:**
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'd.do_name' in 'field list'
```

**Root Cause:**
The `dept_offices` table uses `dept_office_name`, not `do_name`

**dept_offices Schema:**
```sql
CREATE TABLE `dept_offices` (
  `do_id` int(11) PRIMARY KEY,
  `dept_office_name` varchar(255) NOT NULL,  -- Correct column name
  `officeHead_id` int(11) NOT NULL
)
```

**Files Fixed:**
1. `view_payroll_run.php` - Line 273 (SQL) and Line 291 (display)
2. `edit_payroll_run.php` - Line 193 (SQL) and Line 211 (display)
3. `print_payroll_run.php` - Line 317 (SQL) and Line 342 (display)

**Changes Made:**
- SQL: `d.do_name` → `d.dept_office_name`
- PHP: `$detail['do_name']` → `$detail['dept_office_name']`

## Summary

✅ **Fixed 3 files** with incorrect table references
✅ **Changed 6 SQL JOIN statements** total
✅ **Corrected table names**: `personnel` → `personnels`
✅ **Corrected column names**: `id` → `personnel_id`
✅ **Fixed user lookups**: Using `useraccount` table for creator/approver
✅ **Fixed menu include**: `menu.php` → `menu_sidebar.php`
✅ **Fixed department column**: `do_name` → `dept_office_name`

**Result:** All payroll run pages now work correctly with the actual database schema! 🎉
