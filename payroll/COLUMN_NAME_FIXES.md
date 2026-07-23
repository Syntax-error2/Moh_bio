# Column Name Fixes - Income and Deduction Tables

## Issues Found

### 1. SQL Error: Unknown column 'i.income_name'
```
Error: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'i.income_name' in 'field list'
```

### 2. Multiple Constant Definition Warnings
```
Warning: Constant DB_HOST already defined in dbcon.php on line 12
Warning: Constant DB_NAME already defined in dbcon.php on line 13
Warning: Constant DB_USER already defined in dbcon.php on line 14
Warning: Constant DB_PASS already defined in dbcon.php on line 15
Warning: Constant DB_CHARSET already defined in dbcon.php on line 16
```

---

## Root Cause Analysis

### Issue 1: Wrong Column Names

**Actual Database Schema:**

#### pr_tbl_income:
```sql
+-------------+-------------+
| Field       | Type        |
+-------------+-------------+
| income_id   | int(11)     |
| income_type | varchar(55) |  ✅
| income_title| varchar(55) |  ✅ (NOT income_name)
+-------------+-------------+
```

#### pr_tbl_deductions:
```sql
+-----------------+-------------+
| Field           | Type        |
+-----------------+-------------+
| deduction_id    | int(11)     |
| deduction_type  | varchar(55) |  ✅
| deduction_title | varchar(55) |  ✅ (NOT deduction_name)
+-----------------+-------------+
```

#### pr_tbl_payroll_run_deductions:
```sql
+------------------+---------------+
| Field            | Type          |
+------------------+---------------+
| employee_amount  | decimal(10,2) |  ✅ (NOT employee_share)
| employer_amount  | decimal(10,2) |  ✅ (NOT employer_share)
+------------------+---------------+
```

**Code Was Using:**
- ❌ `income_name` → Should be `income_title`
- ❌ `deduction_name` → Should be `deduction_title`
- ❌ `employee_share` → Should be `employee_amount`
- ❌ `employer_share` → Should be `employer_amount`

### Issue 2: Multiple Inclusions of dbcon.php

`dbcon.php` was being included multiple times in the same request (likely through `session.php` and other files), causing constants to be redefined.

---

## Fixes Applied

### Fix 1: ajax_get_personnel_payroll_details.php

#### A. Income Query (Line 39-45)
**Before:**
```php
SELECT pri.*, i.income_name, i.income_type  ❌
FROM pr_tbl_payroll_run_income pri
LEFT JOIN pr_tbl_income i ON pri.income_id = i.income_id
WHERE pri.detail_id = :detail_id
ORDER BY i.income_type, i.income_name  ❌
```

**After:**
```php
SELECT pri.*, i.income_title, i.income_type  ✅
FROM pr_tbl_payroll_run_income pri
LEFT JOIN pr_tbl_income i ON pri.income_id = i.income_id
WHERE pri.detail_id = :detail_id
ORDER BY i.income_type, i.income_title  ✅
```

#### B. Deduction Query (Line 49-55)
**Before:**
```php
SELECT prd.*, d.deduction_name, d.deduction_type  ❌
FROM pr_tbl_payroll_run_deductions prd
LEFT JOIN pr_tbl_deductions d ON prd.deduction_id = d.deduction_id
WHERE prd.detail_id = :detail_id
ORDER BY d.deduction_type, d.deduction_name  ❌
```

**After:**
```php
SELECT prd.*, d.deduction_title, d.deduction_type  ✅
FROM pr_tbl_payroll_run_deductions prd
LEFT JOIN pr_tbl_deductions d ON prd.deduction_id = d.deduction_id
WHERE prd.detail_id = :detail_id
ORDER BY d.deduction_type, d.deduction_title  ✅
```

#### C. Income Display (Line 109)
**Before:**
```php
<td><?php echo htmlspecialchars($item['income_name']); ?></td>  ❌
```

**After:**
```php
<td><?php echo htmlspecialchars($item['income_title']); ?></td>  ✅
```

#### D. Deduction Display (Lines 145-157)
**Before:**
```php
$total_employee_share += $item['employee_share'];  ❌
$total_employer_share += $item['employer_share'];  ❌
$item_total = $item['employee_share'] + $item['employer_share'];  ❌

<td><?php echo htmlspecialchars($item['deduction_name']); ?></td>  ❌
<td>₱<?php echo number_format($item['employee_share'], 2); ?></td>  ❌
<td>₱<?php echo number_format($item['employer_share'], 2); ?></td>  ❌
```

**After:**
```php
$total_employee_share += $item['employee_amount'];  ✅
$total_employer_share += $item['employer_amount'];  ✅
$item_total = $item['employee_amount'] + $item['employer_amount'];  ✅

<td><?php echo htmlspecialchars($item['deduction_title']); ?></td>  ✅
<td>₱<?php echo number_format($item['employee_amount'], 2); ?></td>  ✅
<td>₱<?php echo number_format($item['employer_amount'], 2); ?></td>  ✅
```

### Fix 2: dbcon.php

Added check to prevent multiple inclusions:

**Before:**
```php
<?php
/**
 * Database Connection Configuration
 */

// Set timezone
date_default_timezone_set('Asia/Manila');

// Database configuration constants
define('DB_HOST', '127.0.0.1');  ❌ Causes warning on 2nd include
```

**After:**
```php
<?php
/**
 * Database Connection Configuration
 */

// Prevent multiple inclusions  ✅
if (defined('DB_CONNECTION_LOADED')) {
    return;
}
define('DB_CONNECTION_LOADED', true);

// Set timezone
date_default_timezone_set('Asia/Manila');

// Database configuration constants
define('DB_HOST', '127.0.0.1');  ✅ Only defined once
```

---

## Column Name Reference Guide

### Income Tables

| Code Reference | Correct Column Name | Table |
|----------------|---------------------|-------|
| `income_name` ❌ | `income_title` ✅ | `pr_tbl_income` |
| `income_type` ✅ | `income_type` ✅ | `pr_tbl_income` |

### Deduction Tables

| Code Reference | Correct Column Name | Table |
|----------------|---------------------|-------|
| `deduction_name` ❌ | `deduction_title` ✅ | `pr_tbl_deductions` |
| `deduction_type` ✅ | `deduction_type` ✅ | `pr_tbl_deductions` |

### Payroll Run Deductions

| Code Reference | Correct Column Name | Table |
|----------------|---------------------|-------|
| `employee_share` ❌ | `employee_amount` ✅ | `pr_tbl_payroll_run_deductions` |
| `employer_share` ❌ | `employer_amount` ✅ | `pr_tbl_payroll_run_deductions` |

---

## Impact Analysis

### Files Fixed:
1. ✅ `ajax_get_personnel_payroll_details.php` - 8 changes
2. ✅ `dbcon.php` - Added include guard

### Files That Still Need Checking:
These files might also have similar issues (check if they use the same tables):

- `process_payroll_generation.php` (already uses correct `income_title` and `deduction_title`)
- Any other files that query `pr_tbl_income` or `pr_tbl_deductions`

---

## Testing Instructions

### 1. Test Personnel Details Modal
```
1. Go to: http://localhost/moh_hrms/payroll/view_payroll_run.php?run_id=[run_id]
2. Click "View Details" button on any personnel
3. Modal should open WITHOUT SQL errors ✅
4. Should show:
   - Income breakdown with item names
   - Deduction breakdown with item names
   - Correct employee and employer amounts
```

### 2. Test Direct AJAX Call
```
http://localhost/moh_hrms/payroll/ajax_get_personnel_payroll_details.php?detail_id=[detail_id]

Expected:
✅ No SQL errors
✅ Income items display
✅ Deduction items display
✅ All amounts calculate correctly
```

### 3. Verify No Warnings
```
Expected:
✅ No "Constant already defined" warnings
✅ Clean page load
✅ No PHP errors in error log
```

---

## Verification Queries

### Check Income Data:
```sql
SELECT i.income_id, i.income_title, i.income_type, pri.amount
FROM pr_tbl_payroll_run_income pri
LEFT JOIN pr_tbl_income i ON pri.income_id = i.income_id
WHERE pri.run_id = 5
LIMIT 5;

Expected: Returns income records with titles ✅
```

### Check Deduction Data:
```sql
SELECT d.deduction_id, d.deduction_title, d.deduction_type, 
       prd.employee_amount, prd.employer_amount
FROM pr_tbl_payroll_run_deductions prd
LEFT JOIN pr_tbl_deductions d ON prd.deduction_id = d.deduction_id
WHERE prd.run_id = 5
LIMIT 5;

Expected: Returns deduction records with titles and amounts ✅
```

---

## Summary of Changes

### Column Name Corrections:
| Old (Wrong) | New (Correct) | Occurrences Fixed |
|-------------|---------------|-------------------|
| `income_name` | `income_title` | 3 times |
| `deduction_name` | `deduction_title` | 3 times |
| `employee_share` | `employee_amount` | 5 times |
| `employer_share` | `employer_amount` | 5 times |

**Total: 16 column name corrections**

### Include Protection:
- Added `DB_CONNECTION_LOADED` guard in `dbcon.php`
- Prevents multiple inclusions
- Eliminates constant redefinition warnings

---

## Expected Results After Fix

### ✅ Personnel Details Modal:
```
Income Breakdown:
- Basic Salary        ₱15,000.00  ✅
- PERA               ₱2,000.00   ✅
Total Income         ₱17,000.00  ✅

Deduction Breakdown:
Item        Employee    Employer    Total
GSIS        ₱100.00     ₱100.00     ₱200.00  ✅
PhilHealth  ₱50.00      ₱50.00      ₱100.00  ✅
Total       ₱150.00     ₱150.00     ₱300.00  ✅
```

### ✅ No Errors:
```
✅ No SQL errors
✅ No constant warnings
✅ Clean error log
✅ Smooth page loads
```

---

## Related Documentation

- **TABLE_NAME_FIX.md** - Personnel/personnels table fix
- **PERSONNEL_DETAILS_MODAL_FIX.md** - Modal implementation
- **DEDUCTION_FALLBACK_FIX.md** - Fallback logic for deductions

---

**STATUS:** ✅ ALL FIXES APPLIED - READY FOR TESTING

**Next Step:** Regenerate payroll run and test the personnel details modal!
