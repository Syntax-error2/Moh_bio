<!-- Add Absence / Undertime Modal -->
<div class="modal fade" id="absenceModal" tabindex="-1" role="dialog" aria-labelledby="absenceModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="save_add_absence.php" method="POST">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="absenceModalLabel"><i class="fa fa-minus-circle"></i> Manual Input (Absence / Undertime)</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="personnel_id" value="<?php echo $personnel_id; ?>">
        <input type="hidden" name="dept" value="<?php echo isset($_GET['dept']) ? $_GET['dept'] : ''; ?>">
        
        <div class="form-group">
            <label>Period (e.g. 01-01-26 to 01-31-26)</label>
            <input type="text" class="form-control" name="period" required>
        </div>
        
        <div class="form-group">
            <label>Particulars (e.g. Vacation, Sick, Tardiness)</label>
            <input type="text" class="form-control" name="particulars" required>
        </div>
        
        <hr>
        <h6>Vacation Leave Deduction</h6>
        <div class="row">
            <div class="col-6">
                <label><small>W/ Pay (Deducts Balance)</small></label>
                <input type="number" step="0.001" min="0" class="form-control" name="vl_w_pay" value="0.000">
            </div>
            <div class="col-6">
                <label><small>W/Out Pay (Logs Only)</small></label>
                <input type="number" step="0.001" min="0" class="form-control" name="vl_wout_pay" value="0.000">
            </div>
        </div>
        
        <hr>
        <h6>Sick Leave Deduction</h6>
        <div class="row">
            <div class="col-6">
                <label><small>W/ Pay (Deducts Balance)</small></label>
                <input type="number" step="0.001" min="0" class="form-control" name="sl_w_pay" value="0.000">
            </div>
            <div class="col-6">
                <label><small>W/Out Pay (Logs Only)</small></label>
                <input type="number" step="0.001" min="0" class="form-control" name="sl_wout_pay" value="0.000">
            </div>
        </div>
        
        <hr>
        <div class="form-group">
            <label>Remarks</label>
            <textarea class="form-control" name="remarks" rows="2"></textarea>
        </div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" name="add_absence" class="btn btn-warning"><i class="fa fa-save"></i> Save Entry</button>
      </div>
      </form>
    </div>
  </div>
</div>
