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
                        <div class="col-md-4 form-group">
                            <label>Period From</label>
                            <input type="text" name="period_from" class="form-control" placeholder="01-01-26" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Period To</label>
                            <input type="text" name="period_to" class="form-control" placeholder="01-31-26" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Particulars</label>
                            <input type="text" name="particulars" class="form-control" placeholder="Balance Forwarded" value="Balance Forwarded" required>
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
                        <label>Remarks / Reason</label>
                        <textarea name="remarks" class="form-control" rows="2" placeholder="e.g. Initial balance entry, Carry over..."></textarea>
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
