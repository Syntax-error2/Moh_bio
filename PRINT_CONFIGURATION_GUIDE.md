# Print Configuration Guide

## Overview
All print forms and reports in the MOH HRMS system are now configured to support multiple standard paper sizes:
- **Letter**: 8.5" × 11" (most common in Philippines)
- **Folio**: 8.5" × 13" 
- **Legal**: 8.5" × 14"

## How It Works

### Automatic Paper Size Detection
The system uses `@page { size: auto; }` which automatically adapts to whatever paper size is selected in the browser's print dialog. This means:
- ✅ No need to manually configure paper size in code
- ✅ Users can choose their preferred paper size when printing
- ✅ Content automatically adjusts to fit within margins

### Standard Margins
All print pages use consistent margins:
- **Top/Bottom**: 0.5 inches (12.7mm)
- **Left/Right**: 0.5 inches (12.7mm)

### Content Width
All containers are limited to **7.5 inches** maximum width, ensuring content fits within:
- 8.5" paper width
- Minus 0.5" left margin
- Minus 0.5" right margin
- = 7.5" usable width

## Updated Files

### Core Print Files
1. ✅ `print_leave_application_csform6.php` - CS Form No. 6 (Leave Application)
2. ✅ `print_monetized_leave_certification.php` - Certification on Appropriations
3. ✅ `print_monetized_leave_voucher.php` - Disbursement Voucher
4. ✅ `print_leave_application.php` - Standalone Leave Application
5. ✅ `printPersonnelDataSheet_detailed_SR.php` - Service Record Details
6. ✅ `leave_card.php` - Leave Card (Landscape)

### Payroll Print Files
7. ✅ `payroll/print_payroll_run.php` - Payroll Run Report
8. ✅ `payroll/generate_payslip.php` - Employee Payslip
9. ✅ `payroll/header_print.php` - Payroll Print Header

### Shared Components
10. ✅ `header_print.php` - Common Print Header
11. ✅ `print_config_styles.php` - **NEW** Universal Print Configuration

## Using the Print Configuration

### Method 1: Include Universal Styles (Recommended)
For new print pages or modals, include the universal configuration:

```php
<?php include('print_config_styles.php'); ?>
```

This provides:
- Automatic paper size detection
- Standard margins and spacing
- Common print utilities
- Browser compatibility fixes

### Method 2: Custom Implementation
For existing pages with specific requirements, use this pattern:

```css
@media print {
    @page {
        size: auto; /* Auto-detects paper size */
        margin: 0.5in;
    }
    
    .your-container {
        max-width: 7.5in;
        margin: 0 auto;
        page-break-inside: avoid;
    }
}
```

## Print Dialog Instructions

### For Users
When printing any form or report:

1. **Open Print Dialog**
   - Press `Ctrl + P` (Windows) or `Cmd + P` (Mac)
   - Or click the Print button on the form

2. **Select Paper Size**
   - Click "More settings" or "Printer properties"
   - Choose your paper size:
     - Letter (8.5" × 11")
     - Folio (8.5" × 13")
     - Legal (8.5" × 14")

3. **Adjust Orientation** (if needed)
   - Portrait (default for most forms)
   - Landscape (for wide tables like Leave Card)

4. **Preview Before Printing**
   - Always check the preview to ensure:
     - Content fits within page boundaries
     - No important information is cut off
     - Page breaks occur at logical points

5. **Print**
   - Click "Print" or press Enter

## Common Print Scenarios

### Scenario 1: Letter Size (8.5" × 11")
**Use for:**
- CS Form No. 6 (Leave Applications)
- Personnel Data Sheets
- Most single-page forms

**Configuration:** Already set as default. No changes needed.

### Scenario 2: Folio Size (8.5" × 13")
**Use for:**
- Forms with more content
- Detailed reports
- Multi-section documents

**Configuration:** User selects in print dialog. Form auto-adjusts.

### Scenario 3: Legal Size (8.5" × 14")
**Use for:**
- Long forms
- Detailed service records
- Comprehensive reports

**Configuration:** User selects in print dialog. Form auto-adjusts.

### Scenario 4: Landscape Orientation
**Use for:**
- Wide tables (Leave Card, DTR)
- Payroll reports
- Multi-column layouts

**Configuration:**
```css
@page {
    size: auto landscape;
    margin: 0.5in 0.75in;
}
```

## Best Practices

### ✅ Do's
1. **Always use max-width: 7.5in** for content containers
2. **Test with multiple paper sizes** before deployment
3. **Use `page-break-inside: avoid`** for critical sections
4. **Include print preview** in your UI
5. **Provide clear print instructions** to users

### ❌ Don'ts
1. **Don't hardcode specific paper sizes** (e.g., `size: 8.5in 11in;`)
2. **Don't use fixed pixel widths** for print containers
3. **Don't ignore page breaks** - control where they occur
4. **Don't forget mobile users** - responsive print is important
5. **Don't overcrowd pages** - leave adequate whitespace

## Troubleshooting

### Problem: Content is cut off
**Solution:** 
- Reduce font sizes slightly
- Decrease padding/margins
- Check max-width is not exceeding 7.5in

### Problem: Unwanted page breaks
**Solution:**
```css
.your-section {
    page-break-inside: avoid;
}
```

### Problem: Headers/footers not repeating
**Solution:**
```css
thead { display: table-header-group; }
tfoot { display: table-footer-group; }
```

### Problem: Colors not printing
**Solution:**
```css
@media print {
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}
```

### Problem: Blank pages between content
**Solution:**
- Remove excessive margins
- Check for hidden elements pushing content
- Use `page-break-after: avoid` where needed

## Testing Checklist

Before deploying any print functionality:

- [ ] Test with Letter (8.5" × 11")
- [ ] Test with Folio (8.5" × 13")
- [ ] Test with Legal (8.5" × 14")
- [ ] Test both Portrait and Landscape
- [ ] Verify margins are consistent
- [ ] Check page breaks are logical
- [ ] Ensure no content overflow
- [ ] Test in Chrome, Firefox, and Edge
- [ ] Verify with actual printer output
- [ ] Check PDF export quality

## Browser Compatibility

### ✅ Fully Supported
- Chrome/Edge 90+
- Firefox 88+
- Safari 14+

### ⚠️ Partial Support
- Internet Explorer 11 (use fallback styles)
- Older mobile browsers

### Browser-Specific Notes

**Chrome/Edge:**
- Best print preview
- Most reliable `size: auto` support

**Firefox:**
- May require manual paper size selection
- Good overall compatibility

**Safari:**
- Excellent print quality
- May need `-webkit-` prefixes for some properties

## Additional Resources

### CSS Print Properties
- `@page` - Page size and margins
- `page-break-before/after/inside` - Control page breaks
- `orphans/widows` - Control text splitting across pages

### Useful Classes (from print_config_styles.php)
```css
.page-break-before  /* Force page break before element */
.page-break-after   /* Force page break after element */
.page-break-avoid   /* Prevent page break inside element */
.print-only         /* Show only when printing */
.no-print           /* Hide when printing */
```

## Support

For issues or questions about print configuration:
1. Check this guide first
2. Review `print_config_styles.php` for available utilities
3. Test in print preview before reporting issues
4. Document specific paper size and browser when reporting problems

---

**Last Updated:** November 17, 2025
**Version:** 2.0
**Compatibility:** Letter (8.5"×11"), Folio (8.5"×13"), Legal (8.5"×14")
