<!-- Adjust Leave Modal -->
<div id="adjustLeave<?php echo $personnel_id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <form action="save_adjust_leave.php" method="POST">
                <input type="hidden" name="personnel_id" value="<?php echo $personnel_id; ?>" />
                <input type="hidden" name="dept" value="<?php echo $_GET['dept']; ?>" />
                
                <div class="modal-header">
                    <h5 class="modal-title">Add Manual Leave Record</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                </div>
                
                <div class="modal-body">
                    <p>Add a manual log entry for <strong><?php echo $staff_row['fname'] . ' ' . $staff_row['lname']; ?></strong>.</p>
                    
                    <div class="row">
                        <div class="col-12 mb-2" style="font-size: 11px;">
                            <span class="text-muted mr-1">Quick Select Month:</span>
                            <a href="javascript:void(0);" class="btn btn-xs btn-outline-info py-0 px-2 btn-quick-month-lc" data-from="<?php echo date('m-01-y'); ?>" data-to="<?php echo date('m-t-y'); ?>"><i class="fa fa-calendar"></i> <?php echo date('F Y'); ?> (Current)</a>
                            <a href="javascript:void(0);" class="btn btn-xs btn-outline-secondary py-0 px-2 ml-1 btn-quick-month-lc" data-from="<?php echo date('m-01-y', strtotime('-1 month')); ?>" data-to="<?php echo date('m-t-y', strtotime('-1 month')); ?>"><i class="fa fa-calendar"></i> <?php echo date('F Y', strtotime('-1 month')); ?> (Last)</a>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Period From</label>
                            <input type="text" name="period_from" class="form-control" placeholder="01-01-26 (or 9/1/2026)" required autocomplete="off">
                            <small class="text-muted">e.g. 9/1/2026 or 01-01-26</small>
                        </div>
                        <div class="col-md-4 form-group">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="mb-0">Period To</label>
                                <span class="badge badge-success" style="font-size: 10px; font-weight: normal;"><i class="fa fa-magic"></i> Auto-fills</span>
                            </div>
                            <input type="text" name="period_to" class="form-control" placeholder="01-31-26 (or 9/30/2026)" required autocomplete="off">
                            <small class="text-muted">Auto-computed from Period From</small>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Particulars</label>
                            <input type="text" name="particulars" list="lc_particulars_list<?php echo $personnel_id; ?>" class="form-control" placeholder="Balance Forwarded" value="Balance Forwarded" required>
                            <datalist id="lc_particulars_list<?php echo $personnel_id; ?>">
                                <option value="Balance Forwarded">
                                <option value="Terminal Leave">
                                <option value="Monetization">
                                <option value="Accrual">
                            </datalist>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>VL Amount (+/-)</label>
                            <input type="number" step="0.001" name="vl_balance" class="form-control" value="0" required>
                            <small class="text-muted">Balance delta (+/-)</small>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>VL w/ Pay (Days)</label>
                            <input type="number" step="0.001" min="0" name="vl_w_pay" class="form-control" value="0">
                            <small class="text-muted">Wellness / Special w/ pay</small>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>VL Without Pay (LWOP)</label>
                            <input type="number" step="0.001" name="vl_wout_pay" class="form-control" value="0">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>SL Amount (+/-)</label>
                            <input type="number" step="0.001" name="sl_balance" class="form-control" value="0" required>
                            <small class="text-muted">Balance delta (+/-)</small>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>SL w/ Pay (Days)</label>
                            <input type="number" step="0.001" min="0" name="sl_w_pay" class="form-control" value="0">
                            <small class="text-muted">Special Leave w/ pay</small>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>SL Without Pay (LWOP)</label>
                            <input type="number" step="0.001" name="sl_wout_pay" class="form-control" value="0">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="mb-0">Remarks / Reason</label>
                            <a href="javascript:void(0);" onclick="$('#adjustLeave<?php echo $personnel_id; ?> textarea[name=\'remarks\']').val('Certified Correct');" class="small text-primary font-weight-bold" style="text-decoration: none;"><i class="fa fa-certificate"></i> Set 'Certified Correct'</a>
                        </div>
                        <textarea name="remarks" class="form-control" rows="2" placeholder="e.g. Certified Correct, Initial balance entry..."></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-secondary">Cancel</button>
                    <button type="submit" name="adjust_leave" class="btn btn-primary">Save Log Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
if (typeof autoCalculatePeriodTo !== 'function') {
    function autoCalculatePeriodTo(dateStr) {
        if (!dateStr) return '';
        dateStr = dateStr.trim();
        var sep = dateStr.indexOf('/') !== -1 ? '/' : (dateStr.indexOf('-') !== -1 ? '-' : '');
        if (!sep) return '';
        var parts = dateStr.split(sep);
        if (parts.length < 3) return '';

        var m, d, y, mStr, yStr;
        if (parts[0].length === 4) {
            y = parseInt(parts[0], 10);
            m = parseInt(parts[1], 10);
            d = parseInt(parts[2], 10);
            yStr = parts[0];
            mStr = parts[1];
            if (isNaN(y) || isNaN(m)) return '';
            var lastDay = new Date(y, m, 0).getDate();
            var ldStr = (lastDay < 10 ? '0' : '') + lastDay;
            return yStr + sep + mStr + sep + ldStr;
        } else {
            m = parseInt(parts[0], 10);
            d = parseInt(parts[1], 10);
            y = parseInt(parts[2], 10);
            mStr = parts[0];
            yStr = parts[2];
            if (isNaN(y) || isNaN(m)) return '';
            var fullYear = (y < 100) ? (2000 + y) : y;
            var lastDay = new Date(fullYear, m, 0).getDate();
            var ldStr = (mStr.length === 2 && lastDay < 10) ? ('0' + lastDay) : lastDay;
            return mStr + sep + ldStr + sep + yStr;
        }
    }
}

$(document).on('input change blur keyup', '#adjustLeave<?php echo $personnel_id; ?> input[name="period_from"]', function() {
    var fromVal = $(this).val();
    var calcTo = autoCalculatePeriodTo(fromVal);
    if (calcTo) {
        $('#adjustLeave<?php echo $personnel_id; ?> input[name="period_to"]').val(calcTo);
    }
});

$(document).on('click', '#adjustLeave<?php echo $personnel_id; ?> .btn-quick-month-lc', function(e) {
    e.preventDefault();
    var $modal = $('#adjustLeave<?php echo $personnel_id; ?>');
    $modal.find('input[name="period_from"]').val($(this).data('from'));
    $modal.find('input[name="period_to"]').val($(this).data('to'));
});

$(document).on('input change', '#adjustLeave<?php echo $personnel_id; ?> input[name="particulars"]', function() {
    var val = $(this).val();
    var $modal = $('#adjustLeave<?php echo $personnel_id; ?>');
    if (val.toLowerCase().indexOf('terminal') !== -1) {
        var $rem = $modal.find('textarea[name="remarks"]');
        if ($rem.val().trim() === '' || $rem.val() === 'Manual adjustment by HR') {
            $rem.val('Certified Correct');
        }
    }
});
</script>