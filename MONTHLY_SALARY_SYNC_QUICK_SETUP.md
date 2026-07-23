# Quick Setup Guide: Monthly Salary Synchronization

## ⚡ Quick Start (2 Minutes)

### Option 1: Install Database Triggers (Recommended)
```bash
# 1. Open MySQL/phpMyAdmin
# 2. Select database: moh_hrms
# 3. Run the SQL file:
```
```sql
SOURCE c:\xampp\htdocs\moh_hrms\sync_monthly_salary_triggers.sql;
```

**✅ Done! Synchronization is now automatic.**

### Option 2: Use PHP Sync Only (Already Active)
The PHP sync is already integrated in:
- ✅ When adding new service records
- ✅ When updating existing service records

**✅ No additional setup needed!**

---

## 🔍 Verify Installation

Run this command:
```bash
php c:\xampp\htdocs\moh_hrms\sync_monthly_salary.php
```

Expected output:
```
=== Monthly Salary Synchronization ===

Current Status:
Total Personnel: 666
Synced: 666
Not Synced: 0

=== Synchronization Complete ===
```

---

## 📊 Current Status

✅ **Initial Sync Completed**
- Date: November 3, 2025
- Records Synced: 666/666 (100%)
- Mismatches Fixed: 1 record (JETTER BARROCA)

✅ **Files Updated**
- `save_add_personnel.php` - Auto-sync on new service record
- `save_add_personnel_tables.php` - Auto-sync on update + SQL injection fix

✅ **Security Improvements**
- Fixed SQL injection in `save_add_personnel_tables.php`
- All queries use prepared statements

---

## 🎯 What This Does

### Automatic Synchronization
When you update salary in **either** table, it automatically syncs to the other:

**Scenario 1**: Update service record salary
```
service_record.monthly_salary = 15,000
↓ (auto-sync)
personnels.monthly_salary = 15,000
```

**Scenario 2**: Add new service record
```
INSERT INTO service_record (monthly_salary = 20,000)
↓ (auto-sync)
UPDATE personnels SET monthly_salary = 20,000
```

---

## 🛠️ Maintenance

### Weekly Check (Optional)
```bash
# Run sync verification
php c:\xampp\htdocs\moh_hrms\sync_monthly_salary.php
```

### If You See Mismatches
The script automatically fixes them! Just check the output.

---

## 📝 Notes

- **Database Triggers**: Instant sync, works for all operations
- **PHP Sync**: Backup sync, runs during form submissions
- **Best Practice**: Use both for maximum reliability

---

## 📚 Full Documentation
See: `MONTHLY_SALARY_SYNC_IMPLEMENTATION.md`

## ✅ Status: READY FOR PRODUCTION
