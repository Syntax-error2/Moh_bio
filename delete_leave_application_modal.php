<!-- DELETE LEAVE APPLICATION Modal -->
<div id="delete_leave_application" tabindex="-1" role="dialog" aria-labelledby="deleteLeaveModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog">
    <div class="modal-content">
    
    <form action="save_leave_application.php" method="POST">
    
    <input name="leave_application_id" id="delete_leave_application_id" type="hidden" />
    <input name="personnel_id" value="<?php echo $staff_row['personnel_id']; ?>" type="hidden" />
    <input name="dept" value="<?php echo isset($_GET['dept']) ? htmlspecialchars($_GET['dept']) : ''; ?>" type="hidden" />
    
      <div class="modal-header bg-danger text-white">
        <h5 id="deleteLeaveModalLabel" class="modal-title">
          <i class="fa fa-trash"></i> DELETE LEAVE APPLICATION
        </h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close text-white">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <div class="modal-body">
        
        <div class="alert alert-danger">
          <i class="fa fa-exclamation-triangle"></i> <strong>WARNING!</strong> This action cannot be undone.
        </div>
        
        <p class="mb-3">Are you sure you want to delete this leave application?</p>
        
        <div class="card">
          <div class="card-body">
            <h6 class="card-title text-bold mb-3">Leave Application Details:</h6>
            
            <div class="row mb-2">
              <div class="col-5 text-bold">Application Date:</div>
              <div class="col-7" id="delete_display_application_date">-</div>
            </div>
            
            <div class="row mb-2">
              <div class="col-5 text-bold">Leave Type:</div>
              <div class="col-7" id="delete_display_leave_type">-</div>
            </div>
            
            <div class="row mb-2">
              <div class="col-5 text-bold">Inclusive Dates:</div>
              <div class="col-7" id="delete_display_dates">-</div>
            </div>
            
            <div class="row mb-2">
              <div class="col-5 text-bold">Number of Days:</div>
              <div class="col-7" id="delete_display_days">-</div>
            </div>
            
            <div class="row">
              <div class="col-5 text-bold">Status:</div>
              <div class="col-7" id="delete_display_status">-</div>
            </div>
          </div>
        </div>
        
      </div>
      
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-secondary"><i class="fa fa-times"></i> Cancel</button>
        <button name="delete_leave_application" type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Delete Application</button>
      </div>
      </form>
    </div>
  </div>
</div>
<!-- end DELETE LEAVE APPLICATION Modal -->

<script>
// Wait for jQuery to be available
(function() {
    function initDeleteLeaveAppScripts() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initDeleteLeaveAppScripts, 50);
            return;
        }
        
        var $ = jQuery;

// Populate delete modal with existing data
function deleteLeaveApplication(id) {
    $.ajax({
        url: 'get_leave_application.php',
        type: 'POST',
        data: {leave_application_id: id},
        dataType: 'json',
        success: function(data) {
            $('#delete_leave_application_id').val(data.id);
            
            // Format and display data
            var appDate = new Date(data.application_date);
            var formattedAppDate = appDate.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            $('#delete_display_application_date').text(formattedAppDate);
            
            $('#delete_display_leave_type').text(data.leave_type);
            
            var dateFrom = new Date(data.inclusive_date_from);
            var dateTo = new Date(data.inclusive_date_to);
            var formattedDateFrom = dateFrom.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            var formattedDateTo = dateTo.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            $('#delete_display_dates').text(formattedDateFrom + ' - ' + formattedDateTo);
            
            $('#delete_display_days').text(data.number_of_days + ' day(s)');
            
            var statusBadge = '';
            if (data.status === 'approved') {
                statusBadge = '<span class="badge badge-success">Approved</span>';
            } else if (data.status === 'disapproved') {
                statusBadge = '<span class="badge badge-danger">Disapproved</span>';
            } else {
                statusBadge = '<span class="badge badge-warning">Pending</span>';
            }
            $('#delete_display_status').html(statusBadge);
            
            $('#delete_leave_application').modal('show');
        }
    });
}

    // Make function globally available
    window.deleteLeaveApplication = deleteLeaveApplication;

    } // End initDeleteLeaveAppScripts
    
    initDeleteLeaveAppScripts();
})();
</script>
