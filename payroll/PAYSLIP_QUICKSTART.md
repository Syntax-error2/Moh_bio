# Payslip Generator - Quick Start Guide

## ✅ What You Just Got

A complete **Payslip Generator System** that creates professional payslips showing:
- Income (Basic Salary, PERA, COLA, etc.)
- Deductions (PhilHealth, SSS, Pag-IBIG, Tax, etc.)  
- **Net Pay** (Take-home pay)
- Print-ready format

---

## 🚀 How to Use (3 Simple Steps)

### Step 1: Set Up Personnel Income & Deductions

**First, add income data:**
1. Go to: `Payroll → Personnel List`
2. Click on any personnel
3. Click **"INCOME"** tab
4. Enter amounts (Basic Salary, PERA, etc.)
5. Click **"Save Income"**

**Then, add deductions:**
1. Click **"DEDUCTIONS"** tab (same personnel)
2. Enter employee and employer amounts
3. Click **"Save Deductions"**

### Step 2: Generate Payslip

**From Income page:**
- Click the blue **"Generate Payslip"** button (top right)

**From Deductions page:**
- Click the blue **"Generate Payslip"** button (top right)

### Step 3: Print or Save

- Click **"Print Payslip"** button
- Use browser's print dialog to:
  - Print to paper
  - Save as PDF
  - Email as attachment

---

## 📋 What the Payslip Shows

### Example Payslip Output:

```
═══════════════════════════════════════════
              MINISTRY OF HEALTH
       Human Resource Management System
                                         
                 PAYSLIP
───────────────────────────────────────────

Employee Name:     Juan D. Cruz
Employee ID:       14
Department:        Human Resources
Position:          HR Manager
Pay Period:        October 1-31, 2025

───────────────────────────────────────────
INCOME
───────────────────────────────────────────
Basic Salary              ₱ 25,000.00
PERA                      ₱  2,000.00
COLA                      ₱  1,500.00
                          ───────────

───────────────────────────────────────────
DEDUCTIONS
───────────────────────────────────────────
                    Employee    Employer
PhilHealth          ₱   700.00  ₱   700.00
SSS                 ₱ 1,125.00  ₱ 2,475.00
Pag-IBIG            ₱   100.00  ₱   100.00
Tax                 ₱ 2,500.00  ₱     0.00
                    ───────────  ───────────

═══════════════════════════════════════════
TOTAL GROSS INCOME:          ₱ 28,500.00
TOTAL DEDUCTIONS:            ₱  4,425.00
───────────────────────────────────────────
NET PAY:                     ₱ 24,075.00
═══════════════════════════════════════════

Employer Contribution:       ₱  3,275.00

_______________________  _______________________
Employee Signature       HR Manager Signature
```

---

## 🔍 Where to Find the Button

### Option 1: From Income Page
```
Personnel List → Select Personnel → INCOME Tab
                                      ↓
                        [Generate Payslip] button
```

### Option 2: From Deductions Page
```
Personnel List → Select Personnel → DEDUCTIONS Tab
                                      ↓
                        [Generate Payslip] button
```

---

## ✅ Features

| Feature | Description |
|---------|-------------|
| **Real-time Calculation** | Automatically calculates net pay |
| **Print-Ready** | Professional format for paper |
| **PDF Export** | Save as PDF via browser |
| **Employer Share** | Shows both employee and employer contributions |
| **Complete Summary** | Gross, Deductions, Net Pay all in one view |
| **Professional Design** | Clean, organized layout |

---

## 🎯 Common Use Cases

### Use Case 1: Monthly Payslip Distribution
1. Generate payslip for each personnel
2. Print or save as PDF
3. Distribute to employees
4. File for records

### Use Case 2: Quick Salary Review
1. Click "Generate Payslip" button
2. Review net pay and deductions
3. Print if approved
4. Close if just reviewing

### Use Case 3: Year-End Summary
1. Generate payslips for all personnel
2. Save as PDF files
3. Archive for annual records
4. Submit to accounting

---

## 🔧 Calculations Explained

### Net Pay Formula:
```
Gross Income = Sum of all income items
             = Basic Salary + PERA + COLA + Other Income

Total Deductions = Sum of employee portions
                 = PhilHealth + SSS + Pag-IBIG + Tax + Others

NET PAY = Gross Income - Total Deductions
        (This is the employee's take-home pay)
```

### Example:
```
Income:
  Basic Salary:  ₱25,000.00
  PERA:          ₱ 2,000.00
  COLA:          ₱ 1,500.00
                 ─────────────
  Gross Total:   ₱28,500.00

Deductions:
  PhilHealth:    ₱   700.00
  SSS:           ₱ 1,125.00
  Pag-IBIG:      ₱   100.00
  Tax:           ₱ 2,500.00
                 ─────────────
  Deduct Total:  ₱ 4,425.00

NET PAY:         ₱24,075.00 ← Take-home pay
```

---

## ⚠️ Important Notes

### Before Generating Payslip:
1. ✅ Ensure personnel has at least one income item
2. ✅ Add deductions if applicable
3. ✅ Verify amounts are correct
4. ✅ Click "Save" before generating

### If Payslip is Empty:
- Go back and add income/deductions first
- Make sure you clicked "Save Income" and "Save Deductions"
- Check that items are marked as active

### Printing Tips:
- Use **Portrait** orientation
- Paper size: **Letter** or **A4**
- Margins: Default
- Save as PDF to preserve formatting

---

## 🐛 Troubleshooting

### Problem: "Generate Payslip" button not visible
**Solution:** Update your browser cache (Ctrl+F5)

### Problem: Payslip shows zero amounts
**Solution:** 
1. Go back to Income/Deductions tabs
2. Add amounts
3. Click Save
4. Try generating again

### Problem: Print button doesn't work
**Solution:**
1. Try keyboard shortcut: Ctrl+P (Windows) or Cmd+P (Mac)
2. Try different browser (Chrome recommended)
3. Check if pop-ups are blocked

### Problem: Calculations seem wrong
**Solution:**
1. Verify amounts in Income tab
2. Verify amounts in Deductions tab
3. Check for duplicate entries
4. Ensure only active items are counted

---

## 📂 Files Created

| File | Purpose |
|------|---------|
| `generate_payslip.php` | Main payslip generator |
| `PAYSLIP_GENERATOR.md` | Complete documentation |
| `PAYSLIP_QUICKSTART.md` | This quick start guide |

### Updated Files:
| File | Change |
|------|--------|
| `list_personnel_income.php` | Added "Generate Payslip" button |
| `list_personnel_deductions.php` | Added "Generate Payslip" button |

---

## 🎓 Training Tips

### For HR Staff:
1. Practice generating payslips for test personnel first
2. Verify calculations manually for accuracy
3. Print sample payslips on draft paper
4. Create a checklist for monthly payslip generation

### For Accounting:
1. Use payslips to verify salary disbursements
2. Print payslips for audit trail
3. Archive payslips by month/year
4. Cross-reference with bank transfer records

---

## ✅ Next Steps

1. **Test with sample data**
   - Create test personnel
   - Add income and deductions
   - Generate and print payslip

2. **Set up real data**
   - Configure income types (see `income.php`)
   - Configure deduction types (see `deductions.php`)
   - Add data for all personnel

3. **Monthly process**
   - Review and update amounts monthly
   - Generate payslips for distribution
   - Print and file for records

4. **Future enhancements** (optional)
   - Add batch generation for all personnel
   - Implement email distribution
   - Create payslip archive system

---

## 🆘 Support

### If you need help:
1. Check the complete documentation: `PAYSLIP_GENERATOR.md`
2. Review error logs: `C:\xampp\apache\logs\error.log`
3. Test with simple data first
4. Verify database tables exist and have data

### Quick SQL Check:
```sql
-- Check if personnel has income data
SELECT * FROM pr_tbl_personnel_income 
WHERE personnel_id = 'YOUR_ID';

-- Check if personnel has deduction data
SELECT * FROM pr_tbl_personnel_deductions 
WHERE personnel_id = 'YOUR_ID';
```

---

## 🎉 You're Ready!

The payslip generator is now fully operational. Just:
1. Add income/deductions for personnel
2. Click "Generate Payslip"
3. Print or save as PDF

**It's that simple!**

---

*Created: October 20, 2025*  
*Status: ✅ Production Ready*
