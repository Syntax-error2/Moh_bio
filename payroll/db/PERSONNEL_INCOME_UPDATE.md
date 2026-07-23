# 💰 Personnel Income Management - Enhancement Update

**Date:** October 20, 2025  
**Module:** Payroll - Personnel Income  
**Files Updated:** `list_personnel_income.php`, `save_personnel_income.php`  
**New Files:** `setup_personnel_income.php`, `db/personnel_income_schema.sql`

---

## 🎯 Overview

Applied the same enhanced logic and UI improvements from `list_personnel_deductions.php` to `list_personnel_income.php`, creating a consistent, professional, and user-friendly income management interface.

---

## ✨ Enhancements Applied

### 1. **Security & Validation Improvements**

#### Before:
```php
$get_dept = $_GET['dept'];
$staff_query = $conn->query("SELECT * FROM personnels WHERE personnel_id='$_GET[personnel_id]'");
```

#### After:
```php
// Sanitize and validate BEFORE header inclusion
$get_dept = $_GET['dept'] ?? '';
$personnel_id = $_GET['personnel_id'] ?? '';

if(empty($personnel_id)) {
    header('Location: list_personnel.php?dept=' . urlencode($get_dept));
    exit();
}

// Prepared statements with PDO
$staff_query = $conn->prepare("SELECT personnel_id, fname, mname, lname, suffix, img, shift_id 
                                FROM personnels 
                                WHERE personnel_id = :personnel_id 
                                LIMIT 1");
$staff_query->execute([':personnel_id' => $personnel_id]);
```

**Benefits:**
- ✅ Prevents SQL injection attacks
- ✅ Validates required parameters
- ✅ Fixes "headers already sent" error
- ✅ Uses prepared statements throughout
- ✅ Proper error handling with try-catch blocks

---

### 2. **Summary Card Display**

**New Feature:** Real-time total gross income card at the top

```php
<?php if ($table_exists) { ?>
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title"><i class="fa fa-money"></i> Total Gross Income</h6>
                <h3 class="mb-0">₱<?php echo number_format($total_income ?? 0, 2); ?></h3>
                <small>per pay period</small>
            </div>
        </div>
    </div>
</div>
<?php } ?>
```

**SQL Query for Summary:**
```php
$total_query = $conn->prepare("SELECT COALESCE(SUM(amount_per_pay), 0) as total_income
                              FROM pr_tbl_personnel_income 
                              WHERE personnel_id = :personnel_id 
                                AND is_active = 1");
$total_query->execute([':personnel_id' => $personnel_id]);
$total_result = $total_query->fetch();
$total_income = $total_result['total_income'];
```

---

### 3. **Enhanced Table Display**

#### Before:
```html
<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th>Income Details</th>
      <th>Amount per Pay</th>
    </tr>
  </thead>
```

#### After:
```html
<table class="table table-bordered table-striped table-hover" id="incomeTable">
  <thead class="thead-dark">
    <tr>
      <th style="width: 60%;">Income Details</th>
      <th style="width: 40%; text-align: right;">Amount per Pay</th>
    </tr>
  </thead>
```

**Improvements:**
- ✅ Dark theme header
- ✅ Hover effects
- ✅ Fixed column widths
- ✅ Right-aligned amounts
- ✅ ID for JavaScript targeting

---

### 4. **Enhanced Input Fields with Currency Symbol**

#### Before:
```html
<input name="amount[]" class="form-control text-right" type="number" min="0.0" step="0.01" />
```

#### After:
```html
<div class="input-group">
    <div class="input-group-prepend">
        <span class="input-group-text">₱</span>
    </div>
    <input name="amount_per_pay[]" 
           class="form-control text-right income-amt" 
           type="number" 
           min="0" 
           step="0.01" 
           value="<?php echo number_format($amount, 2, '.', ''); ?>" 
           placeholder="0.00"
           data-toggle="tooltip" 
           title="Amount paid per pay period" />
</div>
```

**Features:**
- ✅ Philippine Peso symbol (₱) prefix
- ✅ Pre-populated with existing amounts
- ✅ Tooltips for guidance
- ✅ CSS class for JavaScript targeting
- ✅ Proper number formatting

---

### 5. **Real-Time Calculations with JavaScript**

**New Feature:** Live total calculation as user types

```javascript
function calculateTotal() {
    let totalIncome = 0;
    
    // Sum all income amounts
    $('.income-amt').each(function() {
        let val = parseFloat($(this).val()) || 0;
        totalIncome += val;
    });
    
    // Update total field
    $('#total_income').val(totalIncome.toFixed(2));
}

// Calculate on input with debounce (300ms delay)
let calcTimeout;
$('.income-amt').on('input', function() {
    clearTimeout(calcTimeout);
    calcTimeout = setTimeout(calculateTotal, 300);
});

// Immediate calculation on blur
$('.income-amt').on('blur', function() {
    calculateTotal();
});
```

**Benefits:**
- ✅ Instant feedback to users
- ✅ Debounced for performance
- ✅ No page reload needed
- ✅ Validates calculations before save

---

### 6. **Row Highlighting for Active Entries**

**New Feature:** Green highlighting for rows with values

```javascript
// Highlight rows on input/blur
$('.income-amt').on('input blur', function() {
    let $row = $(this).closest('tr');
    let val = parseFloat($(this).val()) || 0;
    
    if (val > 0) {
        $row.addClass('table-success');
    } else {
        $row.removeClass('table-success');
    }
});
```

**Visual Feedback:**
- Green background = Has amount entered
- White background = No amount (inactive)

---

### 7. **Improved Total Display**

#### Before:
```html
<tr>
  <th>Total Income</th>
  <th>
    <input value="123.00" class="form-control" readonly />
  </th>
</tr>
```

#### After:
```html
<tr class="table-success">
  <th style="text-align: right; vertical-align: middle;">
      <strong>Total Gross Income per Pay Period:</strong>
  </th>
  <th style="text-align: right;">
    <input id="total_income" 
           value="<?php echo number_format($loop_total_income ?? 0, 2, '.', ''); ?>" 
           class="form-control text-right font-weight-bold bg-light" 
           style="font-size: 1.1em;" 
           type="text" 
           readonly 
           title="Total gross income per pay period" />
  </th>
</tr>
```

**Improvements:**
- ✅ Clear labeling
- ✅ Larger font size
- ✅ Success color scheme (green)
- ✅ Right-aligned
- ✅ Bold text

---

### 8. **Database Table Detection & Setup Wizard**

**New Feature:** Automatic detection if table exists

```php
$table_exists = false;
try {
    $check_table = $conn->query("SHOW TABLES LIKE 'pr_tbl_personnel_income'");
    $table_exists = ($check_table->rowCount() > 0);
} catch (PDOException $e) {
    error_log("Error checking table existence: " . $e->getMessage());
}

if (!$table_exists) {
    // Show setup wizard alert
}
```

**Alert Display:**
```html
<div class="alert alert-warning">
    <h5><i class="fa fa-exclamation-triangle"></i> Database Setup Required</h5>
    <p>The <code>pr_tbl_personnel_income</code> table has not been created yet.</p>
    <ol>
        <li><strong>One-Click Setup:</strong> 
            <a href="setup_personnel_income.php" class="btn btn-sm btn-warning">
                <i class="fa fa-magic"></i> Run Setup Wizard
            </a>
        </li>
        <li><strong>Manual Setup:</strong> Import SQL file: 
            <code>payroll/db/personnel_income_schema.sql</code>
        </li>
    </ol>
</div>
```

---

### 9. **Enhanced Form Validation**

**New Feature:** Multi-level validation before submission

```javascript
$('#incomeForm').on('submit', function(e) {
    // 1. Check if table exists
    let tableExists = $('input[name="table_exists"]').val() === '1';
    if (!tableExists) {
        e.preventDefault();
        if (confirm('Warning: The personnel income table has not been created yet...')) {
            window.open('setup_personnel_income.php', '_blank');
        }
        return false;
    }
    
    // 2. Check if at least one amount is entered
    let hasValue = false;
    let totalAmount = 0;
    $('.income-amt').each(function() {
        let val = parseFloat($(this).val()) || 0;
        if (val > 0) {
            hasValue = true;
            totalAmount += val;
        }
    });
    
    if (!hasValue) {
        e.preventDefault();
        alert('⚠️ Please enter at least one income amount before saving.');
        return false;
    }
    
    // 3. Confirmation with summary
    let confirmMsg = 'You are about to update personnel income.\n\n';
    confirmMsg += 'Total Gross Income: ₱' + totalAmount.toFixed(2) + '\n';
    confirmMsg += 'per pay period\n\n';
    confirmMsg += 'Do you want to continue?';
    
    if (!confirm(confirmMsg)) {
        e.preventDefault();
        return false;
    }
    
    // 4. Show loading state
    $(this).find('button[type="submit"]').prop('disabled', true)
           .html('<i class="fa fa-spinner fa-spin"></i> Saving...');
});
```

**Validation Levels:**
1. ✅ Table existence check
2. ✅ Minimum one value required
3. ✅ User confirmation with summary
4. ✅ Loading state during submission
5. ✅ Prevent negative values

---

### 10. **Success/Error Alert System**

**New Feature:** User feedback after save operation

```php
// Success message
if (isset($_GET['success']) && $_GET['success'] == 1) {
    ?>
    <div class="alert alert-success alert-dismissible fade show">
        <strong><i class="fa fa-check-circle"></i> Success!</strong> 
        Personnel income has been updated successfully.
    </div>
    <?php
}

// Error message
if (isset($_GET['error'])) {
    $error_msg = htmlspecialchars($_GET['error']);
    ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <strong><i class="fa fa-times-circle"></i> Error!</strong> 
        <?php echo $error_msg; ?>
    </div>
    <?php
}
```

**Auto-dismiss:**
```javascript
// Auto-dismiss alerts after 5 seconds
setTimeout(function() {
    $('.alert').fadeOut('slow');
}, 5000);
```

---

## 📁 New Files Created

### 1. `save_personnel_income.php` (120 lines)

**Purpose:** Handle form submission and save income data to database

**Key Features:**
- Transaction support (BEGIN/COMMIT/ROLLBACK)
- Upsert logic (INSERT or UPDATE)
- Deactivates old entries, activates current ones
- Comprehensive error handling
- Success/error redirects with parameters

**Process Flow:**
```
1. Validate input data
2. Begin transaction
3. Deactivate all existing income for personnel
4. Loop through submitted income items
   - Skip if amount is 0
   - Check if record exists
   - UPDATE existing OR INSERT new
5. Commit transaction
6. Redirect with success message
```

---

### 2. `setup_personnel_income.php` (300+ lines)

**Purpose:** One-click setup wizard for creating the income table

**Features:**
- Visual table structure display
- One-click table creation
- Success/error feedback
- Alternative manual setup instructions
- Relationship documentation
- Feature list

**UI Components:**
- Table structure documentation
- Relationship diagram
- Feature checklist
- Creation button
- Navigation links

---

### 3. `db/personnel_income_schema.sql` (70 lines)

**Purpose:** SQL schema for the income table

**Structure:**
```sql
CREATE TABLE IF NOT EXISTS `pr_tbl_personnel_income` (
  `personnel_income_id` INT(11) PRIMARY KEY AUTO_INCREMENT,
  `personnel_id` VARCHAR(50) NOT NULL,
  `income_id` INT(11) NOT NULL,
  `amount_per_pay` DECIMAL(10,2) DEFAULT 0.00,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` DATETIME ON UPDATE CURRENT_TIMESTAMP(),
  `user_id` INT(11),
  UNIQUE KEY (personnel_id, income_id)
);
```

**Includes:**
- Table schema
- Indexes for performance
- Sample usage queries
- Comments for documentation

---

## 🔄 Database Operations

### Save Logic (Upsert Pattern):

```php
// First, deactivate all existing income
$deactivate_query = $conn->prepare("UPDATE pr_tbl_personnel_income 
                                   SET is_active = 0, updated_at = NOW() 
                                   WHERE personnel_id = :personnel_id");

// Then, for each income item with amount > 0:
// Check if exists
$check_query = $conn->prepare("SELECT personnel_income_id 
                              FROM pr_tbl_personnel_income 
                              WHERE personnel_id = :personnel_id 
                                AND income_id = :income_id");

if (exists) {
    // UPDATE and reactivate
    $update_query = $conn->prepare("UPDATE pr_tbl_personnel_income 
                                   SET amount_per_pay = :amount,
                                       is_active = 1,
                                       updated_at = NOW(),
                                       user_id = :user_id
                                   WHERE personnel_income_id = :id");
} else {
    // INSERT new record
    $insert_query = $conn->prepare("INSERT INTO pr_tbl_personnel_income 
                                   (personnel_id, income_id, amount_per_pay, 
                                    is_active, user_id, created_at) 
                                   VALUES (:personnel_id, :income_id, :amount, 
                                           1, :user_id, NOW())");
}
```

**Benefits:**
- ✅ Handles both new and existing records
- ✅ Maintains audit trail
- ✅ No duplicate entries (UNIQUE constraint)
- ✅ Soft delete pattern (is_active flag)

---

## 📊 Visual Comparison

### Before vs After

| Feature | Before | After |
|---------|--------|-------|
| **Security** | Direct SQL queries | Prepared statements with PDO |
| **Validation** | None | Multi-level validation |
| **UI Design** | Basic table | Enhanced with cards, badges, icons |
| **Input Fields** | Plain text boxes | Currency-prefixed input groups |
| **Total Display** | Static hardcoded value | Real-time calculated |
| **Row Highlighting** | None | Green for active entries |
| **Error Handling** | Database errors shown | Try-catch with user-friendly messages |
| **Save Handler** | Missing | Complete save_personnel_income.php |
| **Setup Wizard** | None | Interactive setup_personnel_income.php |
| **Documentation** | None | Complete schema + comments |
| **User Feedback** | None | Success/error alerts |
| **Loading State** | None | Spinner during save |

---

## 🎨 UI/UX Improvements Summary

1. **Summary Card** - Total gross income at top
2. **Enhanced Table** - Dark header, hover effects, sorted by type
3. **Currency Symbol** - ₱ prefix on all inputs
4. **Tooltips** - Helpful hints on hover
5. **Row Highlighting** - Visual feedback for active entries
6. **Real-time Totals** - Live calculation as user types
7. **Setup Wizard** - One-click table creation
8. **Alert System** - Success/error notifications
9. **Loading States** - Spinner during save
10. **Better Navigation** - Clear breadcrumbs and back buttons

---

## 🔗 Integration with Payroll System

### Data Flow:

```
Income Master List (pr_tbl_income)
        ↓
Personnel Income Page (list_personnel_income.php)
        ↓
User enters amounts for each income type
        ↓
Save Handler (save_personnel_income.php)
        ↓
Personnel Income Table (pr_tbl_personnel_income)
        ↓
Used in Payslip Generation
        ↓
Final Payslip Output (moh-payslip.jpg)
```

### Payslip Calculation:

```php
// Get total gross income
SELECT SUM(amount_per_pay) as gross_pay
FROM pr_tbl_personnel_income
WHERE personnel_id = :personnel_id 
  AND is_active = 1;

// Get breakdown by type
SELECT i.income_type, i.income_title, pi.amount_per_pay
FROM pr_tbl_personnel_income pi
INNER JOIN pr_tbl_income i ON pi.income_id = i.income_id
WHERE pi.personnel_id = :personnel_id 
  AND pi.is_active = 1
ORDER BY i.income_type, i.income_title;
```

---

## ✅ Testing Checklist

- [ ] Create `pr_tbl_personnel_income` table via setup wizard
- [ ] Add income types in `income.php` (Basic Salary, PERA, COLA, etc.)
- [ ] Select a personnel from list
- [ ] Navigate to INCOME tab
- [ ] Enter amounts for various income types
- [ ] Verify real-time total calculation
- [ ] Submit form and check success message
- [ ] Verify data saved in database
- [ ] Edit amounts and save again
- [ ] Verify UPDATE functionality works
- [ ] Set all amounts to 0 and save
- [ ] Verify entries become inactive

---

## 📝 Next Steps

1. **Create Table:**
   - Visit: `http://localhost/moh_hrms/payroll/setup_personnel_income.php`
   - Click: "Create Table Now"

2. **Add Income Types:**
   - Visit: `http://localhost/moh_hrms/payroll/income.php`
   - Add: Basic Salary, PERA, COLA, etc.

3. **Assign Income to Personnel:**
   - Visit: `http://localhost/moh_hrms/payroll/list_personnel.php`
   - Select personnel → Click "INCOME" tab
   - Enter amounts → Save

4. **Generate Payslip:**
   - Implement payslip generator using data from both:
     - `pr_tbl_personnel_income` (earnings)
     - `pr_tbl_personnel_deductions` (deductions)

---

## 🐛 Bug Fixes Included

Same fixes as deductions module:

1. **Headers Already Sent Error**
   - Fixed by validating GET parameters before `include('header.php')`

2. **SQL Injection Prevention**
   - All queries use prepared statements with PDO

3. **XSS Protection**
   - All output uses `htmlspecialchars()`

4. **Error Handling**
   - Try-catch blocks around database operations
   - User-friendly error messages
   - Error logging for debugging

---

## 📚 Related Documentation

- **Schema Reference:** `payroll/db/PAYROLL_SCHEMA_REFERENCE.md`
- **Payslip Integration:** `payroll/db/PAYSLIP_INTEGRATION.md`
- **Deductions Update:** `payroll/db/PERSONNEL_DEDUCTIONS_UPDATE.md`
- **Quick Reference:** `payroll/db/QUICK_REFERENCE.md`

---

**Implementation Date:** October 20, 2025  
**Status:** ✅ COMPLETE  
**Lines of Code:** 600+ (including new files)  
**Files Modified:** 1 (list_personnel_income.php)  
**Files Created:** 3 (save, setup, schema)

---

**END OF PERSONNEL INCOME ENHANCEMENT DOCUMENTATION**
