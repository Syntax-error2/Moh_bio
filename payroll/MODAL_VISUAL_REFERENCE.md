# Visual Modal Reference Guide

## Personnel Filter Modal Layouts

### Add Personnel Filter Modal
```
┌─────────────────────────────────────────────────────────────┐
│ ⊕ Add Personnel Filter                                   × │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Filter Type *        │  Filter Operator                    │
│  [Department      ▼]  │  [Equals (=)     ▼]                │
│                                                             │
│  ┌─ Department Options (shown when type=department) ─────┐ │
│  │ Select Department(s)                                   │ │
│  │ ┌────────────────────────────────────────────────┐   │ │
│  │ │ ☑ Human Resources Department                   │   │ │
│  │ │ ☐ Finance Department                           │   │ │
│  │ │ ☐ Medical Services                             │   │ │
│  │ │ ☑ Administrative Division                      │   │ │
│  │ │ ☐ IT Department                                │   │ │
│  │ └────────────────────────────────────────────────┘   │ │
│  │ Hold Ctrl/Cmd to select multiple                     │ │
│  └──────────────────────────────────────────────────────┘ │
│                                                             │
│  Description (Optional)                                     │
│  [All permanent staff in HR and Admin              ]        │
│                                                             │
│  ☑ Active (Apply this filter when generating payroll)      │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│                     [× Cancel]  [💾 Save Filter]            │
└─────────────────────────────────────────────────────────────┘
```

### Filter Type Options
```
Department/Office       → Multi-select departments
Employment Status      → Multi-select (Permanent, Casual, etc.)
Position/Designation   → Text input with wildcards
Salary Grade          → From/To numeric range
Gender                → Dropdown (Male/Female)
Age Range             → From/To numeric range
Custom SQL            → Textarea for advanced SQL
```

---

## Income Item Modal Layouts

### Add Income Item Modal
```
┌─────────────────────────────────────────────────────────────┐
│ ⊕ Add Income Item                                        × │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Select Income Item *                                       │
│  [Basic Salary [BASIC] - Fixed                         ▼]  │
│  Fixed: Same amount for all | Variable: Per personnel       │
│                                                             │
│  Default Amount          │  Display Order                   │
│  ₱ [15000.00        ]   │  [1                ]             │
│  Leave blank for         │  Lower numbers appear first      │
│  variable amounts        │                                  │
│                                                             │
│  Calculation Method                                         │
│  [Fixed Amount                                         ▼]  │
│                                                             │
│  ┌─ Formula (shown when method=formula) ────────────────┐  │
│  │ Formula                                               │  │
│  │ [{basic_salary} * 0.10                        ]      │  │
│  │ Available: {basic_salary}, {daily_rate},             │  │
│  │ {monthly_rate}, {days_worked}                        │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                             │
│  ☑ Mandatory (Always include in payroll)                   │
│  ☑ Active (Enable this income item)                        │
│                                                             │
│  ℹ️ Note: After adding income items, you can assign        │
│  specific amounts to individual personnel from their        │
│  profile page.                                             │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│                    [× Cancel]  [💾 Add Income Item]         │
└─────────────────────────────────────────────────────────────┘
```

### Edit Income Item Modal
```
┌─────────────────────────────────────────────────────────────┐
│ ✏️ Edit Income Item                                       × │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Income Item                                                │
│  [PERA - Personnel Economic Relief Allowance     ] (disabled)│
│                                                             │
│  Default Amount          │  Display Order                   │
│  ₱ [2000.00         ]   │  [5                ]             │
│                                                             │
│  ☑ Mandatory             │  ☑ Active                        │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│                    [× Cancel]  [💾 Update Income]           │
└─────────────────────────────────────────────────────────────┘
```

---

## Deduction Item Modal Layouts

### Add Deduction Item Modal
```
┌─────────────────────────────────────────────────────────────┐
│ ⊖ Add Deduction Item                                     × │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Select Deduction Item *                                    │
│  [SSS Contribution [SSS] - Variable                    ▼]  │
│  Fixed: Same for all | Variable: Varies per personnel       │
│                                                             │
│  Default Amount          │  Display Order                   │
│  ₱ [0.00            ]   │  [1                ]             │
│  Leave blank for         │  Lower numbers appear first      │
│  variable amounts        │                                  │
│                                                             │
│  Calculation Method                                         │
│  [Manual Entry Per Personnel                           ▼]  │
│                                                             │
│  ┌─ Formula (shown when method=formula) ────────────────┐  │
│  │ Formula                                               │  │
│  │ [{gross_income} * 0.05                        ]      │  │
│  │ Available: {gross_income}, {basic_salary},           │  │
│  │ {total_income}, {taxable_income}                     │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                             │
│  ☑ Mandatory (Always deduct in payroll)                    │
│  ☑ Active (Enable this deduction)                          │
│                                                             │
│  Deduction Priority                                         │
│  [High (Deduct first, e.g., Government mandatories)    ▼]  │
│  Determines the order of deductions when processing         │
│                                                             │
│  ⚠️ Important: Government-mandated deductions (SSS,         │
│  PhilHealth, Pag-IBIG, Tax) should be marked as            │
│  Mandatory and set to High Priority.                       │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│                   [× Cancel]  [💾 Add Deduction Item]       │
└─────────────────────────────────────────────────────────────┘
```

---

## Item Display Cards

### Personnel Filter Card (View Mode)
```
┌─────────────────────────────────────────────────────────────┐
│ 🔍 Personnel Filters (2)                                    │
├─────────────────────────────────────────────────────────────┤
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ Department: Human Resources, Finance                    │ │
│ │ Employment Status: Permanent                            │ │
│ └─────────────────────────────────────────────────────────┘ │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ Salary Grade: 15 to 30                                  │ │
│ └─────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

### Personnel Filter Card (Edit Mode)
```
┌─────────────────────────────────────────────────────────────┐
│ 🔍 Personnel Filters (2)                                    │
├─────────────────────────────────────────────────────────────┤
│ ┌────────────────────────────────────────────┬─────────┐   │
│ │ Department: Human Resources, Finance        │[✏️][🗑️]│   │
│ │ Employment Status: Permanent                │         │   │
│ └────────────────────────────────────────────┴─────────┘   │
│ ┌────────────────────────────────────────────┬─────────┐   │
│ │ Salary Grade: 15 to 30                      │[✏️][🗑️]│   │
│ └────────────────────────────────────────────┴─────────┘   │
│                                                             │
│ [➕ Add Filter]                                             │
└─────────────────────────────────────────────────────────────┘
```

### Income Items Card (Edit Mode)
```
┌─────────────────────────────────────────────────────────────┐
│ ➕ Income Items (3)                                         │
├─────────────────────────────────────────────────────────────┤
│ ┌────────────────────────────────────────────┬─────────┐   │
│ │ Basic Salary            [Fixed]             │[✏️][🗑️]│   │
│ │ ✓ Required | Display Order: 1               │         │   │
│ └────────────────────────────────────────────┴─────────┘   │
│ ┌────────────────────────────────────────────┬─────────┐   │
│ │ PERA                    [Fixed]             │[✏️][🗑️]│   │
│ │ ✓ Required | Display Order: 2 | Default:    │         │   │
│ │ ₱2,000.00                                   │         │   │
│ └────────────────────────────────────────────┴─────────┘   │
│ ┌────────────────────────────────────────────┬─────────┐   │
│ │ Overtime Pay            [Variable]          │[✏️][🗑️]│   │
│ │ Display Order: 3                            │         │   │
│ └────────────────────────────────────────────┴─────────┘   │
│                                                             │
│ [➕ Add Income Item]                                        │
└─────────────────────────────────────────────────────────────┘
```

### Deduction Items Card (Edit Mode)
```
┌─────────────────────────────────────────────────────────────┐
│ ➖ Deduction Items (4)                                      │
├─────────────────────────────────────────────────────────────┤
│ ┌────────────────────────────────────────────┬─────────┐   │
│ │ SSS Contribution        [Variable]          │[✏️][🗑️]│   │
│ │ ✓ Required | Display Order: 1               │         │   │
│ └────────────────────────────────────────────┴─────────┘   │
│ ┌────────────────────────────────────────────┬─────────┐   │
│ │ PhilHealth              [Variable]          │[✏️][🗑️]│   │
│ │ ✓ Required | Display Order: 2               │         │   │
│ └────────────────────────────────────────────┴─────────┘   │
│ ┌────────────────────────────────────────────┬─────────┐   │
│ │ Pag-IBIG                [Fixed]             │[✏️][🗑️]│   │
│ │ ✓ Required | Display Order: 3 | Default:    │         │   │
│ │ ₱200.00                                     │         │   │
│ └────────────────────────────────────────────┴─────────┘   │
│ ┌────────────────────────────────────────────┬─────────┐   │
│ │ Cash Advance            [Variable]          │[✏️][🗑️]│   │
│ │ Display Order: 10                           │         │   │
│ └────────────────────────────────────────────┴─────────┘   │
│                                                             │
│ [➕ Add Deduction Item]                                     │
└─────────────────────────────────────────────────────────────┘
```

### Empty State (No Items)
```
┌─────────────────────────────────────────────────────────────┐
│ ➕ Income Items (0)                                         │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│                      ➕                                     │
│                                                             │
│          No income items configured for this profile.       │
│                                                             │
│                 [➕ Add Income Item]                        │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## Modal Color Scheme

### Add Modals (Blue Header)
```
Header: bg-primary (Blue) + text-white
- Personnel Filter: Blue + 🔍 filter icon
- Income Item: Blue + ➕ plus-circle icon  
- Deduction Item: Blue + ➖ minus-circle icon

Footer:
- Cancel: btn btn-secondary (Gray) + ✖️ times icon
- Save: btn btn-primary (Blue) + 💾 save icon
```

### Edit Modals (Yellow Header)
```
Header: bg-warning (Yellow) + text-white
- All Edit Modals: Yellow + ✏️ pencil icon

Footer:
- Cancel: btn btn-secondary (Gray)
- Update: btn btn-warning (Yellow) + 💾 save icon
```

---

## Badge Colors

### Income Type Badges
```
Fixed Income:    [Fixed]    - Green background (#d4edda), Dark green text
Variable Income: [Variable] - Blue background (#d1ecf1), Dark blue text
```

### Deduction Type Badges
```
Fixed Deduction:    [Fixed]    - Orange background (#fff3cd), Dark brown text
Variable Deduction: [Variable] - Blue background (#d1ecf1), Dark blue text
```

---

## Action Button Colors

### In Edit Mode
```
✏️ Edit Button:   btn btn-warning btn-xs  (Yellow)
🗑️ Delete Button: btn btn-danger btn-xs   (Red)
➕ Add Button:    btn btn-primary btn-sm  (Blue)
```

### In View Mode
```
No action buttons visible - Clean read-only display
```

---

## Icon Reference (Font Awesome 4.7.0)

```
Filters:      fa-filter
Plus:         fa-plus, fa-plus-circle
Minus:        fa-minus-circle
Edit:         fa-pencil
Delete:       fa-trash
Save:         fa-save
Close:        fa-times
Check:        fa-check-circle
Info:         fa-info-circle
Warning:      fa-exclamation-triangle
Arrow:        fa-arrow-left
Toggle On:    fa-toggle-on
Toggle Off:   fa-toggle-off
```

---

## Responsive Behavior

### Desktop (> 992px)
- Modals: modal-lg (large width)
- 2-column layouts work well
- All fields visible side-by-side

### Tablet (768px - 991px)
- Modals: Adapt to screen width
- Form fields stack vertically
- Dropdowns maintain full width

### Mobile (< 768px)
- Modals: Full width with padding
- All form elements stack
- Larger touch targets for buttons
- Action buttons may wrap

---

## Form Validation

### Required Fields (marked with *)
```
Personnel Filter:
  ✓ Filter Type (dropdown)

Income Item:
  ✓ Income Item (dropdown)

Deduction Item:
  ✓ Deduction Item (dropdown)
```

### Optional But Recommended
```
- Default Amount (for fixed items)
- Description (for filters)
- Display Order (defaults to 0)
```

### Client-Side Validation
```javascript
// jQuery validation on submit
if (!$('#filter_type').val()) {
    alert('Please select a filter type');
    return false;
}
```

### Server-Side Validation (Required in PHP)
```php
if (empty($_POST['income_id'])) {
    echo json_encode(['success' => false, 'message' => 'Income item required']);
    exit;
}
```

---

## Data Flow Diagram

```
User Action → Frontend Modal → JavaScript Function → AJAX POST
                                                          ↓
                                                    PHP Handler
                                                          ↓
                                                    Database Query
                                                          ↓
                                                    JSON Response
                                                          ↓
                                                    Success/Error
                                                          ↓
                                                    Page Reload
```

---

## Summary

✅ **6 Modals** - 3 Add + 3 Edit
✅ **Color-coded** - Blue for add, Yellow for edit
✅ **Consistent Layout** - All follow same pattern
✅ **Responsive** - Mobile-friendly
✅ **Accessible** - Proper labels, ARIA attributes
✅ **Professional** - Clean, modern design
✅ **Font Awesome 4.7.0** - All icons compatible
✅ **Bootstrap Standards** - No custom outline classes

---

*Visual Reference for MOH HRMS Payroll System*
*Last Updated: January 2025*
