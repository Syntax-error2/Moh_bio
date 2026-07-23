# CS FORM NO. 6 PRINT FEATURE - QUICK SETUP GUIDE

## 🚀 Quick Start (3 Steps)

### Step 1: Run Database Migration
```sql
-- Open phpMyAdmin → Select your database → SQL tab
-- Copy and paste from: signatories_settings_schema.sql
-- Click "Go"
```

**OR** Skip this step - table will auto-create on first save!

### Step 2: Configure Signatories
1. Go to Leave Application or Leave Card page
2. Click **Print** button on any leave application
3. Click **Signatories Settings** button
4. Fill in names and positions:
   - **HRMO**: e.g., "MARIA CLARA D. SANTOS", "Human Resource Management Officer III"
   - **Recommending**: e.g., "DR. JUAN DELA CRUZ", "Chief, Medical Division"
   - **Approving**: e.g., "DR. ADRIANO G. SUBA-AN", "Regional Director"
5. Click **Save Settings**

### Step 3: Test Print
1. Click **Print** on any leave application
2. Review the preview
3. Click **Print** button
4. Choose printer or Save as PDF

---

## ✅ What Was Installed

### New Files Created (7 files):
1. ✅ `print_leave_application_csform6.php` - Print modal & preview
2. ✅ `signatories_settings_modal.php` - Settings interface
3. ✅ `get_leave_application_print_data.php` - Fetch leave data API
4. ✅ `get_signatories_settings.php` - Fetch settings API
5. ✅ `save_signatories_settings.php` - Save settings API
6. ✅ `signatories_settings_schema.sql` - Database migration
7. ✅ `CS_FORM6_PRINT_IMPLEMENTATION.md` - Full documentation

### Modified Files (2 files):
1. ✅ `leave_application.php` - Added print button & includes
2. ✅ `leave_card.php` - Added includes for modals

### Database:
1. ✅ New table: `signatories_settings` (auto-created or manual)

---

## 📋 Features Available

### Print Features:
- ✅ Professional CS Form No. 6 layout
- ✅ Print preview before printing
- ✅ Print to printer or Save as PDF
- ✅ All leave application data included
- ✅ With Pay / Without Pay distinction
- ✅ Leave credits certification section
- ✅ Signature lines with names & positions

### Signatories Management:
- ✅ Easy configuration interface
- ✅ Three signatory levels (HRMO, Recommending, Approving)
- ✅ Saved in database (persistent)
- ✅ Default positions pre-filled
- ✅ One-time setup, used for all prints

---

## 🎯 Where to Find It

### Leave Application Page (`leave_application.php`):
```
1. Navigate to personnel's Leave Application page
2. Look for the ellipsis menu (⋮) in each row
3. Click → Select "Print CS Form No. 6"
```

### Leave Card Page (`leave_card.php`):
```
1. Click "Leave Application" button
2. Submit a leave application
3. Then access print via Leave Application page
```

---

## 🔧 Troubleshooting

### Problem: "Signatories Settings" button not appearing
**Solution:** 
```
1. Clear browser cache (Ctrl+Shift+Delete)
2. Refresh page (Ctrl+F5)
3. Check if modals are included in page source
```

### Problem: Print preview shows "Loading..."
**Solution:**
```
1. Check if leave application ID exists
2. Open browser console (F12) - check for errors
3. Verify get_leave_application_print_data.php file exists
```

### Problem: Settings not saving
**Solution:**
```
1. Check PHP error log
2. Verify database connection
3. Run SQL migration manually
4. Check if user is logged in (session active)
```

### Problem: Print layout looks wrong
**Solution:**
```
1. Use Chrome or Edge browser (best compatibility)
2. Check print preview before printing
3. Ensure @media print CSS is not blocked
4. Try Save as PDF instead of direct print
```

---

## 📱 Browser Compatibility

| Browser | Status | Notes |
|---------|--------|-------|
| Chrome 120+ | ✅ Excellent | Recommended |
| Edge 120+ | ✅ Excellent | Recommended |
| Firefox 120+ | ✅ Good | Works well |
| Safari 17+ | ✅ Good | Works well |

---

## 📞 Need Help?

**Check these in order:**
1. ✅ This Quick Setup Guide
2. ✅ Full Documentation: `CS_FORM6_PRINT_IMPLEMENTATION.md`
3. ✅ PHP Error Logs: `xampp/apache/logs/error.log`
4. ✅ Browser Console: Press F12 → Console tab
5. ✅ Contact System Administrator

---

## 🎉 You're All Set!

**Next Steps:**
1. ✅ Configure your signatories (one-time setup)
2. ✅ Test print a leave application
3. ✅ Train HR staff on how to use
4. ✅ Enjoy automated CS Form No. 6 generation!

---

**Quick Tip:** Set up signatories ONCE and they'll be used for ALL future printouts!

**Document Version:** 1.0  
**Setup Time:** ~5 minutes  
**Difficulty:** ⭐ Easy
