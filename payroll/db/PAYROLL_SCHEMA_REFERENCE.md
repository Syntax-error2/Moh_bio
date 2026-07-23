# MOH HRMS Payroll Module - Database Schema Reference

**Last Updated:** October 20, 2025  
**Server Version:** MariaDB 10.4.32  
**PHP Version:** 8.2.12

---

## Overview
This document contains the official schema for all payroll-related tables in the MOH HRMS system. Use this as the authoritative reference for all database operations.

---

## Table Structure

### 1. `pr_tbl_deductions` - Master Deductions List

**Purpose:** Central catalog of all available deduction types (GSIS, PhilHealth, Pag-IBIG, Tax, etc.)

```sql
CREATE TABLE `pr_tbl_deductions` (
  `deduction_id` int(11) NOT NULL AUTO_INCREMENT,
  `deduction_type` varchar(55) DEFAULT NULL,
  `deduction_title` varchar(55) DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`deduction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

**Columns:**
- `deduction_id`: Primary key, auto-increment
- `deduction_type`: Type/category of deduction (e.g., "Mandatory", "Voluntary")
- `deduction_title`: Display name (e.g., "GSIS", "PhilHealth")
- `is_deleted`: Soft delete flag (0 = active, 1 = deleted)
- `created_at`: Timestamp when deduction was created
- `user_id`: ID of user who created this deduction

**Usage:**
- Used as lookup table for available deductions
- Personnel-specific amounts stored in `pr_tbl_personnel_deductions` (junction table)

---

### 2. `pr_tbl_income` - Master Income/Earnings List

**Purpose:** Central catalog of all income types (Basic Salary, Allowances, Bonuses, etc.)

```sql
CREATE TABLE `pr_tbl_income` (
  `income_id` int(11) NOT NULL AUTO_INCREMENT,
  `income_type` varchar(55) DEFAULT NULL,
  `income_title` varchar(55) DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`income_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

**Columns:**
- `income_id`: Primary key, auto-increment
- `income_type`: Type/category of income (e.g., "Regular", "Additional")
- `income_title`: Display name (e.g., "Basic Salary", "PERA")
- `is_deleted`: Soft delete flag (0 = active, 1 = deleted)
- `created_at`: Timestamp when income type was created
- `user_id`: ID of user who created this income type

**Usage:**
- Used as lookup table for available income types
- Personnel-specific amounts stored in separate junction table (to be created)

---

### 3. `pr_tbl_payroll_profile` - Payroll Schedules/Profiles

**Purpose:** Defines different payroll schedules (Weekly, Bi-weekly, Semi-monthly, Monthly)

```sql
CREATE TABLE `pr_tbl_payroll_profile` (
  `payprofile_id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(55) DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`payprofile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

**Columns:**
- `payprofile_id`: Primary key, auto-increment
- `description`: Profile name (e.g., "Weekly", "Monthly")
- `is_deleted`: Soft delete flag (0 = active, 1 = deleted)
- `created_at`: Timestamp when profile was created
- `user_id`: ID of user who created this profile

**Sample Data:**
```sql
INSERT INTO `pr_tbl_payroll_profile` VALUES
(1, 'Weekly', 0, '2025-10-20 13:15:55', 3);
```

---

### 4. `pr_tbl_pay_pro_personnels` - Personnel-Profile Assignments

**Purpose:** Links personnels to their payroll profiles (which payroll schedule each employee follows)

```sql
CREATE TABLE `pr_tbl_pay_pro_personnels` (
  `ppp_id` int(11) NOT NULL AUTO_INCREMENT,
  `personnel_id` int(11) NOT NULL,
  `payprofile_id` int(11) NOT NULL,
  `status` varchar(55) NOT NULL DEFAULT 'Active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`ppp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

**Columns:**
- `ppp_id`: Primary key, auto-increment
- `personnel_id`: Foreign key to `personnels` table
- `payprofile_id`: Foreign key to `pr_tbl_payroll_profile`
- `status`: Current status (e.g., "Active", "Inactive")
- `created_at`: Timestamp when assignment was created
- `user_id`: ID of user who created this assignment

**Sample Data:**
```sql
INSERT INTO `pr_tbl_pay_pro_personnels` VALUES
(1, 14, 1, 'Active', '2025-10-20 13:16:26', 3);
```

**Usage:**
- Links personnel to payroll schedule
- One personnel can have one active profile at a time
- Status field allows for historical tracking

---

## Missing Tables (To Be Created)

### 5. `pr_tbl_personnel_deductions` - Personnel Deduction Assignments

**Purpose:** Junction table linking personnels to deductions with specific amounts

**Status:** ⚠️ **NEEDS TO BE CREATED** - Schema file ready at `db/personnel_deductions_schema.sql`

**Expected Structure:**
```sql
CREATE TABLE `pr_tbl_personnel_deductions` (
  `personnel_deduction_id` int(11) NOT NULL AUTO_INCREMENT,
  `personnel_id` varchar(50) NOT NULL,
  `deduction_id` int(11) NOT NULL,
  `employer_amt_per_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `employee_amt_per_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `user_id` int(11) NULL,
  PRIMARY KEY (`personnel_deduction_id`),
  UNIQUE KEY `unique_personnel_deduction` (`personnel_id`, `deduction_id`),
  KEY `idx_personnel_id` (`personnel_id`),
  KEY `idx_deduction_id` (`deduction_id`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

**Setup:** Run `setup_personnel_deductions.php` to create this table

---

### 6. `pr_tbl_personnel_income` - Personnel Income Assignments (Future)

**Purpose:** Junction table linking personnels to income types with specific amounts

**Status:** 📋 **PLANNED** - To be created when implementing personnel income management

**Expected Structure:**
```sql
CREATE TABLE `pr_tbl_personnel_income` (
  `personnel_income_id` int(11) NOT NULL AUTO_INCREMENT,
  `personnel_id` varchar(50) NOT NULL,
  `income_id` int(11) NOT NULL,
  `amount_per_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `user_id` int(11) NULL,
  PRIMARY KEY (`personnel_income_id`),
  UNIQUE KEY `unique_personnel_income` (`personnel_id`, `income_id`),
  KEY `idx_personnel_id` (`personnel_id`),
  KEY `idx_income_id` (`income_id`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

---

## Database Design Patterns

### Master-Detail Pattern
The payroll module uses a **master-detail** (or **lookup-junction**) table pattern:

1. **Master Tables** - Store catalog/definitions:
   - `pr_tbl_deductions` - What deductions exist
   - `pr_tbl_income` - What income types exist
   - `pr_tbl_payroll_profile` - What pay schedules exist

2. **Junction Tables** - Store assignments with amounts:
   - `pr_tbl_personnel_deductions` - Which deductions for which personnel + amounts
   - `pr_tbl_personnel_income` - Which income for which personnel + amounts
   - `pr_tbl_pay_pro_personnels` - Which pay schedule for which personnel

**Benefits:**
- ✅ Centralized management of deduction/income types
- ✅ Easy to add new types without modifying personnel records
- ✅ Personnel-specific amounts stored separately
- ✅ Historical tracking via soft deletes and status fields
- ✅ Referential integrity through foreign keys (optional)

---

## Naming Conventions

**Table Prefix:** `pr_tbl_` (payroll table)

**Column Naming:**
- Primary Keys: `{table}_id` (e.g., `deduction_id`, `income_id`)
- Foreign Keys: Match referenced table's primary key name
- Timestamps: `created_at`, `updated_at`
- Soft Delete: `is_deleted` (tinyint: 0/1)
- Active Status: `is_active` or `status` (string)
- User Tracking: `user_id`

**Data Types:**
- IDs: `int(11)` with AUTO_INCREMENT
- Amounts: `decimal(10,2)` for currency
- Titles/Names: `varchar(55)`
- Flags: `tinyint(1)` or `tinyint(4)`
- Timestamps: `datetime` with DEFAULT current_timestamp()

---

## Relationships

```
personnels (main table)
    ↓
pr_tbl_pay_pro_personnels
    ↓
pr_tbl_payroll_profile (Weekly, Monthly, etc.)

personnels
    ↓
pr_tbl_personnel_deductions
    ↓
pr_tbl_deductions (GSIS, PhilHealth, etc.)

personnels
    ↓
pr_tbl_personnel_income
    ↓
pr_tbl_income (Basic Salary, PERA, etc.)
```

---

## Query Examples

### Get all active deductions
```php
$query = $conn->prepare("SELECT deduction_id, deduction_type, deduction_title 
                         FROM pr_tbl_deductions 
                         WHERE is_deleted = 0 
                         ORDER BY deduction_title ASC");
$query->execute();
$deductions = $query->fetchAll(PDO::FETCH_ASSOC);
```

### Get personnel's assigned deductions with amounts
```php
$query = $conn->prepare("
    SELECT 
        d.deduction_id,
        d.deduction_type,
        d.deduction_title,
        pd.employer_amt_per_pay,
        pd.employee_amt_per_pay,
        (pd.employer_amt_per_pay + pd.employee_amt_per_pay) AS total_deduction
    FROM pr_tbl_personnel_deductions pd
    INNER JOIN pr_tbl_deductions d ON pd.deduction_id = d.deduction_id
    WHERE pd.personnel_id = :personnel_id 
    AND pd.is_active = 1 
    AND d.is_deleted = 0
    ORDER BY d.deduction_title ASC
");
$query->execute([':personnel_id' => $personnel_id]);
$personnel_deductions = $query->fetchAll(PDO::FETCH_ASSOC);
```

### Get personnel's payroll profile
```php
$query = $conn->prepare("
    SELECT 
        pp.payprofile_id,
        pp.description AS profile_name,
        ppp.status
    FROM pr_tbl_pay_pro_personnels ppp
    INNER JOIN pr_tbl_payroll_profile pp ON ppp.payprofile_id = pp.payprofile_id
    WHERE ppp.personnel_id = :personnel_id 
    AND ppp.status = 'Active'
    AND pp.is_deleted = 0
");
$query->execute([':personnel_id' => $personnel_id]);
$profile = $query->fetch(PDO::FETCH_ASSOC);
```

---

## Important Notes

1. **Soft Deletes:** All tables use `is_deleted` flag instead of physical deletion
2. **User Tracking:** `user_id` column tracks who created/modified records
3. **Timestamps:** All tables have `created_at`, some have `updated_at`
4. **Data Types:** Use `int(11)` for IDs, `decimal(10,2)` for money, `varchar(55)` for text
5. **Character Set:** UTF-8 (utf8mb4_general_ci) for international character support
6. **Engine:** InnoDB for transaction support and foreign keys

---

## Setup Instructions

### For New Installations:

1. **Import Base Schema** (already done):
   ```sql
   -- Tables already exist:
   -- pr_tbl_deductions
   -- pr_tbl_income
   -- pr_tbl_payroll_profile
   -- pr_tbl_pay_pro_personnels
   ```

2. **Create Personnel Deductions Table**:
   - Navigate to: `payroll/setup_personnel_deductions.php`
   - Click "Create Table" button
   - Verify success message

3. **Create Personnel Income Table** (future):
   - Similar setup wizard to be created

---

## Version History

- **v1.0** (Oct 20, 2025): Initial schema documentation
  - Documented existing 4 tables
  - Identified need for `pr_tbl_personnel_deductions`
  - Planned `pr_tbl_personnel_income` for future implementation

---

## Related Files

- **Database Connections:**
  - `dbcon.php` - Main PDO connection
  - `dbcon2.php` - PDO connection (legacy support)
  - `dbcon3.php` - PDO connection (legacy support)

- **CRUD Operations:**
  - `deductions_cud.php` - Create/Update/Delete deductions
  - `income_cud.php` - Create/Update/Delete income types
  - `save_personnel_deductions.php` - Save personnel deduction assignments

- **Display Files:**
  - `deductions.php` - List all deductions
  - `income.php` - List all income types
  - `list_personnel_deductions.php` - Manage personnel deductions

- **Schema Files:**
  - `db/personnel_deductions_schema.sql` - Schema for junction table
  - `setup_personnel_deductions.php` - Setup wizard

---

**END OF SCHEMA REFERENCE**
