<!-- ADD LEAVE APPLICATION Modal (CS Form No. 6) -->
<div id="add_leave_application" tabindex="-1" role="dialog" aria-labelledby="addLeaveModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog modal-lg">
    <div class="modal-content">
    
    <form action="save_leave_application.php" method="POST" class="standardized-form">
    
    <input name="personnel_id" value="<?php echo $staff_row['personnel_id']; ?>" type="hidden" />
    <input name="do_id" value="<?php echo $staff_row['do_id']; ?>" type="hidden" />
    <input name="dept" value="<?php echo isset($_GET['dept']) ? htmlspecialchars($_GET['dept']) : ''; ?>" type="hidden" />
    
      <div class="modal-header bg-primary text-white">
        <h5 id="addLeaveModalLabel" class="modal-title">
          <i class="fa fa-file-text"></i> APPLICATION FOR LEAVE (CS Form No. 6)
        </h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close text-white">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <div class="modal-body">
        <div class="personnel-category-card p-3 mb-3">
        
        <!-- OFFICE/AGENCY/DEPARTMENT -->
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">Office/Agency/Department</label>
          <div class="col-sm-9">
            <input name="office_agency" type="text" class="form-control" placeholder="e.g., Department of Health - Region X" required />
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
        
        <div class="alert alert-info">
          <strong><i class="fa fa-info-circle"></i> DETAILS OF LEAVE APPLICATION</strong>
        </div>
        
        <!-- TYPE OF LEAVE -->
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">Type of Leave to be Availed Of</label>
          <div class="col-sm-9">
            <select name="leave_type" id="leave_type" class="form-control" required>
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
        <div class="form-group row" id="other_leave_spec_group" style="display: none;">
          <label class="col-sm-3 form-control-label text-bold">Specify Other Leave Type</label>
          <div class="col-sm-9">
            <input name="other_leave_specification" type="text" class="form-control" placeholder="Please specify..." />
          </div>
        </div>
        
        <!-- VACATION LEAVE DETAILS -->
        <div class="form-group row" id="vacation_details_group" style="display: none;">
          <label class="col-sm-3 form-control-label text-bold">Where will you spend vacation?</label>
          <div class="col-sm-9">
            <input name="vacation_details" type="text" class="form-control" placeholder="e.g., Cebu City, Philippines" />
          </div>
        </div>
        
        <!-- SICK LEAVE DETAILS -->
        <div class="form-group row" id="sick_details_group" style="display: none;">
          <label class="col-sm-3 form-control-label text-bold">Illness Details</label>
          <div class="col-sm-9">
            <input name="sick_details" type="text" class="form-control" placeholder="e.g., Hospital name or specify illness" />
          </div>
        </div>
        
        <!-- STUDY LEAVE DETAILS -->
        <div class="form-group row" id="study_details_group" style="display: none;">
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
            <input type="hidden" name="inclusive_dates_json" id="inclusive_dates_json" value="" />
            <!-- Legacy fields for backward compatibility (stores first range) -->
            <input type="hidden" name="inclusive_date_from" id="date_from" value="" />
            <input type="hidden" name="inclusive_date_to" id="date_to" value="" />
            
            <!-- Container for date range entries -->
            <div id="date_ranges_container">
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
                        <button type="button" class="btn btn-danger btn-sm remove-date-range-main" title="Remove this date range" style="display: none;">
                          <i class="fa fa-times"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Add More Date Range Button -->
            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add_date_range_btn">
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
            <input name="number_of_days" id="number_of_days" type="number" step="0.5" min="0.5" class="form-control" placeholder="e.g., 5" required />
            <small class="form-text text-muted">You can enter half days (e.g., 2.5). Total days for all date ranges above.</small>
          </div>
        </div>
        
        <!-- COMMUTATION -->
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">Commutation</label>
          <div class="col-sm-9">
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="commutation_requested" name="commutation" class="custom-control-input" value="requested" checked />
              <label class="custom-control-label" for="commutation_requested">Requested</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="commutation_not_requested" name="commutation" class="custom-control-input" value="not_requested" />
              <label class="custom-control-label" for="commutation_not_requested">Not Requested</label>
            </div>
          </div>
        </div>
        
        </div>

        <hr class="my-3" />
        
        <div class="personnel-category-card p-3 mb-0">
        <div class="alert alert-secondary">
          <strong><i class="fa fa-user"></i> DETAILS OF ACTION ON APPLICATION</strong>
        </div>
        
        <!-- CERTIFICATION OF LEAVE CREDITS -->
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">As of (Date)</label>
          <div class="col-sm-9">
            <input name="as_of_date" type="date" value="<?php echo date('Y-m-d'); ?>" class="form-control" />
            <small class="form-text">Date for leave credits certification</small>
          </div>
        </div>
        
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">Total Earned (VL/SL)</label>
          <div class="col-sm-9">
            <div class="row">
              <div class="col-6">
                <input name="total_earned_vl" type="number" step="0.001" min="0" class="form-control" placeholder="0.000" readonly />
                <small class="form-text">Vacation Leave Balance (auto-fetched)</small>
              </div>
              <div class="col-6">
                <input name="total_earned_sl" type="number" step="0.001" min="0" class="form-control" placeholder="0.000" readonly />
                <small class="form-text">Sick Leave Balance (auto-fetched)</small>
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
                    <input name="less_application_vl" type="number" step="0.001" min="0" class="form-control" placeholder="0.000" readonly />
                    <small class="form-text">
                      <i class="fa fa-money"></i> With Pay
                    </small>
                  </div>
                  <div class="col-6">
                    <input name="less_application_vl_without_pay" type="number" step="0.001" min="0" class="form-control" placeholder="0.000" readonly />
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
                    <input name="less_application_sl" type="number" step="0.001" min="0" class="form-control" placeholder="0.000" readonly />
                    <small class="form-text">
                      <i class="fa fa-money"></i> With Pay
                    </small>
                  </div>
                  <div class="col-6">
                    <input name="less_application_sl_without_pay" type="number" step="0.001" min="0" class="form-control" placeholder="0.000" readonly />
                    <small class="form-text">
                      <i class="fa fa-ban"></i> Without Pay
                    </small>
                  </div>
                </div>
              </div>
            </div>
            
          </div>
        </div>
        
        <div class="form-group row">
          <label class="col-sm-3 form-control-label text-bold">Balance (VL/SL)</label>
          <div class="col-sm-9">
            <div class="row">
              <div class="col-6">
                <input name="balance_vl" type="number" step="0.001" min="0" class="form-control" placeholder="0.000" readonly />
                <small class="form-text">VL Balance</small>
              </div>
              <div class="col-6">
                <input name="balance_sl" type="number" step="0.001" min="0" class="form-control" placeholder="0.000" readonly />
                <small class="form-text">SL Balance</small>
              </div>
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
$(document).ready(function() {
    // ====================================
    // AUTO-FETCH LEAVE BALANCES ON MODAL OPEN
    // ====================================
    $('#addLeaveModal').on('show.bs.modal', function() {
        var personnelId = $('input[name="personnel_id"]').val() || '<?php echo $personnel_id ?? ''; ?>';
        
        if (personnelId) {
            $.ajax({
                url: 'get_leave_card_balance.php',
                type: 'POST',
                dataType: 'json',
                data: { personnel_id: personnelId },
                success: function(response) {
                    if (response.success) {
                        // Set total earned to current leave balances
                        $('input[name="total_earned_vl"]').val(response.vl_balance.toFixed(3));
                        $('input[name="total_earned_sl"]').val(response.sl_balance.toFixed(3));
                        calculateBalance();
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error fetching leave balance:', error);
                }
            });
        }
    });
    // Show/hide conditional fields based on leave type
    $('#leave_type').on('change', function() {
        var leaveType = $(this).val();
        
        // Hide all conditional groups
        $('#other_leave_spec_group').hide();
        $('#vacation_details_group').hide();
        $('#sick_details_group').hide();
        $('#study_details_group').hide();
        
        // Show relevant group
        if (leaveType === 'Others') {
            $('#other_leave_spec_group').show();
        }
        
        if (leaveType.includes('Vacation Leave')) {
            $('#vacation_details_group').show();
        }
        
        if (leaveType.includes('Sick Leave')) {
            $('#sick_details_group').show();
        }
        
        if (leaveType.includes('Study Leave')) {
            $('#study_details_group').show();
        }
    });
    
    // Auto-calculate balance
    function calculateBalance() {
        // VL Balance
        var vlEarned = parseFloat($('input[name="total_earned_vl"]').val()) || 0;
        var vlApplication = parseFloat($('input[name="less_application_vl"]').val()) || 0;
        var vlBalance = vlEarned - vlApplication;
        $('input[name="balance_vl"]').val(vlBalance.toFixed(3));
        
        // SL Balance
        var slEarned = parseFloat($('input[name="total_earned_sl"]').val()) || 0;
        var slApplication = parseFloat($('input[name="less_application_sl"]').val()) || 0;
        var slBalance = slEarned - slApplication;
        $('input[name="balance_sl"]').val(slBalance.toFixed(3));
    }
    
    $('input[name="total_earned_vl"], input[name="less_application_vl"], input[name="total_earned_sl"], input[name="less_application_sl"]').on('input', calculateBalance);
    
    // ====================================
    // MULTIPLE DATE RANGES MANAGEMENT (Main Modal)
    // ====================================
    
    var dateRangeIndexMain = 1; // Start from 1 since index 0 already exists
    
    // Add new date range
    $('#add_date_range_btn').on('click', function() {
        var newIndex = dateRangeIndexMain++;
        
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
                                <button type="button" class="btn btn-danger btn-sm remove-date-range-main" title="Remove this date range">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#date_ranges_container').append(newRangeHtml);
        
        // Show remove buttons when there are multiple ranges
        updateRemoveButtonsVisibilityMain();
        updateDateRangesJsonMain();
    });
    
    // Remove date range
    $(document).on('click', '.remove-date-range-main', function() {
        var container = $('#date_ranges_container');
        if (container.find('.date-range-entry').length > 1) {
            $(this).closest('.date-range-entry').remove();
            updateRemoveButtonsVisibilityMain();
            updateDateRangesJsonMain();
        }
    });
    
    // Update remove buttons visibility
    function updateRemoveButtonsVisibilityMain() {
        var container = $('#date_ranges_container');
        var entries = container.find('.date-range-entry');
        
        if (entries.length > 1) {
            entries.find('.remove-date-range-main').show();
        } else {
            entries.find('.remove-date-range-main').hide();
        }
    }
    
    // Update hidden JSON field when dates change
    $(document).on('change', '#date_ranges_container .date-from-input, #date_ranges_container .date-to-input', function() {
        updateDateRangesJsonMain();
    });
    
    // Collect date ranges and update hidden fields
    function updateDateRangesJsonMain() {
        var dateRanges = [];
        var firstFrom = null;
        var lastTo = null;
        
        $('#date_ranges_container .date-range-entry').each(function() {
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
        $('#inclusive_dates_json').val(JSON.stringify(dateRanges));
        
        // Update legacy fields with overall range (first from to last to)
        $('#date_from').val(firstFrom || '');
        $('#date_to').val(lastTo || '');
    }
});
</script>
