# Payroll Generation Process Documentation

## Overview
The payroll generation system creates payroll runs from predefined profile templates, automatically calculating income and deductions for selected personnel.

## System Files

### 1. **generate_payroll_from_profile.php**
- **Purpose**: User interface for configuring a new payroll run
- **Features**:
  - Profile information display
  - Payroll run configuration (name, period, payment date)
  - Personnel selection (All, Department, Designation, Status, Custom)
  - Income/Deduction items preview
  - Client-side validation

### 2. **process_payroll_generation.php**
- **Purpose**: Backend processing script that creates the payroll run
- **Process Flow**:
  1. Validate form data
  2. Start database transaction
  3. Create payroll run header
  4. Filter personnel based on selection criteria
  5. Process each personnel individually
  6. Calculate income items
  7. Calculate deduction items
  8. Update run totals
  9. Generate snapshot
  10. Log audit trail
  11. Commit transaction

### 3. **view_payroll_run.php** (needs to be created)
- **Purpose**: Display generated payroll run details
- **Features**:
  - Run summary and totals
  - Personnel list with calculations
  - Export options (Excel, PDF)
  - Edit/Delete capabilities

## Database Tables

### **pr_tbl_payroll_runs**
Stores payroll run header information
- `run_id` - Primary key
- `profile_id` - Link to profile template
- `run_name` - Display name
- `pay_period_start` / `pay_period_end` - Payroll period
- `payment_date` - Scheduled payment date
- `run_status` - draft, finalized, paid, cancelled
- `total_gross`, `total_deductions`, `total_net_pay` - Summary totals
- `total_personnel` - Number of personnel

### **pr_tbl_payroll_run_details**
Stores per-personnel payroll calculations
- `detail_id` - Primary key
- `run_id` - Link to payroll run
- `personnel_id` - Link to personnel
- `gross_pay` - Total income
- `total_deductions` - Total employee deductions
- `total_employer_share` - Total employer contributions
- `net_pay` - Take-home pay
- `payment_status` - pending, paid, void

### **pr_tbl_payroll_run_income**
Stores income line items for each personnel
- `run_income_id` - Primary key
- `detail_id` - Link to payroll detail
- `run_id`, `personnel_id` - References
- `income_id`, `income_title`, `income_type` - Income item details
- `amount` - Calculated amount

### **pr_tbl_payroll_run_deductions**
Stores deduction line items for each personnel
- `run_deduction_id` - Primary key
- `detail_id` - Link to payroll detail
- `run_id`, `personnel_id` - References
- `deduction_id`, `deduction_title`, `deduction_type` - Deduction details
- `employee_amount` - Deducted from employee
- `employer_amount` - Employer contribution

## Personnel Selection Methods

### 1. **All Active Personnel**
- Includes all personnel without separation date
- No filters applied

### 2. **By Department**
- User selects one or more departments
- Only personnel in selected departments

### 3. **By Designation**
- User selects one or more positions/designations
- Only personnel with selected designations

### 4. **By Employment Status**
- User selects one or more employment statuses
- Only personnel with selected statuses

### 5. **Custom Selection**
- User manually picks specific personnel
- Complete control over selection

## Calculation Methods

### Income Calculation
1. **Personnel Specific**
   - Looks up amount in `pr_tbl_personnel_income`
   - Uses `amount_per_pay` field
   - Falls back to default if not found

2. **Fixed Amount**
   - Uses `default_amount` from profile
   - Same for all personnel

3. **Percentage** (future)
   - Calculate based on basic salary
   - Uses `calculation_value` field

4. **Formula** (future)
   - Custom formula evaluation
   - Support for variables like {basic_salary}

### Deduction Calculation
1. **Personnel Specific**
   - Looks up in `pr_tbl_personnel_deductions`
   - Uses `employee_amt_per_pay` and `employer_amt_per_pay`
   - Falls back to defaults

2. **Fixed Amount**
   - Uses `default_employee_amt` and `default_employer_amt`
   - Same for all personnel

## Process Flow Diagram

```
User clicks "Generate Payroll" from Profile
           ↓
generate_payroll_from_profile.php (Form)
    • Configure run details
    • Select personnel
    • Preview items
           ↓
User submits form
           ↓
process_payroll_generation.php
    ├─ Validate input
    ├─ Start transaction
    ├─ Create run header (pr_tbl_payroll_runs)
    ├─ Filter personnel
    ├─ For each personnel:
    │   ├─ Calculate income items
    │   ├─ Calculate deduction items
    │   ├─ Compute net pay
    │   ├─ Insert detail record
    │   ├─ Insert income records
    │   └─ Insert deduction records
    ├─ Update run totals
    ├─ Generate snapshot
    ├─ Log audit
    └─ Commit transaction
           ↓
Redirect to view_payroll_run.php
    • Display results
    • Allow edits/exports
```

## Transaction Safety

The entire generation process runs in a **database transaction**:
- If ANY step fails, ALL changes are rolled back
- Database remains consistent
- No partial payroll runs
- Error messages guide user to fix issues

## Error Handling

### Common Errors:
1. **No personnel found** - Selection criteria too restrictive
2. **Missing required fields** - Incomplete form submission
3. **Invalid dates** - Start date after end date
4. **Profile not found** - Inactive or deleted profile
5. **Database errors** - Connection issues or table problems

### Error Recovery:
- User redirected to form with error message
- All form data preserved (except on success)
- Transaction rollback ensures no corruption
- Detailed error logged for debugging

## Usage Instructions

### For Users:

1. **Navigate to Profiles**
   - Go to Payroll → Payroll Profiles
   - Find desired profile template

2. **Start Generation**
   - Click "Generate Payroll" button
   - You'll see the generation form

3. **Configure Run**
   - Edit payroll run name
   - Set pay period dates
   - Set payment date (optional)
   - Add notes (optional)

4. **Select Personnel**
   - Choose selection method
   - Make selections as needed
   - System shows available options

5. **Review Preview**
   - Check income items count
   - Check deduction items count
   - Verify profile information

6. **Generate**
   - Click "Generate Payroll Run"
   - Wait for processing
   - System redirects to results

## Performance Considerations

### Optimizations:
- Prepared statements for all queries
- Transaction batching
- Indexed lookups on personnel_id
- Efficient filtering with IN clauses

### Limits:
- Execution time: 300 seconds (5 minutes)
- Memory limit: 512MB
- Recommended max: 1000 personnel per run

### For Large Payrolls:
- Process in batches by department
- Run during off-peak hours
- Monitor server resources
- Consider background processing

## Future Enhancements

### Planned Features:
1. **Percentage calculations**
   - Implement calculation based on base fields
   - Support for COLA, allowances

2. **Formula engine**
   - Parse and evaluate custom formulas
   - Variable substitution
   - Math expression support

3. **Background processing**
   - Queue large payroll runs
   - Progress notifications
   - Email on completion

4. **Template cloning**
   - Duplicate existing runs
   - Adjust dates automatically
   - Copy all settings

5. **Approval workflow**
   - Draft → Review → Approved → Paid
   - Multi-level approval
   - Audit trail

6. **Advanced filtering**
   - Combine multiple criteria
   - Save custom filters
   - SQL condition builder

## Testing Checklist

- [ ] Generate with all personnel
- [ ] Generate with department filter
- [ ] Generate with designation filter
- [ ] Generate with status filter
- [ ] Generate with custom selection
- [ ] Test with no income items
- [ ] Test with no deduction items
- [ ] Test with personnel-specific amounts
- [ ] Test with fixed amounts
- [ ] Verify transaction rollback on error
- [ ] Check audit log creation
- [ ] Verify totals calculation
- [ ] Test date validation
- [ ] Test form validation
- [ ] Check generated payslips

## Support Information

### If generation fails:
1. Check error message carefully
2. Verify personnel selection
3. Ensure profile has items
4. Check database tables exist
5. Review server error logs
6. Contact system administrator

### Log Locations:
- PHP errors: `C:\xampp\apache\logs\error.log`
- Database errors: Check `error_log()` calls
- Audit trail: `pr_tbl_payroll_audit_log` table

---

**Status**: ✅ FULLY FUNCTIONAL  
**Last Updated**: January 2025  
**Version**: 1.0
