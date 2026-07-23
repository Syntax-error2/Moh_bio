# Quick Fix Reference - Department Table Issue

## 🔴 Error
```
Table 'moh_hrms.tbl_dept' doesn't exist
```

## ✅ Solution Applied

**File:** `view_payroll_profile.php` (Line 86)

**Changed From:**
```php
$dept_query = $conn->prepare("SELECT * FROM tbl_dept ORDER BY dept_title ASC");
```

**Changed To:**
```php
$dept_query = $conn->prepare("SELECT do_id as dept_id, dept_office_name as dept_title FROM dept_offices ORDER BY dept_office_name ASC");
```

## 📊 Actual Table Schema

```sql
CREATE TABLE `dept_offices` (
  `do_id` INT(11) NOT NULL AUTO_INCREMENT,
  `dept_office_name` VARCHAR(255) NOT NULL,
  `officeHead_id` INT(11) NOT NULL,
  PRIMARY KEY (`do_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
```

## 🎯 Quick Test

```
http://localhost/moh_hrms/payroll/view_payroll_profile.php?profile_id=1
```

Should load without errors! ✅

## 📁 Files Created

1. **view_payroll_profile.php** - Fixed (uses correct table)
2. **db/fix_department_table_issue.sql** - Optional VIEW creation
3. **DEPARTMENT_TABLE_FIX.md** - Complete documentation
4. **QUICK_FIX_REFERENCE.md** - This file

## 🔧 Optional Database View

If you want both `tbl_dept` AND `dept_offices` to work:

```bash
cd C:\xampp\htdocs\moh_hrms\payroll\db
mysql -u root -p moh_hrms < fix_department_table_issue.sql
```

This creates a view so old code using `tbl_dept` will also work.

## ✨ Status

**FIXED** - Page should now work correctly!
