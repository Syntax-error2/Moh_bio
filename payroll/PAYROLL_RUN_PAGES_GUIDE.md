# Payroll Run Pages - User Guide

## Overview
Three new pages have been created for managing payroll runs:

1. **view_payroll_run.php** - View detailed payroll run information
2. **edit_payroll_run.php** - Edit payroll run (draft status only)
3. **print_payroll_run.php** - Print-friendly payroll report

---

## 1. View Payroll Run (`view_payroll_run.php`)

### URL Format
```
http://localhost/moh_hrms/payroll/view_payroll_run.php?run_id=4
```

### Features
- **Run Information Display**
  - Run ID, name, type, status
  - Pay period dates
  - Payment date
  - Profile information
  - Creator and approver details
  - Notes

- **Financial Summary**
  - Total personnel count
  - Total gross pay
  - Total deductions
  - Total employer share
  - Total net pay

- **Payment Status Breakdown**
  - Pending count
  - Paid count
  - Hold count
  - Cancelled count

- **Personnel Details Table**
  - Searchable and sortable
  - Shows: ID, Name, Department, Gross Pay, Deductions, Net Pay, Payment Status
  - Actions: View details for each personnel

- **Action Buttons**
  - Back to List
  - Edit Run (if status is draft)
  - Print Report
  - Export to Excel
  - Submit for Approval (if status is draft)
  - Approve/Reject (if status is pending and user is admin)

### Status Badge Colors
- **Draft**: Yellow/Warning
- **Pending**: Orange/Warning
- **Approved**: Blue/Info
- **Completed**: Green/Success
- **Cancelled**: Red/Danger

---

## 2. Edit Payroll Run (`edit_payroll_run.php`)

### URL Format
```
http://localhost/moh_hrms/payroll/edit_payroll_run.php?run_id=3
```

### Access Restrictions
- Only **DRAFT** status payroll runs can be edited
- Redirects to view page if run is not in draft status

### Editable Fields

#### Run Information Section
- Run Name (required)
- Run Type (dropdown: regular, special, 13th month, bonus, adjustment)
- Pay Period Start (required)
- Pay Period End (required)
- Payment Date (optional)
- Notes (optional)

#### Personnel Payroll Details Section
- **Bulk Actions**
  - Select all checkbox
  - Set payment status for multiple personnel (Pending/Paid/Hold)
  - Recalculate all amounts
  - Save all changes

- **Individual Personnel Actions**
  - Edit payroll details (opens modal)
  - Remove personnel from run
  - Change payment status

- **Search Functionality**
  - Real-time search by name or ID

### Save Operations
1. **Save Run Info**: Updates run details only
2. **Save All Changes**: Saves payment status changes for selected personnel
3. **Recalculate All**: Recalculates gross pay, deductions, and net pay based on current profile settings

---

## 3. Print Payroll Run (`print_payroll_run.php`)

### URL Format
```
http://localhost/moh_hrms/payroll/print_payroll_run.php?run_id=3
```

### Optional Parameters
- `?auto_print=1` - Auto-trigger print dialog on load
- `?detailed=1` - Include detailed income/deduction breakdown for each personnel

### Example URLs
```
# Basic print
http://localhost/moh_hrms/payroll/print_payroll_run.php?run_id=3

# Auto-print on load
http://localhost/moh_hrms/payroll/print_payroll_run.php?run_id=3&auto_print=1

# Detailed breakdown
http://localhost/moh_hrms/payroll/print_payroll_run.php?run_id=3&detailed=1
```

### Print Features
- **Clean Layout**: Professional, print-optimized design
- **Header Section**: Organization info, run name, status badge
- **Run Information**: All payroll run details
- **Financial Summary**: Highlighted totals box
- **Personnel Table**: Complete payroll listing with totals row
- **Signature Section**: Prepared by and Approved by fields
- **Footer**: Generation timestamp and page info

### Print Sections
1. Header with run name and status
2. Run information table
3. Financial summary box
4. Personnel payroll details table
5. Optional: Detailed income/deduction breakdown per personnel
6. Signature section
7. Footer with timestamp

---

## Navigation Flow

```
List Payroll History
    ↓
View Payroll Run (view_payroll_run.php?run_id=X)
    ↓ [If draft status]
Edit Payroll Run (edit_payroll_run.php?run_id=X)
    ↓
Update Payroll Run (update_payroll_run.php) [Form processor]
    ↓
Back to Edit or View
```

```
View Payroll Run
    ↓
Print (print_payroll_run.php?run_id=X) [Opens in new tab]
    ↓
Print Dialog (automatic if auto_print=1)
```

---

## Supporting Files Created

### 1. `update_payroll_run.php`
- Processes form submission from edit page
- Updates run information
- Validates data and permissions
- Redirects with success/error messages

### 2. Required AJAX Handlers (To be created separately)
These files are referenced but need to be created:

- `ajax_get_personnel_payroll_details.php` - Load detailed payroll for one personnel
- `ajax_edit_personnel_payroll.php` - Show edit form for personnel payroll
- `ajax_remove_personnel_from_run.php` - Remove personnel from payroll run
- `ajax_bulk_update_payment_status.php` - Update payment status for multiple personnel
- `ajax_save_payroll_changes.php` - Save multiple payroll changes
- `ajax_recalculate_payroll_run.php` - Recalculate all amounts
- `update_payroll_status.php` - Update run status (submit/approve/reject)
- `export_payroll_run.php` - Export to Excel

---

## Database Tables Used

### pr_tbl_payroll_runs
- Main payroll run table
- Fields: run_id, run_name, run_status, pay_period_start, pay_period_end, etc.

### pr_tbl_payroll_run_details
- Personnel-specific payroll details
- Fields: detail_id, run_id, personnel_id, gross_pay, deductions, net_pay, payment_status

### pr_tbl_payroll_run_income
- Individual income items per personnel
- Fields: run_income_id, detail_id, income_id, income_title, amount

### pr_tbl_payroll_run_deductions
- Individual deduction items per personnel
- Fields: run_deduction_id, detail_id, deduction_id, deduction_title, employee_amount, employer_amount

### pr_tbl_payroll_profiles
- Profile information (linked to runs)

### personnel
- Employee information (names, departments)

### dept_offices
- Department information

---

## Testing Instructions

### Test View Page
1. Generate a payroll run first
2. Visit: `http://localhost/moh_hrms/payroll/view_payroll_run.php?run_id=4`
3. Verify all sections display correctly
4. Test action buttons

### Test Edit Page
1. Ensure run status is "draft"
2. Visit: `http://localhost/moh_hrms/payroll/edit_payroll_run.php?run_id=3`
3. Try editing run information
4. Try changing personnel payment status
5. Submit form and verify updates

### Test Print Page
1. Visit: `http://localhost/moh_hrms/payroll/print_payroll_run.php?run_id=3`
2. Verify layout is clean
3. Click "Print Report" button
4. Test with `?detailed=1` parameter
5. Test with `?auto_print=1` parameter

---

## Next Steps

### Required AJAX Handlers
Create these files to enable full functionality:

1. **ajax_get_personnel_payroll_details.php**
   ```php
   // Display income/deductions breakdown for one personnel
   // Returns HTML for modal
   ```

2. **ajax_edit_personnel_payroll.php**
   ```php
   // Show edit form for personnel payroll amounts
   // Returns HTML form for modal
   ```

3. **ajax_remove_personnel_from_run.php**
   ```php
   // Remove personnel from payroll run
   // Returns JSON response
   ```

4. **ajax_bulk_update_payment_status.php**
   ```php
   // Update payment status for multiple personnel
   // Returns JSON response
   ```

5. **update_payroll_status.php**
   ```php
   // Handle status changes (submit, approve, reject)
   // Updates run_status and audit trail
   ```

6. **export_payroll_run.php**
   ```php
   // Export payroll to Excel format
   // Returns downloadable file
   ```

### Integration with Existing System
- Update `list_payroll_history.php` to link to view_payroll_run.php
- Update `generate_payroll_from_profile.php` redirect (already done)
- Add "View" buttons in payroll listings

---

## Troubleshooting

### Issue: 404 Not Found
- Verify files are in `/xampp/htdocs/moh_hrms/payroll/` directory
- Check file names match exactly (case-sensitive on Linux)

### Issue: No Data Displayed
- Verify run_id exists in database
- Check database connection
- Review error logs: `C:\xampp\apache\logs\error.log`

### Issue: Edit Page Redirects to View
- Check run status is "draft"
- Only draft runs can be edited

### Issue: Print Layout Issues
- Use Chrome or Firefox for best results
- Check printer settings (margins, scaling)
- Try print preview before printing

---

## Summary

✅ **Created:**
- `view_payroll_run.php` - Complete view page with all details
- `edit_payroll_run.php` - Full editing capabilities
- `print_payroll_run.php` - Professional print format
- `update_payroll_run.php` - Form processor

✅ **Features:**
- Status-based access control
- Comprehensive financial summaries
- Personnel listing with actions
- Print-optimized layout
- Signature sections
- Search and filter functionality

✅ **Ready to Use:**
- http://localhost/moh_hrms/payroll/view_payroll_run.php?run_id=4
- http://localhost/moh_hrms/payroll/edit_payroll_run.php?run_id=3
- http://localhost/moh_hrms/payroll/print_payroll_run.php?run_id=3

🔧 **Still Needed:**
- AJAX handler files (8 files)
- Integration with list pages
- Excel export functionality
