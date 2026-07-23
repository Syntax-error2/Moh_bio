# Delete Draft Payroll Run Feature - Documentation

## Overview
Added functionality to permanently delete draft payroll runs from the Payroll History page, including all related data.

---

## Features Added

### 1. Delete Button
- **Visibility**: Only appears for payroll runs with `status = 'draft'`
- **Location**: Actions column in the payroll history table
- **Color**: Red (danger) button
- **Icon**: Trash icon (fa-trash)

### 2. Visual Indicators
- **Draft Row Highlighting**: Draft payroll runs have a light yellow background (#fff9e6)
- **Hover Effect**: Background changes to a slightly darker yellow (#fff3cd) on hover
- **Makes it easy to identify which runs can be deleted**

### 3. Confirmation Dialog
Multi-line confirmation message that shows:
```
Are you sure you want to DELETE this draft payroll run?

Run: October 2025 - Regular Monthly Payroll

This will permanently delete:
- The payroll run
- All personnel payroll details
- All income and deduction records

This action CANNOT be undone!
```

### 4. Cascade Deletion
When a payroll run is deleted, the system removes data in this order:

1. **pr_tbl_payroll_run_deductions** - All deduction records
2. **pr_tbl_payroll_run_income** - All income records
3. **pr_tbl_payroll_run_details** - All personnel payroll details
4. **pr_tbl_payroll_audit_log** - Audit trail entries
5. **pr_tbl_payroll_runs** - The main payroll run record

**Transaction Safety**: All deletions happen within a database transaction. If any step fails, all changes are rolled back.

---

## Files Modified/Created

### 1. Modified: `list_payroll_history.php`

#### CSS Added (Lines 53-60):
```css
.draft-row {
    background-color: #fff9e6 !important;
}
.draft-row:hover {
    background-color: #fff3cd !important;
}
```

#### Delete Button Added (Actions Column):
```html
<?php if ($run['run_status'] === 'draft'): ?>
    <button onclick="deletePayrollRun(<?php echo $run['run_id']; ?>, '<?php echo addslashes($run['run_name']); ?>')" 
            class="btn btn-sm btn-danger table-action-btn" 
            title="Delete Draft">
        <i class="fa fa-trash"></i>
    </button>
<?php endif; ?>
```

#### JavaScript Function Added:
```javascript
function deletePayrollRun(runId, runName) {
    // Shows confirmation dialog
    // Calls delete_payroll_run.php via AJAX
    // Shows loading spinner during deletion
    // Reloads page on success
}
```

#### Table Row Highlighting:
```html
<tr class="<?php echo $run['run_status'] === 'draft' ? 'draft-row' : ''; ?>">
```

### 2. Created: `delete_payroll_run.php`

New backend handler that:
- Validates the request (POST method, run_id present)
- Checks if run exists and is in draft status
- Uses database transaction for safety
- Deletes all related data in correct order
- Logs deletion details
- Returns JSON response

**Security Features:**
- Only draft runs can be deleted
- Transaction rollback on any error
- Detailed error logging
- Session authentication required

---

## User Interface

### Before Delete Feature:
```
Actions Column (Draft Run):
[View] [Edit] [Print]
```

### After Delete Feature:
```
Actions Column (Draft Run):
[View] [Edit] [Delete] [Print]
         ↑
      Red trash button
```

### Visual Indicators:

**Draft Row:**
```
┌────────────────────────────────────────────────┐
│ Light yellow background                        │
│ #3  Oct 2025 - Regular  [View][Edit][Del][Print] │
└────────────────────────────────────────────────┘
```

**Completed Run:**
```
┌────────────────────────────────────────────────┐
│ White background (normal)                      │
│ #2  Sep 2025 - Regular  [View][Print]         │
│                         (No Delete button)     │
└────────────────────────────────────────────────┘
```

---

## Database Tables Affected

### Tables Cleaned on Deletion:

1. **pr_tbl_payroll_run_deductions**
   - Columns: run_deduction_id, detail_id, run_id, personnel_id, deduction_id, etc.
   - Foreign Key: run_id → pr_tbl_payroll_runs.run_id

2. **pr_tbl_payroll_run_income**
   - Columns: run_income_id, detail_id, run_id, personnel_id, income_id, etc.
   - Foreign Key: run_id → pr_tbl_payroll_runs.run_id

3. **pr_tbl_payroll_run_details**
   - Columns: detail_id, run_id, personnel_id, gross_pay, deductions, net_pay, etc.
   - Foreign Key: run_id → pr_tbl_payroll_runs.run_id

4. **pr_tbl_payroll_audit_log** (if exists)
   - Columns: log_id, run_id, action_type, performed_by, etc.
   - Foreign Key: run_id → pr_tbl_payroll_runs.run_id

5. **pr_tbl_payroll_runs** (main table)
   - Columns: run_id, profile_id, run_name, run_status, etc.
   - Primary Key: run_id

---

## Workflow

### Delete Process Flow:

1. **User Clicks Delete Button**
   ```
   User clicks red trash button on draft payroll run
   ```

2. **Confirmation Dialog**
   ```
   Browser shows confirmation with run details
   User must click OK to proceed
   ```

3. **AJAX Request**
   ```
   JavaScript sends POST to delete_payroll_run.php
   Button shows spinner: [⟳]
   ```

4. **Backend Validation**
   ```
   ✓ Check if run exists
   ✓ Check if status is 'draft'
   ✓ Start database transaction
   ```

5. **Cascade Delete**
   ```
   Delete in order:
   1. Deductions
   2. Income
   3. Details
   4. Audit logs
   5. Main run record
   ```

6. **Response**
   ```
   Success: Commit transaction → Reload page
   Error: Rollback transaction → Show error message
   ```

---

## Status Restrictions

### ✅ Can Delete:
- **draft** - Payroll run in draft status

### ❌ Cannot Delete:
- **pending** - Submitted for approval
- **approved** - Approved by administrator
- **processing** - Currently being processed
- **completed** - Finished and paid
- **cancelled** - Already cancelled

**Error Message:**
```
Only draft payroll runs can be deleted. 
Current status: [actual status]
```

---

## Security & Safety

### 1. Access Control
- Requires active session (include('session.php'))
- Only authenticated users can delete
- Run ownership not checked (any authenticated user can delete drafts)

### 2. Status Validation
- Backend enforces draft status requirement
- Frontend only shows button for drafts
- Double validation prevents accidental deletion

### 3. Transaction Safety
```php
try {
    $conn->beginTransaction();
    // ... perform deletions ...
    $conn->commit();
} catch (Exception $e) {
    $conn->rollBack(); // Undo all changes if any fail
    throw $e;
}
```

### 4. Audit Trail
- Deletion logged to error_log
- Logs include: run_id, run_name, personnel count, deleted record counts
- Format: `Payroll run deleted: ID=3, Name='Test', Personnel=100, ...`

---

## Testing Checklist

### ✅ Basic Functionality
- [ ] Delete button appears only for draft runs
- [ ] Delete button does NOT appear for other statuses
- [ ] Confirmation dialog shows correct run name
- [ ] Clicking "Cancel" aborts deletion
- [ ] Clicking "OK" proceeds with deletion

### ✅ Visual Feedback
- [ ] Draft rows have yellow background
- [ ] Hover effect works on draft rows
- [ ] Button shows spinner during deletion
- [ ] Page reloads after successful deletion
- [ ] Run disappears from list

### ✅ Data Integrity
- [ ] All related deductions deleted
- [ ] All related income deleted
- [ ] All related details deleted
- [ ] Audit logs deleted
- [ ] Main run record deleted
- [ ] No orphaned records remain

### ✅ Error Handling
- [ ] Cannot delete non-draft runs
- [ ] Error message shown if deletion fails
- [ ] Transaction rolled back on error
- [ ] Button re-enabled after error
- [ ] Error logged to server logs

### ✅ Edge Cases
- [ ] Deleting run with 0 personnel
- [ ] Deleting run with 1000+ personnel
- [ ] Deleting recently created run
- [ ] Multiple quick delete attempts (button disabled)
- [ ] Concurrent deletion attempts

---

## Example Usage Scenarios

### Scenario 1: Delete Accidentally Created Run
```
1. User generates payroll run with wrong profile
2. Realizes mistake immediately
3. Goes to Payroll History
4. Finds draft run (highlighted in yellow)
5. Clicks red delete button
6. Confirms deletion
7. Run and all data removed
8. Can now generate correct run
```

### Scenario 2: Delete Test Run
```
1. Admin tests payroll generation
2. Creates test run with sample data
3. Verifies calculations work correctly
4. No longer needs test data
5. Deletes draft test run
6. Database cleaned up
```

### Scenario 3: Cannot Delete Approved Run
```
1. User tries to delete completed payroll
2. No delete button visible
3. Only View and Print available
4. Approved/Completed runs protected
5. User must use other process to void/cancel
```

---

## Code Snippets

### Delete Button Placement
```html
<!-- Actions column in table -->
<td>
    <!-- View button (always visible) -->
    <a href="view_payroll_run.php?run_id=<?php echo $run['run_id']; ?>">
        <i class="fa fa-eye"></i>
    </a>
    
    <!-- Edit and Delete (draft only) -->
    <?php if ($run['run_status'] === 'draft'): ?>
        <a href="edit_payroll_run.php?run_id=<?php echo $run['run_id']; ?>">
            <i class="fa fa-pencil"></i>
        </a>
        <button onclick="deletePayrollRun(<?php echo $run['run_id']; ?>, ...)">
            <i class="fa fa-trash"></i>
        </button>
    <?php endif; ?>
    
    <!-- Print button (always visible) -->
    <a href="print_payroll_run.php?run_id=<?php echo $run['run_id']; ?>">
        <i class="fa fa-print"></i>
    </a>
</td>
```

### AJAX Delete Call
```javascript
$.ajax({
    url: 'delete_payroll_run.php',
    type: 'POST',
    data: { run_id: runId },
    dataType: 'json',
    success: function(response) {
        if (response.success) {
            alert('Payroll run deleted successfully!');
            location.reload();
        } else {
            alert('Error: ' + response.message);
        }
    }
});
```

### Backend Response
```json
{
  "success": true,
  "message": "Payroll run deleted successfully",
  "details": {
    "run_id": 3,
    "run_name": "October 2025 - Regular Monthly Payroll",
    "deleted_personnel": 666,
    "deleted_income_records": 666,
    "deleted_deduction_records": 3330,
    "deleted_audit_logs": 1
  }
}
```

---

## Troubleshooting

### Issue: Delete button not showing
**Solution:** 
- Verify run status is exactly 'draft' (lowercase)
- Check if you're in view mode, not edit mode
- Clear browser cache

### Issue: "Only draft runs can be deleted" error
**Solution:**
- Run status has changed to pending/approved/completed
- Cannot delete non-draft runs by design
- Cancel or void the run through proper workflow

### Issue: Deletion succeeds but data remains
**Solution:**
- Check foreign key constraints
- Verify transaction committed
- Check error logs for partial deletion
- May need manual database cleanup

### Issue: Page doesn't reload after deletion
**Solution:**
- Check browser console for JavaScript errors
- Verify AJAX success callback
- Try manual refresh (F5)

---

## Future Enhancements

### Possible Improvements:
1. **Soft Delete**: Mark as deleted instead of permanent removal
2. **Delete Confirmation Modal**: Replace alert() with Bootstrap modal
3. **Undo Feature**: Allow restoring recently deleted runs
4. **Bulk Delete**: Select and delete multiple draft runs
5. **Permission Check**: Only allow run creator or admin to delete
6. **Archive Instead**: Move to archived_payroll_runs table

---

## Summary

✅ **Added Features:**
- Delete button for draft payroll runs
- Visual highlighting of draft runs
- Comprehensive confirmation dialog
- Cascade deletion of all related data
- Transaction-safe deletion process
- Loading indicator during deletion
- Error handling and logging

✅ **User Benefits:**
- Clean up test/mistake runs easily
- No orphaned data in database
- Clear visual indication of deletable runs
- Safe deletion with confirmation
- Immediate feedback on success/failure

✅ **Technical Benefits:**
- Transaction safety prevents partial deletions
- Proper cascade order prevents foreign key errors
- Detailed logging for audit purposes
- JSON API for potential future enhancements
- Follows existing code patterns and style

**Result:** Users can now safely delete draft payroll runs, keeping the database clean and organized! 🗑️✨
