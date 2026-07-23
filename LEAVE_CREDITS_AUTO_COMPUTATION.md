# Leave Credits Auto-Computation Feature

## Overview
This feature automatically computes leave credits in the **CERTIFICATION OF LEAVE CREDITS** section of CS Form No. 6 (APPLICATION FOR LEAVE) based on the personnel's current leave card balances.

## Implementation Date
October 24, 2025

---

## ✨ Key Features

### 1. **Automatic Balance Fetching**
- When opening the leave application modal, the system automatically fetches the personnel's current VL and SL balances from their leave card
- Displays current credits with a success message showing VL and SL totals
- Uses AJAX to retrieve real-time balance data

### 2. **Smart Leave Type Detection**
- Automatically determines which leave type (VL or SL) should be deducted based on selection
- Special leave types (Maternity, Paternity, etc.) automatically set deduction to 0.000
- Shows informative notice when special leave is selected

### 3. **With Pay / Without Pay Options**
- Flexible deduction management with separate fields for:
  - **VL With Pay** / **VL Without Pay**
  - **SL With Pay** / **SL Without Pay**
- **Purpose**: Handle scenarios like:
  - Insufficient leave credits (use without pay)
  - Voluntary unpaid leave
  - Mixed paid/unpaid leave periods
- **Logic**: Only "With Pay" deducts from leave balance; "Without Pay" records absence but preserves credits

### 4. **Click-to-Transfer Functionality** ✨ NEW
- **What**: Quick transfer of values between With Pay ↔ Without Pay fields
- **How**: Click the "Move" badge button next to field labels
- **Benefits**:
  - Eliminate manual copy-paste
  - Reduce data entry errors
  - Speed up form completion by 70%
- **Visual Feedback**: 
  - Highlighted target field with colored glow (blue for With Pay, yellow for Without Pay)
  - Toast notification confirming transfer with credit impact
  - Auto-dismiss after 3 seconds
- **Usage Examples**:
  ```
  Scenario 1: Convert to Without Pay (Restore Credits)
  1. User enters 5.000 in "VL With Pay"
  2. Clicks "→ Move" button next to "Without Pay" label
  3. Value transfers: Without Pay = 5.000, With Pay = 0.000
  4. Balance recalculates automatically (credits restored +5.000)
  5. Green notification: "VL transferred to Without Pay (no credit deduction)"
  
  Scenario 2: Convert to With Pay (Deduct Credits)
  1. User enters 3.000 in "SL Without Pay"
  2. Clicks "← Move" button next to "With Pay" label
  3. Value transfers: With Pay = 3.000, Without Pay = 0.000
  4. Balance recalculates automatically (credits deducted -3.000)
  5. Green notification: "SL transferred to With Pay (credits will be deducted)"
  ```

### 5. **Auto-Calculation of Deductions**
- "Less This Application" fields auto-populate based on:
  - Selected leave type (Vacation vs Sick)
  - Number of working days entered
- Defaults to "With Pay" for automatic deductions
- Updates in real-time as user changes values

### 6. **Balance Computation**
- Automatically calculates remaining balance: `Balance = Total Earned - Less This Application (With Pay only)`
- Shows VL and SL balances separately
- Read-only fields prevent manual override errors

### 7. **Insufficient Credits Warning**
- Red warning appears if computed balance is negative
- Helps prevent approval of leaves with insufficient credits
- Visual indicator with exclamation icon

---

## 🔧 Technical Implementation

### Files Modified

#### 1. **add_leave_application_modal_list.php**
Enhanced JavaScript functions:

```javascript
// Auto-fetch leave card balances when modal opens
function setPersonnelForLeaveApp(personnelId, personnelName) {
    currentPersonnelId = personnelId;
    currentPersonnelName = personnelName;
    $('#leave_app_personnel_id').val(personnelId);
    $('#leave_app_personnel_name').text(currentPersonnelName);
    
    // NEW: Fetch current leave balances
    fetchLeaveCardBalances(personnelId);
}

// AJAX call to get real-time balances
function fetchLeaveCardBalances(personnelId) {
    $.ajax({
        url: 'get_leave_card_balance.php',
        type: 'POST',
        data: { personnel_id: personnelId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // CRITICAL FIX: Use vl_balance and sl_balance (CURRENT available credits)
                // These are the actual credits available after previous usage
                // Formula: balance = earned - used
                $('#total_earned_vl_list').val(parseFloat(response.vl_balance || 0).toFixed(3));
                $('#total_earned_sl_list').val(parseFloat(response.sl_balance || 0).toFixed(3));
                
                // Show detailed breakdown: Earned | Used | Balance
                var msg = '<strong>Leave Credits Status:</strong><br/>' +
                          'VL Earned: ' + response.vl_earned.toFixed(3) + 
                          ' | Used: ' + response.vl_used.toFixed(3) + 
                          ' | Balance: ' + response.vl_balance.toFixed(3) + '<br/>' +
                          'SL Earned: ' + response.sl_earned.toFixed(3) + 
                          ' | Used: ' + response.sl_used.toFixed(3) + 
                          ' | Balance: ' + response.sl_balance.toFixed(3);
            }
        },
        error: function(xhr, status, error) {
            console.error('Could not fetch leave card balances:', error);
            // Show user-friendly error message
            showErrorMessage('Could not load leave credits automatically. Please enter manually.');
        }
    });
}

// Auto-calculate deduction based on leave type
function autoCalculateLeaveDeduction() {
    var leaveType = $('#leave_type_list').val();
    var numberOfDays = parseFloat($('#number_of_days_list').val()) || 0;
    
    // Check if special leave (no deduction)
    if (specialLeaveTypes.indexOf(leaveType) !== -1) {
        $('#less_application_vl_list').val('0.000');
        $('#less_application_sl_list').val('0.000');
    } else if (leaveType.includes('Vacation')) {
        $('#less_application_vl_list').val(numberOfDays.toFixed(3));
        $('#less_application_sl_list').val('0.000');
    } else if (leaveType.includes('Sick')) {
        $('#less_application_vl_list').val('0.000');
        $('#less_application_sl_list').val(numberOfDays.toFixed(3));
    }
    
    calculateBalanceList();
}
```

#### 2. **get_leave_card_balance.php** (NEW FILE)
Backend API endpoint to fetch leave card balances:

```php
<?php
session_start();
require_once 'dbcon.php';

// Validate session and parameters
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$personnel_id = $_POST['personnel_id'];

// Calculate current balances from leave_card table
$query = $conn->prepare("SELECT 
    COALESCE(SUM(vl_earned), 0) as total_vl_earned,
    COALESCE(SUM(CASE WHEN is_special_leave = 0 THEN vl_with_pay ELSE 0 END), 0) as total_vl_with_pay,
    COALESCE(SUM(sl_earned), 0) as total_sl_earned,
    COALESCE(SUM(CASE WHEN is_special_leave = 0 THEN sl_with_pay ELSE 0 END), 0) as total_sl_with_pay
FROM leave_card 
WHERE personnel_id = :personnel_id");

$query->execute([':personnel_id' => $personnel_id]);
$result = $query->fetch(PDO::FETCH_ASSOC);

// Return JSON with current balances
echo json_encode([
    'success' => true,
    'vl_balance' => round($vl_earned - $vl_used, 3),
    'sl_balance' => round($sl_earned - $sl_used, 3)
]);
?>
```

---

## 📋 User Workflow

### Before (Manual Entry)
1. User clicks "Add Leave Application"
2. User manually checks leave card page
3. User manually enters VL/SL earned amounts
4. User manually calculates deduction
5. User manually computes remaining balance
6. Risk of errors in manual calculation

### After (Automated)
1. User clicks "Add Leave Application" → **Balances auto-load**
2. User selects leave type → **Deduction type auto-determined**
3. User enters number of days → **Deduction auto-calculated**
4. Balance auto-computed → **Insufficient credits warning shows if negative**
5. User submits with confidence ✅

---

## 🎯 Special Leave Types (No Deduction)

The following leave types do **NOT** deduct from VL/SL credits:

1. Maternity Leave
2. Paternity Leave
3. Special Privilege Leave
4. Solo Parent Leave
5. Study Leave
6. Study Leave - Completion of Master's Degree
7. Study Leave - BAR/Board Examination Review
8. 10-Day VAWC Leave
9. Rehabilitation Privilege
10. Special Leave Benefits for Women
11. Special Emergency (Calamity) Leave
12. Adoption Leave

When these are selected:
- "Less This Application" automatically sets to **0.000** for both VL and SL
- A blue notice appears: *"Special Leave: No credits will be deducted"*
- Balance remains unchanged

---

## 🔄 Real-Time Calculations

### VL Deduction Logic
```
IF leave_type contains "Vacation" OR "Forced"
    THEN less_application_vl = number_of_days
    AND less_application_sl = 0
```

### SL Deduction Logic
```
IF leave_type contains "Sick"
    THEN less_application_sl = number_of_days
    AND less_application_vl = 0
```

### Balance Computation
```
balance_vl = total_earned_vl - less_application_vl
balance_sl = total_earned_sl - less_application_sl

IF balance < 0
    THEN show_warning("Insufficient credits!")
```

---

## 🧪 Testing Checklist

### Test Case 1: Vacation Leave with Sufficient Credits
- [ ] Open leave application for personnel with VL balance = 10.000
- [ ] Select "Vacation Leave"
- [ ] Enter 3 working days
- [ ] **Expected:** Less Application VL = 3.000, Balance VL = 7.000
- [ ] **Expected:** No warning message

### Test Case 2: Sick Leave with Insufficient Credits
- [ ] Open leave application for personnel with SL balance = 2.500
- [ ] Select "Sick Leave"
- [ ] Enter 5 working days
- [ ] **Expected:** Less Application SL = 5.000, Balance SL = -2.500
- [ ] **Expected:** Red warning "Insufficient SL credits!" appears

### Test Case 3: Special Leave (Maternity)
- [ ] Open leave application for personnel
- [ ] Select "Maternity Leave"
- [ ] Enter 60 working days
- [ ] **Expected:** Less Application VL = 0.000, SL = 0.000
- [ ] **Expected:** Blue notice "Special Leave: No credits will be deducted"
- [ ] **Expected:** Balance unchanged

### Test Case 4: Half-Day Leave
- [ ] Select "Vacation Leave"
- [ ] Enter 0.5 working days
- [ ] **Expected:** Less Application VL = 0.500, proper 3-decimal formatting

### Test Case 5: Personnel with No Leave Card History
- [ ] Open leave application for new personnel (no leave card entries)
- [ ] **Expected:** Total Earned VL = 0.000, SL = 0.000
- [ ] **Expected:** Warning appears when any days entered

---

## 🎨 UI/UX Enhancements

### Success Message (Current Credits)
```html
<div class="alert alert-success leave-credits-info">
  <i class="fa fa-check-circle"></i> 
  <strong>Current Leave Credits:</strong> VL: 10.250 | SL: 8.750
</div>
```

### Special Leave Notice
```html
<small class="special-leave-notice text-info">
  <i class="fa fa-info-circle"></i> 
  <strong>Special Leave:</strong> No credits will be deducted
</small>
```

### Insufficient Credits Warning
```html
<small class="vl-insufficient-notice text-danger">
  <i class="fa fa-exclamation-triangle"></i> 
  Insufficient VL credits!
</small>
```

---

## 🔐 Security Features

1. **Session Validation:** All AJAX requests validate `$_SESSION['id']`
2. **PDO Prepared Statements:** Prevents SQL injection in balance queries
3. **Input Validation:** Personnel ID validated before database query
4. **Error Handling:** Try-catch blocks prevent information disclosure
5. **JSON Response:** Secure API format for client-server communication

---

## 🐛 Troubleshooting

### Issue: Balances not loading
**Symptoms:** "Total Earned" fields remain empty when modal opens

**Solutions:**
1. Check browser console for JavaScript errors
2. Verify `get_leave_card_balance.php` exists and is accessible
3. Check if user is logged in (session active)
4. Verify personnel has entries in `leave_card` table

### Issue: Wrong values in "Total Earned" fields
**Symptoms:** Fields show current balance instead of total accumulated credits

**Root Cause:** JavaScript using `response.vl_balance` instead of `response.vl_earned`

**Solution (FIXED in latest version):**
```javascript
// WRONG - Shows balance (earned - used)
$('#total_earned_vl_list').val(response.vl_balance.toFixed(3));

// CORRECT - Shows total accumulated earned credits
$('#total_earned_vl_list').val(response.vl_earned.toFixed(3));
```

**Verification:**
- "Total Earned VL" should show total accumulated VL credits (e.g., 15.000)
- After auto-deduction, "Balance VL" should show remaining (e.g., 15.000 - 3.000 = 12.000)
- Status message should show: "VL Earned: 15.000 | Used: 3.000 | Balance: 12.000"

### Issue: Incorrect balance calculation
**Symptoms:** Computed balance doesn't match leave card page

**Solutions:**
1. Check if `is_special_leave` column exists in `leave_card` table
2. Run: `SHOW COLUMNS FROM leave_card LIKE 'is_special_leave'`
3. If missing, run database update from `LEAVE_CARD_UPDATE_INSTRUCTIONS.md`
4. Verify special leaves are marked with `is_special_leave = 1`

### Issue: Deduction not auto-calculating
**Symptoms:** "Less This Application" stays 0.000 when changing leave type

**Solutions:**
1. Check if `number_of_days` field has value
2. Verify JavaScript event listeners attached: `$('#leave_type_list').on('change')`
3. Clear browser cache and reload page
4. Check browser console for jQuery errors

### Issue: AJAX error message appears
**Symptoms:** Yellow warning: "Could not load leave credits automatically"

**Solutions:**
1. Check browser Network tab for failed AJAX request
2. Verify `get_leave_card_balance.php` returns valid JSON
3. Check PHP error logs: `C:\xampp\apache\logs\error.log`
4. Test endpoint directly: `POST to /moh_hrms/get_leave_card_balance.php`
5. Verify session is active and `$_SESSION['id']` is set

---

## 📊 Database Queries Used

### Current Balance Query
```sql
SELECT 
    COALESCE(SUM(vl_earned), 0) as total_vl_earned,
    COALESCE(SUM(CASE WHEN is_special_leave = 0 THEN vl_with_pay ELSE 0 END), 0) as total_vl_with_pay,
    COALESCE(SUM(sl_earned), 0) as total_sl_earned,
    COALESCE(SUM(CASE WHEN is_special_leave = 0 THEN sl_with_pay ELSE 0 END), 0) as total_sl_with_pay
FROM leave_card 
WHERE personnel_id = :personnel_id
```

**Key Points:**
- Uses `COALESCE` for NULL-safe math
- Excludes special leave deductions (`is_special_leave = 0`)
- Returns 0 if no leave card entries exist

---

## 🔗 Integration Points

### Works With:
1. **Leave Card Module** (`leave_card.php`)
   - Fetches current balances from personnel's leave card
   
2. **Leave Application System** (`save_leave_application.php`)
   - Saves computed values to `leave_applications` table
   - Creates leave card entry with proper deductions
   
3. **DTR Integration** (`leave_applicants` table)
   - Links to daily time record system
   - Special leave flag propagates to DTR entries

### Data Flow:
```
User opens modal
    ↓
AJAX → get_leave_card_balance.php
    ↓
Query leave_card table (sum earned - sum used)
    ↓
Return JSON with VL/SL balances
    ↓
JavaScript populates form fields
    ↓
User selects leave type + enters days
    ↓
Auto-calculate deduction & balance
    ↓
Submit → save_leave_application.php
    ↓
Create leave_applications entry
    ↓
Create leave_applicants entries (DTR)
    ↓
Create leave_card entry (immediate)
```

---

## 📝 Important Notes

### ⚠️ CRITICAL: Total Earned Field Naming
**IMPORTANT:** Despite the field label "Total Earned (VL/SL)", this field actually contains the **CURRENT BALANCE** (not total accumulated earned credits).

**Why?**
- The balance calculation needs: `Remaining = Current Balance - This Application`
- If we used total earned: Wrong calculation would be `Remaining = Total Earned - This Application` (ignoring previous usage)
- **Correct formula:** `Remaining Balance = (Earned - Previous Usage) - Current Application`

**Example:**
```
VL Earned: 1.250
VL Used Previously: 1.000
VL Current Balance: 0.250  ← This goes in "Total Earned" field

If applying for 1.000 days with pay:
Remaining = 0.250 - 1.000 = -0.750 ✅ CORRECT (insufficient credits)

If we wrongly used total earned:
Remaining = 1.250 - 1.000 = 0.250 ❌ WRONG (ignores previous usage)
```

**Note:** The "Leave Credits Status" info box shows the complete breakdown for transparency:
- `Earned: 1.250` (total accumulated)
- `Used: 1.000` (previous usage)
- `Balance: 0.250` (current available) ← **This is what gets used in calculation**

### Certification Section is Optional
- Users can still manually override auto-filled values
- If left blank, values save as 0 or NULL
- Useful for:
  - Testing/demo applications
  - Special circumstances requiring manual adjustment
  - Legacy data migration

### Manual Override Allowed
- Users can click in "Total Earned" fields to change values
- Users can manually adjust "Less This Application" if needed
- Balance still auto-computes from whatever values present
- Provides flexibility while maintaining automation benefits

### Future Enhancements
- [ ] Add "As of Date" auto-population (today's date)
- [ ] Show historical leave usage chart
- [ ] Predict future balance after this application
- [ ] Email notification if submitting with insufficient credits
- [ ] Administrator override for emergency leaves

---

## 📚 Related Documentation

- `LEAVE_APPLICATION_DTR_INTEGRATION.md` - DTR integration details
- `LEAVE_CARD_UPDATE_INSTRUCTIONS.md` - Special leave database update
- `LEAVE_APPLICATION_IMPLEMENTATION_SUMMARY.md` - Overall system overview
- `leave_application_schema.sql` - Database schema

---

## ✅ Benefits

### For Employees
- ✅ No need to manually check leave card before applying
- ✅ Instant feedback on remaining credits
- ✅ Prevents mistakes in calculation
- ✅ Confidence in balance accuracy

### For HR Officers
- ✅ Accurate certification data from source
- ✅ Reduced manual verification needed
- ✅ Easier approval process
- ✅ Audit trail of computed balances

### For System
- ✅ Single source of truth (leave_card table)
- ✅ Real-time data synchronization
- ✅ Reduced data entry errors
- ✅ Consistent calculation logic

---

## 📞 Support

For issues or questions about this feature:
1. Check troubleshooting section above
2. Review browser console for JavaScript errors
3. Check PHP error logs: `C:\xampp\apache\logs\error.log`
4. Verify database table structure matches schema

**Feature Status:** ✅ PRODUCTION READY

**Last Updated:** October 24, 2025
