# 📊 MOH HRMS Payroll Module - Schema Reference (Oct 20, 2025)

## ✅ Existing Tables (Confirmed)

### 1. `pr_tbl_deductions` - Deduction Types Master List
```sql
deduction_id      INT(11) PRIMARY KEY AUTO_INCREMENT
deduction_type    VARCHAR(55)     # e.g., "Mandatory", "Voluntary"
deduction_title   VARCHAR(55)     # e.g., "GSIS", "PhilHealth", "Pag-IBIG"
is_deleted        TINYINT(4)      # 0 = active, 1 = deleted
created_at        DATETIME        # DEFAULT current_timestamp()
user_id           INT(11)         # Who created this
```

### 2. `pr_tbl_income` - Income Types Master List
```sql
income_id         INT(11) PRIMARY KEY AUTO_INCREMENT
income_type       VARCHAR(55)     # e.g., "Regular", "Additional"
income_title      VARCHAR(55)     # e.g., "Basic Salary", "PERA", "COLA"
is_deleted        TINYINT(4)      # 0 = active, 1 = deleted
created_at        DATETIME        # DEFAULT current_timestamp()
user_id           INT(11)         # Who created this
```

### 3. `pr_tbl_payroll_profile` - Pay Schedules
```sql
payprofile_id     INT(11) PRIMARY KEY AUTO_INCREMENT
description       VARCHAR(55)     # e.g., "Weekly", "Monthly"
is_deleted        TINYINT(4)      # 0 = active, 1 = deleted
created_at        DATETIME        # DEFAULT current_timestamp()
user_id           INT(11)         # Who created this

# Sample Data:
# 1, 'Weekly', 0, '2025-10-20 13:15:55', 3
```

### 4. `pr_tbl_pay_pro_personnels` - Personnel → Profile Assignment
```sql
ppp_id            INT(11) PRIMARY KEY AUTO_INCREMENT
personnel_id      INT(11)         # FK to personnels.personnel_id
payprofile_id     INT(11)         # FK to pr_tbl_payroll_profile
status            VARCHAR(55)     # DEFAULT 'Active'
created_at        DATETIME        # DEFAULT current_timestamp()
user_id           INT(11)         # Who created this

# Sample Data:
# 1, 14, 1, 'Active', '2025-10-20 13:16:26', 3
```

---

## ⚠️ Missing Tables (To Be Created)

### 5. `pr_tbl_personnel_deductions` - Personnel Deduction Assignments
**Status:** NEEDS TO BE CREATED  
**Action:** Run `setup_personnel_deductions.php`

```sql
CREATE TABLE pr_tbl_personnel_deductions (
  personnel_deduction_id INT(11) PRIMARY KEY AUTO_INCREMENT,
  personnel_id           VARCHAR(50) NOT NULL,
  deduction_id           INT(11) NOT NULL,
  employer_amt_per_pay   DECIMAL(10,2) DEFAULT 0.00,
  employee_amt_per_pay   DECIMAL(10,2) DEFAULT 0.00,
  is_active              TINYINT(1) DEFAULT 1,
  created_at             DATETIME DEFAULT current_timestamp(),
  updated_at             DATETIME ON UPDATE current_timestamp(),
  user_id                INT(11),
  UNIQUE KEY (personnel_id, deduction_id)
);
```

### 6. `pr_tbl_personnel_income` - Personnel Income Assignments
**Status:** PLANNED (Future)

```sql
CREATE TABLE pr_tbl_personnel_income (
  personnel_income_id    INT(11) PRIMARY KEY AUTO_INCREMENT,
  personnel_id           VARCHAR(50) NOT NULL,
  income_id              INT(11) NOT NULL,
  amount_per_pay         DECIMAL(10,2) DEFAULT 0.00,
  is_active              TINYINT(1) DEFAULT 1,
  created_at             DATETIME DEFAULT current_timestamp(),
  updated_at             DATETIME ON UPDATE current_timestamp(),
  user_id                INT(11),
  UNIQUE KEY (personnel_id, income_id)
);
```

---

## 🔗 Table Relationships

```
personnels (main HRMS table)
    │
    ├─→ pr_tbl_pay_pro_personnels ──→ pr_tbl_payroll_profile
    │                                  (Which pay schedule?)
    │
    ├─→ pr_tbl_personnel_deductions ──→ pr_tbl_deductions
    │                                    (Which deductions? How much?)
    │
    └─→ pr_tbl_personnel_income ──→ pr_tbl_income
                                    (Which income types? How much?)
```

---

## 📋 Design Pattern: Master-Detail

### Master Tables (Catalog/Lookup)
- `pr_tbl_deductions` - WHAT deductions exist
- `pr_tbl_income` - WHAT income types exist
- `pr_tbl_payroll_profile` - WHAT pay schedules exist

### Junction Tables (Assignments + Amounts)
- `pr_tbl_personnel_deductions` - WHO has WHICH deductions + AMOUNTS
- `pr_tbl_personnel_income` - WHO has WHICH income + AMOUNTS
- `pr_tbl_pay_pro_personnels` - WHO has WHICH pay schedule

---

## 🎯 Key Points

1. **No amounts in master tables** - Only in junction tables
2. **personnel_id type varies:**
   - `INT(11)` in pr_tbl_pay_pro_personnels
   - `VARCHAR(50)` in pr_tbl_personnel_deductions (recommended for flexibility)
3. **All tables use soft deletes** - `is_deleted` or `is_active` flags
4. **All amounts use** `DECIMAL(10,2)` - for currency precision
5. **All tables track** `user_id` - for audit trail

---

## 🚀 Quick Setup

### To create missing table:
1. Open: `http://localhost/moh_hrms/payroll/setup_personnel_deductions.php`
2. Click: **"Create Table"**
3. Verify: Success message appears

---

## 📖 Full Documentation

- **Complete Schema:** `PAYROLL_SCHEMA_REFERENCE.md`
- **Quick Reference:** `QUICK_REFERENCE.md`
- **Change History:** `REFACTORING_CHANGELOG.md`

---

**Schema Confirmed:** October 20, 2025  
**Database:** moh_hrms  
**Server:** MariaDB 10.4.32  
**PHP:** 8.2.12
