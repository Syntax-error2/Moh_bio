# Button Class Quick Reference Card

## ✅ Standard Button Classes (Use These)

```php
// PRIMARY COLORS
btn-primary    // Blue - Save, Submit, Main actions
btn-success    // Green - Create, Add, Generate
btn-info       // Cyan - View, Details, Print
btn-warning    // Yellow - Edit, Modify
btn-danger     // Red - Delete, Remove
btn-secondary  // Gray - Cancel, Back, Close

// SIZES
btn-lg         // Large - Main page actions
(no size)      // Medium - Standard buttons
btn-sm         // Small - Table actions

// COMMON COMBINATIONS
btn btn-success btn-lg     // Create/Generate (large green)
btn btn-primary btn-lg     // Save/Submit (large blue)
btn btn-secondary btn-lg   // Back/Cancel (large gray)
btn btn-info btn-sm        // View (small cyan)
btn btn-warning btn-sm     // Edit (small yellow)
btn btn-danger btn-sm      // Delete (small red)
```

## ❌ DO NOT USE

```php
btn-light      // ❌ Not in template - Use btn-secondary
btn-dark       // ❌ Not in template - Use btn-primary
btn-outline-*  // ❌ Not in template
btn-link       // ❌ Use <a> tag instead
```

## 🔄 Change Made

**File:** `view_payroll_profile.php`

```php
// BEFORE
<a href="list_payroll_profiles.php" class="btn btn-light btn-lg btn-action">

// AFTER  
<a href="list_payroll_profiles.php" class="btn btn-secondary btn-lg btn-action">
```

## 📊 Button Usage by Action

| Action | Class | Example |
|--------|-------|---------|
| Create | `btn-success` | Create New Profile |
| Save | `btn-primary` | Save Changes |
| Edit | `btn-warning` | Edit Profile |
| Delete | `btn-danger` | Delete |
| View | `btn-info` | View Details |
| Back | `btn-secondary` | Back to List |
| Cancel | `btn-secondary` | Cancel |

## 🎨 Icon + Button

```php
<button class="btn btn-primary btn-lg">
    <i class="fa fa-save"></i> Save
</button>

<button class="btn btn-danger btn-sm">
    <i class="fa fa-times"></i>
</button>
```

---

**All new payroll files now use consistent button classes!** ✅
