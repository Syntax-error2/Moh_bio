# QUICK START GUIDE - CS Form No. 6 Leave Application System

## ✅ System Status: READY TO USE

All files created, database tables set up, and integration complete!

---

## 🚀 Getting Started (5 Minutes)

### Step 1: Access the System
```
http://localhost/moh_hrms/leave_application.php?personnel_id=1
```
*Replace `1` with actual personnel ID*

### Step 2: Create Your First Leave Application
1. Click **"New Leave Application"** button
2. Fill in Office/Agency field
3. Select leave type (e.g., "Vacation Leave")
4. Enter inclusive dates
5. Enter number of days
6. Click **"Submit Application"**

✅ **Result:** Application created with status = PENDING (yellow badge)

### Step 3: Approve the Application
1. Find your application in the table
2. Click **"Edit"** from the action dropdown
3. Change Status to **"Approved"**
4. Add recommendation text (optional)
5. Click **"Update Application"**

✅ **Result:** 
- Application status = APPROVED (green badge)
- **Leave card entry automatically created!**
- Check leave_card.php to see the new entry

### Step 4: Print Official Form
1. Click **"Print"** from the action dropdown
2. Official CS Form No. 6 opens in new tab
3. Click the print button or use Ctrl+P

✅ **Result:** Professional formatted leave application form ready for signatures

---

## 🔗 Integration with Leave Card

### What Happens When You Approve?

**Before Approval:**
- Leave application exists in database
- Status = "pending"
- No leave card entry yet

**After Approval:**
- Status changes to "approved"
- **System automatically runs `createLeaveCardEntry()` function**
- New leave_card record created with:
  - Personnel ID from application
  - Leave type properly mapped
  - VL/SL deductions from application
  - Special leave flag (if applicable)
  - Date range and number of days
  - `created_from_application = 1` flag
- Leave application updated with `leave_card_entry_id`

**Verification Query:**
```sql
SELECT 
    la.id,
    la.leave_type,
    la.status,
    la.leave_card_entry_id,
    lc.particulars,
    lc.vl_with_pay,
    lc.sl_with_pay,
    lc.created_from_application
FROM leave_applications la
LEFT JOIN leave_card lc ON la.leave_card_entry_id = lc.id
WHERE la.personnel_id = 1;
```

---

## 📋 Leave Types Reference

### Standard Leaves (Deduct from Credits)
- **Vacation Leave** → Deducts from VL balance
- **Sick Leave** → Deducts from SL balance
- **Mandatory/Forced Leave** → Deducts from VL balance

### Special Leaves (NO Deduction)
- Maternity Leave
- Paternity Leave
- Special Privilege Leave
- Solo Parent Leave
- Study Leave
- 10-Day VAWC Leave
- Rehabilitation Privilege
- Special Leave Benefits for Women
- Special Emergency (Calamity) Leave
- Adoption Leave

*Values are saved but marked as special (is_special_leave=1)*

---

## 🎯 Common Tasks

### Create Vacation Leave Application
```
Leave Type: Vacation Leave - Within Philippines
Location: Cebu City, Philippines
Dates: Jan 15, 2025 to Jan 19, 2025
Days: 5
```

### Create Maternity Leave Application
```
Leave Type: Maternity Leave
Dates: Feb 1, 2025 to Apr 1, 2025
Days: 60
Note: Special leave - will NOT deduct from balance
```

### Approve Application
```
1. Edit application
2. Status → Approved
3. Recommendation: "Approved as requested"
4. Save
```

### Print Application
```
Action → Print → Opens CS Form No. 6 → Print (Ctrl+P)
```

### Delete Application (with leave card)
```
1. Click Delete
2. Confirm deletion
3. Both application AND linked leave card entry removed
```

---

## 🗂️ Files Overview

### Main Files You'll Use
| File | Purpose |
|------|---------|
| `leave_application.php` | Main page (view all applications) |
| `add_leave_application_modal.php` | New application form |
| `edit_leave_application_modal.php` | Edit/approve applications |
| `save_leave_application.php` | Backend processor (auto-creates leave card) |
| `print_leave_application.php` | Official form printout |

### Database Tables
| Table | Purpose |
|-------|---------|
| `leave_applications` | Stores all CS Form No. 6 applications |
| `leave_card` | Leave credits and deductions (updated columns) |

---

## 💡 Pro Tips

### For Employees
1. Fill leave credits accurately before submitting
2. Use "Commutation: Requested" if you want to be paid for unused leave
3. Check status badge: Yellow (pending), Green (approved), Red (disapproved)

### For Approvers
1. Review leave credits before approving
2. Add clear recommendations for audit trail
3. Once approved, leave card entry is auto-created (can't be undone easily)
4. Print official form for physical signatures

### For Admins
1. Check `leave_card.created_from_application = 1` to identify auto-created entries
2. Use status filters to find pending applications quickly
3. Export data for reports using SQL queries (see documentation)

---

## 🔍 Troubleshooting

### Application saved but status not changing?
**Check:** Modal is properly closing after save, refresh page to see updated status

### Leave card entry not created?
**Check:** 
1. Application status is "approved" (not "pending")
2. No PHP errors in `c:\xampp\apache\logs\error.log`
3. Database foreign keys are properly set

### Print preview shows no data?
**Check:** 
1. Application ID is valid in URL
2. Personnel data exists in personnels table
3. JOINs in print_leave_application.php query are correct

### Special leave showing balance deduction?
**Check:**
1. Leave type is in $special_leave_types array (save_leave_application.php)
2. is_special_leave flag is set to 1 in leave_card
3. leave_card.php has conditional balance calculation

---

## 📊 Verification Checklist

After creating and approving your first application:

- [ ] Application visible in leave_application.php table
- [ ] Status badge shows "Approved" (green)
- [ ] leave_card table has new entry
- [ ] leave_card.created_from_application = 1
- [ ] leave_applications.leave_card_entry_id is set
- [ ] Deductions match application (VL or SL)
- [ ] Special leave marked correctly (if applicable)
- [ ] Print preview shows all data correctly
- [ ] leave_card.php displays updated balance

---

## 🎉 You're All Set!

The CS Form No. 6 Leave Application system is fully operational with automatic leave card integration.

**Next Steps:**
1. Test with real personnel data
2. Train users on workflow
3. Set up approval permissions (if needed)
4. Generate reports from leave_applications table

**Need Help?**
- Read: `LEAVE_APPLICATION_SETUP_GUIDE.md` (full documentation)
- Read: `LEAVE_APPLICATION_IMPLEMENTATION_SUMMARY.md` (technical details)
- Check: PHP error logs for debugging

---

**Happy Leave Management! 🌴**
