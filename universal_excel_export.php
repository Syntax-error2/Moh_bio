<?php
// universal_excel_export.php
?>
<style>
@media print {
    .export-btn-container { display: none !important; }
}
.export-btn-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
}
.btn-export {
    background-color: #1d6f42;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    font-family: Arial, sans-serif;
    box-shadow: 0 4px 6px rgba(0,0,0,0.3);
}
.btn-export:hover {
    background-color: #144c2c;
}
</style>
<div class="export-btn-container no-print">
    <button type="button" id="universalExportBtn" class="btn-export" onclick="exportUniversalExcel(event)">
        Export to Excel
    </button>
</div>
<script>
function exportUniversalExcel(e) {
    e.preventDefault();
    
    var reportType = "Report";
    var office = "";
    var reportDate = "";
    
    // Determine Report Type
    var path = window.location.pathname.toLowerCase();
    if (path.indexOf('monthly') !== -1 || path.indexOf('daily') !== -1 || path.indexOf('csf48') !== -1 || path.indexOf('dtr') !== -1 || path.indexOf('leave_card') !== -1) {
        reportType = "DTR";
    } else {
        var h3 = document.querySelector('h3');
        if (h3) reportType = h3.innerText.trim();
        else if (document.title && document.title.indexOf('MOB - DTR') === -1) reportType = document.title.trim();
        else {
            var filePart = window.location.pathname.split('/').pop().replace('.php', '');
            reportType = filePart;
        }
    }
    
    // Find office and date
    function findValueForLabel(labelText) {
        var allEls = document.querySelectorAll('small, p, span, td, th, div');
        for (var i = 0; i < allEls.length; i++) {
            if (allEls[i].innerText && allEls[i].innerText.trim().toLowerCase() === labelText.toLowerCase()) {
                var parent = allEls[i].parentNode;
                var strong = parent.querySelector('strong, b');
                if (strong && strong.innerText.trim() !== '') return strong.innerText.trim();
                
                if (allEls[i].nextElementSibling && allEls[i].nextElementSibling.innerText.trim() !== '') {
                   return allEls[i].nextElementSibling.innerText.trim();
                }
            }
        }
        return "";
    }
    
    office = findValueForLabel("Department / Office") || findValueForLabel("Department/Office") || findValueForLabel("OFFICE");
    reportDate = findValueForLabel("Month Covered") || findValueForLabel("Date") || findValueForLabel("Date Covered") || findValueForLabel("For the month of");
    
    if (!reportDate) {
        var h4 = document.querySelector('h4');
        if (h4 && h4.innerText.toLowerCase().indexOf('date') !== -1) {
            reportDate = h4.innerText.replace(/Date/gi, '').trim();
        }
    }
    
    var finalParts = [];
    if (reportType) finalParts.push(reportType);
    if (office) finalParts.push(office);
    if (reportDate) finalParts.push(reportDate);
    
    var reportName = finalParts.join("_").replace(/[^a-zA-Z0-9 \-]/g, "").trim().replace(/\s+/g, "_");
    if (!reportName || reportName === "MOH_-_DTR_v_10") {
        reportName = "Exported_Report";
    }
    
    var filename = reportName + ".xls";
    
    var clone = document.body.cloneNode(true);
    
    var images = clone.querySelectorAll('img');
    for (var i = 0; i < images.length; i++) {
        images[i].setAttribute('src', images[i].src);
    }
    
    var removeSelectors = ['.export-btn-container', 'script', 'iframe', '.dataTables_filter', '.dataTables_length', '.dt-buttons', '.dataTables_info', '.dataTables_paginate', '.no-print'];
    removeSelectors.forEach(function(sel) {
        var elements = clone.querySelectorAll(sel);
        elements.forEach(function(el) {
            el.parentNode.removeChild(el);
        });
    });
    
    // REWRITE HEADER specifically for Excel
    var clonedHeaders = clone.querySelectorAll('.letterHeadTable');
    clonedHeaders.forEach(function(clonedHeader) {
        var tds = clonedHeader.querySelectorAll('td');
        if (tds.length >= 4) {
            var imgEl = tds[0].querySelector('img');
            var logoSrc = imgEl ? imgEl.src : '';
            var schoolName = tds[1].textContent.trim();
            var hrmoText = tds[2].textContent.trim();
            var addressText = tds[3].textContent.trim();
            
            var excelHeaderHTML = '<tr style="height: 45px;">';
            excelHeaderHTML += '<td colspan="5" style="border: none; background-color: white;">&nbsp;</td>';
            excelHeaderHTML += '<td colspan="1" style="border: none; background-color: white; padding: 0px;"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" width="120" height="1" /><img width="40" height="40" src="' + logoSrc + '" /></td>';
            excelHeaderHTML += '<td colspan="9" style="border: none; background-color: white;">&nbsp;</td>';
            excelHeaderHTML += '</tr>';
            excelHeaderHTML += '<tr><td colspan="15" style="font-size: 22px; border: none; background-color: white; padding: 0px; font-weight: bold; text-align: center;"> ' + schoolName + '</td></tr>';
            excelHeaderHTML += '<tr><td colspan="15" style="border: none; font-size: 18px; background-color: white; padding: 0px; font-weight: bold; text-align: center;">' + hrmoText + '</td></tr>';
            excelHeaderHTML += '<tr><td colspan="15" style="border: none; font-size: 14px; background-color: white; padding: 0px; font-weight: bold; text-align: center;">' + addressText + '</td></tr>';
            
            clonedHeader.innerHTML = excelHeaderHTML;
        }
    });
    
    var styles = '';
    var styleTags = document.querySelectorAll('style');
    styleTags.forEach(function(tag) {
        styles += tag.outerHTML;
    });
    styles += '<style>table { border-collapse: collapse; } table td, table th { border: 1px solid #ddd; padding: 4px; }</style>';
    
    var htmlContent = clone.innerHTML;
    
    var excelTemplate = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    excelTemplate += '<head>';
    excelTemplate += '<meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">';
    excelTemplate += '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Report Data</x:Name><x:WorksheetOptions><x:FitToPage/><x:DisplayGridlines/><x:Print><x:ValidPrinterInfo/><x:PaperSizeIndex>9</x:PaperSizeIndex><x:FitWidth>1</x:FitWidth><x:FitHeight>100</x:FitHeight></x:Print></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
    excelTemplate += styles;
    excelTemplate += '</head><body>';
    excelTemplate += htmlContent;
    excelTemplate += '</body></html>';
    
    var blob = new Blob([excelTemplate], { type: 'application/vnd.ms-excel' });
    
    if (window.navigator && window.navigator.msSaveOrOpenBlob) { 
        window.navigator.msSaveOrOpenBlob(blob, filename);
    } else {
        var url = window.URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }
}
</script>
