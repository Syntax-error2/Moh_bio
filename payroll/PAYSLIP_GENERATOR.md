# Payslip Generator - Complete Documentation

**Date Created:** October 20, 2025  
**File:** `payroll/generate_payslip.php`  
**Purpose:** Generate professional payslips combining income and deductions data

---

## Overview

The Payslip Generator creates a detailed, printable payslip for each personnel member showing:
- ✅ All income items (Basic Salary, PERA, COLA, etc.)
- ✅ All deductions (PhilHealth, SSS, Pag-IBIG, Tax, etc.)
- ✅ Gross Pay calculation
- ✅ Total Deductions calculation
- ✅ **Net Pay** (Take-home pay)
- ✅ Employer contributions summary
- ✅ Professional formatting with print support

---

## File Structure

```
payroll/
├── generate_payslip.php          ← Main payslip generator (NEW)
├── list_personnel_income.php     ← Updated with "Generate Payslip" button
├── list_personnel_deductions.php ← Updated with "Generate Payslip" button
└── PAYSLIP_GENERATOR.md          ← This file
```

---

## How It Works

### 1. Data Collection Process

```
┌─────────────────────────────────────────────────────────┐
│                  generate_payslip.php                   │
│                                                         │
│  Step 1: Get Personnel Information                     │
│  ├─ Name, ID, Department, Position                     │
│  └─ Employment Status                                  │
│                                                         │
│  Step 2: Query Income Data                            │
│  ├─ pr_tbl_personnel_income (active items)            │
│  ├─ pr_tbl_income (income definitions)                │
│  └─ Calculate: Total Gross Income                     │
│                                                         │
│  Step 3: Query Deductions Data                        │
│  ├─ pr_tbl_personnel_deductions (active items)        │
│  ├─ pr_tbl_deductions (deduction definitions)         │
│  ├─ Calculate: Total Employee Deductions              │
│  └─ Calculate: Total Employer Contributions           │
│                                                         │
│  Step 4: Calculate Net Pay                            │
│  └─ Net Pay = Gross Income - Employee Deductions      │
│                                                         │
│  Step 5: Generate HTML Payslip                        │
│  └─ Professional format with print styles             │
└─────────────────────────────────────────────────────────┘
```

---

## Database Queries

### Query 1: Personnel Information
```sql
SELECT 
    p.*,
    d.dept_office as department_name,
    des.designation as designation_name,
    es.emp_status as employment_status
FROM personnels p
LEFT JOIN dept_offices d ON p.dept_office = d.dept_office_id
LEFT JOIN designation des ON p.designation = des.designation_id
LEFT JOIN emp_status es ON p.emp_status = es.emp_status_id
WHERE p.personnel_id = :personnel_id
LIMIT 1
```

### Query 2: Income Items
```sql
SELECT 
    i.income_name,
    i.income_code,
    pi.amount_per_pay,
    i.income_type
FROM pr_tbl_personnel_income pi
INNER JOIN pr_tbl_income i ON pi.income_id = i.income_id
WHERE pi.personnel_id = :personnel_id 
  AND pi.is_active = 1
ORDER BY i.display_order ASC, i.income_name ASC
```

### Query 3: Deduction Items
```sql
SELECT 
    d.deduction_name,
    d.deduction_code,
    pd.employer_amt,
    pd.employee_amt,
    d.deduction_type
FROM pr_tbl_personnel_deductions pd
INNER JOIN pr_tbl_deductions d ON pd.deduction_id = d.deduction_id
WHERE pd.personnel_id = :personnel_id 
  AND pd.is_active = 1
ORDER BY d.display_order ASC, d.deduction_name ASC
```

---

## Calculation Logic

### 1. Gross Income Calculation
```php
$total_gross = 0;
foreach ($income_items as $item) {
    $total_gross += floatval($item['amount_per_pay']);
}
```

**Example:**
- Basic Salary: ₱25,000.00
- PERA: ₱2,000.00
- COLA: ₱1,500.00
- **Total Gross = ₱28,500.00**

### 2. Deductions Calculation
```php
$total_deductions = 0;        // Employee portion
$total_employer_share = 0;    // Employer portion

foreach ($deduction_items as $item) {
    $total_deductions += floatval($item['employee_amt']);
    $total_employer_share += floatval($item['employer_amt']);
}
```

**Example:**
| Deduction | Employee | Employer |
|-----------|----------|----------|
| PhilHealth | ₱700.00 | ₱700.00 |
| SSS | ₱1,125.00 | ₱2,475.00 |
| Pag-IBIG | ₱100.00 | ₱100.00 |
| Tax | ₱2,500.00 | ₱0.00 |
| **Total** | **₱4,425.00** | **₱3,275.00** |

### 3. Net Pay Calculation
```php
$net_pay = $total_gross - $total_deductions;
```

**Example:**
- Gross Income: ₱28,500.00
- Less: Deductions: ₱4,425.00
- **Net Pay = ₱24,075.00** ← Take-home pay

---

## URL Parameters

### Required Parameters
| Parameter | Description | Example |
|-----------|-------------|---------|
| `personnel_id` | Employee ID | `14` |

### Optional Parameters
| Parameter | Description | Default | Example |
|-----------|-------------|---------|---------|
| `dept` | Department ID | - | `2` |
| `period_start` | Pay period start date | First day of current month | `2025-10-01` |
| `period_end` | Pay period end date | Last day of current month | `2025-10-31` |
| `format` | Output format | `html` | `html` or `pdf` |
| `auto_print` | Auto-print on load | `0` | `1` |

### Example URLs

**Basic Usage:**
```
http://localhost/moh_hrms/payroll/generate_payslip.php?personnel_id=14
```

**With Department:**
```
http://localhost/moh_hrms/payroll/generate_payslip.php?personnel_id=14&dept=2
```

**Custom Pay Period:**
```
http://localhost/moh_hrms/payroll/generate_payslip.php?personnel_id=14&period_start=2025-10-01&period_end=2025-10-15
```

**Auto-Print:**
```
http://localhost/moh_hrms/payroll/generate_payslip.php?personnel_id=14&auto_print=1
```

---

## Payslip Sections

### 1. Header Section
- Organization name (from `school_form` table)
- Division/Department
- Region
- "PAYSLIP" title

### 2. Personnel Information
- Employee Name
- Employee ID
- Department
- Position/Designation
- Employment Status
- Pay Period dates

### 3. Income Table
| Column | Description |
|--------|-------------|
| Description | Income name (Basic Salary, PERA, etc.) |
| Code | Income code (BS, PERA, COLA, etc.) |
| Amount | Amount per pay period |

### 4. Deductions Table
| Column | Description |
|--------|-------------|
| Description | Deduction name |
| Code | Deduction code |
| Employee | Amount deducted from employee |
| Employer | Amount contributed by employer |

### 5. Summary Section
- **Total Gross Income** (green highlight)
- **Total Deductions** (red highlight)
- **NET PAY** (large, bold - final take-home)
- Total Employer Contribution (gray highlight)

### 6. Footer Section
- Generation timestamp
- Employee signature line
- HR Manager signature line

---

## Features

### ✅ Professional Design
- Clean, print-ready layout
- Color-coded sections (green=income, red=deductions)
- Monospace font for amounts (alignment)
- Professional header with organization details

### ✅ Print Support
- Optimized for A4/Letter paper
- Print button (triggers browser print dialog)
- Auto-print option via URL parameter
- Clean print styles (no background colors on paper)

### ✅ Data Validation
- Personnel ID required
- Handles missing income/deductions gracefully
- Shows "No items configured" messages
- Error handling with redirect fallback

### ✅ Responsive Layout
- Works on desktop and tablet
- Scales properly for different screen sizes
- Print layout optimized for paper

### ✅ Security
- Session-based access control
- Prepared statements (SQL injection protection)
- Output buffering (prevents header issues)
- Error logging (not displayed to users)

---

## Access Points

### From Income Page
```
list_personnel_income.php
    ↓
[Generate Payslip] button (top right)
    ↓
Opens payslip in new tab
```

### From Deductions Page
```
list_personnel_deductions.php
    ↓
[Generate Payslip] button (top right)
    ↓
Opens payslip in new tab
```

### Direct Link
```
<a href="generate_payslip.php?personnel_id=<?php echo $personnel_id; ?>&dept=<?php echo $dept; ?>" 
   target="_blank">
   Generate Payslip
</a>
```

---

## Sample Output

### Example Payslip Layout
```
═══════════════════════════════════════════════════════════
                  MINISTRY OF HEALTH
           Human Resource Management System
                                                           
                    PAYSLIP
───────────────────────────────────────────────────────────

Employee Name:     Juan D. Cruz
Employee ID:       14
Department:        Human Resources
Position:          HR Manager
Employment Status: Permanent
Pay Period:        October 1, 2025 - October 31, 2025

───────────────────────────────────────────────────────────
INCOME
───────────────────────────────────────────────────────────
Description              Code          Amount
Basic Salary             BS            ₱ 25,000.00
PERA                     PERA          ₱  2,000.00
COLA                     COLA          ₱  1,500.00
───────────────────────────────────────────────────────────

───────────────────────────────────────────────────────────
DEDUCTIONS
───────────────────────────────────────────────────────────
Description        Code    Employee      Employer
PhilHealth         PH      ₱   700.00    ₱   700.00
SSS                SSS     ₱ 1,125.00    ₱ 2,475.00
Pag-IBIG           HDMF    ₱   100.00    ₱   100.00
Withholding Tax    TAX     ₱ 2,500.00    ₱     0.00
───────────────────────────────────────────────────────────

═══════════════════════════════════════════════════════════
TOTAL GROSS INCOME:                          ₱ 28,500.00
TOTAL DEDUCTIONS:                            ₱  4,425.00
───────────────────────────────────────────────────────────
NET PAY:                                     ₱ 24,075.00
═══════════════════════════════════════════════════════════

Total Employer Contribution:                 ₱  3,275.00

───────────────────────────────────────────────────────────
Generated on October 20, 2025 03:00 PM

_________________________    _________________________
Juan D. Cruz                 Authorized Signatory
Employee Signature / Date    HR Manager / Date
═══════════════════════════════════════════════════════════
```

---

## Testing Checklist

### ✅ Before Testing - Setup
1. Ensure personnel exists in database
2. Add at least one income item via `list_personnel_income.php`
3. Add at least one deduction via `list_personnel_deductions.php`
4. Verify both tables have data:
   ```sql
   SELECT * FROM pr_tbl_personnel_income WHERE personnel_id = '14';
   SELECT * FROM pr_tbl_personnel_deductions WHERE personnel_id = '14';
   ```

### ✅ Test 1: Basic Generation
1. Go to: `list_personnel_income.php?dept=2&personnel_id=14`
2. Click "Generate Payslip" button
3. **Expected:** New tab opens with payslip
4. **Verify:** All income items displayed correctly
5. **Verify:** All deduction items displayed correctly
6. **Verify:** Calculations are accurate

### ✅ Test 2: Print Functionality
1. On payslip page, click "Print Payslip" button
2. **Expected:** Browser print dialog opens
3. **Verify:** Print preview shows clean layout
4. **Verify:** No background colors (saves ink)
5. **Verify:** Action buttons hidden in print

### ✅ Test 3: Missing Data Handling
1. Test with personnel having no income:
   ```
   generate_payslip.php?personnel_id=999
   ```
2. **Expected:** Shows "No income items configured"
3. **Verify:** Gross total = ₱0.00
4. **Verify:** No PHP errors

### ✅ Test 4: Calculations
1. Manually verify calculations:
   - Sum all income amounts = Total Gross
   - Sum all employee deductions = Total Deductions
   - Gross - Deductions = Net Pay
2. **Expected:** All calculations match manual calculation
3. **Verify:** No rounding errors

### ✅ Test 5: Back Button
1. Click "Back" button on payslip
2. **Expected:** Returns to income page with parameters preserved
3. **Verify:** dept and personnel_id in URL

---

## Troubleshooting

### Issue 1: Blank Payslip
**Symptom:** Payslip loads but shows no data

**Possible Causes:**
1. No income/deductions configured for personnel
2. Database tables not created
3. Data marked as `is_active = 0`

**Solutions:**
```sql
-- Check income data
SELECT * FROM pr_tbl_personnel_income 
WHERE personnel_id = 'YOUR_ID' AND is_active = 1;

-- Check deductions data
SELECT * FROM pr_tbl_personnel_deductions 
WHERE personnel_id = 'YOUR_ID' AND is_active = 1;

-- If empty, add data via the UI pages first
```

### Issue 2: Wrong Calculations
**Symptom:** Net pay doesn't match expected

**Check:**
1. Verify income amounts in database
2. Verify deduction amounts in database
3. Check for duplicate entries
4. Ensure only active items are counted

**Debug:**
```php
// Add after calculations in generate_payslip.php
error_log("Income items: " . count($income_items));
error_log("Total gross: " . $total_gross);
error_log("Deduction items: " . count($deduction_items));
error_log("Total deductions: " . $total_deductions);
error_log("Net pay: " . $net_pay);
```

### Issue 3: Print Doesn't Work
**Symptom:** Print button does nothing

**Solutions:**
1. Check browser console for JavaScript errors
2. Try manual print: `Ctrl+P` (Windows) or `Cmd+P` (Mac)
3. Test in different browser (Chrome, Firefox, Edge)
4. Check if pop-ups are blocked

### Issue 4: Personnel Not Found
**Symptom:** Error: "Personnel not found"

**Check:**
```sql
-- Verify personnel exists
SELECT * FROM personnels WHERE personnel_id = 'YOUR_ID';

-- Check if ID matches exactly (case-sensitive)
```

---

## Customization Options

### Change Organization Name
**File:** `generate_payslip.php` (lines ~100-110)
```php
// Modify school query or hardcode:
$school_name = 'Your Organization Name';
$division = 'Your Department';
$region = 'Your Region';
```

### Change Colors
**File:** `generate_payslip.php` (CSS section)
```css
/* Income section - currently green */
.summary-row.gross {
    background: #27ae60;  /* Change this */
}

/* Deductions section - currently red */
.summary-row.deductions {
    background: #e74c3c;  /* Change this */
}

/* Net Pay section - currently dark blue */
.summary-row.total {
    background: #2c3e50;  /* Change this */
}
```

### Add Company Logo
**File:** `generate_payslip.php` (after header div)
```html
<div class="payslip-header">
    <img src="path/to/logo.png" alt="Logo" style="max-width: 150px; margin-bottom: 10px;">
    <h1><?php echo htmlspecialchars($school_name); ?></h1>
    ...
</div>
```

### Change Date Format
**File:** `generate_payslip.php` (Pay Period display)
```php
// Current format: October 01, 2025
date('F d, Y', strtotime($pay_period_start))

// Change to: 10/01/2025
date('m/d/Y', strtotime($pay_period_start))

// Change to: 01-Oct-2025
date('d-M-Y', strtotime($pay_period_start))
```

---

## Future Enhancements

### Planned Features
1. **PDF Export** - Using TCPDF or mPDF library
2. **Email Payslip** - Send via email to personnel
3. **Batch Generation** - Generate payslips for all personnel
4. **Payslip History** - Store generated payslips in database
5. **Digital Signature** - Add electronic signature support
6. **Multi-Language** - Support for different languages
7. **Custom Templates** - Allow different payslip layouts

### PDF Export Implementation (Future)
```php
// Install TCPDF: composer require tecnickcom/tcpdf

require_once('vendor/autoload.php');

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->writeHTML($html_content);
$pdf->Output('payslip.pdf', 'D'); // Download
```

---

## Integration with Other Modules

### Attendance Module (Future)
```php
// Calculate actual days worked
$attendance_query = $conn->prepare("
    SELECT COUNT(DISTINCT date) as days_worked
    FROM attendance_logs
    WHERE personnel_id = :personnel_id
      AND date BETWEEN :start AND :end
");
```

### Leave Module (Future)
```php
// Calculate leave deductions
$leave_query = $conn->prepare("
    SELECT SUM(days) as leave_days
    FROM leave_requests
    WHERE personnel_id = :personnel_id
      AND status = 'approved'
      AND date_from BETWEEN :start AND :end
");
```

---

## Security Considerations

### ✅ Implemented
1. **Session validation** - Only logged-in users can generate
2. **Prepared statements** - SQL injection protection
3. **Output buffering** - Prevents header manipulation
4. **Error logging** - Errors logged, not displayed
5. **Input validation** - Personnel ID required and validated

### ⚠️ Recommendations
1. Add **role-based access** - Only HR can generate payslips
2. Add **audit logging** - Log who generated what payslip
3. Add **watermarks** - Mark payslips as "DRAFT" or "OFFICIAL"
4. Add **archive system** - Store generated payslips permanently
5. Add **access logs** - Track payslip views

---

## Related Files

| File | Purpose |
|------|---------|
| `generate_payslip.php` | Main payslip generator (this module) |
| `list_personnel_income.php` | Manage personnel income + Generate button |
| `list_personnel_deductions.php` | Manage personnel deductions + Generate button |
| `save_personnel_income.php` | Save income data |
| `save_personnel_deductions.php` | Save deductions data |
| `pr_tbl_income` | Income definitions table |
| `pr_tbl_personnel_income` | Personnel-specific income amounts |
| `pr_tbl_deductions` | Deduction definitions table |
| `pr_tbl_personnel_deductions` | Personnel-specific deduction amounts |

---

## Summary

### What Was Created
✅ **generate_payslip.php** - Complete payslip generator (600+ lines)
✅ **Integration buttons** - Added to income and deductions pages
✅ **Professional layout** - Print-ready design
✅ **Comprehensive calculations** - Gross, deductions, net pay
✅ **Error handling** - Graceful fallbacks for missing data

### How to Use
1. Navigate to personnel income or deductions page
2. Click "Generate Payslip" button
3. Payslip opens in new tab
4. Click "Print" to print or save as PDF (browser function)
5. Click "Back" to return to previous page

### Key Benefits
- ✅ **Professional appearance** for official documents
- ✅ **Accurate calculations** combining all pay components
- ✅ **Print-ready format** for paper archival
- ✅ **Easy access** from income and deductions pages
- ✅ **No additional setup** required - works immediately

---

**Status:** ✅ Complete and Ready for Production Use  
**Tested:** Database queries, calculations, print functionality  
**Documentation:** Complete with examples and troubleshooting  

---

*For questions or issues, refer to error logs at: `C:\xampp\apache\logs\error.log`*
