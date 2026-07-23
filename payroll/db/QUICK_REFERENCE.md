# Payroll Module - Quick Reference Guide

**Date:** October 20, 2025

---

## 📊 Current Tables (Already Exist)

| Table Name | Purpose | Key Columns |
|------------|---------|-------------|
| `pr_tbl_deductions` | Master list of deduction types | `deduction_id`, `deduction_type`, `deduction_title` |
| `pr_tbl_income` | Master list of income types | `income_id`, `income_type`, `income_title` |
| `pr_tbl_payroll_profile` | Payroll schedules | `payprofile_id`, `description` (e.g., "Weekly") |
| `pr_tbl_pay_pro_personnels` | Personnel → Profile assignments | `personnel_id`, `payprofile_id`, `status` |

---

## 🔧 Tables To Create

| Table Name | Status | Action Required |
|------------|--------|-----------------|
| `pr_tbl_personnel_deductions` | ⚠️ **MISSING** | Run `setup_personnel_deductions.php` |
| `pr_tbl_personnel_income` | 📋 Planned | Future implementation |

---

## 🚀 Quick Setup

### Step 1: Create Missing Table
1. Open browser: `http://localhost/moh_hrms/payroll/setup_personnel_deductions.php`
2. Click **"Create Table"** button
3. Verify success message

### Step 2: Test Personnel Deductions
1. Navigate to personnel list
2. Click on a personnel
3. Go to "Deductions" tab
4. Add deduction amounts
5. Save and verify

---

## 📝 Common Queries

### Get Active Deductions
```php
$stmt = $conn->prepare("SELECT * FROM pr_tbl_deductions WHERE is_deleted = 0");
$stmt->execute();
$deductions = $stmt->fetchAll();
```

### Get Personnel Deductions
```php
$stmt = $conn->prepare("
    SELECT d.*, pd.employer_amt_per_pay, pd.employee_amt_per_pay
    FROM pr_tbl_personnel_deductions pd
    JOIN pr_tbl_deductions d ON pd.deduction_id = d.deduction_id
    WHERE pd.personnel_id = ? AND pd.is_active = 1
");
$stmt->execute([$personnel_id]);
```

### Get Payroll Profile
```php
$stmt = $conn->prepare("
    SELECT pp.description 
    FROM pr_tbl_pay_pro_personnels ppp
    JOIN pr_tbl_payroll_profile pp ON ppp.payprofile_id = pp.payprofile_id
    WHERE ppp.personnel_id = ? AND ppp.status = 'Active'
");
$stmt->execute([$personnel_id]);
```

---

## 🎯 Key Concepts

### Soft Deletes
- Never DELETE records
- Set `is_deleted = 1` instead
- Always filter: `WHERE is_deleted = 0`

### Master-Detail Pattern
- **Master tables** = catalog (deductions, income)
- **Junction tables** = assignments (personnel_deductions)
- Amounts stored in junction tables, not master

### Data Types
- Money: `DECIMAL(10,2)`
- IDs: `INT(11)`
- Text: `VARCHAR(55)`
- Flags: `TINYINT(1)`

---

## 🔗 File Locations

```
payroll/
├── dbcon.php                           # Main database connection
├── deductions.php                      # List deductions
├── income.php                          # List income
├── list_personnel_deductions.php       # Manage personnel deductions
├── save_personnel_deductions.php       # Save deduction assignments
├── setup_personnel_deductions.php      # Setup wizard
└── db/
    ├── personnel_deductions_schema.sql
    ├── PAYROLL_SCHEMA_REFERENCE.md     # Full documentation
    └── QUICK_REFERENCE.md              # This file
```

---

## ✅ Checklist for New Features

When adding new payroll features:

- [ ] Use prepared statements (PDO)
- [ ] Check `is_deleted = 0` in queries
- [ ] Use soft deletes, not hard deletes
- [ ] Add `user_id` tracking
- [ ] Include `created_at` timestamp
- [ ] Use `DECIMAL(10,2)` for amounts
- [ ] Validate input (XSS protection)
- [ ] Test with real data
- [ ] Update documentation

---

## 🆘 Troubleshooting

### "An error occurred while loading deductions"
- **Cause:** `pr_tbl_personnel_deductions` table doesn't exist
- **Fix:** Run `setup_personnel_deductions.php`

### "Table doesn't exist" errors
- **Cause:** Missing junction tables
- **Fix:** Check if setup wizards have been run

### Deduction amounts not saving
- **Cause:** Incorrect column names or table structure
- **Fix:** Compare with schema in `PAYROLL_SCHEMA_REFERENCE.md`

---

**For detailed documentation, see:** `PAYROLL_SCHEMA_REFERENCE.md`
