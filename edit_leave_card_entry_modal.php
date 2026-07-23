<!-- EDIT LEAVE CARD ENTRY Modal -->
<div id="edit_lc_entry<?php echo $lc_row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="save_add_leave_card_entry.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $lc_row['id']; ?>">
        <input type="hidden" name="personnel_id" value="<?php echo $personnel_id; ?>">
        <input type="hidden" name="do_id" value="<?php echo $dept_id; ?>">
        
        <div class="modal-header">
          <h5 id="exampleModalLabel" class="modal-title">EDIT LEAVE CARD ENTRY</h5>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
        </div>
        
        <div class="modal-body">
          <div class="form-group row">
            <div class="col-sm-6">
              <label>Period From <span class="text-danger">*</span></label>
              <input name="period_from" type="date" value="<?php echo $lc_row['period_from']; ?>" class="form-control" required />
            </div>
            <div class="col-sm-6">
              <label>Period To <span class="text-danger">*</span></label>
              <input name="period_to" type="date" value="<?php echo $lc_row['period_to']; ?>" class="form-control" required />
            </div>
          </div>
          
          <div class="form-group">
            <label>Particulars <span class="text-danger">*</span></label>
            <input name="particulars" type="text" value="<?php echo htmlspecialchars($lc_row['particulars']); ?>" class="form-control" required />
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <h6 class="text-primary"><i class="fa fa-umbrella-beach"></i> Vacation Leave</h6>
              <div class="form-group">
                <label>VL Earned</label>
                <input name="vl_earned" type="number" step="0.001" value="<?php echo $lc_row['vl_earned']; ?>" class="form-control" />
              </div>
              <div class="form-group">
                <label>VL With Pay</label>
                <input name="vl_with_pay" type="number" step="0.001" value="<?php echo $lc_row['vl_with_pay']; ?>" class="form-control" />
              </div>
              <div class="form-group">
                <label>VL Without Pay</label>
                <input name="vl_without_pay" type="number" step="0.001" value="<?php echo $lc_row['vl_without_pay']; ?>" class="form-control" />
              </div>
            </div>
            
            <div class="col-md-6">
              <h6 class="text-success"><i class="fa fa-medkit"></i> Sick Leave</h6>
              <div class="form-group">
                <label>SL Earned</label>
                <input name="sl_earned" type="number" step="0.001" value="<?php echo $lc_row['sl_earned']; ?>" class="form-control" />
              </div>
              <div class="form-group">
                <label>SL With Pay</label>
                <input name="sl_with_pay" type="number" step="0.001" value="<?php echo $lc_row['sl_with_pay']; ?>" class="form-control" />
              </div>
              <div class="form-group">
                <label>SL Without Pay</label>
                <input name="sl_without_pay" type="number" step="0.001" value="<?php echo $lc_row['sl_without_pay']; ?>" class="form-control" />
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <label>Remarks</label>
            <input name="remarks" type="text" value="<?php echo htmlspecialchars($lc_row['remarks']); ?>" class="form-control" />
          </div>
          
          <div class="form-group">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input edit-special-leave-checkbox" id="edit_is_special_leave<?php echo $lc_row['id']; ?>" name="is_special_leave" value="1" <?php echo ($lc_row['is_special_leave'] == 1) ? 'checked' : ''; ?>>
              <label class="custom-control-label" for="edit_is_special_leave<?php echo $lc_row['id']; ?>">
                <i class="fa fa-star text-success"></i> Special Leave (No VL/SL Deductions)
              </label>
            </div>
            <small class="form-text text-muted">
              Check this for special leaves (Maternity, Paternity, Solo Parent, VAWC, etc.) that don't deduct from VL/SL balance.
            </small>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" data-dismiss="modal" class="btn btn-secondary">Close</button>
          <button type="submit" name="update_lc_entry" class="btn btn-primary">
            <i class="fa fa-save"></i> Update Entry
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- end EDIT LEAVE CARD ENTRY Modal -->
