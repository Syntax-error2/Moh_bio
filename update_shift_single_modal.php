<?php
$personnel_id_shift = $staff_row['personnel_id'] ?? 0;
$personnel_do_id = !empty($staff_row['do_id']) ? $staff_row['do_id'] : ($_GET['dept'] ?? 0);

if (!empty($staff_row['suffix']) && $staff_row['suffix'] != "-") {
    $fullNameShift = ($staff_row['fname'] ?? '')." ".substr($staff_row['mname'] ?? '', 0, 1).". ".($staff_row['lname'] ?? '')." ".$staff_row['suffix'];
} else {
    $fullNameShift = ($staff_row['fname'] ?? '')." ".substr($staff_row['mname'] ?? '', 0, 1).". ".($staff_row['lname'] ?? '');
}
?>
<div id="updateShiftSingleModal" tabindex="-1" role="dialog" aria-labelledby="updateShiftModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog">
    <div class="modal-content">
      <form action="save_add_personnel.php?dept=<?php echo htmlspecialchars((string)($_GET['dept'] ?? $personnel_do_id)); ?>" method="POST">
        <input value="<?php echo (int)$personnel_id_shift; ?>" name="personnel_id" type="hidden" />
        <input value="<?php echo htmlspecialchars((string)($_SERVER['REQUEST_URI'] ?? '')); ?>" name="return_url" type="hidden" />

        <div class="modal-header">
          <h5 id="updateShiftModalLabel" class="modal-title"><i class="fa fa-clock-o"></i> Set Shift / Schedule [ <?php echo htmlspecialchars($fullNameShift); ?> ]</h5>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
        </div>

        <div class="modal-body">
          <div class="form-group row">
            <div class="col-sm-12">
              <label style="font-weight: bold;">Select Work-Hour Shift / Schedule:</label>
              <select name="shift_id" class="form-control" style="font-size: 15px;">
                <option value="0" <?php if(empty($staff_row['shift_id']) || $staff_row['shift_id'] == 0) echo 'selected'; ?>>-- Not Set --</option>
                <?php
                // Fetch all shifts, displaying department/general shifts clearly
                $shifts_query = $conn->query("SELECT s.*, d.dept_office_name FROM shifts s LEFT JOIN dept_offices d ON s.do_id = d.do_id ORDER BY (s.do_id = '$personnel_do_id' OR s.do_id = 0) DESC, s.shift_name ASC");
                while($sh_row = $shifts_query->fetch()){
                    $isSelected = ((int)($staff_row['shift_id'] ?? 0) === (int)$sh_row['shift_id']) ? 'selected' : '';
                    $scope = ($sh_row['do_id'] == 0) ? 'All Offices' : (!empty($sh_row['dept_office_name']) ? $sh_row['dept_office_name'] : 'Dept #'.$sh_row['do_id']);
                ?>
                <option value="<?php echo (int)$sh_row['shift_id']; ?>" <?php echo $isSelected; ?>>
                  <?php echo htmlspecialchars($sh_row['shift_name'].' ('.$sh_row['type'].') - ['.$scope.']'); ?>
                </option>
                <?php } ?>
              </select>
              <small class="form-text text-muted" style="margin-top: 8px;">
                <i class="fa fa-info-circle text-info"></i> For rotating or swapping duties (6-2, 2-10, 10-6), select their current assigned shift here so time-in and time-out are accurately placed on their DTR.
              </small>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" data-dismiss="modal" class="btn btn-secondary">Cancel</button>
          <button name="set_shift" type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Update Shift</button>
        </div>
      </form>
    </div>
  </div>
</div>
