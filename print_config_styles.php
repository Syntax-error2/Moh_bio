<!-- Universal Print Configuration Styles -->
<!-- 
    This file provides standardized print styles for all forms and reports.
    Supports: Letter (8.5" x 11"), Folio (8.5" x 13"), and Legal (8.5" x 14") paper sizes.
    
    Usage: Include this file in any print page/modal:
    <?php include('print_config_styles.php'); ?>
-->

<style>
/* ============================================
   UNIVERSAL PRINT STYLES
   Compatible with Letter, Folio, and Legal paper sizes
   ============================================ */

/* Base Print Settings */
@media print {
    /* Hide non-printable elements */
    .no-print,
    .modal-header,
    .modal-footer,
    button,
    .btn,
    nav,
    .navbar,
    .sidebar,
    .breadcrumb {
        display: none !important;
    }
    
    /* Page setup - Auto adapts to selected paper size */
    @page {
        size: auto; /* Automatically uses the paper size selected in print dialog */
        margin: 0.5in 0.5in; /* Standard margins for all paper sizes */
    }
    
    /* First page specific margin (optional) */
    @page :first {
        margin-top: 0.5in;
    }
    
    /* Body settings */
    body {
        margin: 0;
        padding: 0;
        background: white;
        color: #000;
        font-family: Arial, sans-serif;
        font-size: 11pt;
        line-height: 1.3;
    }
    
    /* Prevent page breaks inside important elements */
    table, 
    .signature-block,
    .form-section,
    .no-break {
        page-break-inside: avoid;
    }
    
    /* Table rows shouldn't break */
    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
    
    /* Headers and footers should repeat on each page */
    thead {
        display: table-header-group;
    }
    
    tfoot {
        display: table-footer-group;
    }
    
    /* Ensure links print with their URLs */
    a[href]:after {
        content: none; /* Remove URL printing for cleaner output */
    }
    
    /* Image handling */
    img {
        max-width: 100% !important;
        height: auto !important;
    }
}

/* ============================================
   STANDARD CONTAINER SIZES
   ============================================ */

/* Letter Size (8.5" x 11") - Most common */
.print-container-letter {
    max-width: 7.5in; /* Content area with margins */
    margin: 0 auto;
    padding: 0;
    box-sizing: border-box;
}

/* Folio Size (8.5" x 13") */
.print-container-folio {
    max-width: 7.5in;
    margin: 0 auto;
    padding: 0;
    box-sizing: border-box;
}

/* Legal Size (8.5" x 14") */
.print-container-legal {
    max-width: 7.5in;
    margin: 0 auto;
    padding: 0;
    box-sizing: border-box;
}

/* Universal container - works with all sizes */
.print-container {
    max-width: 7.5in;
    margin: 0 auto;
    padding: 20px;
    box-sizing: border-box;
    background: white;
}

@media print {
    .print-container,
    .print-container-letter,
    .print-container-folio,
    .print-container-legal {
        width: 100%;
        max-width: 100%;
        padding: 0;
        margin: 0;
    }
}

/* ============================================
   LANDSCAPE ORIENTATION SUPPORT
   ============================================ */

@media print and (orientation: landscape) {
    @page {
        size: auto landscape;
        margin: 0.5in 0.75in;
    }
    
    .print-container {
        max-width: 100%;
    }
}

/* ============================================
   COMMON PRINT ELEMENTS
   ============================================ */

.print-header {
    text-align: center;
    margin-bottom: 20px;
}

.print-title {
    font-size: 16pt;
    font-weight: bold;
    margin: 10px 0;
}

.print-subtitle {
    font-size: 12pt;
    margin: 5px 0;
}

.print-table {
    width: 100%;
    border-collapse: collapse;
    margin: 10px 0;
}

.print-table th,
.print-table td {
    border: 1px solid #000;
    padding: 5px;
    text-align: left;
    font-size: 10pt;
}

.print-table th {
    background-color: #f0f0f0;
    font-weight: bold;
}

.signature-section {
    margin-top: 40px;
    page-break-inside: avoid;
}

.signature-line {
    border-bottom: 1px solid #000;
    width: 250px;
    margin: 30px auto 5px;
    text-align: center;
}

.signature-label {
    text-align: center;
    font-size: 10pt;
    margin-top: 5px;
}

/* ============================================
   SCREEN-ONLY STYLES (Preview)
   ============================================ */

@media screen {
    .print-container {
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        margin: 20px auto;
        background: white;
    }
    
    .print-preview-note {
        background: #fff3cd;
        border: 1px solid #ffc107;
        padding: 10px;
        margin: 10px 0;
        border-radius: 4px;
        text-align: center;
        font-size: 11pt;
    }
}

/* ============================================
   BROWSER-SPECIFIC FIXES
   ============================================ */

/* Chrome/Edge print fixes */
@media print {
    @-webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}

/* Firefox print fixes */
@-moz-document url-prefix() {
    @media print {
        body {
            margin: 0;
        }
    }
}

/* ============================================
   UTILITY CLASSES
   ============================================ */

.page-break-before {
    page-break-before: always;
}

.page-break-after {
    page-break-after: always;
}

.page-break-avoid {
    page-break-inside: avoid;
}

.print-only {
    display: none;
}

@media print {
    .print-only {
        display: block;
    }
}

.text-center {
    text-align: center;
}

.text-right {
    text-align: right;
}

.text-bold {
    font-weight: bold;
}

.mt-20 {
    margin-top: 20px;
}

.mb-20 {
    margin-bottom: 20px;
}

</style>

<!-- 
    USAGE NOTES:
    
    1. Paper Size Selection:
       - The @page { size: auto; } setting allows the browser to use
         whatever paper size is selected in the print dialog.
       - Users can choose Letter, Folio, Legal, or any other size.
    
    2. Content Width:
       - All containers are set to max-width: 7.5in
       - This ensures content fits within the printable area of
         8.5" wide paper with 0.5" margins on each side.
    
    3. Margins:
       - Standard 0.5" margins on all sides
       - Adjust in @page rule if needed
    
    4. Orientation:
       - Portrait is default
       - For landscape, add: @page { size: auto landscape; }
    
    5. Testing:
       - Always test print preview with different paper sizes
       - Check that content doesn't overflow page boundaries
       - Verify page breaks occur at logical points
-->
