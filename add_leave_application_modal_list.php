<!-- ADD LEAVE APPLICATION Modal (CS Form No. 6) - For Personnel List -->
<div id="add_leave_application" tabindex="-1" role="dialog" aria-labelledby="addLeaveModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog modal-lg">
    <div class="modal-content">
    
    <form action="save_leave_application.php" method="POST" id="leave_application_form">
    
    <input name="personnel_id" id="leave_app_personnel_id" type="hidden" />
    <input name="do_id" value="<?php echo isset($_GET['dept']) ? $_GET['dept'] : ''; ?>" type="hidden" />
    <input name="redirect_url" id="leave_app_redirect_url" type="hidden" value="" />
    
      <div class="modal-header bg-primary text-white">
        <h5 id="addLeaveModalLabel" class="modal-title">
          <i class="fa fa-file-text"></i> APPLICATION FOR LEAVE (CS Form No. 6)
        </h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close text-white">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <div class="modal-body">
        
        <div class="alert alert-info">
          <i class="fa fa-info-circle"></i> <strong>Personnel:</strong> <span id="leave_app_personnel_name">Select from list</span>
        </div>
        
        <!-- OFFICE/AGENCY/DEPARTMENT -->
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">Office/Agency/Department</label>
          <div class="col-sm-9">
            <input name="office_agency" type="text" class="form-control" placeholder="e.g., Department of Health - Region X" value="Department of Health - Region X" required />
          </div>
        </div>

        <hr class="my-3" />
        
        <!-- APPLICATION DATE -->
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">Date of Filing</label>
          <div class="col-sm-9">
            <input name="application_date" type="date" value="<?php echo date('Y-m-d'); ?>" class="form-control" required />
          </div>
        </div>
        
        <div class="alert alert-secondary">
          <strong><i class="fa fa-info-circle"></i> DETAILS OF LEAVE APPLICATION</strong>
        </div>
        
        <!-- TYPE OF LEAVE -->
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">Type of Leave</label>
          <div class="col-sm-9">
            <select name="leave_type" id="leave_type_list" class="form-control" required>
              <option value="">-- Select Leave Type --</option>
              <optgroup label="Vacation Leave">
                <option value="Vacation Leave">Vacation Leave</option>
                <option value="Vacation Leave - Within Philippines">Vacation Leave - Within Philippines</option>
                <option value="Vacation Leave - Abroad">Vacation Leave - Abroad</option>
              </optgroup>
              <optgroup label="Mandatory/Forced Leave">
                <option value="Mandatory/Forced Leave">Mandatory/Forced Leave</option>
              </optgroup>
              <optgroup label="Sick Leave">
                <option value="Sick Leave">Sick Leave</option>
                <option value="Sick Leave - In Hospital">Sick Leave - In Hospital</option>
                <option value="Sick Leave - Out Patient">Sick Leave - Out Patient</option>
              </optgroup>
              <optgroup label="Maternity Leave">
                <option value="Maternity Leave">Maternity Leave</option>
              </optgroup>
              <optgroup label="Paternity Leave">
                <option value="Paternity Leave">Paternity Leave</option>
              </optgroup>
              <optgroup label="Special Privilege Leave">
                <option value="Special Privilege Leave">Special Privilege Leave</option>
              </optgroup>
              <optgroup label="Solo Parent Leave">
                <option value="Solo Parent Leave">Solo Parent Leave</option>
              </optgroup>
              <optgroup label="Study Leave">
                <option value="Study Leave">Study Leave</option>
                <option value="Study Leave - Completion of Master's Degree">Study Leave - Completion of Master's Degree</option>
                <option value="Study Leave - BAR/Board Examination Review">Study Leave - BAR/Board Examination Review</option>
              </optgroup>
              <optgroup label="Other Purpose">
                <option value="10-Day VAWC Leave">10-Day VAWC Leave</option>
                <option value="Rehabilitation Privilege">Rehabilitation Privilege</option>
                <option value="Special Leave Benefits for Women">Special Leave Benefits for Women</option>
                <option value="Special Emergency (Calamity) Leave">Special Emergency (Calamity) Leave</option>
                <option value="Adoption Leave">Adoption Leave</option>
                <option value="Terminal Leave">Terminal Leave</option>
                <option value="Monetized Leave">Monetized Leave</option>
                <option value="Others">Others (Please Specify)</option>
              </optgroup>
            </select>
          </div>
        </div>
        
        <!-- OTHERS SPECIFICATION -->
        <div class="form-group row" id="other_leave_spec_group_list" style="display: none;">
          <label class="col-sm-3 form-control-label text-bold">Specify Other Leave Type</label>
          <div class="col-sm-9">
            <input name="other_leave_specification" type="text" class="form-control" placeholder="Please specify..." />
          </div>
        </div>
        
        <!-- VACATION LEAVE DETAILS -->
        <div class="form-group row" id="vacation_details_group_list" style="display: none;">
          <label class="col-sm-3 form-control-label text-bold">Where will you spend vacation?</label>
          <div class="col-sm-9">
            <input name="vacation_details" type="text" class="form-control" placeholder="e.g., Cebu City, Philippines" />
          </div>
        </div>
        
        <!-- SICK LEAVE DETAILS -->
        <div class="form-group row" id="sick_details_group_list" style="display: none;">
          <label class="col-sm-3 form-control-label text-bold">Illness Details</label>
          <div class="col-sm-9">
            <input name="sick_details" type="text" class="form-control" placeholder="e.g., Hospital name or specify illness" />
          </div>
        </div>
        
        <!-- STUDY LEAVE DETAILS -->
        <div class="form-group row" id="study_details_group_list" style="display: none;">
          <label class="col-sm-3 form-control-label text-bold">Study Leave Details</label>
          <div class="col-sm-9">
            <input name="study_details" type="text" class="form-control" placeholder="e.g., University name, Course" />
          </div>
        </div>
        
        <!-- INCLUSIVE DATES - Multiple Date Ranges Support -->
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">
            Inclusive Dates
            <br><small class="text-muted font-weight-normal">Multiple ranges allowed</small>
          </label>
          <div class="col-sm-9">
            <!-- Hidden field to store JSON array of date ranges -->
            <input type="hidden" name="inclusive_dates_json" id="inclusive_dates_json_list" value="" />
            <!-- Legacy fields for backward compatibility (stores first range) -->
            <input type="hidden" name="inclusive_date_from" id="date_from_list" value="" />
            <input type="hidden" name="inclusive_date_to" id="date_to_list" value="" />
            
            <!-- Container for date range entries -->
            <div id="date_ranges_container_list">
              <!-- Date Range 1 (Default) -->
              <div class="date-range-entry mb-2" data-index="0">
                <div class="card">
                  <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                      <div class="col-5">
                        <label class="small mb-1">From</label>
                        <input type="date" class="form-control form-control-sm date-from-input" required />
                      </div>
                      <div class="col-5">
                        <label class="small mb-1">To</label>
                        <input type="date" class="form-control form-control-sm date-to-input" required />
                      </div>
                      <div class="col-2 text-center">
                        <label class="small mb-1 d-block">&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-sm remove-date-range" title="Remove this date range" style="display: none;">
                          <i class="fa fa-times"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Add More Date Range Button -->
            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add_date_range_btn_list">
              <i class="fa fa-plus"></i> Add Another Date Range
            </button>
            <small class="form-text text-muted">
              <i class="fa fa-info-circle"></i> Use multiple date ranges for non-consecutive leave days (e.g., excluding weekends).
            </small>
          </div>
        </div>
        
        <!-- NUMBER OF WORKING DAYS APPLIED FOR -->
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">Number of Working Days</label>
          <div class="col-sm-9">
            <input name="number_of_days" id="number_of_days_list" type="number" step="0.5" min="0.5" class="form-control" placeholder="e.g., 5" required />
            <small class="form-text text-muted">You can enter half days (e.g., 2.5). Total days for all date ranges above.</small>
          </div>
        </div>
        
        <!-- COMMUTATION -->
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">Commutation</label>
          <div class="col-sm-9">
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="commutation_requested_list" name="commutation" class="custom-control-input" value="requested" checked />
              <label class="custom-control-label" for="commutation_requested_list">Requested</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="commutation_not_requested_list" name="commutation" class="custom-control-input" value="not_requested" />
              <label class="custom-control-label" for="commutation_not_requested_list">Not Requested</label>
            </div>
          </div>
        </div>
        
        <hr class="my-3" />
        
        <div class="alert alert-secondary">
          <strong><i class="fa fa-user"></i> CERTIFICATION OF LEAVE CREDITS (Optional)</strong>
        </div>
        
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">As of (Date)</label>
          <div class="col-sm-9">
            <input name="as_of_date" type="date" value="<?php echo date('Y-m-d'); ?>" class="form-control" />
          </div>
        </div>
        
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">Total Earned (VL/SL)</label>
          <div class="col-sm-9">
            <div class="row">
              <div class="col-6">
                <input name="total_earned_vl" id="total_earned_vl_list" type="number" step="0.001" min="0" class="form-control" placeholder="0.000" readonly />
                <small class="form-text">Vacation Leave Balance (auto-fetched)</small>
              </div>
              <div class="col-6">
                <input name="total_earned_sl" id="total_earned_sl_list" type="number" step="0.001" min="0" class="form-control" placeholder="0.000" readonly />
                <small class="form-text">Sick Leave Balance (auto-fetched)</small>
              </div>
            </div>
          </div>
        </div>
        
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">Less This Application</label>
          <div class="col-sm-9">
            
            <!-- Vacation Leave Deductions -->
            <div class="card mb-2">
              <div class="card-body py-2">
                <strong class="text-primary">Vacation Leave</strong>
                <div class="row mt-2">
                  <div class="col-6">
                    <input name="less_application_vl" id="less_application_vl_list" type="number" step="0.001" min="0" class="form-control" placeholder="0.000" />
                    <small class="form-text">
                      <i class="fa fa-money"></i> With Pay
                      <a href="javascript:void(0)" class="badge badge-info ml-1" onclick="transferToWithoutPay('vl')" title="Transfer to Without Pay">
                        <i class="fa fa-arrow-right"></i> Move
                      </a>
                    </small>
                  </div>
                  <div class="col-6">
                    <input name="less_application_vl_without_pay" id="less_application_vl_without_pay_list" type="number" step="0.001" min="0" class="form-control" placeholder="0.000" />
                    <small class="form-text">
                      <i class="fa fa-ban"></i> Without Pay
                      <a href="javascript:void(0)" class="badge badge-warning ml-1" onclick="transferToWithPay('vl')" title="Transfer to With Pay">
                        <i class="fa fa-arrow-left"></i> Move
                      </a>
                    </small>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Sick Leave Deductions -->
            <div class="card mb-2">
              <div class="card-body py-2">
                <strong class="text-info">Sick Leave</strong>
                <div class="row mt-2">
                  <div class="col-6">
                    <input name="less_application_sl" id="less_application_sl_list" type="number" step="0.001" min="0" class="form-control" placeholder="0.000" />
                    <small class="form-text">
                      <i class="fa fa-money"></i> With Pay
                      <a href="javascript:void(0)" class="badge badge-info ml-1" onclick="transferToWithoutPay('sl')" title="Transfer to Without Pay">
                        <i class="fa fa-arrow-right"></i> Move
                      </a>
                    </small>
                  </div>
                  <div class="col-6">
                    <input name="less_application_sl_without_pay" id="less_application_sl_without_pay_list" type="number" step="0.001" min="0" class="form-control" placeholder="0.000" />
                    <small class="form-text">
                      <i class="fa fa-ban"></i> Without Pay
                      <a href="javascript:void(0)" class="badge badge-warning ml-1" onclick="transferToWithPay('sl')" title="Transfer to With Pay">
                        <i class="fa fa-arrow-left"></i> Move
                      </a>
                    </small>
                  </div>
                </div>
              </div>
            </div>
            
            <small class="text-muted">
              <i class="fa fa-info-circle"></i> <strong>With Pay</strong> deducts from leave credits. <strong>Without Pay</strong> does not deduct credits but records the absence.
              <br/><strong>Tip:</strong> Click <span class="badge badge-info"><i class="fa fa-arrow-right"></i> Move</span> to quickly transfer values between With Pay ↔ Without Pay.
            </small>
            
          </div>
        </div>
        
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">Balance (VL/SL)</label>
          <div class="col-sm-9">
            <div class="row">
              <div class="col-6">
                <input name="balance_vl" id="balance_vl_list" type="number" step="0.001" min="0" class="form-control" placeholder="0.000" readonly />
                <small class="form-text">VL Balance</small>
              </div>
              <div class="col-6">
                <input name="balance_sl" id="balance_sl_list" type="number" step="0.001" min="0" class="form-control" placeholder="0.000" readonly />
                <small class="form-text">SL Balance</small>
              </div>
            </div>
          </div>
        </div>
        
      </div>
      
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-secondary">Cancel</button>
        <button name="save_leave_application" type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Submit Application</button>
      </div>
      </form>
    </div>
  </div>
</div>
<!-- end ADD LEAVE APPLICATION Modal -->

<script>
// Wait for jQuery to be available
(function() {
    function initLeaveAppListScripts() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initLeaveAppListScripts, 50);
            return;
        }
        
        var $ = jQuery;
        
// Set personnel ID for leave application
var currentPersonnelId = null;
var currentPersonnelName = '';

function setPersonnelForLeaveApp(personnelId, personnelName, deptName) {
    currentPersonnelId = personnelId;
    currentPersonnelName = personnelName || 'Selected Personnel';
    $('#leave_app_personnel_id').val(personnelId);
    $('#leave_app_personnel_name').text(currentPersonnelName);
    
    // Set office/agency field if deptName is provided
    if (deptName) {
        var officeField = $('input[name="office_agency"]');
        if (officeField.length) {
            officeField.val(deptName);
        }
    }
    
    // Set redirect URL based on current page
    var redirectField = $('#leave_app_redirect_url');
    if (redirectField.length && personnelId) {
        // Try to get dept from current URL or use empty string
        var urlParams = new URLSearchParams(window.location.search);
        var deptId = urlParams.get('dept') || '';
        
        // Detect which page we're on and set appropriate redirect
        var currentPath = window.location.pathname;
        var redirectPage = 'leave_card.php'; // Default to leave_card
        
        if (currentPath.includes('leave_application.php')) {
            redirectPage = 'leave_application.php';
        } else if (currentPath.includes('leave_card.php')) {
            redirectPage = 'leave_card.php';
        }
        
        redirectField.val(redirectPage + '?dept=' + encodeURIComponent(deptId) + '&personnel_id=' + personnelId);
    }
    
    // Auto-fetch leave card balances for this personnel
    fetchLeaveCardBalances(personnelId);
}

// Fetch current leave card balances from server
function fetchLeaveCardBalances(personnelId) {
    if (!personnelId) return;
    
    $.ajax({
        url: 'get_leave_card_balance.php',
        type: 'POST',
        data: { personnel_id: personnelId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Populate Total Earned fields with CURRENT BALANCE (not total accumulated)
                // This is the actual available credits after previous usage
                $('#total_earned_vl_list').val(parseFloat(response.vl_balance || 0).toFixed(3));
                $('#total_earned_sl_list').val(parseFloat(response.sl_balance || 0).toFixed(3));
                
                // Show info message with detailed breakdown
                if (response.vl_earned > 0 || response.sl_earned > 0) {
                    var msg = '<i class="fa fa-check-circle"></i> <strong>Leave Credits Status:</strong><br/>' +
                              '<small>VL Earned: ' + parseFloat(response.vl_earned || 0).toFixed(3) + 
                              ' | Used: ' + parseFloat(response.vl_used || 0).toFixed(3) + 
                              ' | <strong class="text-primary">Balance: ' + parseFloat(response.vl_balance || 0).toFixed(3) + '</strong></small><br/>' +
                              '<small>SL Earned: ' + parseFloat(response.sl_earned || 0).toFixed(3) + 
                              ' | Used: ' + parseFloat(response.sl_used || 0).toFixed(3) + 
                              ' | <strong class="text-info">Balance: ' + parseFloat(response.sl_balance || 0).toFixed(3) + '</strong></small>';
                    
                    if ($('.leave-credits-info').length === 0) {
                        $('.alert-secondary:contains("CERTIFICATION OF LEAVE CREDITS")').after(
                            '<div class="alert alert-success leave-credits-info">' + msg + '</div>'
                        );
                    } else {
                        $('.leave-credits-info').html(msg);
                    }
                }
                
                // Recalculate balances
                calculateBalanceList();
            }
        },
        error: function(xhr, status, error) {
            console.error('Could not fetch leave card balances:', error);
            // Show error message to user
            if ($('.leave-credits-error').length === 0) {
                $('.alert-secondary:contains("CERTIFICATION OF LEAVE CREDITS")').after(
                    '<div class="alert alert-warning leave-credits-error">' +
                    '<i class="fa fa-exclamation-triangle"></i> <strong>Note:</strong> Could not load leave credits automatically. Please enter manually.</div>'
                );
            }
        }
    });
}

// Special leave types that don't deduct from VL/SL
var specialLeaveTypes = [
    'Maternity Leave',
    'Paternity Leave',
    'Special Privilege Leave',
    'Solo Parent Leave',
    'Study Leave',
    'Study Leave - Completion of Master\'s Degree',
    'Study Leave - BAR/Board Examination Review',
    '10-Day VAWC Leave',
    'Rehabilitation Privilege',
    'Special Leave Benefits for Women',
    'Special Emergency (Calamity) Leave',
    'Adoption Leave'
];

$(document).ready(function() {
    // Show/hide conditional fields based on leave type
    $('#leave_type_list').on('change', function() {
        var leaveType = $(this).val();
        
        // Hide all conditional groups
        $('#other_leave_spec_group_list').hide();
        $('#vacation_details_group_list').hide();
        $('#sick_details_group_list').hide();
        $('#study_details_group_list').hide();
        
        // Remove all notices
        $('.special-leave-notice').remove();
        $('.others-leave-notice').remove();
        
        // Show relevant group
        if (leaveType === 'Others') {
            $('#other_leave_spec_group_list').show();
        }
        
        if (leaveType.includes('Vacation Leave')) {
            $('#vacation_details_group_list').show();
        }
        
        if (leaveType.includes('Sick Leave')) {
            $('#sick_details_group_list').show();
        }
        
        if (leaveType.includes('Study Leave')) {
            $('#study_details_group_list').show();
        }
        
        // Auto-calculate deduction based on leave type
        autoCalculateLeaveDeduction();
    });
    
    // Auto-calculate deduction when number of days changes
    $('#number_of_days_list').on('input', function() {
        autoCalculateLeaveDeduction();
    });
    
    // Auto-calculate deduction based on leave type and number of days
    function autoCalculateLeaveDeduction() {
        var leaveType = $('#leave_type_list').val();
        var numberOfDays = parseFloat($('#number_of_days_list').val()) || 0;
        
        if (!leaveType || numberOfDays === 0) {
            $('#less_application_vl_list').val('0.000');
            $('#less_application_vl_without_pay_list').val('0.000');
            $('#less_application_sl_list').val('0.000');
            $('#less_application_sl_without_pay_list').val('0.000');
            calculateBalanceList();
            return;
        }
        
        // Check if special leave (no deduction)
        if (specialLeaveTypes.indexOf(leaveType) !== -1) {
            $('#less_application_vl_list').val('0.000');
            $('#less_application_vl_without_pay_list').val('0.000');
            $('#less_application_sl_list').val('0.000');
            $('#less_application_sl_without_pay_list').val('0.000');
            
            // Show notice
            if ($('.special-leave-notice').length === 0) {
                $('#number_of_days_list').after(
                    '<small class="special-leave-notice text-info d-block mt-1">' +
                    '<i class="fa fa-info-circle"></i> <strong>Special Leave:</strong> No credits will be deducted</small>'
                );
            }
        } else {
            // Remove special leave notice
            $('.special-leave-notice').remove();
            
            // Determine which type to deduct (default to WITH PAY)
            if (leaveType.includes('Vacation') || leaveType.includes('Forced')) {
                $('#less_application_vl_list').val(numberOfDays.toFixed(3));
                $('#less_application_vl_without_pay_list').val('0.000');
                $('#less_application_sl_list').val('0.000');
                $('#less_application_sl_without_pay_list').val('0.000');
            } else if (leaveType.includes('Sick')) {
                $('#less_application_vl_list').val('0.000');
                $('#less_application_vl_without_pay_list').val('0.000');
                $('#less_application_sl_list').val(numberOfDays.toFixed(3));
                $('#less_application_sl_without_pay_list').val('0.000');
            } else if (leaveType === 'Others') {
                // For "Others" leave type, default to VL deduction (most common case)
                // User can manually adjust if needed
                $('#less_application_vl_list').val(numberOfDays.toFixed(3));
                $('#less_application_vl_without_pay_list').val('0.000');
                $('#less_application_sl_list').val('0.000');
                $('#less_application_sl_without_pay_list').val('0.000');
                
                // Show notice that user can adjust
                if ($('.others-leave-notice').length === 0) {
                    $('#number_of_days_list').after(
                        '<small class="others-leave-notice text-warning d-block mt-1">' +
                        '<i class="fa fa-exclamation-circle"></i> <strong>Others Leave:</strong> Defaulted to VL deduction. You can manually adjust the credits below if needed.</small>'
                    );
                }
            } else {
                // Default to no deduction for unknown types
                $('#less_application_vl_list').val('0.000');
                $('#less_application_vl_without_pay_list').val('0.000');
                $('#less_application_sl_list').val('0.000');
                $('#less_application_sl_without_pay_list').val('0.000');
            }
        }
        
        // Recalculate balance
        calculateBalanceList();
    }
    
    // Auto-calculate balance (only WITH PAY deducts from credits)
    function calculateBalanceList() {
        // VL Balance: Current balance - with pay deduction = Remaining balance
        // NOTE: #total_earned_vl_list actually contains CURRENT BALANCE (vl_balance from server)
        var vlCurrentBalance = parseFloat($('#total_earned_vl_list').val()) || 0;
        var vlApplicationWithPay = parseFloat($('#less_application_vl_list').val()) || 0;
        var vlRemainingBalance = vlCurrentBalance - vlApplicationWithPay;
        $('#balance_vl_list').val(vlRemainingBalance.toFixed(3));
        
        // Check for insufficient balance
        if (vlRemainingBalance < 0) {
            $('#balance_vl_list').addClass('is-invalid');
            if ($('.vl-insufficient-notice').length === 0) {
                $('#balance_vl_list').after(
                    '<small class="vl-insufficient-notice text-danger d-block">' +
                    '<i class="fa fa-exclamation-triangle"></i> Insufficient VL credits! (Requesting ' + 
                    vlApplicationWithPay.toFixed(3) + ' but only ' + vlCurrentBalance.toFixed(3) + ' available)</small>'
                );
            }
        } else {
            $('#balance_vl_list').removeClass('is-invalid');
            $('.vl-insufficient-notice').remove();
        }
        
        // SL Balance: Current balance - with pay deduction = Remaining balance
        // NOTE: #total_earned_sl_list actually contains CURRENT BALANCE (sl_balance from server)
        var slCurrentBalance = parseFloat($('#total_earned_sl_list').val()) || 0;
        var slApplicationWithPay = parseFloat($('#less_application_sl_list').val()) || 0;
        var slRemainingBalance = slCurrentBalance - slApplicationWithPay;
        $('#balance_sl_list').val(slRemainingBalance.toFixed(3));
        
        // Check for insufficient balance
        if (slRemainingBalance < 0) {
            $('#balance_sl_list').addClass('is-invalid');
            if ($('.sl-insufficient-notice').length === 0) {
                $('#balance_sl_list').after(
                    '<small class="sl-insufficient-notice text-danger d-block">' +
                    '<i class="fa fa-exclamation-triangle"></i> Insufficient SL credits! (Requesting ' + 
                    slApplicationWithPay.toFixed(3) + ' but only ' + slCurrentBalance.toFixed(3) + ' available)</small>'
                );
            }
        } else {
            $('#balance_sl_list').removeClass('is-invalid');
            $('.sl-insufficient-notice').remove();
        }
    }
    
    // Manual input triggers recalculation (for all WITH PAY and WITHOUT PAY fields)
    $('#total_earned_vl_list, #less_application_vl_list, #less_application_vl_without_pay_list, #total_earned_sl_list, #less_application_sl_list, #less_application_sl_without_pay_list').on('input', calculateBalanceList);
    
});

// Transfer value from With Pay to Without Pay
function transferToWithoutPay(leaveType) {
    var withPayField = '#less_application_' + leaveType + '_list';
    var withoutPayField = '#less_application_' + leaveType + '_without_pay_list';
    var balanceField = '#balance_' + leaveType + '_list';
    
    var value = $(withPayField).val();
    if (value && parseFloat(value) > 0) {
        // Store old balance for comparison
        var oldBalance = parseFloat($(balanceField).val()) || 0;
        
        // Transfer value and trigger input event
        $(withoutPayField).val(value).trigger('input');
        $(withPayField).val('0.000').trigger('input');
        
        // Force recalculation (input event should trigger it, but ensure it happens)
        calculateBalanceList();
        
        // Get new balance after recalculation
        var newBalance = parseFloat($(balanceField).val()) || 0;
        var balanceChange = newBalance - oldBalance;
        
        // Visual feedback - highlight the target field
        $(withoutPayField).addClass('border-warning').css('box-shadow', '0 0 10px rgba(255, 193, 7, 0.5)');
        
        // Highlight balance field to show it changed (green = credits restored)
        $(balanceField).addClass('border-success').css('box-shadow', '0 0 15px rgba(40, 167, 69, 0.6)');
        
        setTimeout(function() {
            $(withoutPayField).removeClass('border-warning').css('box-shadow', '');
            $(balanceField).removeClass('border-success').css('box-shadow', '');
        }, 1200);
        
        // Show notification with balance change
        var msg = leaveType.toUpperCase() + ' transferred to <strong>Without Pay</strong><br/>' +
                  '<small class="text-success"><i class="fa fa-arrow-up"></i> Credits restored: +' + 
                  balanceChange.toFixed(3) + ' (New balance: ' + newBalance.toFixed(3) + ')</small>';
        showTransferNotification(msg);
    }
}

// Transfer value from Without Pay to With Pay
function transferToWithPay(leaveType) {
    var withPayField = '#less_application_' + leaveType + '_list';
    var withoutPayField = '#less_application_' + leaveType + '_without_pay_list';
    var balanceField = '#balance_' + leaveType + '_list';
    
    var value = $(withoutPayField).val();
    if (value && parseFloat(value) > 0) {
        // Store old balance for comparison
        var oldBalance = parseFloat($(balanceField).val()) || 0;
        
        // Transfer value and trigger input event
        $(withPayField).val(value).trigger('input');
        $(withoutPayField).val('0.000').trigger('input');
        
        // Force recalculation (input event should trigger it, but ensure it happens)
        calculateBalanceList();
        
        // Get new balance after recalculation
        var newBalance = parseFloat($(balanceField).val()) || 0;
        var balanceChange = newBalance - oldBalance;
        
        // Visual feedback - highlight the target field
        $(withPayField).addClass('border-info').css('box-shadow', '0 0 10px rgba(23, 162, 184, 0.5)');
        
        // Highlight balance field to show it changed (red = credits deducted)
        var balanceColor = newBalance < 0 ? 'border-danger' : 'border-warning';
        var shadowColor = newBalance < 0 ? 'rgba(220, 53, 69, 0.6)' : 'rgba(255, 193, 7, 0.6)';
        $(balanceField).addClass(balanceColor).css('box-shadow', '0 0 15px ' + shadowColor);
        
        setTimeout(function() {
            $(withPayField).removeClass('border-info').css('box-shadow', '');
            $(balanceField).removeClass(balanceColor).css('box-shadow', '');
        }, 1200);
        
        // Show notification with balance change
        var changeIcon = balanceChange < 0 ? 'fa-arrow-down' : 'fa-minus';
        var changeClass = balanceChange < 0 ? 'text-danger' : 'text-warning';
        var msg = leaveType.toUpperCase() + ' transferred to <strong>With Pay</strong><br/>' +
                  '<small class="' + changeClass + '"><i class="fa ' + changeIcon + '"></i> Credits deducted: ' + 
                  balanceChange.toFixed(3) + ' (New balance: ' + newBalance.toFixed(3) + ')</small>';
        showTransferNotification(msg);
    }
}

// Show transfer notification
function showTransferNotification(message) {
    // Remove existing notification
    $('.transfer-notification').remove();
    
    // Create new notification
    var notification = $('<div class="alert alert-success alert-dismissible fade show transfer-notification" style="position: fixed; top: 80px; right: 20px; z-index: 9999; min-width: 300px;">' +
        '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
        '<i class="fa fa-check-circle"></i> ' + message +
        '</div>');
    
    $('body').append(notification);
    
    // Auto-dismiss after 3 seconds
    setTimeout(function() {
        notification.fadeOut(400, function() {
            $(this).remove();
        });
    }, 3000);
}

// ====================================
// MULTIPLE DATE RANGES MANAGEMENT
// ====================================

var dateRangeIndexList = 1; // Start from 1 since index 0 already exists

$(document).ready(function() {
    // Add new date range
    $('#add_date_range_btn_list').on('click', function() {
        var newIndex = dateRangeIndexList++;
        
        var newRangeHtml = `
        <div class="date-range-entry mb-2" data-index="${newIndex}">
            <div class="card border-primary">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-5">
                            <label class="small mb-1">From</label>
                            <input type="date" class="form-control form-control-sm date-from-input" required />
                        </div>
                        <div class="col-5">
                            <label class="small mb-1">To</label>
                            <input type="date" class="form-control form-control-sm date-to-input" required />
                        </div>
                        <div class="col-2 text-center">
                            <label class="small mb-1 d-block">&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-sm remove-date-range" title="Remove this date range">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#date_ranges_container_list').append(newRangeHtml);
    
    // Show remove buttons when there are multiple ranges
    updateRemoveButtonsVisibility();
    updateDateRangesJson();
});

// Remove date range
$(document).on('click', '.remove-date-range', function() {
    var container = $('#date_ranges_container_list');
    if (container.find('.date-range-entry').length > 1) {
        $(this).closest('.date-range-entry').remove();
        updateRemoveButtonsVisibility();
        updateDateRangesJson();
    }
});

// Update remove buttons visibility
function updateRemoveButtonsVisibility() {
    var container = $('#date_ranges_container_list');
    var entries = container.find('.date-range-entry');
    
    if (entries.length > 1) {
        entries.find('.remove-date-range').show();
    } else {
        entries.find('.remove-date-range').hide();
    }
}

// Update hidden JSON field when dates change
$(document).on('change', '.date-from-input, .date-to-input', function() {
    updateDateRangesJson();
});

// Collect date ranges and update hidden fields
function updateDateRangesJson() {
    var dateRanges = [];
    var firstFrom = null;
    var lastTo = null;
    
    $('#date_ranges_container_list .date-range-entry').each(function() {
        var fromDate = $(this).find('.date-from-input').val();
        var toDate = $(this).find('.date-to-input').val();
        
        if (fromDate && toDate) {
            dateRanges.push({
                from: fromDate,
                to: toDate
            });
            
            // Track first and last dates for legacy fields
            if (!firstFrom || fromDate < firstFrom) {
                firstFrom = fromDate;
            }
            if (!lastTo || toDate > lastTo) {
                lastTo = toDate;
            }
        }
    });
    
    // Update JSON field
    $('#inclusive_dates_json_list').val(JSON.stringify(dateRanges));
    
    // Update legacy fields with overall range (first from to last to)
    $('#date_from_list').val(firstFrom || '');
    $('#date_to_list').val(lastTo || '');
    
    console.log('Date Ranges Updated:', dateRanges);
}

    // Validate form before submit - ensure all date ranges are filled
    $('#leave_application_form').on('submit', function(e) {
        var valid = true;
        var dateRanges = [];
        
        $('#date_ranges_container_list .date-range-entry').each(function() {
            var fromDate = $(this).find('.date-from-input').val();
            var toDate = $(this).find('.date-to-input').val();
            
            if (!fromDate || !toDate) {
                valid = false;
                $(this).find('.date-from-input, .date-to-input').addClass('is-invalid');
            } else {
                $(this).find('.date-from-input, .date-to-input').removeClass('is-invalid');
                dateRanges.push({ from: fromDate, to: toDate });
            }
        });
        
        if (!valid) {
            e.preventDefault();
            alert('Please fill in all date ranges or remove empty ones.');
            return false;
        }
        
        // Final update to ensure JSON is current
        updateDateRangesJson();
        return true;
    });
    
    // Reset form when modal closes
    $('#add_leave_application').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
        $('.leave-credits-info').remove();
        $('.leave-credits-error').remove();
        $('.special-leave-notice').remove();
        $('.others-leave-notice').remove();
        $('.vl-insufficient-notice').remove();
        $('.sl-insufficient-notice').remove();
        $('#balance_vl_list').removeClass('is-invalid');
        $('#balance_sl_list').removeClass('is-invalid');
        
        // Reset date ranges to single entry
        var container = $('#date_ranges_container_list');
        container.find('.date-range-entry:not(:first)').remove();
        container.find('.date-range-entry:first .date-from-input').val('');
        container.find('.date-range-entry:first .date-to-input').val('');
        container.find('.remove-date-range').hide();
        dateRangeIndexList = 1;
        updateDateRangesJson();
    });
}); // End document.ready for date ranges management

    // Make setPersonnelForLeaveApp globally available
    window.setPersonnelForLeaveApp = setPersonnelForLeaveApp;
    
    } // End initLeaveAppListScripts
    
    initLeaveAppListScripts();
})();
</script>
