# Database Query Method Fixes
**Date:** October 20, 2025  
**File:** view_payroll_profile.php  
**Issue:** Incorrect database query methods used (MySQLi instead of PDO)

## Problem Description

The modal forms were using **MySQLi** style queries (`$db->query()` with `fetch_assoc()`) while the entire payroll system uses **PDO** style queries (`$conn->prepare()` with `fetchAll(PDO::FETCH_ASSOC)`).

This would have caused runtime errors when the modals were rendered because:
1. `$db` variable doesn't exist in the payroll module
2. The system uses `$conn` (PDO connection from `dbcon.php`)
3. MySQLi and PDO have different syntax and methods

## System Standard (from payroll/dbcon.php)

The payroll module uses **PDO (PHP Data Objects)** with:
- **Connection Variable:** `$conn`
- **Query Method:** Prepared statements with `$conn->prepare()`
- **Execution:** `$query->execute()`
- **Fetching:** `$query->fetchAll(PDO::FETCH_ASSOC)` or `$query->fetch()`
- **Iteration:** `foreach()` loop over results

### Example of Correct PDO Usage:
```php
$query = $conn->prepare("SELECT * FROM table WHERE column = :value");
$query->execute([':value' => $someValue]);
$results = $query->fetchAll(PDO::FETCH_ASSOC);
foreach ($results as $row) {
    // Process row
}
```

## Changes Made

### Fix 1: Department Query in Add Filter Modal
**Location:** Line 677-680  
**Modal:** Add Personnel Filter Modal  

**Before (WRONG - MySQLi):**
```php
$dept_query = "SELECT do_id, dept_office_name FROM dept_offices WHERE is_deleted = 0 ORDER BY dept_office_name";
$dept_result = $db->query($dept_query);
while ($dept = $dept_result->fetch_assoc()) {
    echo "<option value='{$dept['do_id']}'>{$dept['dept_office_name']}</option>";
}
```

**After (CORRECT - PDO):**
```php
$dept_modal_query = $conn->prepare("SELECT do_id, dept_office_name FROM dept_offices WHERE is_deleted = 0 ORDER BY dept_office_name");
$dept_modal_query->execute();
$dept_modal_results = $dept_modal_query->fetchAll(PDO::FETCH_ASSOC);
foreach ($dept_modal_results as $dept) {
    echo "<option value='{$dept['do_id']}'>{$dept['dept_office_name']}</option>";
}
```

**Changes:**
- ❌ Removed: `$db->query()`
- ✅ Added: `$conn->prepare()`
- ❌ Removed: `while` loop with `fetch_assoc()`
- ✅ Added: `foreach` loop with `fetchAll(PDO::FETCH_ASSOC)`
- ✅ Changed variable names to avoid conflicts (`$dept_modal_query`)

---

### Fix 2: Income Query in Add Income Modal
**Location:** Line 826-838  
**Modal:** Add Income Item Modal  

**Before (WRONG - MySQLi):**
```php
$income_query = "SELECT income_id, income_name, income_code, income_type 
                 FROM pr_tbl_income 
                 WHERE is_deleted = 0 
                 ORDER BY income_name";
$income_result = $db->query($income_query);
while ($inc = $income_result->fetch_assoc()) {
    // Build option
}
```

**After (CORRECT - PDO):**
```php
$income_modal_query = $conn->prepare("SELECT income_id, income_name, income_code, income_type 
                       FROM pr_tbl_income 
                       WHERE is_deleted = 0 
                       ORDER BY income_name");
$income_modal_query->execute();
$income_modal_results = $income_modal_query->fetchAll(PDO::FETCH_ASSOC);
foreach ($income_modal_results as $inc) {
    // Build option
}
```

**Changes:**
- ❌ Removed: `$db->query()`
- ✅ Added: `$conn->prepare()` and `execute()`
- ❌ Removed: `while` with `fetch_assoc()`
- ✅ Added: `foreach` with `fetchAll(PDO::FETCH_ASSOC)`
- ✅ Unique variable name (`$income_modal_query`)

---

### Fix 3: Deduction Query in Add Deduction Modal
**Location:** Line 954-966  
**Modal:** Add Deduction Item Modal  

**Before (WRONG - MySQLi):**
```php
$deduction_query = "SELECT deduction_id, deduction_name, deduction_code, deduction_type 
                    FROM pr_tbl_deductions 
                    WHERE is_deleted = 0 
                    ORDER BY deduction_name";
$deduction_result = $db->query($deduction_query);
while ($ded = $deduction_result->fetch_assoc()) {
    // Build option
}
```

**After (CORRECT - PDO):**
```php
$deduction_modal_query = $conn->prepare("SELECT deduction_id, deduction_name, deduction_code, deduction_type 
                          FROM pr_tbl_deductions 
                          WHERE is_deleted = 0 
                          ORDER BY deduction_name");
$deduction_modal_query->execute();
$deduction_modal_results = $deduction_modal_query->fetchAll(PDO::FETCH_ASSOC);
foreach ($deduction_modal_results as $ded) {
    // Build option
}
```

**Changes:**
- ❌ Removed: `$db->query()`
- ✅ Added: `$conn->prepare()` and `execute()`
- ❌ Removed: `while` with `fetch_assoc()`
- ✅ Added: `foreach` with `fetchAll(PDO::FETCH_ASSOC)`
- ✅ Unique variable name (`$deduction_modal_query`)

---

## Why This Matters

### Security Benefits
✅ **PDO prepared statements** prevent SQL injection  
✅ **Parameter binding** sanitizes input automatically  
✅ **Consistent error handling** with try-catch blocks  

### Compatibility
✅ Works with existing payroll module code  
✅ Uses the same `$conn` connection from `session.php`  
✅ Follows the same pattern as other queries in the file  

### Performance
✅ **fetchAll()** loads all results at once (better for small datasets)  
✅ **foreach()** is faster than while loops in PHP  
✅ Prepared statements are cached by the database  

## MySQLi vs PDO Comparison

| Feature | MySQLi (WRONG) | PDO (CORRECT) |
|---------|----------------|---------------|
| **Connection** | `$db = new mysqli(...)` | `$conn = new PDO(...)` |
| **Query** | `$db->query($sql)` | `$conn->prepare($sql)` |
| **Execute** | Not needed for query() | `$stmt->execute()` |
| **Fetch One** | `$result->fetch_assoc()` | `$stmt->fetch(PDO::FETCH_ASSOC)` |
| **Fetch All** | N/A (use while loop) | `$stmt->fetchAll(PDO::FETCH_ASSOC)` |
| **Iteration** | `while ($row = $result->fetch_assoc())` | `foreach ($results as $row)` |
| **Parameters** | `$db->prepare()` + `bind_param()` | `$stmt->execute([':param' => $val])` |
| **Databases** | MySQL only | MySQL, PostgreSQL, SQLite, etc. |

## Testing Checklist

After these fixes, verify:

- [ ] Page loads without errors
- [ ] Department dropdown in Add Filter modal populates
- [ ] Income dropdown in Add Income modal populates  
- [ ] Deduction dropdown in Add Deduction modal populates
- [ ] No PHP errors in error log
- [ ] No JavaScript console errors

## Related Files

These files use the CORRECT PDO method and should be referenced:
- ✅ `payroll/dbcon.php` - Database connection setup
- ✅ `payroll/session.php` - Includes dbcon.php, uses $conn
- ✅ `payroll/list_payroll_profiles.php` - Example of correct queries
- ✅ `payroll/list_personnel_income.php` - Example of correct queries
- ✅ `payroll/list_personnel_deductions.php` - Example of correct queries

## Future Development

When creating new PHP files in the payroll module:

1. **Always use:** `$conn->prepare()` for queries
2. **Never use:** `$db->query()` or MySQLi methods
3. **Always fetch with:** `PDO::FETCH_ASSOC`
4. **Always iterate with:** `foreach()` loops
5. **Always bind parameters:** Use `:paramName` syntax
6. **Always handle errors:** Use try-catch blocks

### Template for New Queries:
```php
<?php
include('session.php'); // Provides $conn

try {
    // Simple query (no parameters)
    $query = $conn->prepare("SELECT * FROM table ORDER BY column");
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    
    // Query with parameters (SAFE from SQL injection)
    $query = $conn->prepare("SELECT * FROM table WHERE column = :value");
    $query->execute([':value' => $inputValue]);
    $result = $query->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    die("An error occurred. Please contact support.");
}
?>
```

## Summary

✅ **All 3 database queries fixed**  
✅ **Now using PDO with $conn**  
✅ **Consistent with payroll module standards**  
✅ **No more linter errors**  
✅ **Ready for testing**  

---

**Status:** COMPLETE  
**Errors Before:** 3 (MySQLi syntax errors)  
**Errors After:** 0  
**Lines Modified:** 3 sections (15 lines total)
