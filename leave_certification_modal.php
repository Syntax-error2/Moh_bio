<?php
if (isset($has_leave_credits) && !$has_leave_credits) {
    return;
}
// Re-establish DB connection if closed by footer.php
if (!isset($conn) || $conn === null) {
    require('dbcon.php');
}

// Fetch signatories settings from DB if available
$sig_query = $conn->query("SELECT * FROM signatories_settings LIMIT 1");
$sig_row = $sig_query ? $sig_query->fetch(PDO::FETCH_ASSOC) : [];
$default_sig_name = !empty($sig_row['hrmo_name']) ? $sig_row['hrmo_name'] : 'GINALYN O. HERRADURA';
$default_sig_pos = !empty($sig_row['hrmo_position']) ? $sig_row['hrmo_position'] : 'Human Resource Management Officer';
$default_inst_name = !empty($sf_row['institution_name']) ? strtoupper($sf_row['institution_name']) : 'MUNICIPALITY OF HINOBA-AN';
$default_seal = file_exists('img/moh seal.jpg') ? 'img/moh seal.jpg' : (!empty($sf_row['logo']) ? 'img/' . $sf_row['logo'] : 'img/97763-moh-seal.jpg');

// Ensure certification variables are defined if not already computed
if (!isset($cert_vl)) {
    $cert_vl = floatval($staff_row['vl_balance']);
    $cert_sl = floatval($staff_row['sl_balance']);
    $cert_total = $cert_vl + $cert_sl;
    
    $cert_monthly_salary = floatval($staff_row['rate_per_day']);
    if ($cert_monthly_salary <= 0) {
        $sr_cert_q = $conn->query("SELECT salary FROM service_record WHERE personnel_id='".$staff_row['personnel_id']."' ORDER BY sr_id DESC LIMIT 1");
        if ($sr_cert_q && $sr_cert_r = $sr_cert_q->fetch()) {
            $raw_sal = preg_replace('/[^0-9.]/', '', $sr_cert_r['salary']);
            if (stripos($sr_cert_r['salary'], 'annum') !== false || stripos($sr_cert_r['salary'], '/pa') !== false) {
                $cert_monthly_salary = floatval($raw_sal) / 12;
            } else {
                $cert_monthly_salary = floatval($raw_sal);
            }
        }
    }
    $cert_amount_due = $cert_total * 0.0481927 * $cert_monthly_salary;
    
    $cert_des_name = '';
    $des_cert_q = $conn->query("SELECT des_name FROM designation WHERE des_id='".$staff_row['des_id']."'");
    if ($des_cert_q && $des_cert_r = $des_cert_q->fetch()) {
        $cert_des_name = $des_cert_r['des_name'];
    }
    $cert_emp_stat = '';
    $stat_cert_q = $conn->query("SELECT emp_stat_name FROM emp_status WHERE empStat_id='".$staff_row['empStat_id']."'");
    if ($stat_cert_q && $stat_cert_r = $stat_cert_q->fetch()) {
        $cert_emp_stat = strtolower($stat_cert_r['emp_stat_name']);
    }
}

$m_initial = (!empty($staff_row['mname']) && $staff_row['mname'] != '-') ? ' ' . strtoupper(substr(trim($staff_row['mname']), 0, 1)) . '.' : '';
$suffix = (!empty($staff_row['suffix']) && $staff_row['suffix'] != '-') ? ' ' . strtoupper(trim($staff_row['suffix'])) : '';
$cert_emp_fullname = strtoupper(trim($staff_row['fname']) . $m_initial . ' ' . trim($staff_row['lname']) . $suffix);
$cert_pronoun = (strtolower($staff_row['sex'] ?? '') == 'female') ? 'her' : 'his';
$cert_as_of_default = date('F t, Y');
$cert_given_default = date('jS \d\a\y \o\f F Y');
?>

<!-- Leave Certification Print Preview Modal -->
<div class="modal fade" id="certModal" tabindex="-1" role="dialog" aria-labelledby="certModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" style="max-width: 820px;">
    <div class="modal-content">
      <div class="modal-header d-flex justify-content-between align-items-center" style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
        <h5 class="modal-title font-weight-bold" id="certModalLabel" style="font-size: 1.1em; color: #333;">
          <i class="fa fa-print text-primary"></i> Leave Credit Certification &mdash; Print Preview
        </h5>
        <div>
          <button type="button" class="btn btn-outline-secondary btn-sm" data-toggle="collapse" data-target="#certSettingsPanel" aria-expanded="false" style="margin-right: 5px;">
            <i class="fa fa-sliders"></i> Edit Fields
          </button>
          <button type="button" class="btn btn-success btn-sm font-weight-bold" onclick="triggerCertPrint()" style="margin-right: 8px;">
            <i class="fa fa-print"></i> Print
          </button>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size: 1.5rem; line-height: 1;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      </div>
      
      <!-- Collapsible Edit Controls -->
      <div class="collapse" id="certSettingsPanel" style="background-color: #f1f4f8; border-bottom: 1px solid #d0d7de; padding: 15px 20px;">
        <form id="certPreviewForm" onsubmit="event.preventDefault(); triggerCertPrint();">
          <input type="hidden" id="cert_personnel_id" value="<?php echo $_GET['personnel_id']; ?>">
          <div class="row">
            <div class="col-md-6 form-group mb-2">
              <label class="small font-weight-bold mb-1">Employee Name:</label>
              <input type="text" id="cert_in_emp_name" class="form-control form-control-sm" value="<?php echo htmlspecialchars($cert_emp_fullname); ?>">
            </div>
            <div class="col-md-3 form-group mb-2">
              <label class="small font-weight-bold mb-1">Position / Designation:</label>
              <input type="text" id="cert_in_pos" class="form-control form-control-sm" value="<?php echo htmlspecialchars($cert_des_name ? strtoupper($cert_des_name) : 'EMPLOYEE'); ?>">
            </div>
            <div class="col-md-3 form-group mb-2">
              <label class="small font-weight-bold mb-1">Employment Status:</label>
              <input type="text" id="cert_in_stat" class="form-control form-control-sm" value="<?php echo htmlspecialchars($cert_emp_stat ? $cert_emp_stat : 'permanent'); ?>">
            </div>
            <div class="col-md-6 form-group mb-2">
              <label class="small font-weight-bold mb-1">As of Date:</label>
              <input type="text" id="cert_in_as_of" class="form-control form-control-sm" value="<?php echo $cert_as_of_default; ?>">
            </div>
            <div class="col-md-6 form-group mb-2">
              <label class="small font-weight-bold mb-1">Given Date:</label>
              <input type="text" id="cert_in_given" class="form-control form-control-sm" value="<?php echo $cert_given_default; ?>">
            </div>
            <div class="col-md-3 form-group mb-2">
              <label class="small font-weight-bold mb-1">VL Balance:</label>
              <input type="number" step="0.001" id="cert_in_vl" class="form-control form-control-sm" value="<?php echo number_format($cert_vl, 3, '.', ''); ?>">
            </div>
            <div class="col-md-3 form-group mb-2">
              <label class="small font-weight-bold mb-1">SL Balance:</label>
              <input type="number" step="0.001" id="cert_in_sl" class="form-control form-control-sm" value="<?php echo number_format($cert_sl, 3, '.', ''); ?>">
            </div>
            <div class="col-md-3 form-group mb-2">
              <label class="small font-weight-bold mb-1">Monthly Salary (P):</label>
              <input type="number" step="0.01" id="cert_in_salary" class="form-control form-control-sm" value="<?php echo number_format($cert_monthly_salary, 2, '.', ''); ?>">
            </div>
            <div class="col-md-3 form-group mb-2">
              <label class="small font-weight-bold mb-1">Pronoun:</label>
              <select id="cert_in_pronoun" class="form-control form-control-sm">
                <option value="his" <?php if($cert_pronoun == 'his') echo 'selected'; ?>>his (Male)</option>
                <option value="her" <?php if($cert_pronoun == 'her') echo 'selected'; ?>>her (Female)</option>
              </select>
            </div>
            <div class="col-md-6 form-group mb-2">
              <label class="small font-weight-bold mb-1">Signatory Name & Title:</label>
              <div class="input-group input-group-sm">
                <input type="text" id="cert_in_sig_name" class="form-control form-control-sm" value="<?php echo htmlspecialchars($default_sig_name); ?>" placeholder="Name">
                <input type="text" id="cert_in_sig_pos" class="form-control form-control-sm" value="<?php echo htmlspecialchars($default_sig_pos); ?>" placeholder="Title">
              </div>
            </div>
            <div class="col-md-6 form-group mb-2">
              <label class="small font-weight-bold mb-1">Guarantor Name (Signature Below):</label>
              <input type="text" id="cert_in_guarantor_name" class="form-control form-control-sm" value="<?php echo htmlspecialchars($cert_emp_fullname); ?>" placeholder="Employee Name">
            </div>
            <div class="col-md-12 form-group mb-0">
              <div class="d-flex align-items-center">
                <div class="form-check mr-3">
                  <input class="form-check-input" type="checkbox" id="cert_in_guarantee_chk" checked>
                  <label class="form-check-label small font-weight-bold" for="cert_in_guarantee_chk">Include Loan Guarantee Clause</label>
                </div>
                <input type="text" id="cert_in_guarantee_to" class="form-control form-control-sm" style="max-width: 380px;" value="Hinoba-an Multi-Purpose Cooperative" placeholder="Cooperative / Organization">
              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- Print Preview Canvas (Grey backdrop with white document sheet) -->
      <div class="modal-body p-0" style="background-color: #525659; overflow-y: auto; max-height: 72vh;">
        <div style="padding: 30px 20px;">
          <div class="cert-preview-sheet" style="background: #ffffff; color: #000000; width: 100%; max-width: 720px; margin: 0 auto; padding: 45px 55px; box-shadow: 0 4px 18px rgba(0,0,0,0.4); font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 14.5px; line-height: 1.85;">
            
            <!-- Letterhead Header -->
            <table style="width: 100%; border: none; margin-bottom: 25px; border-collapse: collapse;">
              <tr>
                <td style="width: 75px; vertical-align: middle; border: none; padding: 0;">
                  <img src="<?php echo $default_seal; ?>" style="width: 65px; height: 65px;">
                </td>
                <td style="vertical-align: middle; padding-left: 12px; border: none;">
                  <div style="font-size: 12.5px; line-height: 1.35;">
                    <div>Republic of the Philippines</div>
                    <div>Province of Negros Occidental</div>
                    <div style="font-weight: bold; font-size: 14px;"><?php echo $default_inst_name; ?></div>
                    <div style="font-weight: bold; font-size: 11.5px; color: #222;">HUMAN RESOURCE MANAGEMENT OFFICE</div>
                  </div>
                </td>
              </tr>
            </table>

            <!-- Document Title -->
            <div style="text-align: center; font-size: 21px; font-weight: 900; letter-spacing: 2px; margin: 35px 0 35px 0;">CERTIFICATION</div>

            <!-- Salutation -->
            <div style="font-weight: bold; font-size: 15px; margin-bottom: 22px;">TO WHOM IT MAY CONCERN:</div>

            <!-- Body Paragraph 1 -->
            <p style="text-indent: 40px; text-align: justify; margin-bottom: 24px;">
              This is to certify that as per record kept in this office <strong id="pv_emp_name"><?php echo htmlspecialchars($cert_emp_fullname); ?></strong>, <strong id="pv_emp_pos"><?php echo htmlspecialchars($cert_des_name ? strtoupper($cert_des_name) : 'EMPLOYEE'); ?></strong>, <span id="pv_emp_stat"><?php echo htmlspecialchars($cert_emp_stat ? $cert_emp_stat : 'permanent'); ?></span> employee has the following balance of leave credits as of <strong id="pv_as_of_date"><?php echo $cert_as_of_default; ?></strong>.
            </p>

            <!-- Leave Credits Table -->
            <div style="margin-left: 60px; margin-bottom: 24px;">
              <table style="border: none; border-collapse: collapse; font-size: 14.5px; line-height: 2.15;">
                <tr>
                  <td style="border: none; width: 190px; padding: 2px 0;">Vacation Leave</td>
                  <td style="border: none; width: 140px; text-align: right; font-weight: bold; padding: 2px 15px 2px 0;" id="pv_vl"><?php echo number_format($cert_vl, 3); ?></td>
                </tr>
                <tr>
                  <td style="border: none; padding: 2px 0;">Sick Leave</td>
                  <td style="border: none; text-align: right; font-weight: bold; padding: 2px 15px 2px 0;" id="pv_sl"><?php echo number_format($cert_sl, 3); ?></td>
                </tr>
                <tr>
                  <td style="border: none; font-weight: bold; padding: 3px 0;">Total</td>
                  <td style="border: none; text-align: right; font-weight: bold; padding: 3px 15px 3px 0;" id="pv_total"><?php echo number_format($cert_total, 3); ?></td>
                </tr>
                <tr>
                  <td style="border: none; font-weight: bold; padding: 4px 0;">Amount Due:</td>
                  <td style="border: none; text-align: right; font-weight: bold; text-decoration: underline; padding: 4px 15px 4px 0;" id="pv_amount_due">P <?php echo number_format($cert_amount_due, 2); ?></td>
                </tr>
              </table>
            </div>

            <!-- Body Paragraph 2 -->
            <p style="text-indent: 40px; text-align: justify; margin-bottom: 24px;">
              This certification is issued upon the request of the above-named person for whatever legal purpose it may serve <span id="pv_pronoun"><?php echo $cert_pronoun; ?></span> best.
            </p>

            <!-- Body Paragraph 3 -->
            <p style="text-indent: 40px; text-align: justify; margin-bottom: 45px;">
              Given this <span id="pv_given_date"><?php echo $cert_given_default; ?></span> at Hinoba-an, Negros Occidental.
            </p>

            <!-- Signatory Block -->
            <div style="width: 300px; margin-left: auto; text-align: center; margin-bottom: 45px;">
              <div style="font-weight: bold; font-size: 15.5px; text-transform: uppercase;" id="pv_sig_name"><?php echo htmlspecialchars($default_sig_name); ?></div>
              <div style="font-size: 13.5px;" id="pv_sig_pos"><?php echo htmlspecialchars($default_sig_pos); ?></div>
            </div>

            <!-- Loan Guarantee Clause -->
            <div id="pv_guarantee_wrap" style="margin-top: 30px;">
              <p style="text-indent: 40px; text-align: justify; font-size: 14px; margin-top: 0; margin-bottom: 0;">
                With the certification above issued by the Human Resource Development Office. I guarantee my leave credits stated above to <span id="pv_guarantee_to">Hinoba-an Multi-Purpose Cooperative</span> in consideration to my loan application. I further guarantee my leave credits earned until of my retirement.
              </p>
              <div style="width: 300px; margin-left: auto; text-align: center; margin-top: 40px;">
                <div style="font-weight: bold; font-size: 15.5px; text-transform: uppercase;" id="pv_guarantor_name"><?php echo htmlspecialchars($cert_emp_fullname); ?></div>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="modal-footer d-flex justify-content-between" style="background-color: #f8f9fa;">
        <span class="small text-muted"><i class="fa fa-info-circle"></i> Computed using official CSC Constant Factor: Total &times; 0.0481927 &times; Monthly Salary</span>
        <div>
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success btn-sm font-weight-bold" onclick="triggerCertPrint()">
            <i class="fa fa-print"></i> Print Document
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
#certModal {
    z-index: 1060 !important;
}
#certModal .modal-dialog {
    z-index: 1061 !important;
}
</style>

<script>
function updateCertLivePreview() {
    var inVl = document.getElementById('cert_in_vl');
    var inSl = document.getElementById('cert_in_sl');
    var inSal = document.getElementById('cert_in_salary');
    if (!inVl || !inSl || !inSal) return;

    var vlVal = parseFloat(inVl.value) || 0;
    var slVal = parseFloat(inSl.value) || 0;
    var salVal = parseFloat(inSal.value) || 0;
    var totVal = vlVal + slVal;
    var amtVal = totVal * 0.0481927 * salVal;

    var elVl = document.getElementById('pv_vl');
    var elSl = document.getElementById('pv_sl');
    var elTot = document.getElementById('pv_total');
    var elAmt = document.getElementById('pv_amount_due');
    if (elVl) elVl.innerText = vlVal.toFixed(3);
    if (elSl) elSl.innerText = slVal.toFixed(3);
    if (elTot) elTot.innerText = totVal.toFixed(3);
    if (elAmt) elAmt.innerText = 'P ' + amtVal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

    var inAsOf = document.getElementById('cert_in_as_of');
    var inGiven = document.getElementById('cert_in_given');
    var inPos = document.getElementById('cert_in_pos');
    var inStat = document.getElementById('cert_in_stat');
    var inPro = document.getElementById('cert_in_pronoun');
    var inSigN = document.getElementById('cert_in_sig_name');
    var inSigP = document.getElementById('cert_in_sig_pos');
    var inEmpN = document.getElementById('cert_in_emp_name');
    var inGuarN = document.getElementById('cert_in_guarantor_name');

    var elAsOf = document.getElementById('pv_as_of_date');
    var elGiven = document.getElementById('pv_given_date');
    var elPos = document.getElementById('pv_emp_pos');
    var elStat = document.getElementById('pv_emp_stat');
    var elPro = document.getElementById('pv_pronoun');
    var elSigN = document.getElementById('pv_sig_name');
    var elSigP = document.getElementById('pv_sig_pos');
    var elEmpN = document.getElementById('pv_emp_name');
    var elGuarN = document.getElementById('pv_guarantor_name');

    if (inAsOf && elAsOf) elAsOf.innerText = inAsOf.value;
    if (inGiven && elGiven) elGiven.innerText = inGiven.value;
    if (inPos && elPos) elPos.innerText = (inPos.value || '').toUpperCase();
    if (inStat && elStat) elStat.innerText = (inStat.value || '').toLowerCase();
    if (inPro && elPro) elPro.innerText = inPro.value;
    if (inSigN && elSigN) elSigN.innerText = (inSigN.value || '').toUpperCase();
    if (inSigP && elSigP) elSigP.innerText = (inSigP.value || '').toUpperCase();
    if (inEmpN && elEmpN) elEmpN.innerText = (inEmpN.value || '').toUpperCase();
    if (inGuarN && elGuarN) elGuarN.innerText = (inGuarN.value || '').toUpperCase();

    var chk = document.getElementById('cert_in_guarantee_chk');
    var wrap = document.getElementById('pv_guarantee_wrap');
    var inGTo = document.getElementById('cert_in_guarantee_to');
    var elGTo = document.getElementById('pv_guarantee_to');
    if (chk && wrap) {
        wrap.style.display = chk.checked ? 'block' : 'none';
        if (inGTo && elGTo) elGTo.innerText = inGTo.value;
    }
}

function triggerCertPrint() {
    var pidEl = document.getElementById('cert_personnel_id');
    var pid = pidEl ? pidEl.value : '';
    var inVl = document.getElementById('cert_in_vl');
    var inSl = document.getElementById('cert_in_sl');
    var inSal = document.getElementById('cert_in_salary');
    var vlVal = inVl ? (parseFloat(inVl.value) || 0) : 0;
    var slVal = inSl ? (parseFloat(inSl.value) || 0) : 0;
    var salVal = inSal ? (parseFloat(inSal.value) || 0) : 0;
    var totVal = vlVal + slVal;
    var amtVal = totVal * 0.0481927 * salVal;

    var inAsOf = document.getElementById('cert_in_as_of');
    var inGiven = document.getElementById('cert_in_given');
    var inPos = document.getElementById('cert_in_pos');
    var inStat = document.getElementById('cert_in_stat');
    var inPro = document.getElementById('cert_in_pronoun');
    var inSigN = document.getElementById('cert_in_sig_name');
    var inSigP = document.getElementById('cert_in_sig_pos');
    var inEmpN = document.getElementById('cert_in_emp_name');
    var inGuarN = document.getElementById('cert_in_guarantor_name');
    var chk = document.getElementById('cert_in_guarantee_chk');
    var inGTo = document.getElementById('cert_in_guarantee_to');

    var params = new URLSearchParams({
        personnel_id: pid,
        emp_name: inEmpN ? inEmpN.value : '',
        guarantor_name: inGuarN ? inGuarN.value : (inEmpN ? inEmpN.value : ''),
        as_of_date: inAsOf ? inAsOf.value : '',
        given_date: inGiven ? inGiven.value : '',
        vl_balance: vlVal.toFixed(3),
        sl_balance: slVal.toFixed(3),
        total_credits: totVal.toFixed(3),
        monthly_salary: salVal.toFixed(2),
        amount_due: amtVal.toFixed(2),
        emp_position: inPos ? inPos.value : '',
        emp_status: inStat ? inStat.value : '',
        pronoun: inPro ? inPro.value : 'his',
        signatory_name: inSigN ? inSigN.value : '<?php echo addslashes($default_sig_name); ?>',
        signatory_pos: inSigP ? inSigP.value : '<?php echo addslashes($default_sig_pos); ?>',
        include_guarantee: (chk && chk.checked) ? '1' : '0',
        guarantee_to: inGTo ? inGTo.value : 'Hinoba-an Multi-Purpose Cooperative'
    });

    window.open('print_leave_certification.php?' + params.toString(), '_blank');
}

function initCertModalListeners() {
    var inputs = [
        'cert_in_emp_name', 'cert_in_guarantor_name',
        'cert_in_vl', 'cert_in_sl', 'cert_in_salary', 'cert_in_as_of',
        'cert_in_given', 'cert_in_pos', 'cert_in_stat', 'cert_in_pronoun',
        'cert_in_sig_name', 'cert_in_sig_pos', 'cert_in_guarantee_chk', 'cert_in_guarantee_to'
    ];
    inputs.forEach(function(id) {
        var el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', updateCertLivePreview);
            el.addEventListener('change', updateCertLivePreview);
        }
    });

    var empIn = document.getElementById('cert_in_emp_name');
    var guarIn = document.getElementById('cert_in_guarantor_name');
    if (guarIn) {
        guarIn.addEventListener('input', function() {
            this.dataset.userEdited = '1';
        });
    }
    if (empIn && guarIn) {
        empIn.addEventListener('input', function() {
            if (guarIn.dataset.userEdited !== '1') {
                guarIn.value = this.value;
                var elGuarN = document.getElementById('pv_guarantor_name');
                if (elGuarN) elGuarN.innerText = (this.value || '').toUpperCase();
            }
        });
    }
}
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCertModalListeners);
} else {
    initCertModalListeners();
}
</script>
