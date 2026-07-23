# CS FORM NO. 6 - LEAVE APPLICATION SYSTEM
## Setup and Implementation Guide

**Created:** October 24, 2025  
**System:** Philippine Civil Service Leave Application (CS Form No. 6 - Revised 2020)

---

## 📋 OVERVIEW

This system implements a complete leave application workflow based on the official Philippine Civil Service Commission's CS Form No. 6. 

**Key Feature:** Any approved leave application automatically creates a corresponding leave card entry, ensuring data consistency between the application and leave record systems.

---

## 🗂️ FILES CREATED

### 1. **Main Display Page**
- `leave_application.php` - DataTables view of all leave applications with action buttons

### 2. **Modal Files**
- `add_leave_application_modal.php` - New application form (CS Form No. 6 fields)
- `edit_leave_application_modal.php` - Edit existing application with status update
- `delete_leave_application_modal.php` - Delete confirmation with details preview

### 3. **Backend Processing**
- `save_leave_application.php` - Handles save/update/delete operations
  - **AUTO-INTEGRATION:** Creates leave_card entry when status changes to 'approved'
  - Maps leave types to proper leave card categories
  - Handles special leave types (Maternity, Paternity, etc.)
  - Links application to leave card entry via `leave_card_entry_id`

### 4. **AJAX Endpoints**
- `get_leave_application.php` - Fetch single application data (JSON)

### 5. **Print Preview**
- `print_leave_application.php` - Official CS Form No. 6 layout for printing

### 6. **Database Schema**
- `leave_application_schema.sql` - Complete table structure and indexes

---

## 🗄️ DATABASE SETUP

### Step 1: Run the SQL Schema

Execute the SQL file to create the `leave_applications` table:

```sql
-- Run this in phpMyAdmin or MySQL console
SOURCE c:/xampp/htdocs/moh_hrms/leave_application_schema.sql;
```

Or manually execute the SQL in phpMyAdmin.

### Step 2: Verify Tables

Check that the following exist:
- ✅ `leave_applications` table (new)
- ✅ `leave_card` table has `created_from_application` column (added)

---

## 🔗 INTEGRATION WITH LEAVE CARD SYSTEM

### Automatic Leave Card Entry Creation

When a leave application's status changes to **'approved'**, the system automatically:

1. **Creates a new `leave_card` entry** with:
   - Personnel ID from application
   - Period from application date
   - Particulars (leave type mapped)
   - VL/SL deductions from application
   - Special leave flag (if applicable)
   - Date range and number of days
   - `created_from_application = 1` flag

2. **Links the records** by storing `leave_card_entry_id` in the application

3. **Handles special leave types** properly:
   - Maternity, Paternity, Special Privilege, Solo Parent, Study, etc.
   - Values are saved but marked as special leave (no balance deduction)

### Leave Type Mapping

| CS Form No. 6 Leave Type | Leave Card Particulars | Deduction Field |
|--------------------------|------------------------|-----------------|
| Vacation Leave (any) | Vacation Leave | vl_with_pay |
| Sick Leave (any) | Sick Leave | sl_with_pay |
| Mandatory/Forced Leave | Mandatory/Forced Leave | vl_with_pay |
| Maternity Leave | Maternity Leave | Special (no deduction) |
| Paternity Leave | Paternity Leave | Special (no deduction) |
| Special Privilege Leave | Special Privilege Leave | Special (no deduction) |
| Solo Parent Leave | Solo Parent Leave | Special (no deduction) |
| Study Leave | Study Leave | Special (no deduction) |
| 10-Day VAWC Leave | 10-Day VAWC Leave | Special (no deduction) |
| Others | [As specified] | Based on type |

---

## 📊 CS FORM NO. 6 FIELDS

### Application Information
- Office/Agency/Department
- Date of Filing
- Personnel Name (auto-filled)
- Position (auto-filled)

### Leave Type Options
- ✅ Vacation Leave (Within Philippines / Abroad)
- ✅ Mandatory/Forced Leave
- ✅ Sick Leave (In Hospital / Out Patient)
- ✅ Maternity Leave
- ✅ Paternity Leave
- ✅ Special Privilege Leave
- ✅ Solo Parent Leave
- ✅ Study Leave (Master's Degree / BAR/Board Exam)
- ✅ 10-Day VAWC Leave
- ✅ Rehabilitation Privilege
- ✅ Special Leave Benefits for Women
- ✅ Special Emergency (Calamity) Leave
- ✅ Adoption Leave
- ✅ Others (with specification)

### Leave Details
- Conditional fields based on leave type:
  - **Vacation:** Location (within/abroad Philippines)
  - **Sick:** Hospital name or illness details
  - **Study:** University/course details

### Date and Duration
- Inclusive Date From
- Inclusive Date To
- Number of Working Days (supports half days: 0.5, 2.5, etc.)

### Commutation
- Requested / Not Requested

### Leave Credits Certification
- As of (Date)
- Total Earned (VL / SL)
- Less This Application (VL / SL)
- Balance (VL / SL) - Auto-calculated

### Approval Section
- Status (Pending / Approved / Disapproved)
- Recommendation / Remarks

---

## 🔄 WORKFLOW

### 1. **Application Submission**
```
Employee → Leave Application Page → Click "New Leave Application"
→ Fill CS Form No. 6 → Submit → Status: PENDING
```

### 2. **Review and Approval**
```
Authorized Officer → View Applications → Click Edit
→ Update Status to "APPROVED" → Add Recommendation → Save
```

### 3. **Automatic Leave Card Creation**
```
System detects status change to APPROVED
→ Extracts leave type, dates, days, deductions
→ Creates leave_card entry automatically
→ Links records via leave_card_entry_id
→ Employee's leave card is updated ✅
```

### 4. **Print Official Form**
```
Click Print → CS Form No. 6 formatted page
→ All signatures blocks → Ready for printing
```

### 5. **Delete Application** (Optional)
```
Click Delete → Confirmation modal
→ Deletes application AND linked leave_card entry
```

---

## 🎨 STATUS BADGES

| Status | Badge Color | Meaning |
|--------|-------------|---------|
| Pending | Yellow (warning) | Awaiting approval |
| Approved | Green (success) | Application approved, leave card created |
| Disapproved | Red (danger) | Application rejected |

---

## 🔧 TESTING CHECKLIST

### Basic Functionality
- [ ] Create new leave application (all leave types)
- [ ] Conditional fields show/hide correctly
- [ ] Date validation (to >= from)
- [ ] Leave balance auto-calculation works
- [ ] Edit existing application
- [ ] Delete application with confirmation

### Leave Card Integration
- [ ] Submit application (status = pending)
- [ ] No leave card entry created yet ✅
- [ ] Edit and approve application (status = approved)
- [ ] Leave card entry auto-created ✅
- [ ] Check `leave_applications.leave_card_entry_id` is set
- [ ] Check `leave_card.created_from_application = 1`
- [ ] Verify deductions match application
- [ ] Special leave types marked correctly

### Print Preview
- [ ] Print button opens formatted CS Form No. 6
- [ ] All data displays correctly
- [ ] Checkboxes show selected leave type
- [ ] Signature blocks present
- [ ] Print-friendly (no buttons/navigation)

---

## 🗃️ DATABASE QUERIES

### View All Applications with Personnel Info
```sql
SELECT 
    la.id,
    la.application_date,
    la.leave_type,
    la.status,
    la.number_of_days,
    CONCAT(p.lastname, ', ', p.firstname) as full_name,
    d.designation_name,
    la.leave_card_entry_id
FROM leave_applications la
LEFT JOIN personnels p ON la.personnel_id = p.personnel_id
LEFT JOIN designation d ON p.designation_id = d.designation_id
ORDER BY la.application_date DESC;
```

### Check Auto-Created Leave Card Entries
```sql
SELECT 
    la.id as app_id,
    la.application_date,
    la.leave_type,
    la.status,
    lc.lc_id,
    lc.particulars,
    lc.vl_with_pay,
    lc.sl_with_pay,
    lc.is_special_leave,
    lc.created_from_application
FROM leave_applications la
INNER JOIN leave_card lc ON la.leave_card_entry_id = lc.lc_id
WHERE la.status = 'approved'
ORDER BY la.application_date DESC;
```

### Find Orphaned Applications (Approved but No Leave Card)
```sql
SELECT 
    id,
    application_date,
    leave_type,
    status,
    leave_card_entry_id
FROM leave_applications
WHERE status = 'approved' 
AND leave_card_entry_id IS NULL;
```

---

## 🔐 SECURITY NOTES

- ✅ All forms use POST method
- ✅ Session validation on all pages
- ✅ PDO prepared statements (SQL injection prevention)
- ✅ htmlspecialchars() on output (XSS prevention)
- ✅ Foreign key constraints enforce data integrity
- ✅ AJAX endpoints validate user session

---

## 📝 FUTURE ENHANCEMENTS

### Phase 2 (Recommended)
- [ ] Email notifications on approval/disapproval
- [ ] Multi-level approval workflow
- [ ] Approval history/audit trail
- [ ] Bulk approve feature
- [ ] Export to Excel/PDF
- [ ] Dashboard statistics (pending count, etc.)
- [ ] Calendar view of leave schedules
- [ ] Conflict checking (overlapping leaves)

### Phase 3 (Advanced)
- [ ] Mobile responsive improvements
- [ ] Employee self-service portal
- [ ] Integration with HR dashboard
- [ ] Leave balance real-time sync
- [ ] Automated leave credit calculations
- [ ] Digital signature integration

---

## 📞 SUPPORT

For issues or questions about this system:
1. Check database connection in `dbcon.php`
2. Verify SQL schema was executed successfully
3. Check browser console for JavaScript errors
4. Review PHP error logs in `c:\xampp\apache\logs\error.log`

---

**System Status:** ✅ READY FOR TESTING  
**Next Step:** Run `leave_application_schema.sql` to create database tables
