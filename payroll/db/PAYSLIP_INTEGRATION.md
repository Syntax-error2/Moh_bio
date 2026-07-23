# 📄 MOH HRMS Payroll Module - Payslip Integration Guide

**Date:** October 20, 2025  
**Purpose:** Document how deductions and income modules integrate into the final payslip output  
**Reference Image:** `payroll/dev-docs/moh-payslip.jpg`

---

## 🎯 Overview

The payroll module's final output is a **PAYSLIP** that combines:
- ✅ Personnel Income (from `pr_tbl_personnel_income`)
- ✅ Personnel Deductions (from `pr_tbl_personnel_deductions`)
- ✅ Payroll Profile Schedule (from `pr_tbl_pay_pro_personnels`)

---

## 🔄 Data Flow: Input → Processing → Output

```
┌─────────────────────────────────────────────────────────────────┐
│                    PAYROLL DATA COLLECTION                      │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  1. PERSONNEL MASTER DATA (personnels table)                   │
│     ├─→ Name, Position, Department                             │
│     ├─→ Employee ID, Tax Details                               │
│     └─→ Pay Schedule Assignment                                │
│                                                                 │
│  2. INCOME SOURCES (pr_tbl_personnel_income)                   │
│     ├─→ Basic Salary          ₱ XX,XXX.XX                      │
│     ├─→ PERA (Allowance)      ₱  X,XXX.XX                      │
│     ├─→ COLA (Allowance)      ₱  X,XXX.XX                      │
│     ├─→ Overtime Pay          ₱    XXX.XX                      │
│     └─→ Other Benefits        ₱    XXX.XX                      │
│         ─────────────────────────────────                       │
│         GROSS PAY =           ₱ XX,XXX.XX ◄── SUM OF INCOME    │
│                                                                 │
│  3. DEDUCTIONS (pr_tbl_personnel_deductions)                   │
│     ├─→ GSIS (Employer)       ₱  X,XXX.XX                      │
│     ├─→ GSIS (Employee)       ₱  X,XXX.XX                      │
│     ├─→ PhilHealth            ₱    XXX.XX                      │
│     ├─→ Pag-IBIG              ₱    XXX.XX                      │
│     ├─→ Tax Withheld          ₱  X,XXX.XX                      │
│     ├─→ Loans                 ₱    XXX.XX                      │
│     └─→ Other Deductions      ₱    XXX.XX                      │
│         ─────────────────────────────────                       │
│         TOTAL DEDUCTIONS =    ₱  X,XXX.XX ◄── SUM OF DEDUCT    │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                      PAYSLIP COMPUTATION                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   GROSS PAY              ₱ XX,XXX.XX                            │
│   - TOTAL DEDUCTIONS     ₱  X,XXX.XX                            │
│   ═══════════════════════════════════                           │
│   NET PAY (Take Home)    ₱ XX,XXX.XX ◄── FINAL AMOUNT          │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    PAYSLIP OUTPUT (PDF/Print)                   │
│                    Reference: moh-payslip.jpg                   │
└─────────────────────────────────────────────────────────────────┘
```

---

## 📊 Payslip Components Breakdown

### Section 1: Header Information
```
┌───────────────────────────────────────────────┐
│  MINISTRY OF HEALTH - PAYSLIP                 │
│  Pay Period: [Start Date] - [End Date]        │
│  Payment Date: [Date]                         │
└───────────────────────────────────────────────┘
```
**Data Source:**
- `pr_tbl_payroll_profile.description` (e.g., "Weekly", "Monthly")
- `pr_tbl_payroll_profile.pay_period_start`
- `pr_tbl_payroll_profile.pay_period_end`

---

### Section 2: Employee Details
```
┌───────────────────────────────────────────────┐
│  Employee Name:    [LNAME, FNAME MNAME]       │
│  Employee ID:      [personnel_id]             │
│  Position:         [designation]              │
│  Department:       [dept_office_name]         │
└───────────────────────────────────────────────┘
```
**Data Source:**
- `personnels.fname`, `personnels.mname`, `personnels.lname`
- `personnels.personnel_id`
- `designations.designation` (JOIN)
- `dept_office.dept_office_name` (JOIN)

---

### Section 3: Earnings/Income
```
┌─────────────────────────────────┬─────────────┐
│ EARNINGS                        │    AMOUNT   │
├─────────────────────────────────┼─────────────┤
│ Basic Salary                    │ ₱25,000.00  │
│ PERA (Personal Econ Relief)     │  ₱2,000.00  │
│ COLA (Cost of Living Allow)     │  ₱1,500.00  │
│ Overtime Pay                    │    ₱500.00  │
├─────────────────────────────────┼─────────────┤
│ GROSS PAY                       │ ₱29,000.00  │
└─────────────────────────────────┴─────────────┘
```
**SQL Query:**
```sql
SELECT 
    i.income_title,
    pi.amount_per_pay
FROM pr_tbl_personnel_income pi
INNER JOIN pr_tbl_income i ON pi.income_id = i.income_id
WHERE pi.personnel_id = :personnel_id 
  AND pi.is_active = 1
ORDER BY 
    CASE 
        WHEN i.income_type = 'Regular' THEN 1
        WHEN i.income_type = 'Additional' THEN 2
        ELSE 3
    END,
    i.income_title;
```

**Computation:**
```php
$gross_pay = 0;
foreach ($income_rows as $income) {
    $gross_pay += $income['amount_per_pay'];
}
```

---

### Section 4: Deductions
```
┌─────────────────────────────────┬─────────────┬─────────────┐
│ DEDUCTIONS                      │  EMPLOYER   │  EMPLOYEE   │
├─────────────────────────────────┼─────────────┼─────────────┤
│ GSIS Contribution               │  ₱1,500.00  │  ₱1,500.00  │
│ PhilHealth Contribution         │    ₱400.00  │    ₱400.00  │
│ Pag-IBIG Contribution           │    ₱100.00  │    ₱100.00  │
│ Withholding Tax                 │          -  │  ₱2,000.00  │
│ SSS Loan                        │          -  │    ₱500.00  │
├─────────────────────────────────┼─────────────┼─────────────┤
│ TOTAL DEDUCTIONS                │  ₱2,000.00  │  ₱4,500.00  │
└─────────────────────────────────┴─────────────┴─────────────┘
```
**SQL Query:**
```sql
SELECT 
    d.deduction_type,
    d.deduction_title,
    pd.employer_amt_per_pay,
    pd.employee_amt_per_pay,
    (pd.employer_amt_per_pay + pd.employee_amt_per_pay) AS total_deduction
FROM pr_tbl_personnel_deductions pd
INNER JOIN pr_tbl_deductions d ON pd.deduction_id = d.deduction_id
WHERE pd.personnel_id = :personnel_id 
  AND pd.is_active = 1
ORDER BY 
    CASE 
        WHEN d.deduction_type = 'Mandatory' THEN 1
        WHEN d.deduction_type = 'Voluntary' THEN 2
        ELSE 3
    END,
    d.deduction_title;
```

**Computation:**
```php
$total_employer_deductions = 0;
$total_employee_deductions = 0;

foreach ($deduction_rows as $deduction) {
    $total_employer_deductions += $deduction['employer_amt_per_pay'];
    $total_employee_deductions += $deduction['employee_amt_per_pay'];
}

// Note: Employee deductions are subtracted from gross pay
// Employer deductions are for record-keeping/reporting only
```

---

### Section 5: Net Pay Summary
```
┌─────────────────────────────────┬─────────────┐
│ GROSS PAY                       │ ₱29,000.00  │
│ Less: Total Employee Deductions │ (₱4,500.00) │
├─────────────────────────────────┼─────────────┤
│ NET PAY (Take Home)             │ ₱24,500.00  │
└─────────────────────────────────┴─────────────┘
```
**Computation:**
```php
$net_pay = $gross_pay - $total_employee_deductions;
```

---

### Section 6: Employer Cost Summary
```
┌─────────────────────────────────┬─────────────┐
│ Total Employer Contributions    │  ₱2,000.00  │
│ Employee Gross Salary           │ ₱29,000.00  │
├─────────────────────────────────┼─────────────┤
│ TOTAL EMPLOYER COST             │ ₱31,000.00  │
└─────────────────────────────────┴─────────────┘
```
**Computation:**
```php
$total_employer_cost = $gross_pay + $total_employer_deductions;
```

---

## 🔗 Database Relationships for Payslip Generation

```sql
-- Complete payslip data query
SELECT 
    -- Personnel Info
    p.personnel_id,
    CONCAT(p.fname, ' ', COALESCE(p.mname, ''), ' ', p.lname, 
           CASE WHEN p.suffix != '-' THEN CONCAT(' ', p.suffix) ELSE '' END) AS full_name,
    d.designation,
    doff.dept_office_name,
    
    -- Payroll Profile
    pp.description AS pay_period,
    pp.pay_period_start,
    pp.pay_period_end,
    
    -- Income Totals
    COALESCE(SUM(pi.amount_per_pay), 0) AS gross_pay,
    
    -- Deduction Totals
    COALESCE(SUM(pd.employer_amt_per_pay), 0) AS total_employer_deductions,
    COALESCE(SUM(pd.employee_amt_per_pay), 0) AS total_employee_deductions
    
FROM personnels p
LEFT JOIN designations d ON p.designation_id = d.designation_id
LEFT JOIN dept_office doff ON p.do_id = doff.do_id
LEFT JOIN pr_tbl_pay_pro_personnels ppp ON p.personnel_id = ppp.personnel_id
LEFT JOIN pr_tbl_payroll_profile pp ON ppp.payprofile_id = pp.payprofile_id
LEFT JOIN pr_tbl_personnel_income pi ON p.personnel_id = pi.personnel_id AND pi.is_active = 1
LEFT JOIN pr_tbl_personnel_deductions pd ON p.personnel_id = pd.personnel_id AND pd.is_active = 1
WHERE p.personnel_id = :personnel_id
GROUP BY p.personnel_id;
```

---

## 📁 File Structure for Payslip Generation

### Current Files (Based on Search):
```
payroll/
├── home.php
│   └── Line 75: Link to print payslip
│       <a href="printPersonnelPerDept.php?do_id=<?php echo $do_id; ?>">
│           <i class="icon-bill"></i> Payslip
│       </a>
│
├── payroll_profile_assigned_personnels.php
│   └── Line 210: Button to print payslip
│       <button name="parProAssigning">
│           <i class="fa fa-print"></i> Print Payslip
│       </button>
│
├── payroll_profile_cud.php
│   └── Line 81: Handle payslip generation POST
│       if(isset($_POST['parProAssigning'])) { ... }
│
└── dev-docs/
    └── moh-payslip.jpg  ← Reference design for payslip output
```

### Missing/To Be Created:
```
payroll/
├── printPersonnelPerDept.php  ← Main payslip generator (referenced but not found)
├── generate_payslip.php       ← Individual payslip generator
└── payslip_pdf.php            ← PDF export handler
```

---

## 🎨 Payslip Layout Components (From Reference Image)

Based on `payroll/dev-docs/moh-payslip.jpg`, the payslip should include:

### Header Section:
- Organization logo/header
- Document title: "PAYSLIP" or "SALARY STATEMENT"
- Pay period dates
- Payment date

### Employee Information:
- Full name
- Employee ID/Personnel number
- Position/Designation
- Department/Office
- TIN (Tax Identification Number)

### Income Section:
- List of all income types with amounts
- Subtotal for regular income
- Subtotal for additional income
- **GROSS PAY total**

### Deduction Section:
- Mandatory deductions (GSIS, PhilHealth, Pag-IBIG)
- Tax deductions (Withholding Tax)
- Voluntary deductions (Loans, etc.)
- Split into Employer/Employee columns
- **TOTAL DEDUCTIONS**

### Net Pay Section:
- Clear display of NET PAY (Take Home)
- Usually in larger/bold text
- May include amount in words

### Footer Section:
- Signature lines
- Prepared by / Certified by / Approved by
- Date generated
- System watermark/timestamp

---

## 💻 Sample Payslip Generation Code

### Example: `generate_payslip.php`

```php
<?php
include('session.php');
require_once('dbcon.php');

// Get parameters
$personnel_id = $_GET['personnel_id'] ?? '';
$payprofile_id = $_GET['payprofile_id'] ?? '';

if (empty($personnel_id)) {
    die('Personnel ID required');
}

try {
    // Fetch personnel details
    $personnel_query = $conn->prepare("
        SELECT 
            p.*,
            d.designation,
            doff.dept_office_name,
            s.shift_name
        FROM personnels p
        LEFT JOIN designations d ON p.designation_id = d.designation_id
        LEFT JOIN dept_office doff ON p.do_id = doff.do_id
        LEFT JOIN shifts s ON p.shift_id = s.shift_id
        WHERE p.personnel_id = :personnel_id
    ");
    $personnel_query->execute([':personnel_id' => $personnel_id]);
    $personnel = $personnel_query->fetch();
    
    if (!$personnel) {
        die('Personnel not found');
    }
    
    // Fetch payroll profile
    $profile_query = $conn->prepare("
        SELECT pp.*
        FROM pr_tbl_pay_pro_personnels ppp
        INNER JOIN pr_tbl_payroll_profile pp ON ppp.payprofile_id = pp.payprofile_id
        WHERE ppp.personnel_id = :personnel_id
        ORDER BY pp.created_at DESC
        LIMIT 1
    ");
    $profile_query->execute([':personnel_id' => $personnel_id]);
    $payroll_profile = $profile_query->fetch();
    
    // Fetch income details
    $income_query = $conn->prepare("
        SELECT 
            i.income_type,
            i.income_title,
            pi.amount_per_pay
        FROM pr_tbl_personnel_income pi
        INNER JOIN pr_tbl_income i ON pi.income_id = i.income_id
        WHERE pi.personnel_id = :personnel_id 
          AND pi.is_active = 1
        ORDER BY 
            CASE 
                WHEN i.income_type = 'Regular' THEN 1
                WHEN i.income_type = 'Additional' THEN 2
                ELSE 3
            END,
            i.income_title
    ");
    $income_query->execute([':personnel_id' => $personnel_id]);
    $income_items = $income_query->fetchAll();
    
    // Calculate gross pay
    $gross_pay = 0;
    foreach ($income_items as $item) {
        $gross_pay += $item['amount_per_pay'];
    }
    
    // Fetch deduction details
    $deduction_query = $conn->prepare("
        SELECT 
            d.deduction_type,
            d.deduction_title,
            pd.employer_amt_per_pay,
            pd.employee_amt_per_pay
        FROM pr_tbl_personnel_deductions pd
        INNER JOIN pr_tbl_deductions d ON pd.deduction_id = d.deduction_id
        WHERE pd.personnel_id = :personnel_id 
          AND pd.is_active = 1
        ORDER BY 
            CASE 
                WHEN d.deduction_type = 'Mandatory' THEN 1
                WHEN d.deduction_type = 'Voluntary' THEN 2
                ELSE 3
            END,
            d.deduction_title
    ");
    $deduction_query->execute([':personnel_id' => $personnel_id]);
    $deduction_items = $deduction_query->fetchAll();
    
    // Calculate deductions
    $total_employer_deductions = 0;
    $total_employee_deductions = 0;
    foreach ($deduction_items as $item) {
        $total_employer_deductions += $item['employer_amt_per_pay'];
        $total_employee_deductions += $item['employee_amt_per_pay'];
    }
    
    // Calculate net pay
    $net_pay = $gross_pay - $total_employee_deductions;
    
    // Calculate total employer cost
    $total_employer_cost = $gross_pay + $total_employer_deductions;
    
} catch (PDOException $e) {
    error_log("Error generating payslip: " . $e->getMessage());
    die('Error generating payslip. Please contact administrator.');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payslip - <?php echo htmlspecialchars($personnel['fname'] . ' ' . $personnel['lname']); ?></title>
    <style>
        @media print {
            .no-print { display: none; }
        }
        body { font-family: Arial, sans-serif; }
        .payslip-container { 
            max-width: 800px; 
            margin: 20px auto; 
            border: 2px solid #333;
            padding: 20px;
        }
        .header { 
            text-align: center; 
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .section { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .amount { text-align: right; }
        .total-row { font-weight: bold; background-color: #e9ecef; }
        .net-pay { 
            font-size: 1.3em; 
            font-weight: bold; 
            background-color: #d4edda;
            border: 2px solid #28a745;
        }
    </style>
</head>
<body>
    <div class="payslip-container">
        <!-- Header -->
        <div class="header">
            <h1>MINISTRY OF HEALTH</h1>
            <h2>EMPLOYEE PAYSLIP</h2>
            <?php if ($payroll_profile) { ?>
            <p>Pay Period: <?php echo date('M d, Y', strtotime($payroll_profile['pay_period_start'])); ?> - 
                           <?php echo date('M d, Y', strtotime($payroll_profile['pay_period_end'])); ?></p>
            <p>Payment Date: <?php echo date('M d, Y'); ?></p>
            <?php } ?>
        </div>
        
        <!-- Employee Information -->
        <div class="section">
            <table>
                <tr>
                    <th>Employee Name:</th>
                    <td><?php echo htmlspecialchars($personnel['fname'] . ' ' . $personnel['mname'] . ' ' . $personnel['lname']); ?></td>
                </tr>
                <tr>
                    <th>Employee ID:</th>
                    <td><?php echo htmlspecialchars($personnel['personnel_id']); ?></td>
                </tr>
                <tr>
                    <th>Position:</th>
                    <td><?php echo htmlspecialchars($personnel['designation']); ?></td>
                </tr>
                <tr>
                    <th>Department:</th>
                    <td><?php echo htmlspecialchars($personnel['dept_office_name']); ?></td>
                </tr>
            </table>
        </div>
        
        <!-- Income Section -->
        <div class="section">
            <h3>EARNINGS</h3>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="amount">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($income_items as $item) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['income_title']); ?></td>
                        <td class="amount">₱<?php echo number_format($item['amount_per_pay'], 2); ?></td>
                    </tr>
                    <?php } ?>
                    <tr class="total-row">
                        <td>GROSS PAY</td>
                        <td class="amount">₱<?php echo number_format($gross_pay, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Deductions Section -->
        <div class="section">
            <h3>DEDUCTIONS</h3>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="amount">Employer</th>
                        <th class="amount">Employee</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($deduction_items as $item) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['deduction_title']); ?></td>
                        <td class="amount">₱<?php echo number_format($item['employer_amt_per_pay'], 2); ?></td>
                        <td class="amount">₱<?php echo number_format($item['employee_amt_per_pay'], 2); ?></td>
                    </tr>
                    <?php } ?>
                    <tr class="total-row">
                        <td>TOTAL DEDUCTIONS</td>
                        <td class="amount">₱<?php echo number_format($total_employer_deductions, 2); ?></td>
                        <td class="amount">₱<?php echo number_format($total_employee_deductions, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Net Pay Section -->
        <div class="section">
            <table>
                <tr>
                    <th>GROSS PAY</th>
                    <td class="amount">₱<?php echo number_format($gross_pay, 2); ?></td>
                </tr>
                <tr>
                    <th>Less: Total Employee Deductions</th>
                    <td class="amount">(₱<?php echo number_format($total_employee_deductions, 2); ?>)</td>
                </tr>
                <tr class="net-pay">
                    <th>NET PAY (Take Home)</th>
                    <td class="amount">₱<?php echo number_format($net_pay, 2); ?></td>
                </tr>
            </table>
        </div>
        
        <!-- Employer Cost (Optional - for internal records) -->
        <div class="section">
            <table>
                <tr>
                    <th>Total Employer Contributions</th>
                    <td class="amount">₱<?php echo number_format($total_employer_deductions, 2); ?></td>
                </tr>
                <tr>
                    <th>Employee Gross Salary</th>
                    <td class="amount">₱<?php echo number_format($gross_pay, 2); ?></td>
                </tr>
                <tr class="total-row">
                    <th>TOTAL EMPLOYER COST</th>
                    <td class="amount">₱<?php echo number_format($total_employer_cost, 2); ?></td>
                </tr>
            </table>
        </div>
        
        <!-- Footer -->
        <div class="section no-print">
            <button onclick="window.print()">Print Payslip</button>
            <button onclick="window.close()">Close</button>
        </div>
    </div>
</body>
</html>
```

---

## 🚀 Implementation Checklist

### Phase 1: Data Setup (✅ COMPLETED)
- [✅] Create `pr_tbl_income` - Income types master list
- [✅] Create `pr_tbl_deductions` - Deduction types master list
- [✅] Create `pr_tbl_payroll_profile` - Pay schedules
- [✅] Create `pr_tbl_pay_pro_personnels` - Personnel pay schedule assignments
- [✅] Create `pr_tbl_personnel_deductions` - Personnel deduction amounts (needs table creation)
- [⏳] Create `pr_tbl_personnel_income` - Personnel income amounts (future)

### Phase 2: Data Entry Pages (✅ COMPLETED)
- [✅] `income.php` - Manage income types
- [✅] `deductions.php` - Manage deduction types
- [✅] `list_personnel_income.php` - Assign income to personnel
- [✅] `list_personnel_deductions.php` - Assign deductions to personnel

### Phase 3: Payslip Generation (🔴 TO DO)
- [🔴] Create `generate_payslip.php` - Single payslip generator
- [🔴] Create `printPersonnelPerDept.php` - Batch payslip for department
- [🔴] Create `payslip_pdf.php` - PDF export functionality
- [🔴] Add payslip preview before printing
- [🔴] Add email payslip functionality (optional)

### Phase 4: Reports & Analytics (🔴 TO DO)
- [🔴] Payroll summary report
- [🔴] Deduction breakdown report
- [🔴] Employer cost analysis
- [🔴] Monthly payroll register

---

## 🎯 Next Steps

1. **Review Reference Image**
   - Examine `payroll/dev-docs/moh-payslip.jpg` for exact layout requirements
   - Identify any custom fields or additional sections needed

2. **Create Missing Tables**
   - Run `setup_personnel_deductions.php` to create `pr_tbl_personnel_deductions`
   - Plan and create `pr_tbl_personnel_income` table

3. **Implement Payslip Generator**
   - Use the sample code above as a starting point
   - Customize based on moh-payslip.jpg design
   - Add PDF export capability

4. **Testing**
   - Test with sample personnel data
   - Verify calculations: Gross Pay, Deductions, Net Pay
   - Test print functionality across browsers

5. **Deployment**
   - Deploy to production after thorough testing
   - Train users on the complete payroll workflow
   - Create user documentation

---

## 📞 Support & Documentation

- **Schema Reference:** `payroll/db/PAYROLL_SCHEMA_REFERENCE.md`
- **Quick Reference:** `payroll/db/QUICK_REFERENCE.md`
- **Bug Fixes:** `payroll/db/BUGFIX_*.md`
- **This Document:** `payroll/db/PAYSLIP_INTEGRATION.md`

---

**END OF PAYSLIP INTEGRATION GUIDE**
