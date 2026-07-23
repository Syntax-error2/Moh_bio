# Button Class Standardization - MOH HRMS Payroll System

**Date:** October 20, 2025  
**Purpose:** Maintain uniformity of UI across the entire system  
**Framework:** Bootstrap 3/4 (local template)

---

## Standard Button Classes

### Primary Button Colors

| Class | Color | Usage | Example |
|-------|-------|-------|---------|
| `btn-primary` | Blue | Main actions, submit, save | Add, Submit, Save, Update |
| `btn-success` | Green | Positive actions, create, confirm | Create, Generate, Confirm |
| `btn-info` | Cyan | Information, view, details | View Details, Show More |
| `btn-warning` | Yellow/Orange | Edit, caution actions | Edit, Modify |
| `btn-danger` | Red | Delete, remove, critical | Delete, Remove, Cancel |
| `btn-secondary` | Gray | Cancel, back, neutral | Cancel, Back, Close |

### Button Sizes

| Class | Size | Usage |
|-------|------|-------|
| `btn-lg` | Large | Primary page actions, important buttons |
| (default) | Medium | Standard buttons, most common |
| `btn-sm` | Small | Table actions, inline buttons, icon buttons |

### Size Guidelines:
- **Large (`btn-lg`)**: Main page actions (Generate Payroll, Save Changes, etc.)
- **Medium**: Form buttons, modal buttons
- **Small (`btn-sm`)**: Table row actions, quick actions

---

## Common Button Patterns

### 1. **Action Buttons**

#### Primary Action (Blue)
```php
<button type="submit" class="btn btn-primary">Save</button>
<button type="submit" class="btn btn-primary btn-lg">Submit</button>
```

#### Success Action (Green)
```php
<button class="btn btn-success">Create New</button>
<a href="generate.php" class="btn btn-success btn-lg">Generate Payroll</a>
```

#### Info Action (Cyan)
```php
<a href="view.php" class="btn btn-info btn-sm">View</a>
<button class="btn btn-info">Show Details</button>
```

#### Warning Action (Yellow/Orange)
```php
<button class="btn btn-warning btn-sm">Edit</button>
<a href="edit.php" class="btn btn-warning">Modify</a>
```

#### Danger Action (Red)
```php
<button class="btn btn-danger btn-sm">Delete</button>
<button class="btn btn-danger">Remove</button>
```

#### Secondary Action (Gray)
```php
<a href="back.php" class="btn btn-secondary">Back</a>
<button data-dismiss="modal" class="btn btn-secondary">Cancel</button>
```

---

### 2. **Icon Buttons**

#### With Icon (using Font Awesome 4.7.0)
```php
<!-- Small table actions -->
<button class="btn btn-success btn-sm"><i class="fa fa-plus"></i></button>
<button class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i></button>
<button class="btn btn-danger btn-sm"><i class="fa fa-times"></i></button>

<!-- With text -->
<button class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
<a href="#" class="btn btn-success"><i class="fa fa-cogs"></i> Generate</a>
```

---

### 3. **Modal Buttons**

#### Modal Footer (Standard Pattern)
```php
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
    <button type="submit" class="btn btn-primary">Save Changes</button>
</div>
```

#### Delete Confirmation
```php
<div class="modal-footer">
    <a href="" data-dismiss="modal" class="btn btn-primary">No</a>
    <button name="delete" type="submit" class="btn btn-danger">Yes</button>
</div>
```

---

### 4. **Table Action Buttons**

#### Standard Table Row Actions
```php
<!-- View -->
<a href="view.php?id=<?php echo $id; ?>" class="btn btn-info btn-sm" title="View details">
    <i class="fa fa-eye"></i>
</a>

<!-- Edit -->
<button type="button" data-toggle="modal" data-target="#edit<?php echo $id; ?>" 
        class="btn btn-success btn-sm">
    <i class="fa fa-pencil"></i>
</button>

<!-- Delete -->
<button type="button" data-toggle="modal" data-target="#delete<?php echo $id; ?>" 
        class="btn btn-danger btn-sm">
    <i class="fa fa-times"></i>
</button>
```

---

### 5. **Form Buttons**

#### Standard Form Actions
```php
<!-- Submit button (primary) -->
<button type="submit" class="btn btn-primary">Submit</button>

<!-- Cancel link (secondary) -->
<a href="list.php" class="btn btn-secondary">Cancel</a>

<!-- Save and Continue -->
<button type="submit" name="save" class="btn btn-success">Save & Continue</button>
```

---

### 6. **Button Groups**

#### Page Header Actions
```php
<div class="d-flex justify-content-end">
    <a href="create.php" class="btn btn-success btn-lg mr-2">
        <i class="fa fa-plus"></i> Create New
    </a>
    <a href="list.php" class="btn btn-secondary btn-lg">
        <i class="fa fa-list"></i> View All
    </a>
</div>
```

#### Bottom Action Bar
```php
<div class="text-center mt-4">
    <a href="back.php" class="btn btn-secondary btn-lg">
        <i class="fa fa-arrow-left"></i> Back
    </a>
    <button type="submit" class="btn btn-success btn-lg">
        <i class="fa fa-check"></i> Confirm
    </button>
</div>
```

---

## Color Usage Guidelines

### When to Use Each Color:

#### **Primary (Blue)** - `btn-primary`
- ✅ Default submit actions
- ✅ Save actions
- ✅ Main form submission
- ✅ Generic "OK" or "Yes" in confirmations
- ✅ Default action in modals

#### **Success (Green)** - `btn-success`
- ✅ Create new records
- ✅ Add items
- ✅ Generate/Process
- ✅ Confirm positive actions
- ✅ Save successful operations

#### **Info (Cyan)** - `btn-info`
- ✅ View details
- ✅ Show information
- ✅ Preview
- ✅ Print/Export actions
- ✅ Navigation to info pages

#### **Warning (Yellow/Orange)** - `btn-warning`
- ✅ Edit/Modify actions
- ✅ Change settings
- ✅ Update existing data
- ✅ Actions that need caution
- ✅ Temporary changes

#### **Danger (Red)** - `btn-danger`
- ✅ Delete operations
- ✅ Remove items
- ✅ Permanent destructive actions
- ✅ Cancel with consequences
- ✅ Critical warnings

#### **Secondary (Gray)** - `btn-secondary`
- ✅ Cancel operations
- ✅ Back navigation
- ✅ Close modals
- ✅ Neutral actions
- ✅ Alternative options

---

## ❌ Classes to AVOID

### DO NOT USE (Not in system template):

```php
<!-- AVOID THESE -->
<button class="btn btn-light">...</button>        <!-- Use btn-secondary instead -->
<button class="btn btn-dark">...</button>         <!-- Use btn-primary instead -->
<button class="btn btn-outline-*">...</button>    <!-- Not in template -->
<button class="btn btn-link">...</button>         <!-- Use <a> tag instead -->
```

---

## Updated Files for Consistency

### Files Already Standardized:

1. ✅ **list_payroll_profiles.php**
   - Uses: `btn-success btn-lg`, `btn-primary btn-sm`, `btn-info btn-sm`, `btn-warning btn-sm`, `btn-secondary btn-sm`, `btn-danger btn-sm`

2. ✅ **generate_payroll_from_profile.php**
   - Uses: `btn-secondary btn-lg`, `btn-success btn-lg`

3. ✅ **list_payroll_history.php**
   - Uses: `btn-success btn-lg`, `btn-primary btn-block`, `btn-secondary btn-block`

4. ✅ **view_payroll_profile.php** (UPDATED)
   - Changed: `btn-light` → `btn-secondary`
   - Uses: `btn-warning btn-lg`, `btn-success btn-lg`, `btn-secondary btn-lg`, `btn-primary btn-lg`, `btn-info btn-lg`, `btn-danger btn-lg`

---

## Button Class Quick Reference

### Most Common Combinations:

```php
// Large action buttons (page level)
class="btn btn-success btn-lg"      // Create/Generate
class="btn btn-primary btn-lg"      // Save/Submit
class="btn btn-secondary btn-lg"    // Back/Cancel

// Small table buttons
class="btn btn-info btn-sm"         // View
class="btn btn-success btn-sm"      // Quick Add (was warning for edit)
class="btn btn-warning btn-sm"      // Edit
class="btn btn-danger btn-sm"       // Delete

// Modal buttons
class="btn btn-secondary"           // Cancel
class="btn btn-primary"             // Submit/Save
class="btn btn-danger"              // Confirm Delete

// Form buttons
class="btn btn-primary"             // Submit
class="btn btn-secondary"           // Cancel
class="btn btn-success"             // Create/Add
```

---

## Style Overrides (When Needed)

### Text Color Override
```php
<a href="#" class="btn btn-info" style="color: white !important;">
    <i class="fa fa-print"></i> Print
</a>
```

**Note:** Only use `style="color: white !important;"` when text visibility is an issue.

---

## Icon Reference (Font Awesome 4.7.0)

### Common Icons for Buttons:

| Action | Icon Class |
|--------|------------|
| Add/Create | `fa fa-plus` |
| Edit | `fa fa-pencil` |
| Delete | `fa fa-times` or `fa fa-trash` |
| Save | `fa fa-save` or `fa fa-check` |
| Cancel | `fa fa-times` |
| Back | `fa fa-arrow-left` |
| View | `fa fa-eye` |
| Print | `fa fa-print` |
| Export | `fa fa-download` |
| Search | `fa fa-search` |
| Filter | `fa fa-filter` |
| Refresh | `fa fa-refresh` |
| Generate | `fa fa-cogs` |
| Clone | `fa fa-files-o` |

---

## Testing Checklist

After applying button classes:

- [ ] All buttons have proper color coding
- [ ] Large buttons use `btn-lg` for important actions
- [ ] Small buttons use `btn-sm` for table actions
- [ ] No `btn-light` or `btn-dark` classes used
- [ ] Icons are from Font Awesome 4.7.0
- [ ] Buttons have consistent spacing
- [ ] Hover states work correctly
- [ ] Colors match existing system pages

---

## Examples from Existing System

### Income.php (Reference)
```php
<!-- Add button -->
<a style="color: white !important;" data-toggle="modal" data-target="#add_income_reference" 
   href="#" class="btn btn-primary btn-sm">
    <i class="fa fa-plus"></i>
</a>

<!-- Edit button -->
<button type="button" data-toggle="modal" data-target="#edit_income<?php echo $income_row['income_id']; ?>" 
        class="btn btn-success btn-sm">
    <i class="fa fa-pencil"></i>
</button>

<!-- Delete button -->
<button type="button" data-toggle="modal" data-target="#del_income<?php echo $income_row['income_id']; ?>" 
        class="btn btn-danger btn-sm">
    <i class="fa fa-times"></i>
</button>

<!-- Modal footer -->
<a href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
<button name="createIncome" type="submit" class="btn btn-primary">Add</button>
```

---

## Summary of Changes

### view_payroll_profile.php
**Changed:**
```php
// BEFORE
class="btn btn-light btn-lg btn-action"

// AFTER
class="btn btn-secondary btn-lg btn-action"
```

**Reason:** `btn-light` is not part of the system template. Use `btn-secondary` for neutral/back actions.

---

## Maintenance Notes

1. **Always use standard Bootstrap classes** from the system template
2. **Consistent sizing**: Large for page actions, small for table actions
3. **Color coding**: Follow the semantic meaning (success=green, danger=red, etc.)
4. **Icon placement**: Icon before text with space
5. **Style overrides**: Use only when necessary (white text on dark buttons)

---

**Status:** ✅ All new payroll files now use standardized button classes consistent with the existing MOH HRMS system!
