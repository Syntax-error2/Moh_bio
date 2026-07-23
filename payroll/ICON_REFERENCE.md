# Icon Reference - Font Awesome 4.7.0 Compatible

**Date:** October 20, 2025  
**System:** MOH HRMS Payroll Module  
**Icon Libraries:** Font Awesome 4.7.0 + Fontastic Custom Icons

---

## 📚 Available Icon Libraries

### 1. **Font Awesome 4.7.0** (Primary)
- **Path:** `../vendor/font-awesome/css/font-awesome.min.css`
- **Usage:** `<i class="fa fa-icon-name"></i>`
- **Documentation:** https://fontawesome.com/v4.7.0/icons/

### 2. **Fontastic Custom Icons** (Secondary)
- **Path:** `../css/fontastic.css`
- **Usage:** `<i class="icon-icon-name"></i>`
- **Limited Set:** Custom dashboard icons

---

## ✅ Updated Menu Icons (Compatible with FA 4.7.0)

### Menu Structure with Icons

```
📂 Home
   icon-home (Fontastic)

👥 Personnels
   icon-user (Fontastic)
   
📄 Payroll Templates
   icon-bill (Fontastic) ← Changed from fa-file-invoice-dollar
   ├─ All Templates
   │  fa fa-folder-open ← Changed from fa-folder
   ├─ Regular Payroll
   │  fa fa-calendar (Same, exists in FA 4.7.0)
   ├─ 13th Month
   │  fa fa-gift (Same, exists in FA 4.7.0)
   ├─ Bonus
   │  fa fa-star (Same, exists in FA 4.7.0)
   └─ Special Payroll
      fa fa-certificate (Same, exists in FA 4.7.0)

📜 Payroll History
   icon-clock (Fontastic) ← Changed from fa-history
   ├─ All Payroll Runs
   │  fa fa-list (Same, exists in FA 4.7.0)
   ├─ Draft Runs
   │  fa fa-pencil ← Changed from fa-edit
   ├─ Pending Approval
   │  fa fa-clock-o ← Changed from fa-clock
   └─ Completed Runs
      fa fa-check-circle (Same, exists in FA 4.7.0)

💰 Income & Deductions
   fa fa-money ← Changed from fa-coins
   ├─ Personnel Income
   │  fa fa-plus-circle ← Changed from fa-money-bill-wave
   ├─ Personnel Deductions
   │  fa fa-minus-circle (Same, exists in FA 4.7.0)
   ├─ Income Reference
   │  fa fa-list-alt (Same, exists in FA 4.7.0)
   └─ Deduction Reference
      fa fa-list-alt (Same, exists in FA 4.7.0)

📅 Old Payroll Profile
   fa fa-calendar-o ← Changed from fa-calendar

📊 Reports
   icon-page (Fontastic)
```

---

## 📋 Icon Changes Summary

| Old Icon (Non-existent in FA 4.7.0) | New Icon (FA 4.7.0 Compatible) | Usage |
|--------------------------------------|--------------------------------|-------|
| `fa fa-file-invoice-dollar` | `icon-bill` | Payroll Templates (main) |
| `fa fa-folder` | `fa fa-folder-open` | All Templates |
| `fa fa-calendar-check` | `fa fa-calendar` | Regular Payroll |
| `fa fa-history` | `icon-clock` | Payroll History (main) |
| `fa fa-edit` | `fa fa-pencil` | Draft Runs |
| `fa fa-clock` | `fa fa-clock-o` | Pending Approval |
| `fa fa-coins` | `fa fa-money` | Income & Deductions (main) |
| `fa fa-money-bill-wave` | `fa fa-plus-circle` | Personnel Income |
| `fa fa-calendar` | `fa fa-calendar-o` | Old Payroll Profile |

**Icons Kept (Already in FA 4.7.0):**
- `fa fa-gift` (13th Month)
- `fa fa-star` (Bonus)
- `fa fa-certificate` (Special Payroll)
- `fa fa-list` (All Payroll Runs)
- `fa fa-check-circle` (Completed Runs)
- `fa fa-minus-circle` (Personnel Deductions)
- `fa fa-list-alt` (Income/Deduction Reference)

---

## 🎨 Fontastic Custom Icons Available

From `fontastic.css`, these are the custom icons available:

```css
.icon-home           /* Home/Dashboard */
.icon-form           /* Forms */
.icon-list           /* Lists */
.icon-presentation   /* Presentations */
.icon-bill           /* Bills/Invoices/Payroll */
.icon-check          /* Checkmarks */
.icon-list-1         /* Alternative list */
.icon-padnote        /* Notes/Pad */
.icon-pencil-case    /* Pencils/Edit */
.icon-user           /* Users/Personnel */
.icon-bars           /* Menu bars */
.icon-line-chart     /* Charts */
.icon-flask          /* Science/Lab */
.icon-grid           /* Grids */
.icon-picture        /* Images */
.icon-website        /* Websites */
.icon-screen         /* Screens */
.icon-interface-windows /* Windows */
.icon-clock          /* Time/Clock */
.icon-rss-feed       /* RSS */
.icon-mail           /* Email */
.icon-ios-email-outline /* Email outline */
.icon-paper-airplane /* Send */
.icon-ios-email      /* iOS email */
.icon-page           /* Pages/Documents */
.icon-close          /* Close/Delete */
.icon-search         /* Search */
```

---

## 📖 Font Awesome 4.7.0 Common Icons

### Money & Finance
- `fa fa-money` - Generic money
- `fa fa-usd` / `fa fa-dollar` - Dollar sign
- `fa fa-credit-card` - Credit card
- `fa fa-calculator` - Calculator

### Time & History
- `fa fa-clock-o` - Clock outline
- `fa fa-calendar` - Calendar solid
- `fa fa-calendar-o` - Calendar outline
- `fa fa-calendar-check-o` - Calendar with check (outline)

### Files & Documents
- `fa fa-file` - Generic file
- `fa fa-file-o` - File outline
- `fa fa-file-text` - Text file
- `fa fa-folder` - Folder solid
- `fa fa-folder-open` - Folder open
- `fa fa-folder-o` - Folder outline

### Actions
- `fa fa-edit` / `fa fa-pencil` - Edit
- `fa fa-trash` / `fa fa-trash-o` - Delete
- `fa fa-save` / `fa fa-floppy-o` - Save
- `fa fa-print` - Print
- `fa fa-search` - Search
- `fa fa-plus` / `fa fa-plus-circle` - Add
- `fa fa-minus` / `fa fa-minus-circle` - Subtract

### Status
- `fa fa-check` / `fa fa-check-circle` - Success
- `fa fa-times` / `fa fa-times-circle` - Error/Close
- `fa fa-exclamation-triangle` - Warning
- `fa fa-info-circle` - Information

### Lists & Navigation
- `fa fa-list` - List
- `fa fa-list-ul` - Bullet list
- `fa fa-list-ol` - Numbered list
- `fa fa-list-alt` - Alternative list
- `fa fa-bars` - Menu bars
- `fa fa-navicon` - Navigation icon

### Misc
- `fa fa-gift` - Gift/Bonus
- `fa fa-star` - Star/Favorite
- `fa fa-certificate` - Certificate/Award
- `fa fa-cog` / `fa fa-gear` - Settings
- `fa fa-user` / `fa fa-user-circle` - User
- `fa fa-users` - Multiple users
- `fa fa-building` - Building/Office

---

## 🚀 Best Practices

### 1. **Always Check Icon Availability**
Before using any Font Awesome icon, verify it exists in FA 4.7.0:
```bash
# Search in font-awesome.min.css
grep "fa-icon-name" ../vendor/font-awesome/css/font-awesome.min.css
```

### 2. **Prefer Fontastic for System-Specific Icons**
Use Fontastic custom icons (`icon-*`) for:
- Home navigation
- User/Personnel links
- Bills/Payroll-specific items
- System-unique features

### 3. **Use Font Awesome for Common Actions**
Use Font Awesome (`fa fa-*`) for:
- Standard actions (edit, delete, save, print)
- Status indicators (success, error, warning)
- Common UI elements (lists, calendars, folders)

### 4. **Icon Naming Conventions**
- **FA 4.7.0:** Uses single dash after `fa` → `fa fa-icon-name`
- **FA 5+:** Uses double dash (NOT compatible) → `fas fa-icon-name`
- **Fontastic:** Uses `icon-` prefix → `icon-icon-name`

### 5. **Fallback Strategy**
If an icon doesn't exist:
1. Check Fontastic custom icons first
2. Find FA 4.7.0 equivalent
3. Use generic icon as fallback (e.g., `fa fa-file` for documents)

---

## 🔍 How to Find Available Icons

### Method 1: Check CSS Files
```bash
# List all Font Awesome icons
cd c:\xampp\htdocs\moh_hrms\vendor\font-awesome\css
findstr /C:".fa-" font-awesome.min.css

# List all Fontastic icons
cd c:\xampp\htdocs\moh_hrms\css
findstr /C:".icon-" fontastic.css
```

### Method 2: Online Reference
- Font Awesome 4.7.0: https://fontawesome.com/v4.7.0/icons/
- Search by category (Web Application, Currency, Text Editor, etc.)

### Method 3: Existing Usage
Search existing PHP files for icon usage:
```bash
cd c:\xampp\htdocs\moh_hrms\payroll
findstr /S /C:"fa fa-" *.php
findstr /S /C:"icon-" *.php
```

---

## ⚠️ Icons to Avoid (Not in FA 4.7.0)

These Font Awesome 5+ icons do NOT exist in FA 4.7.0:

❌ `fa-file-invoice-dollar` → Use `icon-bill` or `fa fa-money`  
❌ `fa-calendar-check` → Use `fa fa-calendar` or `fa fa-calendar-check-o`  
❌ `fa-coins` → Use `fa fa-money`  
❌ `fa-money-bill-wave` → Use `fa fa-money` or `fa fa-usd`  
❌ `fa-history` → Use `icon-clock` or `fa fa-clock-o`  
❌ `fa-clock` (solid) → Use `fa fa-clock-o` (outline)  
❌ `fas` / `far` / `fal` classes → FA 5+ only  

---

## 📝 Testing Icons

To test if an icon works, create a simple HTML file:

```html
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../vendor/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/fontastic.css">
</head>
<body>
    <h3>Font Awesome Test</h3>
    <i class="fa fa-money"></i> Money Icon<br>
    <i class="fa fa-calendar"></i> Calendar Icon<br>
    
    <h3>Fontastic Test</h3>
    <i class="icon-bill"></i> Bill Icon<br>
    <i class="icon-clock"></i> Clock Icon<br>
</body>
</html>
```

If you see a square/box instead of an icon, it doesn't exist in that version.

---

## 📊 Icon Compatibility Matrix

| Feature | Recommended Icon | FA 4.7.0 | Fontastic | Notes |
|---------|------------------|----------|-----------|-------|
| Home | `icon-home` | ❌ | ✅ | Custom icon preferred |
| Users/Personnel | `icon-user` | ❌ | ✅ | Custom icon preferred |
| Payroll/Bills | `icon-bill` | ❌ | ✅ | Perfect for payroll |
| Money | `fa fa-money` | ✅ | ❌ | FA generic money |
| Calendar | `fa fa-calendar` | ✅ | ❌ | FA solid calendar |
| Clock/Time | `icon-clock` | ❌ | ✅ | Custom preferred |
| Edit | `fa fa-pencil` | ✅ | ❌ | FA standard edit |
| Delete | `fa fa-trash` | ✅ | ❌ | FA standard delete |
| Save | `fa fa-save` | ✅ | ❌ | FA floppy disk |
| Print | `fa fa-print` | ✅ | ❌ | FA printer |
| List | `fa fa-list` | ✅ | ✅ | Both available |
| Page/Reports | `icon-page` | ❌ | ✅ | Custom for reports |
| Close | `icon-close` | ❌ | ✅ | Custom X icon |

---

## 🎯 Updated Menu - Final Icon Set

```html
<!-- Payroll Templates -->
<li>
    <a href="#payroll_templates_dd" data-toggle="collapse">
        <i class="icon-bill"></i>Payroll Templates
    </a>
    <ul id="payroll_templates_dd" class="collapse">
        <li><a href="list_payroll_profiles.php"><i class="fa fa-folder-open"></i>All Templates</a></li>
        <li><a href="list_payroll_profiles.php?type=regular"><i class="fa fa-calendar"></i>Regular Payroll</a></li>
        <li><a href="list_payroll_profiles.php?type=13th_month"><i class="fa fa-gift"></i>13th Month</a></li>
        <li><a href="list_payroll_profiles.php?type=bonus"><i class="fa fa-star"></i>Bonus</a></li>
        <li><a href="list_payroll_profiles.php?type=special"><i class="fa fa-certificate"></i>Special Payroll</a></li>
    </ul>
</li>

<!-- Payroll History -->
<li>
    <a href="#payroll_history_dd" data-toggle="collapse">
        <i class="icon-clock"></i>Payroll History
    </a>
    <ul id="payroll_history_dd" class="collapse">
        <li><a href="list_payroll_history.php"><i class="fa fa-list"></i>All Payroll Runs</a></li>
        <li><a href="list_payroll_history.php?status=draft"><i class="fa fa-pencil"></i>Draft Runs</a></li>
        <li><a href="list_payroll_history.php?status=pending"><i class="fa fa-clock-o"></i>Pending Approval</a></li>
        <li><a href="list_payroll_history.php?status=completed"><i class="fa fa-check-circle"></i>Completed Runs</a></li>
    </ul>
</li>

<!-- Income & Deductions -->
<li>
    <a href="#income_deductions_dd" data-toggle="collapse">
        <i class="fa fa-money"></i>Income & Deductions
    </a>
    <ul id="income_deductions_dd" class="collapse">
        <li><a href="list_personnel.php?dept=All"><i class="fa fa-plus-circle"></i>Personnel Income</a></li>
        <li><a href="list_personnel.php?dept=All"><i class="fa fa-minus-circle"></i>Personnel Deductions</a></li>
        <li><a href="income.php"><i class="fa fa-list-alt"></i>Income Reference</a></li>
        <li><a href="deductions.php"><i class="fa fa-list-alt"></i>Deduction Reference</a></li>
    </ul>
</li>
```

---

## 📚 Additional Resources

- **Font Awesome 4.7.0 Cheatsheet:** https://fontawesome.com/v4.7.0/cheatsheet/
- **Icon Search:** https://fontawesome.com/v4.7.0/icons/
- **Local CSS Files:**
  - FA 4.7.0: `c:\xampp\htdocs\moh_hrms\vendor\font-awesome\css\font-awesome.min.css`
  - Fontastic: `c:\xampp\htdocs\moh_hrms\css\fontastic.css`

---

**All icons are now compatible with Font Awesome 4.7.0 and Fontastic custom icons!** ✅

*Document Version: 1.0*  
*Last Updated: October 20, 2025*
