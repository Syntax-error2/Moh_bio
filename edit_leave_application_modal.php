<!-- EDIT LEAVE APPLICATION Modal (CS Form No. 6) -->
<div id="edit_leave_application" tabindex="-1" role="dialog" aria-labelledby="editLeaveModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog modal-lg">
    <div class="modal-content">
    
    <form action="save_leave_application.php" method="POST">
    
    <input name="leave_application_id" id="edit_leave_application_id" type="hidden" />
    <input name="personnel_id" value="<?php echo $staff_row['personnel_id']; ?>" type="hidden" />
    <input name="do_id" value="<?php echo $staff_row['do_id']; ?>" type="hidden" />
    <input name="dept" value="<?php echo isset($_GET['dept']) ? htmlspecialchars($_GET['dept']) : ''; ?>" type="hidden" />
    
      <div class="modal-header bg-warning text-white">
        <h5 id="editLeaveModalLabel" class="modal-title">
          <i class="fa fa-edit"></i> EDIT LEAVE APPLICATION (CS Form No. 6)
        </h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close text-white">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <div class="modal-body">
        
        <!-- OFFICE/AGENCY/DEPARTMENT -->
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">Office/Agency/Department</label>
          <div class="col-sm-9">
            <input name="office_agency" id="edit_office_agency" type="text" class="form-control" required />
          </div>
        </div>

        <hr class="my-3" />
        
        <!-- APPLICATION DATE -->
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">Date of Filing</label>
          <div class="col-sm-9">
            <input name="application_date" id="edit_application_date" type="date" class="form-control" required />
          </div>
        </div>
        
        <div class="alert alert-info">
          <strong><i class="fa fa-info-circle"></i> DETAILS OF LEAVE APPLICATION</strong>
        </div>
        
        <!-- TYPE OF LEAVE -->
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">Type of Leave to be Availed Of</label>
          <div class="col-sm-9">
            <select name="leave_type" id="edit_leave_type" class="form-control" required>
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
        <div class="form-group row" id="edit_other_leave_spec_group" style="display: none;">
          <label class="col-sm-3 form-control-label text-bold">Specify Other Leave Type</label>
          <div class="col-sm-9">
            <input name="other_leave_specification" id="edit_other_leave_specification" type="text" class="form-control" />
          </div>
        </div>
        
        <!-- VACATION LEAVE DETAILS -->
        <div class="form-group row" id="edit_vacation_details_group" style="display: none;">
          <label class="col-sm-3 form-control-label text-bold">Where will you spend vacation?</label>
          <div class="col-sm-9">
            <input name="vacation_details" id="edit_vacation_details" type="text" class="form-control" />
          </div>
        </div>
        
        <!-- SICK LEAVE DETAILS -->
        <div class="form-group row" id="edit_sick_details_group" style="display: none;">
          <label class="col-sm-3 form-control-label text-bold">Illness Details</label>
          <div class="col-sm-9">
            <input name="sick_details" id="edit_sick_details" type="text" class="form-control" />
          </div>
        </div>
        
        <!-- STUDY LEAVE DETAILS -->
        <div class="form-group row" id="edit_study_details_group" style="display: none;">
          <label class="col-sm-3 form-control-label text-bold">Study Leave Details</label>
          <div class="col-sm-9">
            <input name="study_details" id="edit_study_details" type="text" class="form-control" />
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
            <input type="hidden" name="inclusive_dates_json" id="edit_inclusive_dates_json" value="" />
            <!-- Legacy fields for backward compatibility (stores first range) -->
            <input type="hidden" name="inclusive_date_from" id="edit_date_from" value="" />
            <input type="hidden" name="inclusive_date_to" id="edit_date_to" value="" />
            
            <!-- Container for date range entries -->
            <div id="edit_date_ranges_container">
              <!-- Date ranges will be populated dynamically -->
            </div>
            
            <!-- Add More Date Range Button -->
            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="edit_add_date_range_btn">
              <i class="fa fa-plus"></i> Add Another Date Range
            </button>
            <small class="form-text text-muted">
              <i class="fa fa-info-circle"></i> Use multiple date ranges for non-consecutive leave days (e.g., excluding weekends).
            </small>
          </div>
        </div>
        
        <!-- NUMBER OF WORKING DAYS APPLIED FOR -->
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">Number of Working Days Applied For</label>
          <div class="col-sm-9">
            <input name="number_of_days" id="edit_number_of_days" type="number" step="0.5" min="0.5" class="form-control" required />
            <small class="form-text text-muted">You can enter half days (e.g., 2.5)</small>
          </div>
        </div>
        
        <!-- COMMUTATION -->
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">Commutation</label>
          <div class="col-sm-9">
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="edit_commutation_requested" name="commutation" class="custom-control-input" value="requested" />
              <label class="custom-control-label" for="edit_commutation_requested">Requested</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="edit_commutation_not_requested" name="commutation" class="custom-control-input" value="not_requested" />
              <label class="custom-control-label" for="edit_commutation_not_requested">Not Requested</label>
            </div>
          </div>
        </div>
        
        <hr class="my-3" />
        
        <div class="alert alert-secondary">
          <strong><i class="fa fa-user"></i> DETAILS OF ACTION ON APPLICATION</strong>
        </div>
        
        <!-- CERTIFICATION OF LEAVE CREDITS -->
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">As of (Date)</label>
          <div class="col-sm-9">
            <input name="as_of_date" id="edit_as_of_date" type="date" class="form-control" />
            <small class="form-text">Date for leave credits certification</small>
          </div>
        </div>
        
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">Total Earned (VL/SL)</label>
          <div class="col-sm-9">
            <div class="row">
              <div class="col-6">
                <input name="total_earned_vl" id="edit_total_earned_vl" type="number" step="0.001" min="0" class="form-control" placeholder="0.000" readonly />
                <small class="form-text">Vacation Leave Balance (snapshot)</small>
              </div>
              <div class="col-6">
                <input name="total_earned_sl" id="edit_total_earned_sl" type="number" step="0.001" min="0" class="form-control" placeholder="0.000" readonly />
                <small class="form-text">Sick Leave Balance (snapshot)</small>
              </div>
            </div>
          </div>
        </div>
        
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">Less This Application</label>
          <div class="col-sm-9">
            <div class="alert alert-warning py-1 px-2 mb-2" style="font-size:0.85em;"><i class="fa fa-lock"></i> To be filled up by the <strong>HR Head / Admin</strong>.</div>
            <!-- Vacation Leave Deductions -->
            <div class="card mb-2">
              <div class="card-body py-2">
                <strong class="text-primary">Vacation Leave</strong>
                <div class="row mt-2">
                  <div class="col-6">
                    <input name="less_application_vl" id="edit_less_application_vl" type="number" step="0.001" min="0" class="form-control" placeholder="0.000" readonly />
                    <small class="form-text">
                      <i class="fa fa-money"></i> With Pay
                    </small>
                  </div>
                  <div class="col-6">
                    <input name="less_application_vl_without_pay" id="edit_less_application_vl_without_pay" type="number" step="0.001" min="0" class="form-control" placeholder="0.000" readonly />
                    <small class="form-text">
                      <i class="fa fa-ban"></i> Without Pay
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
                    <input name="less_application_sl" id="edit_less_application_sl" type="number" step="0.001" min="0" class="form-control" placeholder="0.000" readonly />
                    <small class="form-text">
                      <i class="fa fa-money"></i> With Pay
                    </small>
                  </div>
                  <div class="col-6">
                    <input name="less_application_sl_without_pay" id="edit_less_application_sl_without_pay" type="number" step="0.001" min="0" class="form-control" placeholder="0.000" readonly />
                    <small class="form-text">
                      <i class="fa fa-ban"></i> Without Pay
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
                <input name="balance_vl" id="edit_balance_vl" type="number" step="0.001" min="0" class="form-control" placeholder="0.000" readonly />
                <small class="form-text">VL Balance</small>
              </div>
              <div class="col-6">
                <input name="balance_sl" id="edit_balance_sl" type="number" step="0.001" min="0" class="form-control" placeholder="0.000" readonly />
                <small class="form-text">SL Balance</small>
              </div>
            </div>
          </div>
        </div>
        
        <!-- STATUS UPDATE (for Admin/Authorized Personnel) -->
        <hr class="my-3" />
        
        <div class="alert alert-dark">
          <strong><i class="fa fa-check-circle"></i> STATUS & RECOMMENDATION</strong>
        </div>
        
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">Status</label>
          <div class="col-sm-9">
            <select name="status" id="edit_status" class="form-control">
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="disapproved">Disapproved</option>
            </select>
          </div>
        </div>
        
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">Recommendation / Remarks</label>
          <div class="col-sm-9">
            <textarea name="recommendation" id="edit_recommendation" class="form-control" rows="3" placeholder="Enter recommendation or remarks..."></textarea>
          </div>
        </div>
        
      </div>
      
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-secondary">Cancel</button>
        <button name="update_leave_application" type="submit" class="btn btn-warning"><i class="fa fa-save"></i> Update Application</button>
      </div>
      </form>
    </div>
  </div>
</div>
<!-- end EDIT LEAVE APPLICATION Modal -->

<script>
// Wait for jQuery to be available
(function() {
    function initEditLeaveAppScripts() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initEditLeaveAppScripts, 50);
            return;
        }
        
        var $ = jQuery;

$(document).ready(function() {
    // Show/hide conditional fields based on leave type (edit modal)
    $('#edit_leave_type').on('change', function() {
        var leaveType = $(this).val();
        
        // Hide all conditional groups
        $('#edit_other_leave_spec_group').hide();
        $('#edit_vacation_details_group').hide();
        $('#edit_sick_details_group').hide();
        $('#edit_study_details_group').hide();
        
        // Show relevant group
        if (leaveType === 'Others') {
            $('#edit_other_leave_spec_group').show();
        }
        
        if (leaveType.includes('Vacation Leave')) {
            $('#edit_vacation_details_group').show();
        }
        
        if (leaveType.includes('Sick Leave')) {
            $('#edit_sick_details_group').show();
        }
        
        if (leaveType.includes('Study Leave')) {
            $('#edit_study_details_group').show();
        }
    });
    
    // Auto-calculate balance (edit modal) - only WITH PAY deducts from credits
    function calculateEditBalance() {
        // VL Balance: Current balance - with pay deduction = Remaining balance
        var vlEarned = parseFloat($('#edit_total_earned_vl').val()) || 0;
        var vlApplicationWithPay = parseFloat($('#edit_less_application_vl').val()) || 0;
        var vlBalance = vlEarned - vlApplicationWithPay;
        $('#edit_balance_vl').val(vlBalance.toFixed(3));
        
        // SL Balance: Current balance - with pay deduction = Remaining balance
        var slEarned = parseFloat($('#edit_total_earned_sl').val()) || 0;
        var slApplicationWithPay = parseFloat($('#edit_less_application_sl').val()) || 0;
        var slBalance = slEarned - slApplicationWithPay;
        $('#edit_balance_sl').val(slBalance.toFixed(3));
    }
    
    // Trigger calculation on input change (for all WITH PAY and WITHOUT PAY fields)
    $('#edit_total_earned_vl, #edit_less_application_vl, #edit_less_application_vl_without_pay, #edit_total_earned_sl, #edit_less_application_sl, #edit_less_application_sl_without_pay').on('input', calculateEditBalance);
});

// Populate edit modal with existing data
function editLeaveApplication(id) {
    $.ajax({
        url: 'get_leave_application.php',
        type: 'POST',
        data: {leave_application_id: id},
        dataType: 'json',
        success: function(data) {
            // Handle error responses from server
            if (data.error) {
              console.error('Server error loading leave application:', data.error);
              alert('Error loading leave application: ' + data.error);
              return;
            }

            $('#edit_leave_application_id').val(data.id);
            $('#edit_office_agency').val(data.office_agency);
            $('#edit_application_date').val(data.application_date);
            $('#edit_leave_type').val(data.leave_type).trigger('change');
            $('#edit_other_leave_specification').val(data.other_leave_specification);
            $('#edit_vacation_details').val(data.vacation_details);
            $('#edit_sick_details').val(data.sick_details);
            $('#edit_study_details').val(data.study_details);
            
            // Load multiple date ranges
            loadEditDateRanges(data.inclusive_dates_json, data.inclusive_date_from, data.inclusive_date_to);
            
            $('#edit_number_of_days').val(data.number_of_days);
            
            // Set commutation radio button
            if (data.commutation === 'requested') {
                $('#edit_commutation_requested').prop('checked', true);
            } else {
                $('#edit_commutation_not_requested').prop('checked', true);
            }
            
            $('#edit_as_of_date').val(data.as_of_date);
            $('#edit_total_earned_vl').val(data.total_earned_vl);
            $('#edit_total_earned_sl').val(data.total_earned_sl);
            $('#edit_less_application_vl').val(data.less_application_vl);
            $('#edit_less_application_vl_without_pay').val(data.less_application_vl_without_pay || '0.000');
            $('#edit_less_application_sl').val(data.less_application_sl);
            $('#edit_less_application_sl_without_pay').val(data.less_application_sl_without_pay || '0.000');
            $('#edit_balance_vl').val(data.balance_vl);
            $('#edit_balance_sl').val(data.balance_sl);
            $('#edit_status').val(data.status);
            $('#edit_recommendation').val(data.recommendation);

            // Show modal with fallback (avoid jQuery due to extension conflicts)
            try {
              var modalShown = false;
              
              // Try Bootstrap Modal API first (most reliable)
              if (!modalShown && window.bootstrap && typeof window.bootstrap.Modal === 'function') {
                try {
                  var modalEl = document.getElementById('edit_leave_application');
                  var modalInstance = new window.bootstrap.Modal(modalEl);
                  modalInstance.show();
                  modalShown = true;
                } catch (bsErr) {
                  console.warn('Bootstrap Modal failed:', bsErr);
                }
              }
              
              // Fallback to jQuery if Bootstrap failed
              if (!modalShown) {
                try {
                  var jq = window.jQuery;
                  if (jq && typeof jq.fn === 'object' && typeof jq.fn.modal === 'function') {
                    jq('#edit_leave_application').modal('show');
                    modalShown = true;
                  }
                } catch (jqErr) {
                  console.warn('jQuery modal failed:', jqErr);
                }
              }
              
              // Final DOM fallback if both failed
              if (!modalShown) {
                var modalEl = document.getElementById('edit_leave_application');
                if (modalEl) {
                  modalEl.classList.add('show');
                  modalEl.style.display = 'block';
                  modalEl.setAttribute('aria-modal', 'true');
                  modalEl.removeAttribute('aria-hidden');
                  document.body.classList.add('modal-open');
                  
                  // Add backdrop
                  if (!document.querySelector('.modal-backdrop')) {
                    var backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade show';
                    document.body.appendChild(backdrop);
                  }
                  modalShown = true;
                }
              }
              
              if (!modalShown) {
                throw new Error('All modal display methods failed');
              }
            } catch (e) {
              console.error('Error showing edit modal:', e);
              alert('Could not open edit modal. Please refresh the page and try again.');
            }
        },
        error: function(xhr, status, error) {
          console.error('AJAX error loading leave application:', error);
          console.error('Response:', xhr.responseText);
          alert('Error loading leave application data. Check console for details.');
        }
    });
}

// ====================================
// EDIT MODAL - MULTIPLE DATE RANGES MANAGEMENT
// ====================================

// Make updateEditRemoveButtonsVisibility global so it can be called from anywhere
function updateEditRemoveButtonsVisibility() {
  var container = jQuery('#edit_date_ranges_container');
  var entries = container.find('.edit-date-range-entry');
    
  if (entries.length > 1) {
    entries.find('.edit-remove-date-range').show();
  } else {
    entries.find('.edit-remove-date-range').hide();
  }
}

var editDateRangeIndex = 0;

// Load date ranges into edit modal
function loadEditDateRanges(jsonData, legacyFrom, legacyTo) {
    var container = $('#edit_date_ranges_container');
    container.empty();
    editDateRangeIndex = 0;
    
    var dateRanges = [];
    
    // Try to parse JSON data
    if (jsonData) {
        try {
            dateRanges = typeof jsonData === 'string' ? JSON.parse(jsonData) : jsonData;
        } catch (e) {
            console.log('Could not parse inclusive_dates_json, using legacy fields');
        }
    }
    
    // Fall back to legacy single range if no JSON data
    if (!dateRanges || dateRanges.length === 0) {
        if (legacyFrom && legacyTo) {
            dateRanges = [{ from: legacyFrom, to: legacyTo }];
        } else {
            dateRanges = [{ from: '', to: '' }];
        }
    }
    
    // Create date range entries
    dateRanges.forEach(function(range, index) {
        addEditDateRangeEntry(range.from, range.to);
    });
    
    updateEditRemoveButtonsVisibility();
    updateEditDateRangesJson();
}

// Add a date range entry to edit modal
function addEditDateRangeEntry(fromDate, toDate) {
    var index = editDateRangeIndex++;
    var isFirst = index === 0;
    
    var entryHtml = `
        <div class="edit-date-range-entry mb-2" data-index="${index}">
            <div class="card ${isFirst ? '' : 'border-primary'}">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-5">
                            <label class="small mb-1">From</label>
                            <input type="date" class="form-control form-control-sm edit-date-from-input" value="${fromDate || ''}" required />
                        </div>
                        <div class="col-5">
                            <label class="small mb-1">To</label>
                            <input type="date" class="form-control form-control-sm edit-date-to-input" value="${toDate || ''}" required />
                        </div>
                        <div class="col-2 text-center">
                            <label class="small mb-1 d-block">&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-sm edit-remove-date-range" title="Remove this date range" style="display: none;">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#edit_date_ranges_container').append(entryHtml);
}

$(document).ready(function() {
    // Add new date range in edit modal
    $('#edit_add_date_range_btn').on('click', function() {
        addEditDateRangeEntry('', '');
        updateEditRemoveButtonsVisibility();
        updateEditDateRangesJson();
    });

    // Remove date range in edit modal
    $(document).on('click', '.edit-remove-date-range', function() {
    var container = $('#edit_date_ranges_container');
    if (container.find('.edit-date-range-entry').length > 1) {
        $(this).closest('.edit-date-range-entry').remove();
        updateEditRemoveButtonsVisibility();
        updateEditDateRangesJson();
    }
});

  // Update remove buttons visibility is defined globally above
  // call it here when needed
  updateEditRemoveButtonsVisibility();
}); // End document.ready for edit date ranges

// Update hidden JSON field when dates change in edit modal
$(document).on('change', '.edit-date-from-input, .edit-date-to-input', function() {
    updateEditDateRangesJson();
});

// Collect date ranges and update hidden fields for edit modal
function updateEditDateRangesJson() {
    var dateRanges = [];
    var firstFrom = null;
    var lastTo = null;
    
    $('#edit_date_ranges_container .edit-date-range-entry').each(function() {
        var fromDate = $(this).find('.edit-date-from-input').val();
        var toDate = $(this).find('.edit-date-to-input').val();
        
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
    $('#edit_inclusive_dates_json').val(JSON.stringify(dateRanges));
    
    // Update legacy fields with overall range
    $('#edit_date_from').val(firstFrom || '');
    $('#edit_date_to').val(lastTo || '');
}

// Transfer value from With Pay to Without Pay (Edit Modal)
function transferToWithoutPayEdit(leaveType) {
    var withPayField = '#edit_less_application_' + leaveType;
    var withoutPayField = '#edit_less_application_' + leaveType + '_without_pay';
    
    var value = $(withPayField).val();
    if (value && parseFloat(value) > 0) {
        $(withoutPayField).val(value).trigger('input');
        $(withPayField).val('0.000').trigger('input');
        
        // Visual feedback
        $(withoutPayField).addClass('border-warning').css('box-shadow', '0 0 10px rgba(255, 193, 7, 0.5)');
        setTimeout(function() {
            $(withoutPayField).removeClass('border-warning').css('box-shadow', '');
        }, 800);
    }
}

// Transfer value from Without Pay to With Pay (Edit Modal)
function transferToWithPayEdit(leaveType) {
    var withPayField = '#edit_less_application_' + leaveType;
    var withoutPayField = '#edit_less_application_' + leaveType + '_without_pay';
    
    var value = $(withoutPayField).val();
    if (value && parseFloat(value) > 0) {
        $(withPayField).val(value).trigger('input');
        $(withoutPayField).val('0.000').trigger('input');
        
        // Visual feedback
        $(withPayField).addClass('border-info').css('box-shadow', '0 0 10px rgba(23, 162, 184, 0.5)');
        setTimeout(function() {
            $(withPayField).removeClass('border-info').css('box-shadow', '');
        }, 800);
    }
}

    // Make functions globally available
    window.editLeaveApplication = editLeaveApplication;
    window.transferToWithoutPayEdit = transferToWithoutPayEdit;
    window.transferToWithPayEdit = transferToWithPayEdit;

    } // End initEditLeaveAppScripts
    
    initEditLeaveAppScripts();
})();
</script>
