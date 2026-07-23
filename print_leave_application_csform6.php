<!-- PRINT LEAVE APPLICATION CS FORM NO. 6 Modal -->
<!-- html2pdf.js library for direct PDF download -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
// Reuse the institutional logo from the sidebar as favicon on pages using this modal.
(function() {
  var logoImg = document.querySelector('.sidenav-header-inner img');
  if (!logoImg) {
    return;
  }

  var logoPath = logoImg.getAttribute('src');
  if (!logoPath) {
    return;
  }

  function upsertFavicon(relValue) {
    var faviconEl = document.querySelector('link[rel="' + relValue + '"]');
    if (!faviconEl) {
      faviconEl = document.createElement('link');
      faviconEl.rel = relValue;
      document.head.appendChild(faviconEl);
    }
    faviconEl.type = 'image/png';
    faviconEl.href = logoPath;
  }

  upsertFavicon('icon');
  upsertFavicon('shortcut icon');
})();
</script>

<div id="print_leave_app_csform6" tabindex="-1" role="dialog" aria-labelledby="printLeaveAppLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog modal-lg">
    <div class="modal-content">
    
      <div class="modal-header bg-success text-white">
        <h5 id="printLeaveAppLabel" class="modal-title">
          <i class="fa fa-print"></i> PRINT APPLICATION FOR LEAVE (CS Form No. 6)
        </h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close text-white">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <div class="modal-body p-2" id="print_leave_content">
        <!-- Print Preview Content Will Be Loaded Here -->
        <div class="text-center py-5">
          <i class="fa fa-spinner fa-spin fa-3x text-secondary"></i>
          <p class="mt-3 text-muted">Loading leave application...</p>
        </div>
      </div>
      
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-secondary">
          <i class="fa fa-times"></i> Close
        </button>
        <button type="button" onclick="openSignatoriesSettings()" class="btn btn-info">
          <i class="fa fa-cog"></i> Signatories Settings
        </button>
        <button type="button" onclick="openPrintPage()" class="btn btn-primary">
          <i class="fa fa-external-link"></i> Open Full Page
        </button>
      </div>
    </div>
  </div>
</div>
<!-- end PRINT LEAVE APPLICATION CS FORM NO. 6 Modal -->

<style>
/* CS Form No. 6 Layout - Matches Official CSC Template */
.csform6-container {
  font-family: Arial, Helvetica, sans-serif;
  font-size: 9px;
  line-height: 1.2;
  color: #000;
  background: white;
  padding: 10px;
  width: 7.5in;
  margin: 0 auto;
  box-sizing: border-box;
}

@media screen {
  .csform6-container {
    border: 1px solid #ccc;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    transform: scale(0.85);
    transform-origin: top center;
  }
}

@media print {
  .modal-header, .modal-footer { display: none !important; }
  .csform6-container { 
    width: 100% !important; 
    box-shadow: none !important; 
    border: none !important;
    transform: none !important;
  }
}

.cs6-maintable {
  width: 100%;
  border-collapse: collapse;
  border: 2px solid #000;
}

.cs6-maintable td {
  border: 1px solid #000;
  padding: 3px 5px;
  vertical-align: top;
  font-size: 9px;
}

.cs6-maintable .section-header {
  background-color: #d0d0d0;
  text-align: center;
  font-weight: bold;
  font-size: 10px;
  padding: 2px;
}

.cs6-chk {
  display: inline-block;
  width: 11px;
  height: 11px;
  border: 1px solid #000;
  margin-right: 4px;
  vertical-align: middle;
  text-align: center;
  line-height: 9px;
  font-size: 10px;
}

.cs6-chk.checked::after {
  content: '✓';
  font-weight: bold;
}

.cs6-underline {
  border-bottom: 1px solid #000;
  display: inline-block;
  min-width: 80px;
}

.cs6-sig {
  text-align: center;
  margin-top: 8px;
}

.cs6-sig-line {
  border-top: 1px solid #000;
  display: inline-block;
  min-width: 160px;
  margin-top: 14px;
  padding-top: 2px;
  font-weight: bold;
}

.cs6-credit-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 9px;
  margin: 3px 0;
}

.cs6-credit-table td, .cs6-credit-table th {
  border: 1px solid #000;
  padding: 2px 4px;
  text-align: center;
}

.cs6-credit-table th {
  font-weight: bold;
}

.cs6-leave-item {
  margin: 1px 0;
  line-height: 1.3;
}
</style>

<script>
// Wait for jQuery to be available
(function() {
    function initPrintLeaveScripts() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initPrintLeaveScripts, 50);
            return;
        }
        
        var $ = jQuery;

var currentLeaveAppId = null;
var currentLeaveData = null; // Store current leave data for PDF filename

function openPrintLeaveModal(leaveAppId) {
    currentLeaveAppId = leaveAppId;
    currentLeaveData = null;
    
    // Show modal
    $('#print_leave_app_csform6').modal('show');
    
    // Load leave application data
    loadLeaveApplicationForPrint(leaveAppId);
}

function loadLeaveApplicationForPrint(leaveAppId) {
    $.ajax({
        url: 'get_leave_application_print_data.php',
        type: 'POST',
        data: { leave_application_id: leaveAppId },
        dataType: 'json',
        success: function(response) {
            console.log('CS Form 6 Data:', response);
            if (response.success) {
                // Merge leave_application and personnel data for compatibility
                var combinedData = Object.assign({}, response.leave_application, response.personnel);
                currentLeaveData = combinedData; // Store for PDF filename
                renderCSForm6(combinedData, response.signatories, response.leave_credits);
            } else {
                $('#print_leave_content').html(
                    '<div class="alert alert-danger">' +
                    '<i class="fa fa-exclamation-triangle"></i> ' + response.message +
                    '</div>'
                );
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            console.error('Response:', xhr.responseText);
            $('#print_leave_content').html(
                '<div class="alert alert-danger">' +
                '<i class="fa fa-exclamation-triangle"></i> Error loading leave application: ' + error +
                '<br><small>' + xhr.responseText + '</small>' +
                '</div>'
            );
        }
    });
}

function renderCSForm6(data, signatories, leave_credits) {
    // Default leave_credits if not provided
    if (!leave_credits) {
        leave_credits = {
            vl_total_earned: 0, vl_this_application: 0, vl_balance: 0,
            sl_total_earned: 0, sl_this_application: 0, sl_balance: 0
        };
    }
    
    var leaveTypeLower = (data.leave_type || '').toLowerCase();
    
    // Determine which leave type checkbox to check
    function isLeaveType(keywords) {
        for (var i = 0; i < keywords.length; i++) {
            if (leaveTypeLower.includes(keywords[i])) return true;
        }
        return false;
    }
    
    var chk = function(checked) { return checked ? 'cs6-chk checked' : 'cs6-chk'; };
    
    var html = '<div class="csform6-container">';
    
    // Title
    html += '<div style="text-align: center; font-size: 14px; font-weight: bold; margin-bottom: 8px;">APPLICATION FOR LEAVE</div>';
    
    // Main Table
    html += '<table class="cs6-maintable">';
    
    // Row 1: Office/Dept and Name
    html += '<tr>';
    html += '<td style="width: 50%;" colspan="2"><strong>1. OFFICE/DEPARTMENT</strong><br><span style="margin-left: 20px;">' + (data.office_agency || '') + '</span></td>';
    html += '<td style="width: 50%;" colspan="2"><strong>2. NAME:</strong> <span style="margin-left: 30px;">(Last)</span> <span style="margin-left: 60px;">(First)</span> <span style="margin-left: 60px;">(Middle)</span><br>';
    html += '<span style="margin-left: 60px;">' + (data.lname || '') + '</span> <span style="margin-left: 30px;">' + (data.fname || '') + '</span> <span style="margin-left: 30px;">' + (data.mname || '') + '</span></td>';
    html += '</tr>';
    
    // Row 2: Date of Filing, Position, Salary
    html += '<tr>';
    html += '<td><strong>3. DATE OF FILING</strong><br><span class="cs6-underline" style="min-width: 120px;">' + formatDateShort(data.application_date) + '</span></td>';
    html += '<td><strong>4. POSITION</strong><br><span class="cs6-underline" style="min-width: 120px;">' + (data.position || '') + '</span></td>';
    html += '<td colspan="2"><strong>5. SALARY</strong><br><span class="cs6-underline" style="min-width: 120px;">' + (data.monthly_salary || '') + '</span></td>';
    html += '</tr>';
    
    // Row 3: Section Header - Details of Application
    html += '<tr><td colspan="4" class="section-header">6. DETAILS OF APPLICATION</td></tr>';
    
    // Row 4: Type of Leave (6.A) and Details of Leave (6.B)
    html += '<tr>';
    
    // 6.A TYPE OF LEAVE
    html += '<td colspan="2" style="vertical-align: top; width: 55%;">';
    html += '<strong>6.A TYPE OF LEAVE TO BE AVAILED OF</strong><br>';
    html += '<div class="cs6-leave-item"><span class="' + chk(isLeaveType(['vacation'])) + '"></span>Vacation Leave <span style="font-size: 8px;">(Sec. 51, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</span></div>';
    html += '<div class="cs6-leave-item"><span class="' + chk(isLeaveType(['mandatory', 'forced'])) + '"></span>Mandatory/Forced Leave <span style="font-size: 8px;">(Sec. 25, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</span></div>';
    html += '<div class="cs6-leave-item"><span class="' + chk(isLeaveType(['sick'])) + '"></span>Sick Leave <span style="font-size: 8px;">(Sec. 43, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</span></div>';
    html += '<div class="cs6-leave-item"><span class="' + chk(isLeaveType(['maternity'])) + '"></span>Maternity Leave <span style="font-size: 8px;">(R.A. No. 11210 / IRR issued by CSC, DOH, DOLE and SSS)</span></div>';
    html += '<div class="cs6-leave-item"><span class="' + chk(isLeaveType(['paternity'])) + '"></span>Paternity Leave <span style="font-size: 8px;">(R.A. No. 8187 / CSC MC No. 71, s. 1998, as amended)</span></div>';
    html += '<div class="cs6-leave-item"><span class="' + chk(isLeaveType(['special privilege'])) + '"></span>Special Privilege Leave <span style="font-size: 8px;">(Sec. 21, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</span></div>';
    html += '<div class="cs6-leave-item"><span class="' + chk(isLeaveType(['solo parent'])) + '"></span>Solo Parent Leave <span style="font-size: 8px;">(RA No. 8972 / CSC MC No. 8, s. 2004)</span></div>';
    html += '<div class="cs6-leave-item"><span class="' + chk(isLeaveType(['study'])) + '"></span>Study Leave <span style="font-size: 8px;">(Sec. 68, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</span></div>';
    html += '<div class="cs6-leave-item"><span class="' + chk(isLeaveType(['vawc', '10-day'])) + '"></span>10-Day VAWC Leave <span style="font-size: 8px;">(RA No. 9262 / CSC MC No. 15, s. 2005)</span></div>';
    html += '<div class="cs6-leave-item"><span class="' + chk(isLeaveType(['rehabilitation'])) + '"></span>Rehabilitation Privilege <span style="font-size: 8px;">(Sec. 55, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</span></div>';
    html += '<div class="cs6-leave-item"><span class="' + chk(isLeaveType(['women', 'gynecological'])) + '"></span>Special Leave Benefits for Women <span style="font-size: 8px;">(RA No. 9710 / CSC MC No. 25, s. 2010)</span></div>';
    html += '<div class="cs6-leave-item"><span class="' + chk(isLeaveType(['calamity', 'emergency'])) + '"></span>Special Emergency (Calamity) Leave <span style="font-size: 8px;">(CSC MC No. 2, s. 2012, as amended)</span></div>';
    html += '<div class="cs6-leave-item"><span class="' + chk(isLeaveType(['adoption'])) + '"></span>Adoption Leave <span style="font-size: 8px;">(R.A. No. 8552)</span></div>';
    html += '<div class="cs6-leave-item" style="margin-top: 3px;"><em>Others:</em></div>';
    html += '<div class="cs6-underline" style="width: 90%; margin-left: 20px; min-height: 14px;"></div>';
    html += '</td>';
    
    // 6.B DETAILS OF LEAVE
    html += '<td colspan="2" style="vertical-align: top; width: 45%;">';
    html += '<strong>6.B DETAILS OF LEAVE</strong><br><br>';
    
    // Auto-detect vacation location from leave type and vacation_details
    var isVacationPhilippines = isLeaveType(['vacation']) && leaveTypeLower.includes('local');
    var isVacationAbroad = isLeaveType(['vacation']) && leaveTypeLower.includes('abroad');
    var vacationText = data.vacation_details || '';
    
    // If vacation_details is filled but checkboxes not set by type, detect from text
    if (vacationText && !isVacationPhilippines && !isVacationAbroad) {
        var vacTextLower = vacationText.toLowerCase();
        if (vacTextLower.includes('abroad') || vacTextLower.includes('international') || 
            vacTextLower.includes('foreign') || /^[A-Z]{2,3}$/i.test(vacationText.trim())) {
            isVacationAbroad = true;
        } else if (vacTextLower.includes('philippines') || vacTextLower.includes('local') || 
                   vacTextLower.includes('domestic')) {
            isVacationPhilippines = true;
        }
    }
    
    html += '<div style="margin-bottom: 5px;"><em>In case of Vacation/Special Privilege Leave:</em>';
    html += '<div style="margin-left: 10px;"><span class="' + chk(isVacationPhilippines) + '"></span> Within the Philippines <span class="cs6-underline" style="min-width: 60px;">' + (isVacationPhilippines ? vacationText : '') + '</span></div>';
    html += '<div style="margin-left: 10px;"><span class="' + chk(isVacationAbroad) + '"></span> Abroad (Specify) <span class="cs6-underline" style="min-width: 60px;">' + (isVacationAbroad ? vacationText : '') + '</span></div></div>';
    
    // Auto-detect sick leave type from sick_details
    var sickText = data.sick_details || '';
    var isInHospital = false;
    var isOutPatient = false;
    if (sickText) {
        var sickTextLower = sickText.toLowerCase();
        if (sickTextLower.includes('hospital') || sickTextLower.includes('confined') || 
            sickTextLower.includes('admitted') || sickTextLower.includes('in-patient') || 
            sickTextLower.includes('inpatient')) {
            isInHospital = true;
        } else if (sickTextLower.includes('out') || sickTextLower.includes('patient') || 
                   sickTextLower.includes('outpatient') || sickTextLower.includes('out-patient')) {
            isOutPatient = true;
        } else if (isLeaveType(['sick'])) {
            // Default to out-patient if sick leave and text provided but not specified
            isOutPatient = true;
        }
    }
    
    html += '<div style="margin-bottom: 5px;"><em>In case of Sick Leave:</em>';
    html += '<div style="margin-left: 10px;"><span class="' + chk(isInHospital) + '"></span> In Hospital (Specify Illness) <span class="cs6-underline" style="min-width: 50px;">' + (isInHospital ? sickText : '') + '</span></div>';
    html += '<div style="margin-left: 10px;"><span class="' + chk(isOutPatient) + '"></span> Out Patient (Specify Illness) <span class="cs6-underline" style="min-width: 50px;">' + (isOutPatient ? sickText : '') + '</span></div></div>';
    
    html += '<div style="margin-bottom: 5px;"><em>In case of Special Leave Benefits for Women:</em>';
    html += '<div style="margin-left: 10px;">(Specify Illness) <span class="cs6-underline" style="min-width: 100px;"></span></div></div>';
    
    // Auto-detect study leave type from study_details
    var studyText = data.study_details || '';
    var isMastersDegree = false;
    var isBarBoardReview = false;
    if (studyText) {
        var studyTextLower = studyText.toLowerCase();
        if (studyTextLower.includes('master') || studyTextLower.includes('graduate') || 
            studyTextLower.includes('phd') || studyTextLower.includes('doctorate')) {
            isMastersDegree = true;
        } else if (studyTextLower.includes('bar') || studyTextLower.includes('board') || 
                   studyTextLower.includes('exam') || studyTextLower.includes('review')) {
            isBarBoardReview = true;
        }
    }
    
    html += '<div style="margin-bottom: 5px;"><em>In case of Study Leave:</em>';
    html += '<div style="margin-left: 10px;"><span class="' + chk(isMastersDegree) + '"></span> Completion of Master\'s Degree</div>';
    html += '<div style="margin-left: 10px;"><span class="' + chk(isBarBoardReview) + '"></span> BAR/Board Examination Review</div></div>';
    
    html += '<div><em>Other purpose:</em>';
    html += '<div style="margin-left: 10px;"><span class="' + chk(isLeaveType(['monetiz'])) + '"></span> Monetization of Leave Credits</div>';
    html += '<div style="margin-left: 10px;"><span class="' + chk(isLeaveType(['terminal'])) + '"></span> Terminal Leave</div></div>';
    html += '</td>';
    html += '</tr>';
    
    // Row 5: Number of Days (6.C) and Commutation (6.D)
    html += '<tr>';
    html += '<td colspan="2" style="vertical-align: top;">';
    html += '<strong>6.C NUMBER OF WORKING DAYS APPLIED FOR</strong><br>';
    html += '<div class="cs6-underline" style="width: 80%; text-align: center; margin: 5px auto;"><strong>' + (data.number_of_days || '') + '</strong></div>';
    html += '<strong>INCLUSIVE DATES</strong><br>';
    var inclusiveDates = formatDateShort(data.inclusive_date_from);
    if (data.inclusive_date_to && data.inclusive_date_to !== data.inclusive_date_from) {
        inclusiveDates += ' - ' + formatDateShort(data.inclusive_date_to);
    }
    html += '<div class="cs6-underline" style="width: 80%; text-align: center; margin: 5px auto;">' + inclusiveDates + '</div>';
    html += '</td>';
    html += '<td colspan="2" style="vertical-align: top;">';
    html += '<strong>6.D COMMUTATION</strong><br>';
    html += '<div style="margin: 5px 0;"><span class="cs6-chk"></span> Not Requested</div>';
    html += '<div style="margin: 5px 0;"><span class="cs6-chk checked"></span> Requested</div>';
    html += '<div class="cs6-sig" style="margin-top: 15px;"><div class="cs6-sig-line" style="min-width: 140px;"></div><br><span style="font-size: 8px;">(Signature of Applicant)</span></div>';
    html += '</td>';
    html += '</tr>';
    
    // Row 6: Section Header - Details of Action
    html += '<tr><td colspan="4" class="section-header">7. DETAILS OF ACTION ON APPLICATION</td></tr>';
    
    // Row 7: Certification (7.A) and Recommendation (7.B)
    html += '<tr>';
    html += '<td colspan="2" style="vertical-align: top;">';
    html += '<strong>7.A CERTIFICATION OF LEAVE CREDITS</strong><br>';
    html += '<div style="text-align: center; margin: 3px 0;">As of <span class="cs6-underline">' + formatDateShort(data.application_date) + '</span></div>';
    html += '<table class="cs6-credit-table"><tr><th></th><th>Vacation Leave</th><th>Sick Leave</th></tr>';
    html += '<tr><td style="text-align: left;"><em>Total Earned</em></td><td>' + parseFloat(leave_credits.vl_total_earned || 0).toFixed(3) + '</td><td>' + parseFloat(leave_credits.sl_total_earned || 0).toFixed(3) + '</td></tr>';
    html += '<tr><td style="text-align: left;"><em>Less this application</em></td><td>' + parseFloat(leave_credits.vl_this_application || 0).toFixed(3) + '</td><td>' + parseFloat(leave_credits.sl_this_application || 0).toFixed(3) + '</td></tr>';
    html += '<tr><td style="text-align: left;"><em>Balance</em></td><td>' + parseFloat(leave_credits.vl_balance || 0).toFixed(3) + '</td><td>' + parseFloat(leave_credits.sl_balance || 0).toFixed(3) + '</td></tr></table>';
    html += '<div class="cs6-sig"><div class="cs6-sig-line">' + (signatories.hrmo_name || '') + '</div><br><span style="font-size: 8px;">' + (signatories.hrmo_position || 'Human Resource Management Officer') + '</span></div>';
    html += '</td>';
    
    html += '<td colspan="2" style="vertical-align: top;">';
    html += '<strong>7.B RECOMMENDATION</strong><br>';
    html += '<div style="margin: 5px 0;"><span class="cs6-chk checked"></span> For approval</div>';
    html += '<div style="margin: 5px 0;"><span class="cs6-chk"></span> For disapproval due to <span class="cs6-underline" style="min-width: 100px;"></span></div>';
    html += '<div class="cs6-underline" style="width: 90%; margin-left: 20px;"></div>';
    html += '<div class="cs6-sig" style="margin-top: 15px;"><div class="cs6-sig-line">' + (signatories.recommending_name || '') + '</div><br><span style="font-size: 8px;">' + (signatories.recommending_position || '') + '</span></div>';
    html += '</td>';
    html += '</tr>';
    
    // Row 8: Approved For (7.C) and Disapproved (7.D)
    var daysWithPay = parseFloat(leave_credits.vl_this_application || 0) + parseFloat(leave_credits.sl_this_application || 0);
    var daysWithoutPay = parseFloat(leave_credits.vl_without_pay || 0) + parseFloat(leave_credits.sl_without_pay || 0);
    if (daysWithPay === 0 && daysWithoutPay === 0) daysWithPay = parseFloat(data.number_of_days || 0);
    
    html += '<tr>';
    html += '<td colspan="2" style="vertical-align: top;">';
    html += '<strong>7.C APPROVED FOR:</strong><br>';
    html += '<div style="margin: 3px 0;"><span class="cs6-underline" style="min-width: 40px; text-align: center;">' + (daysWithPay > 0 ? daysWithPay.toFixed(1) : '') + '</span> days with pay</div>';
    html += '<div style="margin: 3px 0;"><span class="cs6-underline" style="min-width: 40px; text-align: center;">' + (daysWithoutPay > 0 ? daysWithoutPay.toFixed(1) : '') + '</span> days without pay</div>';
    html += '<div style="margin: 3px 0;">others (Specify) <span class="cs6-underline" style="min-width: 60px;"></span></div>';
    html += '<div class="cs6-sig" style="margin-top: 10px;"><div class="cs6-sig-line">' + (signatories.approving_name || '') + '</div><br><span style="font-size: 8px;">' + (signatories.approving_position || '') + '</span></div>';
    html += '</td>';
    
    html += '<td colspan="2" style="vertical-align: top;">';
    html += '<strong>7.D DISAPPROVED DUE TO:</strong><br>';
    html += '<div class="cs6-underline" style="width: 90%; margin: 5px 0;"></div>';
    html += '<div class="cs6-underline" style="width: 90%; margin: 5px 0;"></div>';
    html += '<div style="margin-top: 10px;"><strong>Approved:</strong></div>';
    html += '<div class="cs6-sig" style="margin-top: 10px;"><div class="cs6-sig-line">' + (signatories.mayor_name || '') + '</div><br><span style="font-size: 8px;">' + (signatories.mayor_position || 'Municipal Mayor') + '</span></div>';
    html += '</td>';
    html += '</tr>';
    
    html += '</table>';
    html += '</div>';
    
    $('#print_leave_content').html(html);
}

function formatDateShort(dateString) {
    if (!dateString) return '';
    var date = new Date(dateString);
    var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    return months[date.getMonth()] + ' ' + date.getDate() + ', ' + date.getFullYear();
}

function formatDateUpper(dateString) {
    if (!dateString) return '';
    var date = new Date(dateString);
    var months = ['JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'];
    return months[date.getMonth()] + ' ' + date.getDate() + ', ' + date.getFullYear();
}


function downloadLeaveApplicationPDF() {
    var element = document.querySelector('.csform6-container');
    if (!element) {
        alert('Please wait for the form to load.');
        return;
    }
    
    // Temporarily remove transform for PDF capture
    element.style.transform = 'none';
    
    // Generate filename with date-time and employee name
    var now = new Date();
    var dateStr = now.getFullYear().toString() +
                  ('0' + (now.getMonth() + 1)).slice(-2) +
                  ('0' + now.getDate()).slice(-2) + '_' +
                  ('0' + now.getHours()).slice(-2) +
                  ('0' + now.getMinutes()).slice(-2);
    
    var empName = '';
    if (currentLeaveData) {
        var lname = (currentLeaveData.lname || '').toLowerCase().replace(/[^a-z0-9]/g, '');
        var fname = (currentLeaveData.fname || '').toLowerCase().replace(/[^a-z0-9]/g, '');
        empName = '_' + lname + '_' + fname;
    }
    
    var filename = 'CS_Form6_Leave_Application_' + dateStr + empName + '.pdf';
    
    // Show loading
    var btn = event.target.closest('button');
    var originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Generating PDF...';
    btn.disabled = true;
    
    // PDF options - Letter paper size
    var opt = {
        margin: [0.5, 0.5, 0.5, 0.5],
        filename: filename,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
            scale: 1,
            useCORS: true,
            logging: false,
            backgroundColor: '#ffffff'
        },
        jsPDF: { 
            unit: 'in', 
            format: 'letter', 
            orientation: 'portrait' 
        }
    };
    
    // Generate PDF directly from element
    html2pdf().set(opt).from(element).save().then(function() {
        btn.innerHTML = originalText;
        btn.disabled = false;
        element.style.transform = '';
    }).catch(function(err) {
        console.error('PDF generation error:', err);
        btn.innerHTML = originalText;
        btn.disabled = false;
        element.style.transform = '';
        alert('Error generating PDF. Please try again.');
    });
}

function printLeaveApplication() {
    window.print();
}

function openSignatoriesSettings() {
    $('#print_leave_app_csform6').modal('hide');
    $('#signatories_settings_modal').modal('show');
}

function openPrintPage() {
    if (currentLeaveAppId) {
        window.open('print_leave_application_csform6_page.php?id=' + currentLeaveAppId, '_blank');
    } else {
        alert('No leave application selected.');
    }
}

// Reopen print modal after settings is closed
$('#signatories_settings_modal').on('hidden.bs.modal', function() {
    if (currentLeaveAppId) {
        $('#print_leave_app_csform6').modal('show');
    }
});

    // Make functions globally available
    window.openPrintLeaveModal = openPrintLeaveModal;
    window.printLeaveApplication = printLeaveApplication;
    window.downloadLeaveApplicationPDF = downloadLeaveApplicationPDF;
    window.openSignatoriesSettings = openSignatoriesSettings;
    window.openPrintPage = openPrintPage;

    } // End initPrintLeaveScripts
    
    initPrintLeaveScripts();
})();
</script>
