# System Workflow & Process Flows
## Payroll Template, History & Snapshot System

---

## 1. OVERALL SYSTEM FLOW

```
┌─────────────────────────────────────────────────────────┐
│                    PAYROLL SYSTEM                       │
└─────────────────────────────────────────────────────────┘
                            │
            ┌───────────────┼───────────────┐
            ▼               ▼               ▼
    ┌──────────────┐ ┌──────────────┐ ┌──────────────┐
    │  TEMPLATES   │ │   HISTORY    │ │  SNAPSHOTS   │
    │  (Profiles)  │ │   (Runs)     │ │  (Reports)   │
    └──────┬───────┘ └──────┬───────┘ └──────┬───────┘
           │                │                 │
           │ Creates        │ Generates       │
           ▼                ▼                 ▼
    ┌──────────────┐ ┌──────────────┐ ┌──────────────┐
    │   Income/    │ │  Personnel   │ │  Department  │
    │  Deduction   │ │   Details    │ │  Statistics  │
    │    Items     │ │   Amounts    │ │   Analysis   │
    └──────────────┘ └──────────────┘ └──────────────┘
```

---

## 2. PROFILE CREATION WORKFLOW

```
START
  │
  ▼
┌─────────────────────┐
│ User clicks         │
│ "Create New Profile"│
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ Fill Profile Form   │
│ • Name              │
│ • Type              │
│ • Frequency         │
│ • Description       │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ Click "Create"      │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ save_payroll_       │
│ profile.php         │
│ • Validate input    │
│ • Check duplicates  │
│ • Insert to DB      │
└──────────┬──────────┘
           │
           ▼
    ┌──────┴──────┐
    │ Success?    │
    └──────┬──────┘
           │
    ┌──────┼──────┐
    │ YES          │ NO
    ▼              ▼
┌────────┐   ┌────────────┐
│ Redirect│   │Show Error  │
│to View  │   │Go Back     │
└────────┘   └────────────┘
           │
           ▼
┌─────────────────────┐
│ Add Income Items    │
│ • Select income     │
│ • Set amount method │
│ • Set default amt   │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ Add Deduction Items │
│ • Select deduction  │
│ • Set amount method │
│ • Set defaults      │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ Profile Ready!      │
│ Can generate payroll│
└─────────────────────┘
END
```

---

## 3. PAYROLL GENERATION WORKFLOW

```
START
  │
  ▼
┌─────────────────────────┐
│ User selects profile    │
│ Clicks "Generate"       │
└──────────┬──────────────┘
           │
           ▼
┌─────────────────────────┐
│ generate_payroll_       │
│ from_profile.php        │
│ • Show profile details  │
│ • Show income/deductions│
└──────────┬──────────────┘
           │
           ▼
┌─────────────────────────┐
│ Configure Run           │
│ • Run name              │
│ • Pay period dates      │
│ • Payment date          │
└──────────┬──────────────┘
           │
           ▼
┌─────────────────────────┐
│ Select Personnel        │
│ Choose one:             │
│ ○ All                   │
│ ○ By Department         │
│ ○ By Designation        │
│ ○ By Status             │
│ ○ Custom                │
└──────────┬──────────────┘
           │
           ▼
┌─────────────────────────┐
│ Review Preview          │
│ • Income items count    │
│ • Deduction items count │
│ • Personnel selected    │
└──────────┬──────────────┘
           │
           ▼
┌─────────────────────────┐
│ Click "Generate"        │
└──────────┬──────────────┘
           │
           ▼
┌─────────────────────────┐
│ process_payroll_        │
│ generation.php          │
│ START TRANSACTION       │
└──────────┬──────────────┘
           │
           ▼
┌─────────────────────────┐
│ Validate Input          │
│ • Required fields?      │
│ • Valid dates?          │
│ • Profile exists?       │
└──────────┬──────────────┘
           │
           ▼
    ┌──────┴──────┐
    │ Valid?      │
    └──────┬──────┘
           │
    ┌──────┼──────┐
    │ YES          │ NO
    ▼              ▼
┌────────┐   ┌────────────┐
│Continue│   │ROLLBACK    │
│        │   │Show Error  │
└────┬───┘   └────────────┘
     │
     ▼
┌─────────────────────────┐
│ Build Personnel Query   │
│ Based on selection:     │
│ • All: No filter        │
│ • Dept: WHERE do_id IN  │
│ • Des: WHERE des_id IN  │
│ • Status: WHERE stat IN │
│ • Custom: WHERE id IN   │
└──────────┬──────────────┘
           │
           ▼
┌─────────────────────────┐
│ Get Personnel List      │
│ Execute query           │
│ Count: N personnel      │
└──────────┬──────────────┘
           │
           ▼
┌─────────────────────────┐
│ Get Profile Items       │
│ • Income items          │
│ • Deduction items       │
└──────────┬──────────────┘
           │
           ▼
┌─────────────────────────┐
│ Create Payroll Run      │
│ INSERT INTO             │
│ pr_tbl_payroll_runs     │
│ Get run_id              │
└──────────┬──────────────┘
           │
           ▼
┌─────────────────────────┐
│ FOR EACH Personnel:     │
│ (Loop N times)          │
└──────────┬──────────────┘
           │
     ┌─────┴─────┐
     │ Loop Start│
     └─────┬─────┘
           │
           ▼
  ┌────────────────────┐
  │ Calculate Income   │
  └────────┬───────────┘
           │
           ▼
  ┌────────────────────────────┐
  │ FOR EACH Income Item:      │
  └────────┬───────────────────┘
           │
           ▼
  ┌────────────────────────────┐
  │ Get Amount:                │
  │ IF personnel_specific:     │
  │   Query pr_tbl_personnel_  │
  │   income for amount        │
  │ ELSE IF fixed:             │
  │   Use default_amount       │
  │ ELSE IF percentage:        │
  │   Calculate from base      │
  │ ELSE:                      │
  │   Use default              │
  └────────┬───────────────────┘
           │
           ▼
  ┌────────────────────────────┐
  │ Add to gross_pay          │
  │ Store in income_data[]    │
  └────────┬───────────────────┘
           │
           ▼
  ┌────────────────────┐
  │ End Income Loop    │
  └────────┬───────────┘
           │
           ▼
  ┌────────────────────────────┐
  │ Calculate Deductions       │
  └────────┬───────────────────┘
           │
           ▼
  ┌────────────────────────────┐
  │ FOR EACH Deduction Item:   │
  └────────┬───────────────────┘
           │
           ▼
  ┌────────────────────────────┐
  │ Get Amounts:               │
  │ IF personnel_specific:     │
  │   Query pr_tbl_personnel_  │
  │   deductions               │
  │ ELSE IF fixed:             │
  │   Use defaults             │
  │ ELSE:                      │
  │   Use defaults             │
  └────────┬───────────────────┘
           │
           ▼
  ┌────────────────────────────┐
  │ Add to deductions          │
  │ Add to employer_share      │
  │ Store in deduction_data[]  │
  └────────┬───────────────────┘
           │
           ▼
  ┌────────────────────┐
  │ End Deduction Loop │
  └────────┬───────────┘
           │
           ▼
  ┌────────────────────────────┐
  │ Calculate Net Pay          │
  │ net = gross - deductions   │
  └────────┬───────────────────┘
           │
           ▼
  ┌────────────────────────────┐
  │ INSERT INTO                │
  │ pr_tbl_payroll_run_details │
  │ Get detail_id              │
  └────────┬───────────────────┘
           │
           ▼
  ┌────────────────────────────┐
  │ INSERT Income Items        │
  │ pr_tbl_payroll_run_income  │
  │ (Snapshot at this moment)  │
  └────────┬───────────────────┘
           │
           ▼
  ┌────────────────────────────┐
  │ INSERT Deduction Items     │
  │ pr_tbl_payroll_run_        │
  │ deductions                 │
  └────────┬───────────────────┘
           │
           ▼
  ┌────────────────────────────┐
  │ Add to Run Totals:         │
  │ • total_gross              │
  │ • total_deductions         │
  │ • total_employer_share     │
  │ • total_net_pay            │
  └────────┬───────────────────┘
           │
     ┌─────┴─────┐
     │ More?     │
     └─────┬─────┘
           │
    ┌──────┼──────┐
    │ YES          │ NO
    ▼              ▼
  Loop Back    Continue
           │
           ▼
┌─────────────────────────┐
│ Update Run Totals       │
│ UPDATE pr_tbl_payroll_  │
│ runs SET totals         │
└──────────┬──────────────┘
           │
           ▼
┌─────────────────────────┐
│ Generate Snapshot       │
│ CALL sp_generate_       │
│ payroll_snapshot()      │
│ • Overall stats         │
│ • Department stats      │
│ • Income summaries      │
│ • Deduction summaries   │
└──────────┬──────────────┘
           │
           ▼
┌─────────────────────────┐
│ Log Audit Trail         │
│ INSERT INTO audit_log   │
└──────────┬──────────────┘
           │
           ▼
┌─────────────────────────┐
│ COMMIT TRANSACTION      │
└──────────┬──────────────┘
           │
           ▼
    ┌──────┴──────┐
    │ Success?    │
    └──────┬──────┘
           │
    ┌──────┼──────┐
    │ YES          │ NO
    ▼              ▼
┌────────────┐ ┌──────────┐
│ Redirect to│ │ ROLLBACK │
│ View Run   │ │ Error    │
└────────────┘ └──────────┘
END
```

---

## 4. STATUS WORKFLOW

```
┌─────────────────┐
│     DRAFT       │ ← Initial state after generation
└────────┬────────┘
         │
         │ Submit for approval
         ▼
┌─────────────────┐
│    PENDING      │ ← Awaiting approval
└────────┬────────┘
         │
         │ Approve
         ▼
┌─────────────────┐
│   APPROVED      │ ← Approved, ready to process
└────────┬────────┘
         │
         │ Start processing
         ▼
┌─────────────────┐
│  PROCESSING     │ ← Currently processing payments
└────────┬────────┘
         │
         │ All payments made
         ▼
┌─────────────────┐
│   COMPLETED     │ ← Finished successfully
└─────────────────┘

      ┌─────────────────┐
      │   CANCELLED     │ ← Can cancel from any status
      └─────────────────┘
```

---

## 5. DATA SNAPSHOT FLOW

```
┌─────────────────────────────────────┐
│ Payroll Run Completed               │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ Trigger: sp_generate_payroll_       │
│          snapshot(run_id)            │
└──────────────┬──────────────────────┘
               │
         ┌─────┼─────┐
         ▼     ▼     ▼
    ┌────┐ ┌────┐ ┌────┐
    │ 1  │ │ 2  │ │ 3  │
    └──┬─┘ └──┬─┘ └──┬─┘
       │      │      │
       │      │      └─────────────────┐
       │      │                        │
       │      └───────────┐            │
       │                  │            │
       ▼                  ▼            ▼
┌──────────────┐  ┌──────────────┐  ┌──────────────┐
│   Overall    │  │  Department  │  │   Income/    │
│   Snapshot   │  │  Snapshots   │  │  Deduction   │
│              │  │              │  │  Summaries   │
│ • All stats  │  │ • Per dept   │  │ • Per type   │
│ • Totals     │  │ • Counts     │  │ • Counts     │
│ • Averages   │  │ • Totals     │  │ • Totals     │
│ • Min/Max    │  │ • Averages   │  │ • Averages   │
└──────┬───────┘  └──────┬───────┘  └──────┬───────┘
       │                  │                  │
       │                  │                  │
       └──────────┬───────┴──────────────────┘
                  │
                  ▼
┌─────────────────────────────────────┐
│ Snapshots Stored in:                │
│ • pr_tbl_payroll_snapshots          │
│ • pr_tbl_payroll_snapshot_items     │
└─────────────────────────────────────┘
```

---

## 6. USER INTERACTION FLOW

```
┌────────────────────────────────────────┐
│           MAIN MENU                    │
└────────────┬───────────────────────────┘
             │
    ┌────────┼────────┐
    ▼        ▼        ▼
┌────────┐ ┌────┐ ┌────────┐
│Profiles│ │Runs│ │Reports │
└───┬────┘ └─┬──┘ └───┬────┘
    │        │        │
    ▼        ▼        ▼

PROFILES MENU:
├─ List Profiles
│  ├─ View
│  ├─ Edit
│  ├─ Clone
│  ├─ Delete
│  └─ Generate Payroll ──┐
├─ Create New Profile     │
└─ Manage Items           │
                          │
RUNS MENU:                │
├─ List History ◄─────────┘
│  ├─ View Details
│  ├─ Edit (if draft)
│  ├─ Print
│  └─ Export
├─ Filter Runs
└─ Search

REPORTS MENU:
├─ Snapshot Dashboard
│  ├─ Overall View
│  ├─ Department View
│  ├─ Income Type View
│  └─ Deduction Type View
├─ Comparison Reports
└─ Export Data
```

---

## 7. CALCULATION METHODS FLOW

```
┌─────────────────────────────────────┐
│ Profile Item Calculation Method     │
└──────────────┬──────────────────────┘
               │
    ┌──────────┼──────────┬───────────┐
    ▼          ▼          ▼           ▼
┌────────┐ ┌───────┐ ┌─────────┐ ┌─────────┐
│ Fixed  │ │Percent│ │Formula  │ │Personnel│
│        │ │       │ │         │ │Specific │
└───┬────┘ └───┬───┘ └────┬────┘ └────┬────┘
    │          │           │           │
    ▼          ▼           ▼           ▼
┌────────┐ ┌───────────┐ ┌─────────┐ ┌─────────┐
│Use     │ │Calculate: │ │Execute  │ │Query    │
│default │ │base × %   │ │custom   │ │personnel│
│amount  │ │from       │ │formula  │ │table    │
│        │ │profile    │ │         │ │         │
└───┬────┘ └───┬───────┘ └────┬────┘ └────┬────┘
    │          │               │           │
    └──────────┴───────┬───────┴───────────┘
                       │
                       ▼
            ┌──────────────────┐
            │  Amount Result   │
            └──────────────────┘
```

---

## 8. PERMISSION FLOW (Future Enhancement)

```
┌─────────────────────────┐
│     User Login          │
└──────────┬──────────────┘
           │
           ▼
┌─────────────────────────┐
│   Check Role            │
└──────────┬──────────────┘
           │
    ┌──────┼──────┬───────────┐
    ▼      ▼      ▼           ▼
┌────┐ ┌────┐ ┌──────┐ ┌──────────┐
│HR  │ │Mgr │ │Admin │ │Approver  │
│Staff│ │   │ │     │ │          │
└──┬─┘ └──┬─┘ └───┬──┘ └────┬─────┘
   │      │       │         │
   ▼      ▼       ▼         ▼
┌────────────────────────────────┐
│ PERMISSIONS:                   │
├────────────────────────────────┤
│ HR Staff:                      │
│ • Create profiles              │
│ • Generate payroll (draft)     │
│ • View runs                    │
│                                │
│ Manager:                       │
│ • View department runs         │
│ • View department reports      │
│                                │
│ Admin:                         │
│ • All HR Staff permissions     │
│ • Edit profiles                │
│ • Delete profiles              │
│ • Manage items                 │
│                                │
│ Approver:                      │
│ • View runs                    │
│ • Approve runs                 │
│ • Reject runs                  │
│ • Complete runs                │
└────────────────────────────────┘
```

---

## 9. ERROR HANDLING FLOW

```
┌─────────────────────────┐
│   Any Operation         │
└──────────┬──────────────┘
           │
     TRY {  │
           ▼
┌─────────────────────────┐
│ Execute Operation       │
│ • Validate input        │
│ • Process data          │
│ • Update database       │
└──────────┬──────────────┘
           │
    ┌──────┴──────┐
    │ Success?    │
    └──────┬──────┘
           │
    ┌──────┼──────┐
    │ YES          │ NO
    ▼              ▼
┌─────────┐   ┌─────────────┐
│ COMMIT  │   │  CATCH {    │
│         │   │             │
└────┬────┘   └──────┬──────┘
     │               │
     │               ▼
     │      ┌─────────────────┐
     │      │ Log Error       │
     │      │ • Error message │
     │      │ • Stack trace   │
     │      │ • Timestamp     │
     │      └────────┬────────┘
     │               │
     │               ▼
     │      ┌─────────────────┐
     │      │ ROLLBACK        │
     │      │ • Undo changes  │
     │      │ • Clean state   │
     │      └────────┬────────┘
     │               │
     │               ▼
     │      ┌─────────────────┐
     │      │ Show User Error │
     │      │ • Friendly msg  │
     │      │ • Redirect back │
     │      └────────┬────────┘
     │               │
     └───────┬───────┘
             │
             ▼
┌─────────────────────────┐
│      Continue           │
└─────────────────────────┘
```

---

**Document Version:** 1.0  
**Date:** October 20, 2025  
**Visual Aids for:** Payroll Template, History & Snapshot System
