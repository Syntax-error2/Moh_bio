# 🚀 Quick Start Guide - Payroll Income & Deductions

**For:** End Users & Administrators  
**Last Updated:** October 20, 2025

---

## ⚡ Quick Setup (First Time Only)

### Step 1: Create Tables (5 minutes)

**For Deductions:**
1. Open: `http://localhost/moh_hrms/payroll/setup_personnel_deductions.php`
2. Click: **"Create Table Now"**
3. Wait for success message

**For Income:**
1. Open: `http://localhost/moh_hrms/payroll/setup_personnel_income.php`
2. Click: **"Create Table Now"**
3. Wait for success message

---

### Step 2: Add Deduction Types (10 minutes)

1. Open: `http://localhost/moh_hrms/payroll/deductions.php`
2. Click: **"+ Add New Deduction"**
3. Add these common deductions:

| Type | Title |
|------|-------|
| Mandatory | GSIS |
| Mandatory | PhilHealth |
| Mandatory | Pag-IBIG |
| Mandatory | Withholding Tax |
| Voluntary | SSS Loan |
| Voluntary | PAG-IBIG Loan |

4. Click **"Create"** for each

---

### Step 3: Add Income Types (10 minutes)

1. Open: `http://localhost/moh_hrms/payroll/income.php`
2. Click: **"+ Add New Income"**
3. Add these common income types:

| Type | Title |
|------|-------|
| Regular | Basic Salary |
| Regular | PERA |
| Additional | COLA |
| Additional | Overtime Pay |
| Additional | Hazard Pay |

4. Click **"Create"** for each

---

## 💰 Managing Personnel Income

### Assigning Income to a Personnel

1. Go to: **Personnel List** (`list_personnel.php`)
2. Select a personnel by clicking their name
3. Click: **"INCOME"** tab
4. You'll see all income types you created
5. Enter amounts in the **"Amount per Pay"** column
   - Example: Basic Salary = 25000.00
   - Example: PERA = 2000.00
   - Example: COLA = 1500.00
6. Watch the **Total Gross Income** update automatically
7. Click: **"Save Income"** button
8. Success message will appear

### Tips for Income Entry
- ✅ Enter amounts without commas (25000 not 25,000)
- ✅ Use 2 decimal places for cents (25000.00)
- ✅ Leave blank or enter 0.00 for unused income types
- ✅ The ₱ symbol is added automatically
- ✅ Total calculates as you type
- ✅ Green highlight shows active income entries

---

## 💸 Managing Personnel Deductions

### Assigning Deductions to a Personnel

1. Go to: **Personnel List** (`list_personnel.php`)
2. Select a personnel by clicking their name
3. Click: **"DEDUCTIONS"** tab
4. You'll see all deduction types you created
5. Enter amounts in **two columns**:
   - **Employer Amount per Pay** - What employer pays
   - **Employee Amount per Pay** - Deducted from salary

Example:
| Deduction | Employer | Employee |
|-----------|----------|----------|
| GSIS | 1500.00 | 1500.00 |
| PhilHealth | 400.00 | 400.00 |
| Pag-IBIG | 100.00 | 100.00 |
| Withholding Tax | 0.00 | 2000.00 |

6. Watch the **totals** update automatically
7. Click: **"Save Deductions"** button
8. Success message will appear

### Understanding Deduction Columns
- **Employer Amount** - Government mandated employer contribution
- **Employee Amount** - Deducted from employee's gross pay
- **Total Deductions** - Sum of both (for reporting)
- Only **Employee Amount** affects net pay (take-home)

---

## 📊 Summary Cards Explained

### Income Page - Total Gross Income Card
```
┌─────────────────────────────────┐
│ 💰 Total Gross Income          │
│ ₱29,000.00                     │
│ per pay period                 │
└─────────────────────────────────┘
```
- Shows sum of all income amounts entered
- Updates in real-time as you type
- This is the employee's earnings before deductions

### Deductions Page - Three Cards
```
┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐
│ 🏢 Employer     │ │ 👤 Employee     │ │ 🧮 Total        │
│ Contributions   │ │ Deductions      │ │ Deductions      │
│ ₱2,000.00       │ │ ₱4,500.00       │ │ ₱6,500.00       │
└─────────────────┘ └─────────────────┘ └─────────────────┘
```
- **Employer**: What company pays (not from employee)
- **Employee**: Deducted from gross pay
- **Total**: Sum of both (for reports)

---

## 🧮 How Net Pay is Calculated

```
Gross Pay (Income)               ₱29,000.00
- Employee Deductions            ₱ 4,500.00
─────────────────────────────────────────────
= Net Pay (Take Home)            ₱24,500.00
```

**Note:** Employer deductions are NOT subtracted from employee pay

---

## 🎯 Common Tasks

### Task: Update Salary for a Personnel
1. Go to Personnel List → Select personnel
2. Click **"INCOME"** tab
3. Change the Basic Salary amount
4. Click **"Save Income"**
5. Success ✅

### Task: Add a New Loan Deduction
1. Go to **Deductions Management** (`deductions.php`)
2. Add deduction: Type = "Voluntary", Title = "Salary Loan"
3. Go to Personnel → **"DEDUCTIONS"** tab
4. Enter amount in **Employee Amount** column
5. Click **"Save Deductions"**
6. Success ✅

### Task: Remove a Deduction from Personnel
1. Go to Personnel → **"DEDUCTIONS"** tab
2. Change both amounts to **0.00**
3. Click **"Save Deductions"**
4. The deduction becomes inactive ✅

### Task: Bulk Update Multiple Personnels
Currently:
- Must update one personnel at a time
- Future: Bulk import feature planned

---

## ⚠️ Important Notes

### Mandatory vs Voluntary Deductions
- **Mandatory**: Government-required (GSIS, PhilHealth, Pag-IBIG, Tax)
- **Voluntary**: Optional (Loans, Insurance, etc.)
- Both display separately, sorted automatically

### Regular vs Additional Income
- **Regular**: Fixed monthly (Basic Salary, PERA)
- **Additional**: Variable (Overtime, Bonuses)
- Both display separately, sorted automatically

### Data Validation
- ✅ At least one income amount required
- ✅ Negative values prevented
- ✅ Confirmation prompt before saving
- ✅ Success/error alerts for feedback

---

## 🔧 Troubleshooting

### Problem: "Database table not created" message

**Solution:**
1. Click the **"Run Setup Wizard"** button in the alert
2. OR manually visit setup page:
   - Deductions: `setup_personnel_deductions.php`
   - Income: `setup_personnel_income.php`

### Problem: No income/deduction types showing

**Solution:**
1. Add types first:
   - Income: Visit `income.php`
   - Deductions: Visit `deductions.php`
2. Click "+ Add New" and create types
3. Then assign to personnel

### Problem: Save button is disabled

**Possible Causes:**
1. Database table not created → Click setup wizard link
2. No amounts entered → Enter at least one amount
3. JavaScript error → Refresh page

### Problem: Amounts not saving

**Checklist:**
1. ✅ Table created via setup wizard?
2. ✅ At least one amount entered?
3. ✅ Clicked "Save" button?
4. ✅ Success message appeared?
5. ✅ Check browser console for errors

---

## 📱 Browser Compatibility

**Recommended:**
- ✅ Google Chrome (latest)
- ✅ Mozilla Firefox (latest)
- ✅ Microsoft Edge (latest)

**Not Recommended:**
- ❌ Internet Explorer (outdated)

---

## 🆘 Getting Help

### Check Documentation
1. **Quick Reference:** `payroll/db/QUICK_REFERENCE.md`
2. **Full Guide:** `payroll/db/PERSONNEL_INCOME_UPDATE.md`
3. **Schema Info:** `payroll/db/PAYROLL_SCHEMA_REFERENCE.md`

### Contact Administrator
- Report the error message you see
- Include what you were trying to do
- Note which personnel you were working with

---

## ✅ Best Practices

### Data Entry
- ✅ Double-check amounts before saving
- ✅ Use consistent decimal places (2 decimals)
- ✅ Review confirmation dialog before clicking OK
- ✅ Watch for success message after save

### Organization
- ✅ Create all deduction types first
- ✅ Create all income types first
- ✅ Then assign to personnel
- ✅ Update regularly (monthly/semi-monthly)

### Maintenance
- ✅ Review deductions quarterly
- ✅ Update tax tables annually
- ✅ Verify government contribution rates
- ✅ Keep income types current

---

## 🎓 Training Tips

### For New Users (30 minutes)
1. Explain income vs deductions (5 min)
2. Demo setup wizard (5 min)
3. Show income assignment (10 min)
4. Show deduction assignment (10 min)

### For Administrators (60 minutes)
1. Database structure overview (10 min)
2. Setup wizards demonstration (10 min)
3. Managing types (income/deductions) (15 min)
4. Personnel assignment workflow (15 min)
5. Troubleshooting common issues (10 min)

---

## 📈 Workflow Summary

```
1. ONE-TIME SETUP
   ├─ Create tables (wizards)
   ├─ Add income types
   └─ Add deduction types

2. PER PERSONNEL (Repeat for each)
   ├─ Open personnel record
   ├─ Go to INCOME tab
   ├─ Enter amounts → Save
   ├─ Go to DEDUCTIONS tab
   └─ Enter amounts → Save

3. PAYROLL PERIOD (Monthly/Semi-monthly)
   ├─ Update variable income (overtime, etc.)
   ├─ Update loan deductions
   ├─ Generate payslips
   └─ Process payments
```

---

## 🎯 Success Indicators

You're doing it right when:
- ✅ Total income calculates automatically
- ✅ Row highlights green when amount entered
- ✅ Success message appears after save
- ✅ Data persists after page refresh
- ✅ Summary cards show correct totals

---

**Quick Start Complete!** 🎉

For detailed documentation, see: `payroll/db/README.md`

---

*Last Updated: October 20, 2025*  
*Version: 2.0*  
*Support: Contact your system administrator*
