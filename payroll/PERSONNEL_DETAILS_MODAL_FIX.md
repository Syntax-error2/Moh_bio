# Personnel Payroll Details Modal - AJAX Implementation

## Overview
Created AJAX endpoint to display detailed payroll information for individual personnel when clicking the "View Details" button in the payroll run page.

---

## Problem
The "Personnel Payroll Details" modal was showing "Error loading details" because the AJAX endpoint file didn't exist.

**Error:**
- Modal showed: "Error loading details"
- Missing file: `ajax_get_personnel_payroll_details.php`
- Called from: `view_payroll_run.php` line 358

---

## Solution

### Created: `ajax_get_personnel_payroll_details.php`

**Purpose:** Load and display comprehensive payroll details for a single personnel in a modal popup

**Input:** `detail_id` (from GET parameter)

**Output:** HTML content with complete payroll breakdown

---

## Features

### 1. Personnel Information Card
Displays basic personnel details:
- Full name (formatted: Last, First M.)
- Personnel ID/Code
- Department
- Designation
- Payroll run name
- Pay period dates

### 2. Income Breakdown Card (Green)
Shows all income items:
- Income item name
- Income type (badge)
- Amount
- **Total Income** row

**Table Columns:**
- Income Item
- Type
- Amount

### 3. Deduction Breakdown Card (Yellow)
Shows all deduction items with dual-share breakdown:
- Deduction item name
- Deduction type (badge)
- Employee share (deducted from pay)
- Employer share (additional cost)
- Total per item
- **Total Deductions** row

**Table Columns:**
- Deduction Item
- Type
- Employee Share
- Employer Share
- Total

**Note:** Explains difference between employee and employer shares

### 4. Payroll Summary Card (Blue)
Complete financial summary:
- **Gross Pay:** Total earnings before deductions
- **Total Deductions (Employee):** Amount deducted from gross pay
- **Net Pay:** Take-home amount (gross - deductions)
- **Employer Share:** Additional cost to employer
- **Total Cost to Employer:** Gross pay + employer share

**Additional Info:**
- Payment status (badge with colors)
- Payment date (if paid)
- Notes (if any)

---

## Database Schema Used

### Main Query (Personnel Details):
```sql
SELECT prd.*,
       p.fname, p.lname, p.mname, p.personnel_id_code,
       d.dept_office_name,
       des.des_name as designation_name,
       pr.run_name, pr.pay_period_start, pr.pay_period_end
FROM pr_tbl_payroll_run_details prd
LEFT JOIN personnels p ON prd.personnel_id = p.personnel_id
LEFT JOIN dept_offices d ON p.do_id = d.do_id
LEFT JOIN designation des ON p.des_id = des.des_id
LEFT JOIN pr_tbl_payroll_runs pr ON prd.run_id = pr.run_id
WHERE prd.detail_id = :detail_id
```

### Income Items Query:
```sql
SELECT pri.*, i.income_name, i.income_type
FROM pr_tbl_payroll_run_income pri
LEFT JOIN pr_tbl_income i ON pri.income_id = i.income_id
WHERE pri.detail_id = :detail_id
ORDER BY i.income_type, i.income_name
```

### Deduction Items Query:
```sql
SELECT prd.*, d.deduction_name, d.deduction_type
FROM pr_tbl_payroll_run_deductions prd
LEFT JOIN pr_tbl_deductions d ON prd.deduction_id = d.deduction_id
WHERE prd.detail_id = :detail_id
ORDER BY d.deduction_type, d.deduction_name
```

---

## Tables Involved

### 1. pr_tbl_payroll_run_details (Main)
- `detail_id` - Primary key
- `run_id` - Foreign key to payroll runs
- `personnel_id` - Foreign key to personnels
- `gross_pay` - Total earnings
- `total_deductions` - Employee share of deductions
- `employer_share` - Employer's contribution
- `net_pay` - Take-home pay
- `payment_status` - unpaid/pending/paid
- `payment_date` - Date of payment
- `notes` - Additional notes

### 2. personnels
- `personnel_id` - Primary key
- `personnel_id_code` - Employee code
- `fname`, `lname`, `mname` - Name fields
- `do_id` - Foreign key to dept_offices
- `des_id` - Foreign key to designation

### 3. dept_offices
- `do_id` - Primary key
- `dept_office_name` - Department name

### 4. designation
- `des_id` - Primary key
- `des_name` - Designation/position name

### 5. pr_tbl_payroll_runs
- `run_id` - Primary key
- `run_name` - Payroll run name
- `pay_period_start` - Start date
- `pay_period_end` - End date

### 6. pr_tbl_payroll_run_income
- `run_income_id` - Primary key
- `detail_id` - Foreign key to run details
- `income_id` - Foreign key to income types
- `amount` - Income amount

### 7. pr_tbl_income
- `income_id` - Primary key
- `income_name` - Income item name
- `income_type` - Type/category

### 8. pr_tbl_payroll_run_deductions
- `run_deduction_id` - Primary key
- `detail_id` - Foreign key to run details
- `deduction_id` - Foreign key to deduction types
- `employee_share` - Amount from employee
- `employer_share` - Amount from employer

### 9. pr_tbl_deductions
- `deduction_id` - Primary key
- `deduction_name` - Deduction item name
- `deduction_type` - Type/category

---

## Column Name Fixes Applied

### Fixed: `designation` → `des_name`
**Before (INCORRECT):**
```sql
des.designation as designation_name
```

**After (CORRECT):**
```sql
des.des_name as designation_name
```

**Reason:** The `designation` table uses `des_name` column, not `designation`

---

## UI/UX Features

### 1. Color-Coded Cards
- **Blue:** Personnel info and summary (primary information)
- **Green:** Income breakdown (positive/earnings)
- **Yellow:** Deduction breakdown (warnings/costs)
- **Info:** Summary totals

### 2. Badge Colors for Status
- **Success (green):** Paid status
- **Warning (yellow):** Pending status
- **Secondary (gray):** Unpaid status

### 3. Typography Indicators
- **Bold:** Important totals and labels
- **Text-danger (red):** Deductions (negative impact)
- **Text-info (blue):** Employer costs (informational)
- **Text-muted:** Notes and explanations

### 4. Responsive Tables
- Small table size (`.table-sm`) for better spacing
- Bordered tables for clarity
- Right-aligned numbers for easy comparison
- Summary rows highlighted with background colors

### 5. Icons
- 👤 User icon for personnel info
- ➕ Plus icon for income
- ➖ Minus icon for deductions
- 🧮 Calculator icon for summary

---

## AJAX Flow

### 1. User Clicks "View Details"
```javascript
viewPersonnelDetails(detailId)
```

### 2. Modal Opens
```javascript
$('#personnelDetailsModal').modal('show');
```

### 3. Loading Indicator
```javascript
$('#personnelDetailsContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i><p>Loading...</p></div>');
```

### 4. AJAX Request
```javascript
$.ajax({
    url: 'ajax_get_personnel_payroll_details.php',
    method: 'GET',
    data: { detail_id: detailId },
    success: function(response) {
        $('#personnelDetailsContent').html(response);
    },
    error: function() {
        $('#personnelDetailsContent').html('<div class="alert alert-danger">Error loading details</div>');
    }
});
```

### 5. Display Results
- Success: Shows detailed breakdown
- Error: Shows error message

---

## Example Output Structure

```
┌─────────────────────────────────────┐
│ 👤 Personnel Information (Blue)     │
├─────────────────────────────────────┤
│ Name: CORTADO, ROGELIO              │
│ ID: EMP-001                         │
│ Department: Municipal Mayor Office  │
│ Designation: Administrative Officer │
│ Payroll Run: Oct 2025 Regular      │
│ Pay Period: Oct 1-15, 2025         │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│ ➕ Income Breakdown (Green)         │
├─────────────────────────────────────┤
│ Basic Salary      ₱15,000.00       │
│ PERA             ₱2,000.00        │
│ ─────────────────────────────      │
│ Total Income      ₱17,000.00       │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│ ➖ Deduction Breakdown (Yellow)     │
├─────────────────────────────────────┤
│ Item      Employee  Employer  Total │
│ GSIS      ₱1,000   ₱1,500   ₱2,500│
│ PhilHealth ₱300    ₱300     ₱600 │
│ Pag-IBIG   ₱100    ₱100     ₱200 │
│ ─────────────────────────────────  │
│ Total     ₱1,400   ₱1,900   ₱3,300│
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│ 🧮 Payroll Summary (Blue)           │
├─────────────────────────────────────┤
│ Gross Pay:              ₱17,000.00  │
│ Total Deductions:      -₱1,400.00  │
│ Net Pay:                ₱15,600.00  │
│ Employer Share:         ₱1,900.00  │
│ Total Cost:             ₱18,900.00  │
│                                     │
│ Status: 💰 Paid                     │
│ Payment Date: Oct 20, 2025         │
└─────────────────────────────────────┘
```

---

## Security Features

1. **Input Validation:** 
   - Checks if `detail_id` is valid integer
   - Returns error if invalid

2. **Database Preparation:**
   - Uses PDO prepared statements
   - Prevents SQL injection

3. **Session Check:**
   - Includes `session.php`
   - Only authenticated users can access

4. **Output Escaping:**
   - Uses `htmlspecialchars()` on all user data
   - Prevents XSS attacks

5. **Error Handling:**
   - Try-catch blocks
   - User-friendly error messages
   - Doesn't expose technical details

---

## Error Handling

### Invalid detail_id:
```
Invalid detail ID
```

### Personnel not found:
```
Personnel payroll details not found
```

### Database error:
```
Error: [Safe error message]
```

### AJAX error (in modal):
```
Error loading details
```

---

## Testing Checklist

### ✅ Functionality
- [ ] Modal opens when clicking "View Details"
- [ ] Loading spinner shows while fetching data
- [ ] Personnel information displays correctly
- [ ] Income items show with correct amounts
- [ ] Deduction items show employee/employer shares
- [ ] Summary calculations are accurate
- [ ] Payment status badge shows correct color
- [ ] Modal closes properly

### ✅ Data Validation
- [ ] Invalid detail_id shows error
- [ ] Non-existent detail_id shows error
- [ ] Empty income list handled gracefully
- [ ] Empty deduction list handled gracefully
- [ ] Null values display as "N/A"

### ✅ UI/UX
- [ ] Cards have proper colors
- [ ] Icons display correctly
- [ ] Tables are responsive
- [ ] Numbers align right
- [ ] Currency symbols present
- [ ] Badges show proper colors
- [ ] Text is readable

### ✅ Security
- [ ] SQL injection prevented
- [ ] XSS attacks prevented
- [ ] Requires authentication
- [ ] Errors don't expose internals

---

## Future Enhancements

### Possible Improvements:
1. **Print Button:** Allow printing individual payslip
2. **Export PDF:** Generate PDF payslip
3. **Edit Button:** Quick edit from modal (if draft)
4. **Payment History:** Show payment transactions
5. **Attendance Link:** Show attendance records
6. **Comparison View:** Compare with previous payrolls
7. **Email Payslip:** Send payslip to personnel email

---

## Related Files

**Main Page:**
- `view_payroll_run.php` - Calls this AJAX endpoint

**AJAX Endpoint:**
- `ajax_get_personnel_payroll_details.php` - **NEW FILE CREATED**

**Dependencies:**
- `dbcon.php` - Database connection
- `session.php` - Authentication check

**Database Tables:**
- 9 tables joined (see Tables Involved section)

---

## Summary

✅ **Created:** `ajax_get_personnel_payroll_details.php` (218 lines)
✅ **Fixed:** Column name `designation` → `des_name`
✅ **Features:** 4 information cards with complete breakdown
✅ **Security:** Input validation, prepared statements, XSS prevention
✅ **UI:** Color-coded cards, responsive tables, status badges
✅ **Error Handling:** User-friendly messages, proper try-catch

**Result:** Personnel details modal now loads correctly with comprehensive payroll information! 🎉
