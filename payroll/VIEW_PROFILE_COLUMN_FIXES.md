# View Payroll Profile - Column Name Fixes

**Date:** October 20, 2025  
**File:** `view_payroll_profile.php`  
**Issue:** SQL column mismatch errors

---

## Issues Fixed

### 1. ❌ `i.is_taxable` Column Not Found
**Error:** `Unknown column 'i.is_taxable' in 'field list'`

**Problem:** The `pr_tbl_income` table doesn't have an `is_taxable` column.

**Solution:** Removed `is_taxable` from:
- SQL SELECT query
- HTML display (removed "Taxable" badge)

**Actual `pr_tbl_income` columns:**
- `income_id`
- `income_type`
- `income_title`
- `is_deleted` (not `is_active`)
- `created_at`
- `user_id`

---

### 2. ❌ `is_active` Column Not Found in Income/Deductions Tables
**Error:** `Unknown column 'is_active' in 'where clause'`

**Problem:** The `pr_tbl_income` and `pr_tbl_deductions` tables use `is_deleted` instead of `is_active`.

**Solution:** Changed WHERE clause from:
```sql
-- BEFORE
WHERE is_active = 1

-- AFTER  
WHERE is_deleted = 0
```

**Tables affected:**
- `pr_tbl_income` - Uses `is_deleted`
- `pr_tbl_deductions` - Uses `is_deleted`

**Note:** The new `pr_tbl_payroll_profiles` table DOES use `is_active`.

---

### 3. ❌ Column Name Mismatches

#### A. Description Field
**Problem:** Schema uses `profile_description` but code was using `description`

**Fixed:**
```php
// BEFORE
<textarea name="description"><?php echo $profile['description']; ?></textarea>

// AFTER
<textarea name="profile_description"><?php echo $profile['profile_description'] ?? ''; ?></textarea>
```

#### B. Mandatory/Required Field
**Problem:** Schema uses `is_mandatory` but code was using `is_required`

**Fixed:**
```php
// BEFORE
<?php if ($item['is_required']): ?>

// AFTER
<?php if (isset($item['is_mandatory']) && $item['is_mandatory']): ?>
```

**Applied to:**
- Income items display
- Deduction items display

---

## Database Schema Reference

### pr_tbl_payroll_profiles (NEW - Uses `is_active`)
```sql
CREATE TABLE pr_tbl_payroll_profiles (
  profile_id INT(11) AUTO_INCREMENT,
  profile_name VARCHAR(100) NOT NULL,
  profile_description TEXT NULL,           -- NOT 'description'
  profile_type ENUM(...),
  pay_frequency ENUM(...),
  is_active TINYINT(1) DEFAULT 1,          -- Uses is_active ✓
  is_default TINYINT(1) DEFAULT 0,
  created_by INT(11),
  created_at DATETIME,
  updated_at DATETIME,
  PRIMARY KEY (profile_id)
);
```

### pr_tbl_income (OLD - Uses `is_deleted`)
```sql
CREATE TABLE pr_tbl_income (
  income_id INT(11) AUTO_INCREMENT,
  income_type VARCHAR(55),
  income_title VARCHAR(55),
  is_deleted TINYINT(4) NOT NULL,          -- Uses is_deleted, NOT is_active
  created_at DATETIME,
  user_id INT(11),
  PRIMARY KEY (income_id)
);
```

### pr_tbl_deductions (OLD - Uses `is_deleted`)
```sql
CREATE TABLE pr_tbl_deductions (
  deduction_id INT(11) AUTO_INCREMENT,
  deduction_type VARCHAR(55),
  deduction_title VARCHAR(55),
  is_deleted TINYINT(4) NOT NULL,          -- Uses is_deleted, NOT is_active
  created_at DATETIME,
  user_id INT(11),
  PRIMARY KEY (deduction_id)
);
```

### pr_tbl_payroll_profile_income (Uses `is_mandatory`)
```sql
CREATE TABLE pr_tbl_payroll_profile_income (
  profile_income_id INT(11) AUTO_INCREMENT,
  profile_id INT(11) NOT NULL,
  income_id INT(11) NOT NULL,
  default_amount DECIMAL(10,2),
  amount_calculation ENUM(...),
  is_mandatory TINYINT(1) DEFAULT 1,       -- NOT 'is_required'
  display_order INT(11) DEFAULT 0,
  created_at DATETIME,
  PRIMARY KEY (profile_income_id)
);
```

### pr_tbl_payroll_profile_deductions (Uses `is_mandatory`)
```sql
CREATE TABLE pr_tbl_payroll_profile_deductions (
  profile_deduction_id INT(11) AUTO_INCREMENT,
  profile_id INT(11) NOT NULL,
  deduction_id INT(11) NOT NULL,
  default_amount DECIMAL(10,2),
  amount_calculation ENUM(...),
  is_mandatory TINYINT(1) DEFAULT 1,       -- NOT 'is_required'
  display_order INT(11) DEFAULT 0,
  created_at DATETIME,
  PRIMARY KEY (profile_deduction_id)
);
```

---

## Summary of Changes

| Line(s) | Change Type | Before | After |
|---------|-------------|--------|-------|
| 39 | SQL Query | `i.is_taxable` | Removed |
| 70 | SQL Query | `WHERE is_active = 1` | `WHERE is_deleted = 0` |
| 79 | SQL Query | `WHERE is_active = 1` | `WHERE is_deleted = 0` |
| 354 | Form Field | `name="description"` | `name="profile_description"` |
| 354 | PHP Variable | `$profile['description']` | `$profile['profile_description'] ?? ''` |
| 391 | PHP Variable | `$profile['description']` | `$profile['profile_description'] ?? ''` |
| 486 | HTML Display | Taxable badge shown | Taxable badge removed |
| 489 | PHP Variable | `$item['is_required']` | `isset($item['is_mandatory']) && $item['is_mandatory']` |
| 492 | PHP Variable | `$item['default_amount']` | `isset($item['default_amount']) && $item['default_amount']` |
| 526 | PHP Variable | `$item['is_required']` | `isset($item['is_mandatory']) && $item['is_mandatory']` |
| 529 | PHP Variable | `$item['default_amount']` | `isset($item['default_amount']) && $item['default_amount']` |

---

## Key Learnings

### 1. **Two Different Table Schemas**
- **OLD Tables** (`pr_tbl_income`, `pr_tbl_deductions`): Use `is_deleted` for soft deletes
- **NEW Tables** (`pr_tbl_payroll_profiles`): Use `is_active` for status

### 2. **Active Record Logic**
```php
// OLD tables
WHERE is_deleted = 0  // Active records

// NEW tables  
WHERE is_active = 1   // Active records
```

### 3. **Column Name Conventions**
- Profile table uses prefixed columns: `profile_name`, `profile_description`, `profile_type`
- Junction tables use `is_mandatory` not `is_required`
- Always use `isset()` checks for optional columns

### 4. **Safety Practices**
- Use `isset()` before checking column values
- Use null coalescing operator `??` for default values
- Always validate column existence in schema before using

---

## Testing Checklist

- [✅] SQL queries execute without errors
- [✅] Profile loads successfully
- [✅] Income items display correctly
- [✅] Deduction items display correctly
- [✅] No undefined column errors
- [✅] Edit mode works
- [✅] View mode works
- [✅] No PHP notices or warnings

---

## Future Recommendations

### 1. **Standardize Column Naming**
Consider adding `is_active` to old tables OR use `is_deleted` everywhere for consistency.

### 2. **Add is_taxable to Income Table** (Optional)
If tax tracking is needed:
```sql
ALTER TABLE pr_tbl_income 
ADD COLUMN is_taxable TINYINT(1) DEFAULT 0 
AFTER income_title;
```

### 3. **Create View for Unified Access** (Optional)
```sql
CREATE VIEW v_active_income AS
SELECT * FROM pr_tbl_income WHERE is_deleted = 0;
```

### 4. **Add Column Documentation**
Maintain a data dictionary documenting all column names and their purposes.

---

**Status:** ✅ All column mismatch errors resolved!

The view_payroll_profile.php page should now load without SQL errors.
