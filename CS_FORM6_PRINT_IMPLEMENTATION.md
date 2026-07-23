# CS FORM NO. 6 - PRINT FEATURE IMPLEMENTATION

## Overview
This document describes the implementation of the print functionality for **CS Form No. 6 (Application for Leave)** with signatories management.

## Implementation Date
January 24, 2025

---

## Features Implemented

### 1. Print Leave Application (CS Form No. 6)
- **Print Preview Modal**: Full-featured preview before printing
- **Professional Layout**: Matches the official CS Form No. 6 format
- **Responsive Design**: Optimized for both screen and print
- **Complete Data Display**: All leave application fields included

### 2. Signatories Settings
- **Configuration Modal**: Easy-to-use interface for setting up signatories
- **Three Signatory Levels**:
  - HRMO / Certifying Officer
  - Recommending Officer (Immediate Supervisor)
  - Approving Officer (Head of Agency)
- **Persistent Storage**: Settings saved in database
- **Default Values**: Pre-populated with common positions

### 3. Print Layout Features
- Standard 8.5" x 11" paper size
- Professional formatting matching official CS Form No. 6
- Checkboxes for leave types
- Signature lines with names and positions
- Complete leave credits certification section
- With Pay / Without Pay distinction

---

## Files Created

### Frontend Files

#### 1. **print_leave_application_csform6.php**
- Print preview modal
- CS Form No. 6 layout rendering
- Print functionality
- Styling for print media

**Key Functions:**
- `openPrintLeaveModal(leaveAppId)` - Opens print modal
- `loadLeaveApplicationForPrint(leaveAppId)` - Fetches data via AJAX
- `renderCSForm6(data, signatories)` - Generates form HTML
- `printLeaveApplication()` - Triggers browser print
- `openSignatoriesSettings()` - Opens settings modal

#### 2. **signatories_settings_modal.php**
- Signatories configuration interface
- Three sections for different signatory roles
- Save/load functionality
- User-friendly form design

**Key Functions:**
- `loadSignatoriesSettings()` - Loads saved settings
- `saveSignatoriesSettings()` - Saves settings via AJAX
- `showNotification(type, message)` - User feedback

### Backend Files

#### 3. **get_leave_application_print_data.php**
- Fetches leave application data
- Joins personnel information
- Returns JSON response
- Security: Session validation

**API Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "personnel_id": 123,
    "leave_type": "Vacation Leave",
    "fname": "Juan",
    "lname": "Dela Cruz",
    ...
  },
  "signatories": {
    "hrmo_name": "MARIA SANTOS",
    "hrmo_position": "HRMO III",
    ...
  }
}
```

#### 4. **get_signatories_settings.php**
- Fetches signatories configuration
- Returns default values if not set
- Security: Session validation

#### 5. **save_signatories_settings.php**
- Saves/updates signatories settings
- Auto-creates table if not exists
- Handles both INSERT and UPDATE
- Security: Session validation

### Database Files

#### 6. **signatories_settings_schema.sql**
- SQL migration script
- Creates `signatories_settings` table
- Includes default values

**Table Structure:**
```sql
signatories_settings
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- hrmo_name (VARCHAR 255)
- hrmo_position (VARCHAR 255)
- recommending_name (VARCHAR 255)
- recommending_position (VARCHAR 255)
- approving_name (VARCHAR 255)
- approving_position (VARCHAR 255)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

---

## Installation Instructions

### Step 1: Database Setup

Run the SQL migration script in phpMyAdmin:

```bash
# Navigate to phpMyAdmin
# Select your database (usually 'hrms' or 'moh_hrms')
# Go to SQL tab
# Copy and paste contents of signatories_settings_schema.sql
# Click "Go" to execute
```

**OR** The table will be auto-created when you first save settings (handled by `save_signatories_settings.php`).

### Step 2: File Integration

Files are already integrated into:
- `leave_application.php` - Individual personnel leave applications list
- `leave_card.php` - Leave card with application button

**Includes added:**
```php
<?php include('print_leave_application_csform6.php'); ?>
<?php include('signatories_settings_modal.php'); ?>
```

### Step 3: Configure Signatories

1. Navigate to any leave application page
2. Click the **Print** button next to any leave application
3. In the print preview modal, click **Signatories Settings**
4. Enter the names and positions for:
   - HRMO / Certifying Officer
   - Recommending Officer
   - Approving Officer
5. Click **Save Settings**

### Step 4: Test Printing

1. Open any leave application
2. Click **Print CS Form No. 6**
3. Review the preview
4. Click **Print** button
5. Use browser's print dialog to print or save as PDF

---

## Usage Guide

### For HR Personnel

**Printing a Leave Application:**
1. Go to Leave Application list or Leave Card page
2. Find the leave application to print
3. Click the **ellipsis (⋮)** button
4. Select **Print CS Form No. 6**
5. Review the preview
6. Click **Print** to print

**Setting Up Signatories:**
1. Click **Print** on any leave application
2. In the modal, click **Signatories Settings**
3. Fill in the names and positions
4. Click **Save Settings**
5. Settings will be used for all future printouts

### For Administrators

**Updating Signatories:**
- Settings can be updated anytime
- Changes apply to all new printouts
- Previous printouts remain unchanged
- Only one set of signatories per system

**Database Management:**
```sql
-- View current signatories
SELECT * FROM signatories_settings;

-- Update manually if needed
UPDATE signatories_settings 
SET hrmo_name = 'NEW NAME',
    hrmo_position = 'NEW POSITION'
WHERE id = 1;
```

---

## CS Form No. 6 Layout Sections

### Header
- Form title and subtitle
- Office/Agency name
- Personnel name (Last, First, Middle)
- Date of filing
- Position and salary

### Section 6: Details of Application
- **6.A** Type of leave (checkboxes for all leave types)
- **6.B** Where leave will be spent
- **6.C** Number of working days
- **6.D** Inclusive dates

### Section 7: Details of Leave
- Vacation/Special Privilege Leave details
- Sick Leave details
- Special Leave Benefits for Women
- Study Leave details
- Other purposes

### Section 8: Certification of Leave Credits
- As of date
- Total Earned (VL/SL)
- Less This Application (With Pay / Without Pay)
- Balance (VL/SL)
- Signature of Applicant
- Signature of HRMO

### Section 9: Recommendation
- For approval / For disapproval
- Signature of Recommending Officer

### Section 10: Approved For
- Days with pay / without pay
- Disapproval reason
- Signature of Approving Officer

---

## Print Styling

### Print Media Queries
```css
@media print {
  - Hides modal headers/footers
  - Optimizes layout for 8.5" x 11" paper
  - Sets proper margins (0.5 inch)
  - Ensures only form content is visible
}
```

### Typography
- **Font Family**: Arial, sans-serif
- **Base Size**: 11px
- **Line Height**: 1.3
- **Labels**: 9px
- **Values**: 11px bold
- **Titles**: 14px bold

### Checkboxes
- Custom checkboxes with ✓ mark when checked
- 12px x 12px size
- Border: 1px solid black

---

## Troubleshooting

### Issue: Signatories not showing
**Solution:**
1. Check if table exists: `SHOW TABLES LIKE 'signatories_settings'`
2. Run migration SQL if table missing
3. Check PHP error logs
4. Verify `get_signatories_settings.php` returns data

### Issue: Print preview not loading
**Solution:**
1. Check browser console for JavaScript errors
2. Verify `get_leave_application_print_data.php` returns JSON
3. Check if leave application ID is valid
4. Verify database connection

### Issue: Data not displaying correctly
**Solution:**
1. Check if all form fields are populated in `leave_applications` table
2. Verify `renderCSForm6()` JavaScript function
3. Check for NULL values in database
4. Ensure `formatDate()` function works correctly

### Issue: Print button not working
**Solution:**
1. Check if jQuery is loaded
2. Verify modal IDs are correct
3. Check browser's print functionality
4. Try different browser

---

## API Endpoints

### GET /get_signatories_settings.php
**Description:** Retrieves current signatories configuration

**Response:**
```json
{
  "success": true,
  "data": {
    "hrmo_name": "MARIA SANTOS",
    "hrmo_position": "HRMO III",
    "recommending_name": "DR. JUAN CRUZ",
    "recommending_position": "Chief, Medical Division",
    "approving_name": "DR. ADRIANO SUBA-AN",
    "approving_position": "Regional Director"
  }
}
```

### POST /save_signatories_settings.php
**Description:** Saves signatories configuration

**Request:**
```
hrmo_name: "MARIA SANTOS"
hrmo_position: "HRMO III"
recommending_name: "DR. JUAN CRUZ"
recommending_position: "Chief, Medical Division"
approving_name: "DR. ADRIANO SUBA-AN"
approving_position: "Regional Director"
```

**Response:**
```json
{
  "success": true,
  "message": "Signatories settings saved successfully"
}
```

### POST /get_leave_application_print_data.php
**Description:** Fetches leave application with personnel details

**Request:**
```
leave_application_id: 123
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 123,
    "personnel_id": 456,
    "office_agency": "DOH-Region X",
    "leave_type": "Vacation Leave",
    "number_of_days": 5,
    "inclusive_date_from": "2025-01-20",
    "inclusive_date_to": "2025-01-24",
    ...
  },
  "signatories": {
    ...
  }
}
```

---

## Security Features

1. **Session Validation**: All PHP endpoints check for active session
2. **SQL Injection Prevention**: All queries use PDO prepared statements
3. **XSS Protection**: All output escaped with `htmlspecialchars()`
4. **Error Logging**: Database errors logged to PHP error log
5. **Access Control**: Only logged-in users can access

---

## Future Enhancements

### Planned Features
- [ ] Multiple signatory sets (per department)
- [ ] E-signature integration
- [ ] QR code for verification
- [ ] Batch printing multiple applications
- [ ] Export to PDF server-side
- [ ] Email leave application as PDF
- [ ] Digital signature capture
- [ ] Approval workflow tracking on form

### Performance Optimizations
- [ ] Cache signatories settings
- [ ] Lazy load print preview
- [ ] Optimize print CSS
- [ ] Reduce AJAX calls

---

## Browser Compatibility

**Tested and Working:**
- ✅ Google Chrome 120+
- ✅ Microsoft Edge 120+
- ✅ Mozilla Firefox 120+
- ✅ Safari 17+

**Print Features:**
- ✅ Print to printer
- ✅ Save as PDF
- ✅ Print preview
- ✅ Page breaks

---

## Credits

**Based on:** Official CS Form No. 6 (Revised 2020) by Civil Service Commission

**Implementation:** MOH HRMS Development Team

**Date:** January 24, 2025

---

## Support

For issues or questions:
1. Check this documentation
2. Review troubleshooting section
3. Check PHP error logs
4. Check browser console
5. Contact system administrator

---

**Document Version:** 1.0  
**Last Updated:** January 24, 2025
