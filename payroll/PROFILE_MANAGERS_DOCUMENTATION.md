# Personnel Filters, Income Items & Deduction Items Manager

## Overview
Comprehensive manager modals have been added to `view_payroll_profile.php` for managing:
- **Personnel Filters** - Define which employees are included in the payroll profile
- **Income Items** - Configure salary components (basic pay, allowances, bonuses, etc.)
- **Deduction Items** - Configure payroll deductions (taxes, loans, SSS, PhilHealth, etc.)

---

## Features Added

### 1. Personnel Filters Manager

#### Add Filter Modal
**Modal ID:** `#addFilterModal`

**Filter Types Available:**
1. **Department/Office** - Filter by specific departments
2. **Employment Status** - Permanent, Casual, Job Order, Contract of Service, etc.
3. **Position/Designation** - Filter by job title or position
4. **Salary Grade** - Filter by salary grade range (1-30)
5. **Gender** - Male or Female
6. **Age Range** - Filter by age (e.g., 25-60 years old)
7. **Custom SQL** - Advanced users can write custom SQL conditions

**Filter Operators:**
- Equals (=)
- Not Equals (!=)
- In (Multiple Values)
- Not In
- Contains (LIKE)
- Greater Than (>)
- Less Than (<)
- Between

**Form Fields:**
```php
- filter_type (required) - Type of filter
- filter_operator - Comparison operator
- department_ids[] - Multiple department selection
- employment_status[] - Multiple status selection
- position_value - Position name with wildcard support
- salary_grade_from - Starting grade
- salary_grade_to - Ending grade
- gender_value - Male/Female
- age_from - Minimum age
- age_to - Maximum age
- custom_condition - Custom SQL WHERE condition
- filter_description - Human-readable description
- is_active - Whether filter is active
```

**Dynamic Form:**
- Form fields change based on selected filter type
- jQuery shows/hides relevant options
- Multi-select for departments and employment status
- Wildcard support for position filtering (e.g., "Nurse%")

**Example Filters:**
```
1. All Permanent Employees in HR Department
2. Salary Grade 15 and above
3. Female employees between 25-50 years old
4. Job Order employees in Medical Department
5. Custom: date_hired >= '2020-01-01'
```

#### Edit Filter Modal
**Modal ID:** `#editFilterModal`

**Editable Fields:**
- Filter description
- Active status

**Note:** To change filter type or conditions, delete and create new filter.

#### Filter Display
- Shows all active filters in card format
- Edit/Delete buttons appear in edit mode
- Positioned absolutely in top-right corner
- Yellow edit button (btn-warning)
- Red delete button (btn-danger)

---

### 2. Income Items Manager

#### Add Income Modal
**Modal ID:** `#addIncomeModal`

**Form Fields:**
```php
- income_id (required) - Select from pr_tbl_income
- default_amount - Default amount (₱)
- sort_order - Display order (0-999)
- calculation_method - Fixed, Percentage, Formula, Manual
- formula - Custom calculation formula (if method = formula)
- is_mandatory - Always include in payroll
- is_active - Enable this income item
```

**Calculation Methods:**
1. **Fixed Amount** - Same amount for all personnel
2. **Percentage of Basic Salary** - Calculated based on basic salary
3. **Custom Formula** - Use variables like {basic_salary}, {daily_rate}, {monthly_rate}, {days_worked}
4. **Manual Entry Per Personnel** - Different amount per employee

**Income Types:**
- **Fixed** - Same for all (e.g., COLA, Rice Subsidy)
- **Variable** - Varies per employee (e.g., Overtime, Performance Bonus)

**Income Selection Dropdown:**
```html
<option value="1" data-type="fixed" data-code="BASIC">
    Basic Salary [BASIC] - Fixed
</option>
<option value="2" data-type="variable" data-code="OT">
    Overtime Pay [OT] - Variable
</option>
```

**Example Incomes:**
```
1. Basic Salary - Fixed, ₱15,000.00
2. PERA - Fixed, ₱2,000.00
3. Overtime - Variable, Manual entry
4. Hazard Pay - Fixed, 25% of basic salary
```

#### Edit Income Modal
**Modal ID:** `#editIncomeModal`

**Editable Fields:**
- Default amount
- Display order
- Mandatory status
- Active status

**Note:** Income item name is read-only (shows which income from master list)

#### Income Display
- Shows income name, type badge, and details
- Edit/Delete buttons in edit mode
- Green badge for fixed income
- Blue badge for variable income
- Action buttons: Edit (yellow), Delete (red)

---

### 3. Deduction Items Manager

#### Add Deduction Modal
**Modal ID:** `#addDeductionModal`

**Form Fields:**
```php
- deduction_id (required) - Select from pr_tbl_deductions
- default_amount - Default amount (₱)
- sort_order - Display order (0-999)
- calculation_method - Fixed, Percentage, Formula, Manual
- formula - Custom calculation formula
- is_mandatory - Always deduct in payroll
- is_active - Enable this deduction
- priority - High, Medium, Low
```

**Calculation Methods:**
1. **Fixed Amount** - Same amount for all
2. **Percentage of Gross Income** - % of total income
3. **Custom Formula** - Use {gross_income}, {basic_salary}, {total_income}, {taxable_income}
4. **Manual Entry Per Personnel** - Different per employee

**Deduction Priority Levels:**
- **High** - Deduct first (Government mandatories: SSS, PhilHealth, Pag-IBIG, Tax)
- **Medium** - Standard deductions (Loans, Uniforms)
- **Low** - Deduct last (Optional: Canteen, Parking)

**Deduction Types:**
- **Fixed** - Same for all (e.g., Union Dues)
- **Variable** - Varies per employee (e.g., Loans, Cash Advances)

**Example Deductions:**
```
1. SSS - Variable, Manual, High Priority, Mandatory
2. PhilHealth - Percentage, 2% of basic, High Priority, Mandatory
3. Pag-IBIG - Fixed, ₱200.00, High Priority, Mandatory
4. Withholding Tax - Formula, Based on tax table, High Priority, Mandatory
5. Cash Advance - Variable, Manual, Low Priority
```

#### Edit Deduction Modal
**Modal ID:** `#editDeductionModal`

**Editable Fields:**
- Default amount
- Display order
- Mandatory status
- Active status

#### Deduction Display
- Shows deduction name, type badge, and details
- Edit/Delete buttons in edit mode
- Orange badge for fixed deduction
- Blue badge for variable deduction
- Action buttons: Edit (yellow), Delete (red)

---

## JavaScript Functions

### Personnel Filters
```javascript
// Filter type change handler
$('#filter_type').change() - Shows/hides relevant filter options

// Save filter
saveFilter() - AJAX POST to save_profile_filter.php

// Edit filter
editFilter(filterId, description, isActive) - Opens edit modal

// Update filter
updateFilter() - AJAX POST to update_profile_filter.php

// Delete filter
deleteFilter(filterId) - AJAX POST to delete_profile_filter.php
```

### Income Items
```javascript
// Save income item
saveIncomeItem() - AJAX POST to save_profile_income_item.php

// Edit income
editIncome(id, name, amount, sortOrder, isMandatory, isActive)

// Update income
updateIncomeItem() - AJAX POST to update_profile_income_item.php

// Delete income
deleteIncomeItem(itemId) - AJAX POST to delete_profile_income_item.php
```

### Deduction Items
```javascript
// Save deduction item
saveDeductionItem() - AJAX POST to save_profile_deduction_item.php

// Edit deduction
editDeduction(id, name, amount, sortOrder, isMandatory, isActive)

// Update deduction
updateDeductionItem() - AJAX POST to update_profile_deduction_item.php

// Delete deduction
deleteDeductionItem(itemId) - AJAX POST to delete_profile_deduction_item.php
```

---

## Backend PHP Files Required

### Personnel Filters
```
1. save_profile_filter.php - Add new filter
2. update_profile_filter.php - Update existing filter
3. delete_profile_filter.php - Remove filter
```

**Expected Request Format (save_profile_filter.php):**
```php
POST {
    profile_id: 123,
    filter_type: "department",
    filter_operator: "in",
    department_ids: [1, 3, 5],
    filter_description: "HR and Finance departments",
    is_active: 1
}
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Filter added successfully",
    "filter_id": 45
}
```

### Income Items
```
1. save_profile_income_item.php - Add income to profile
2. update_profile_income_item.php - Update income item
3. delete_profile_income_item.php - Remove income from profile
```

**Expected Request Format (save_profile_income_item.php):**
```php
POST {
    profile_id: 123,
    income_id: 5,
    default_amount: 2000.00,
    sort_order: 10,
    calculation_method: "fixed",
    is_mandatory: 1,
    is_active: 1
}
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Income item added successfully",
    "profile_income_id": 78
}
```

### Deduction Items
```
1. save_profile_deduction_item.php - Add deduction to profile
2. update_profile_deduction_item.php - Update deduction item
3. delete_profile_deduction_item.php - Remove deduction from profile
```

**Expected Request Format (save_profile_deduction_item.php):**
```php
POST {
    profile_id: 123,
    deduction_id: 8,
    default_amount: 200.00,
    sort_order: 5,
    calculation_method: "fixed",
    is_mandatory: 1,
    is_active: 1,
    priority: "high"
}
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Deduction item added successfully",
    "profile_deduction_id": 92
}
```

---

## Database Tables

### Personnel Filters
**Table:** `pr_tbl_payroll_profile_filters` (needs to be created)

```sql
CREATE TABLE pr_tbl_payroll_profile_filters (
    filter_id INT AUTO_INCREMENT PRIMARY KEY,
    profile_id INT NOT NULL,
    filter_type ENUM('department', 'employment_status', 'position', 'salary_grade', 'gender', 'age_range', 'custom'),
    filter_operator VARCHAR(20),
    filter_value TEXT,
    filter_description VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES pr_tbl_payroll_profiles(profile_id) ON DELETE CASCADE
);
```

### Income Items Link
**Table:** `pr_tbl_payroll_profile_income` (needs to be created)

```sql
CREATE TABLE pr_tbl_payroll_profile_income (
    profile_income_id INT AUTO_INCREMENT PRIMARY KEY,
    profile_id INT NOT NULL,
    income_id INT NOT NULL,
    default_amount DECIMAL(10,2),
    sort_order INT DEFAULT 0,
    calculation_method VARCHAR(50),
    formula VARCHAR(255),
    is_mandatory TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES pr_tbl_payroll_profiles(profile_id) ON DELETE CASCADE,
    FOREIGN KEY (income_id) REFERENCES pr_tbl_income(income_id) ON DELETE CASCADE,
    UNIQUE KEY unique_profile_income (profile_id, income_id)
);
```

### Deduction Items Link
**Table:** `pr_tbl_payroll_profile_deductions` (needs to be created)

```sql
CREATE TABLE pr_tbl_payroll_profile_deductions (
    profile_deduction_id INT AUTO_INCREMENT PRIMARY KEY,
    profile_id INT NOT NULL,
    deduction_id INT NOT NULL,
    default_amount DECIMAL(10,2),
    sort_order INT DEFAULT 0,
    calculation_method VARCHAR(50),
    formula VARCHAR(255),
    priority ENUM('high', 'medium', 'low') DEFAULT 'medium',
    is_mandatory TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES pr_tbl_payroll_profiles(profile_id) ON DELETE CASCADE,
    FOREIGN KEY (deduction_id) REFERENCES pr_tbl_deductions(deduction_id) ON DELETE CASCADE,
    UNIQUE KEY unique_profile_deduction (profile_id, deduction_id)
);
```

---

## Modal UI Structure

### Common Modal Elements

**Header:**
- Primary blue background for Add modals
- Warning yellow background for Edit modals
- White text
- Close button (×)
- Icon + Title

**Body:**
- Form with clear labels
- Required fields marked with red asterisk (*)
- Input groups for currency (₱ prefix)
- Checkboxes for boolean flags
- Help text (text-muted) for guidance
- Alert boxes for important notes

**Footer:**
- Secondary (gray) Cancel button - Left aligned
- Primary/Warning Save button - Right aligned
- Icons on buttons (fa-times, fa-save)

### Button Classes Used
```css
btn btn-primary    - Add/Save actions (blue)
btn btn-warning    - Edit actions (yellow)
btn btn-danger     - Delete actions (red)
btn btn-secondary  - Cancel/Close actions (gray)
btn btn-xs         - Extra small for action buttons
btn btn-sm         - Small for card buttons
```

---

## User Workflow

### Adding Personnel Filter
1. Click "Add Filter" button in Personnel Filters card (edit mode)
2. Select filter type from dropdown
3. Form dynamically shows relevant options
4. Fill in filter criteria
5. Add optional description
6. Check "Active" to apply filter
7. Click "Save Filter"
8. Page reloads with new filter displayed

### Adding Income Item
1. Click "Add Income Item" button (edit mode)
2. Select income from dropdown (shows code and type)
3. Enter default amount (optional)
4. Set display order
5. Choose calculation method
6. If "Formula", enter formula with variables
7. Check "Mandatory" if always required
8. Check "Active" to enable
9. Click "Add Income Item"
10. Page reloads with income in list

### Adding Deduction Item
1. Click "Add Deduction Item" button (edit mode)
2. Select deduction from dropdown
3. Enter default amount (optional)
4. Set display order
5. Choose calculation method
6. Set priority level (High for government mandatories)
7. Check "Mandatory" for required deductions
8. Check "Active" to enable
9. Click "Add Deduction Item"
10. Page reloads with deduction in list

### Editing Items
1. Click yellow edit button (pencil icon) on item
2. Modal opens with current values
3. Modify editable fields
4. Click "Update" button
5. Page reloads with changes applied

### Deleting Items
1. Click red delete button (trash icon) on item
2. Confirmation dialog appears
3. Confirm deletion
4. Item removed from profile (not from master list)
5. Page reloads

---

## Security Considerations

### Input Validation
- All required fields validated client-side
- Server-side validation required in PHP handlers
- SQL injection protection (use prepared statements)
- XSS protection (htmlspecialchars on output)

### Custom SQL Filters
- High risk feature - only for advanced users
- Warning displayed about invalid SQL
- Should validate/sanitize in backend
- Consider restricting to admin users only

### AJAX Error Handling
```javascript
.fail(function() {
    alert('An error occurred while...');
})
```

All AJAX calls have error handlers for network/server issues.

---

## Styling

### Custom CSS (Already in view_payroll_profile.php)
```css
.filter-card {
    position: relative;
    padding-right: 100px; /* Space for action buttons */
}

.item-card {
    position: relative;
    padding-right: 100px; /* Space for action buttons */
}

.badge-custom {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 11px;
}

.badge-income {
    background: #d4edda;
    color: #155724;
}

.badge-deduction {
    background: #fff3cd;
    color: #856404;
}
```

### Action Button Positioning
```html
<div style="position: absolute; top: 10px; right: 10px;">
    <button class="btn btn-warning btn-xs">Edit</button>
    <button class="btn btn-danger btn-xs">Delete</button>
</div>
```

---

## Testing Checklist

### Personnel Filters
- [ ] Add department filter with multiple departments
- [ ] Add employment status filter
- [ ] Add salary grade range filter
- [ ] Add custom SQL filter
- [ ] Edit filter description and active status
- [ ] Delete filter
- [ ] Verify filter appears immediately after adding
- [ ] Verify edit/delete buttons only show in edit mode

### Income Items
- [ ] Add fixed income item
- [ ] Add variable income item
- [ ] Add income with formula calculation
- [ ] Edit income amount and sort order
- [ ] Toggle mandatory/active status
- [ ] Delete income item
- [ ] Verify income displays with correct badge
- [ ] Verify default amount formatting (₱ symbol)

### Deduction Items
- [ ] Add high priority deduction (government)
- [ ] Add medium/low priority deduction
- [ ] Add deduction with percentage calculation
- [ ] Edit deduction settings
- [ ] Delete deduction item
- [ ] Verify priority affects processing order
- [ ] Verify mandatory deductions enforced

### UI/UX
- [ ] All modals open/close correctly
- [ ] Forms submit via AJAX
- [ ] Success messages display
- [ ] Error messages display
- [ ] Page reloads after save/update/delete
- [ ] Action buttons positioned correctly
- [ ] Mobile responsive (modals adapt to screen)

---

## Future Enhancements

### Potential Features
1. **Drag & Drop Reordering** - Reorder items by dragging
2. **Bulk Actions** - Select multiple items to activate/deactivate
3. **Import/Export** - Import filters/items from another profile
4. **Preview Personnel** - See which employees match filters before saving
5. **Formula Validator** - Real-time validation of calculation formulas
6. **Templates** - Save common filter combinations as templates
7. **Audit Log** - Track who added/modified/deleted items
8. **Duplicate Detection** - Warn if adding duplicate income/deduction

---

## Integration Points

### Payroll Generation Process
When generating payroll using this profile:

1. **Apply Filters** - Query personnel matching all active filters
2. **Calculate Income** - For each matched employee:
   - Apply all active income items
   - Use calculation method (fixed, percentage, formula, manual)
   - Use default amount or personnel-specific amount
3. **Calculate Deductions** - Process in priority order:
   - High priority first (government)
   - Medium priority second
   - Low priority last
4. **Generate Payslip** - Create payslip with all items

### Personnel Income/Deduction Pages
- Link to assign specific amounts per employee
- Override default amounts when needed
- View which profiles include this income/deduction

---

## Troubleshooting

### Common Issues

**Issue:** Modal doesn't open
- Check jQuery is loaded
- Check Bootstrap JS is loaded
- Check modal ID matches data-target
- Check for JavaScript console errors

**Issue:** AJAX request fails
- Verify PHP handler files exist
- Check file permissions
- Verify database connection
- Check PHP error logs
- Use browser Developer Tools > Network tab

**Issue:** Filter doesn't apply
- Verify filter is marked as "Active"
- Check filter conditions are valid
- Test SQL query manually in database
- Review payroll generation logic

**Issue:** Items don't save
- Check required fields are filled
- Verify foreign key constraints exist
- Check database user permissions
- Review PHP error logs

---

## Summary

**Files Modified:**
- `view_payroll_profile.php` - Added 6 modals, updated display sections, added JavaScript functions

**Modals Added:**
1. Add Personnel Filter Modal
2. Edit Personnel Filter Modal
3. Add Income Item Modal
4. Edit Income Item Modal
5. Add Deduction Item Modal
6. Edit Deduction Item Modal

**PHP Files Needed (Create These):**
1. `save_profile_filter.php`
2. `update_profile_filter.php`
3. `delete_profile_filter.php`
4. `save_profile_income_item.php`
5. `update_profile_income_item.php`
6. `delete_profile_income_item.php`
7. `save_profile_deduction_item.php`
8. `update_profile_deduction_item.php`
9. `delete_profile_deduction_item.php`

**Database Tables Needed:**
1. `pr_tbl_payroll_profile_filters`
2. `pr_tbl_payroll_profile_income`
3. `pr_tbl_payroll_profile_deductions`

**JavaScript Functions:** 15 functions for CRUD operations

**Total Lines Added:** ~900 lines of HTML, PHP, JavaScript

---

*Last Updated: January 2025*
*Part of MOH HRMS Payroll System*
