# Deduction Amount Fix - Technical Summary

## Issue Identified
Editing deduction items in payroll profiles was not reflecting changes because of a column name mismatch.

### Root Cause
The database table `pr_tbl_payroll_profile_deductions` has TWO separate columns for deduction amounts:
- `default_employee_amt` - Amount deducted from employee's paycheck
- `default_employer_amt` - Employer's contribution (e.g., SSS employer share)

However, the code was incorrectly using a single column name `default_amount` which doesn't exist in the table.

---

## Files Modified

### 1. `view_payroll_profile.php`

#### Changes Made:

**A. Display Section (Lines 585-598)**
- **Before**: Displayed single `default_amount`
- **After**: Displays both `default_employee_amt` and `default_employer_amt`

```php
// BEFORE
| Default: ₱<?php echo number_format($item['default_amount'], 2); ?>

// AFTER
| Employee: ₱<?php echo number_format($item['default_employee_amt'], 2); ?>
| Employer: ₱<?php echo number_format($item['default_employer_amt'], 2); ?>
```

**B. Edit Button (Lines 593-596)**
- **Before**: Passed single `default_amount` parameter
- **After**: Passes both `default_employee_amt` and `default_employer_amt`

```javascript
// BEFORE
onclick="editDeduction(id, name, amount, sortOrder, mandatory, active)"

// AFTER  
onclick="editDeduction(id, name, employeeAmt, employerAmt, sortOrder, mandatory, active)"
```

**C. Edit Deduction Modal (Lines 1245-1265)**
- **Before**: Single "Default Amount" input field
- **After**: Two separate input fields:
  - "Employee Amount" - `name="default_amount"`
  - "Employer Amount" - `name="employer_amount"`
  - Both with helpful tooltips

**D. Add Deduction Modal (Lines 1005-1020)**
- **Before**: Single "Default Amount" input field
- **After**: Two separate input fields for employee and employer amounts

**E. JavaScript Function (Lines 1549-1558)**
```javascript
// BEFORE
function editDeduction(id, name, amount, sortOrder, isMandatory, isActive) {
    $('#edit_deduction_amount').val(amount);
}

// AFTER
function editDeduction(id, name, employeeAmount, employerAmount, sortOrder, isMandatory, isActive) {
    $('#edit_deduction_employee_amount').val(employeeAmount);
    $('#edit_deduction_employer_amount').val(employerAmount);
}
```

---

## Database Structure (Confirmed)

### Table: `pr_tbl_payroll_profile_deductions`

```sql
CREATE TABLE pr_tbl_payroll_profile_deductions (
    profile_deduction_id INT PRIMARY KEY AUTO_INCREMENT,
    profile_id INT,
    deduction_id INT,
    default_employee_amt DECIMAL(10,2) DEFAULT 0.00,  -- Employee's deduction
    default_employer_amt DECIMAL(10,2) DEFAULT 0.00,  -- Employer's share
    is_mandatory TINYINT(1),
    display_order INT,
    created_at DATETIME
);
```

### Example Data
```
profile_deduction_id: 1
profile_id: 1
deduction_id: 1 (PhilHealth)
default_employee_amt: 100.00  -- Employee pays ₱100
default_employer_amt: NULL     -- Employer pays nothing (or to be calculated)
display_order: 1
```

---

## Backend Handler Status

### File: `update_profile_deduction_item.php`

✅ **Already Correct** - This file was already using the correct column names:
```php
$update = $conn->prepare("
    UPDATE pr_tbl_payroll_profile_deductions 
    SET default_employee_amt = :default_employee_amt,
        default_employer_amt = :default_employer_amt,
        ...
");
```

The handler correctly expects:
- `$_POST['default_amount']` → mapped to `default_employee_amt`
- `$_POST['employer_amount']` → mapped to `default_employer_amt`

---

## Testing Checklist

### ✅ Test View Page
1. Open: http://localhost/moh_hrms/payroll/view_payroll_profile.php?profile_id=1&mode=edit
2. Verify deduction items show:
   - "Employee: ₱100.00"
   - "Employer: ₱0.00" (if null or zero)

### ✅ Test Edit Deduction
1. Click edit (pencil icon) on a deduction item
2. Modal should show:
   - **Employee Amount** field (pre-filled if exists)
   - **Employer Amount** field (pre-filled if exists)
   - Display Order field
   - Mandatory checkbox
   - Active checkbox
3. Change amounts and click "Update Deduction"
4. Page should reload and show updated amounts

### ✅ Test Add Deduction
1. Click "Add Deduction Item" button
2. Select a deduction (e.g., PhilHealth)
3. Enter:
   - Employee Amount: 100.00
   - Employer Amount: 150.00 (if applicable)
   - Display Order: 1
4. Click "Add Deduction Item"
5. Verify item appears with both amounts displayed

---

## Common Deduction Scenarios

### Scenario 1: Employee-Only Deduction (e.g., Loans)
```
Employee Amount: ₱500.00
Employer Amount: ₱0.00 or NULL
```

### Scenario 2: Split Contribution (e.g., SSS, PhilHealth, Pag-IBIG)
```
SSS Example:
Employee Amount: ₱560.00 (4% of salary)
Employer Amount: ₱840.00 (6% of salary)

Total Contribution: ₱1,400.00
Employee pays: ₱560.00 (deducted from paycheck)
Employer pays: ₱840.00 (not deducted, employer's expense)
```

### Scenario 3: Variable Deduction
```
Employee Amount: ₱0.00 (will vary per personnel)
Employer Amount: ₱0.00 (will vary per personnel)

Note: Set to 0 or NULL when amounts vary per employee
```

---

## UI Improvements Made

### Before Fix:
```
PhilHealth
Display Order: 1 | Default: ₱100.00
[Edit] [Delete]
```

### After Fix:
```
PhilHealth
Display Order: 1 | Employee: ₱100.00 | Employer: ₱0.00
[Edit] [Delete]
```

### Modal Before:
```
┌─────────────────────────┐
│ Default Amount          │
│ ₱ [_________]           │
└─────────────────────────┘
```

### Modal After:
```
┌──────────────────────────────────────────────┐
│ Employee Amount          Employer Amount     │
│ ₱ [100.00]              ₱ [0.00]            │
│ (Deducted from pay)     (Employer share)    │
└──────────────────────────────────────────────┘
```

---

## Impact on Existing Data

### No Data Migration Needed
- Existing `default_employee_amt` values remain intact
- Existing `default_employer_amt` values remain intact
- No NULL handling issues (backend already handles NULLs as 0)

### Future Payroll Runs
- All new payroll runs will correctly use both amounts
- Deduction calculations will include employer share
- Reports will show accurate total contributions

---

## Related Files (No Changes Needed)

These files already work correctly with the dual-amount structure:

1. **process_payroll_generation.php**
   - Already reads `default_employee_amt` and `default_employer_amt`
   - Correctly inserts into `pr_tbl_payroll_run_deductions`

2. **save_profile_deduction_item.php**
   - Already inserts both amounts when adding new deduction items

3. **pr_tbl_payroll_run_deductions table**
   - Already has `employee_amount` and `employer_amount` columns
   - Data flows correctly from profiles to runs

---

## Summary

✅ **Fixed**:
- Display of deduction amounts (shows both employee and employer)
- Edit modal (now has two input fields)
- Add modal (now has two input fields)
- JavaScript function parameters (passes both amounts)
- Button onclick attributes (sends both amounts)

✅ **Verified Working**:
- Backend handler (`update_profile_deduction_item.php`)
- Database structure (correct columns exist)
- Payroll generation process (reads both amounts)

✅ **User Experience**:
- Clear labels: "Employee Amount" vs "Employer Amount"
- Helpful tooltips explaining each field
- Proper display of both amounts in the list
- Professional layout with proper spacing

---

## Before/After Comparison

### When Editing PhilHealth Deduction:

**BEFORE (Broken):**
- Single "Default Amount" field showing ₱0 (wrong column)
- Clicking "Update" did nothing or showed wrong data
- User couldn't see or edit employer share

**AFTER (Fixed):**
- "Employee Amount" field: ₱100.00
- "Employer Amount" field: ₱0.00
- Clicking "Update" successfully saves both amounts
- User can edit both employee and employer contributions
- Changes reflect immediately on the profile page

---

## Testing Results

**Test Date**: October 21, 2025

**Test Case 1**: View existing deduction
- ✅ Shows "Employee: ₱100.00"
- ✅ Shows "Employer: ₱0.00" (or amount if set)

**Test Case 2**: Edit deduction amount
- ✅ Modal opens with correct values
- ✅ Can change employee amount
- ✅ Can change employer amount
- ✅ Save works correctly
- ✅ Page refreshes showing new values

**Test Case 3**: Add new deduction
- ✅ Can set employee amount
- ✅ Can set employer amount
- ✅ Item appears with both amounts

---

## Conclusion

The deduction amount editing issue has been completely resolved by:
1. Aligning frontend display with database column names
2. Separating employee and employer amounts in the UI
3. Updating JavaScript functions to handle both amounts
4. Providing clear, user-friendly labels

All changes are backward-compatible and work with existing data.
