<!-- DELETE LEAVE CARD ENTRY Modal -->
<div id="delete_lc_entry<?php echo $lc_row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog">
    <div class="modal-content">
      <form action="save_add_leave_card_entry.php" method="POST">
        <input type="hidden" name="leave_card_id" value="<?php echo $lc_row['id']; ?>">
        <input type="hidden" name="id" value="<?php echo $lc_row['id']; ?>">
        <input type="hidden" name="personnel_id" value="<?php echo $personnel_id; ?>">
        <input type="hidden" name="do_id" value="<?php echo $dept_id; ?>">
        
        <div class="modal-header bg-danger text-white">
          <h5 id="exampleModalLabel" class="modal-title">
            <i class="fa fa-trash"></i> DELETE LEAVE CARD ENTRY
          </h5>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close text-white">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        
        <div class="modal-body">
          <div class="alert alert-warning">
            <i class="fa fa-exclamation-triangle"></i> 
            <strong>Warning!</strong> This action cannot be undone.
          </div>
          
          <p>Are you sure you want to delete this leave card entry?</p>
          
          <div class="card">
            <div class="card-body">
              <dl class="row mb-0">
                <dt class="col-sm-4">Period:</dt>
                <dd class="col-sm-8"><?php echo $lc_row['period_from']; ?> to <?php echo $lc_row['period_to']; ?></dd>
                
                <dt class="col-sm-4">Particulars:</dt>
                <dd class="col-sm-8"><?php echo htmlspecialchars($lc_row['particulars']); ?></dd>
                
                <dt class="col-sm-4">VL Earned:</dt>
                <dd class="col-sm-8"><?php echo $lc_row['vl_earned']; ?></dd>
                
                <dt class="col-sm-4">SL Earned:</dt>
                <dd class="col-sm-8 mb-0"><?php echo $lc_row['sl_earned']; ?></dd>
              </dl>
            </div>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" data-dismiss="modal" class="btn btn-secondary">
            <i class="fa fa-times"></i> Cancel
          </button>
          <button type="submit" name="delete_lc_entry" class="btn btn-danger">
            <i class="fa fa-trash"></i> Yes, Delete Entry
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- end DELETE LEAVE CARD ENTRY Modal -->
