<!-- SIGNATORIES SETTINGS Modal -->
<style>
  #signatories_settings_modal .modal-body {
    max-height: calc(100vh - 220px);
    overflow-y: auto;
  }
</style>

<div id="signatories_settings_modal" tabindex="-1" role="dialog" aria-labelledby="signatoriesSettingsLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog modal-lg">
    <div class="modal-content">
    
      <div class="modal-header bg-info text-white">
        <h5 id="signatoriesSettingsLabel" class="modal-title">
          <i class="fa fa-cog"></i> CS FORM NO. 6 - SIGNATORIES SETTINGS
        </h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close text-white">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <form id="signatories_settings_form">
      <div class="modal-body">
        
        <div class="alert alert-info">
          <i class="fa fa-info-circle"></i> <strong>Note:</strong> Configure the signatories for CS Form No. 6 (Application for Leave). These settings will be used for all leave application printouts.
        </div>
        
        <!-- HRMO Section -->
        <div class="card mb-3">
          <div class="card-header bg-primary text-white">
            <strong><i class="fa fa-user"></i> HRMO / Certifying Officer</strong>
          </div>
          <div class="card-body">
            <div class="form-group row">
              <label class="col-sm-3 form-control-label">Name:</label>
              <div class="col-sm-9">
                <input type="text" name="hrmo_name" id="hrmo_name" class="form-control" placeholder="e.g., MARIA CLARA D. SANTOS" />
                <small class="form-text text-muted">Full name of the HRMO or authorized certifying officer</small>
              </div>
            </div>
            
            <div class="form-group row">
              <label class="col-sm-3 form-control-label">Position:</label>
              <div class="col-sm-9">
                <input type="text" name="hrmo_position" id="hrmo_position" class="form-control" placeholder="e.g., Human Resource Management Officer III" value="Human Resource Management Officer" />
                <small class="form-text text-muted">Official position/designation</small>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Recommending Officer Section -->
        <div class="card mb-3">
          <div class="card-header bg-success text-white">
            <strong><i class="fa fa-user-check"></i> Recommending Officer (Immediate Supervisor)</strong>
          </div>
          <div class="card-body">
            <div class="form-group row">
              <label class="col-sm-3 form-control-label">Name:</label>
              <div class="col-sm-9">
                <input type="text" name="recommending_name" id="recommending_name" class="form-control" placeholder="e.g., DR. JUAN DELA CRUZ" />
                <small class="form-text text-muted">Full name of the immediate supervisor</small>
              </div>
            </div>
            
            <div class="form-group row">
              <label class="col-sm-3 form-control-label">Position:</label>
              <div class="col-sm-9">
                <input type="text" name="recommending_position" id="recommending_position" class="form-control" placeholder="e.g., Chief, Medical Division" value="Immediate Supervisor" />
                <small class="form-text text-muted">Official position/designation</small>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Approving Officer Section -->
        <div class="card mb-3">
          <div class="card-header bg-warning text-dark">
            <strong><i class="fa fa-user-shield"></i> Approving Officer (Head of Agency)</strong>
          </div>
          <div class="card-body">
            <div class="form-group row">
              <label class="col-sm-3 form-control-label">Name:</label>
              <div class="col-sm-9">
                <input type="text" name="approving_name" id="approving_name" class="form-control" placeholder="e.g., DR. ADRIANO G. SUBA-AN" />
                <small class="form-text text-muted">Full name of the head of agency or authorized representative</small>
              </div>
            </div>
            
            <div class="form-group row">
              <label class="col-sm-3 form-control-label">Position:</label>
              <div class="col-sm-9">
                <input type="text" name="approving_position" id="approving_position" class="form-control" placeholder="e.g., Regional Director" value="Regional Director" />
                <small class="form-text text-muted">Official position/designation</small>
              </div>
            </div>
          </div>
        </div>
        
        <!-- MONETIZED LEAVE SETTINGS -->
        <div class="card mb-3 border-danger">
          <div class="card-header bg-danger text-white">
            <strong><i class="fa fa-calculator"></i> MONETIZED LEAVE SETTINGS</strong>
          </div>
          <div class="card-body">
            <div class="form-group row">
              <label class="col-sm-3 form-control-label">Constant:</label>
              <div class="col-sm-9">
                <input type="number" step="0.0000001" name="monetization_constant" id="monetization_constant" class="form-control" placeholder="0.0481927" value="0.0481927" />
                <small class="form-text text-muted"><strong>Formula:</strong> Total Monetization = (Monthly Salary × No. of days Leave Credit) × Constant</small>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Budget Officer Section -->
        <div class="card mb-3">
          <div class="card-header bg-secondary text-white">
            <strong><i class="fa fa-user"></i> Municipal Budget Officer</strong>
          </div>
          <div class="card-body">
            <div class="form-group row">
              <label class="col-sm-3 form-control-label">Name:</label>
              <div class="col-sm-9">
                <input type="text" name="budget_officer_name" id="budget_officer_name" class="form-control" placeholder="e.g., ALTHEA C. VILLARUBIA" />
                <small class="form-text text-muted">Full name of the Municipal Budget Officer</small>
              </div>
            </div>
            
            <div class="form-group row">
              <label class="col-sm-3 form-control-label">Position:</label>
              <div class="col-sm-9">
                <input type="text" name="budget_officer_position" id="budget_officer_position" class="form-control" value="Municipal Budget Officer" />
                <small class="form-text text-muted">Official position/designation</small>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Treasurer Section -->
        <div class="card mb-3">
          <div class="card-header bg-secondary text-white">
            <strong><i class="fa fa-user"></i> Municipal Treasurer</strong>
          </div>
          <div class="card-body">
            <div class="form-group row">
              <label class="col-sm-3 form-control-label">Name:</label>
              <div class="col-sm-9">
                <input type="text" name="treasurer_name" id="treasurer_name" class="form-control" placeholder="e.g., JOSEPHINE V. QUINTOS" />
                <small class="form-text text-muted">Full name of the Municipal Treasurer</small>
              </div>
            </div>
            
            <div class="form-group row">
              <label class="col-sm-3 form-control-label">Position:</label>
              <div class="col-sm-9">
                <input type="text" name="treasurer_position" id="treasurer_position" class="form-control" value="Acting Municipal Treasurer" />
                <small class="form-text text-muted">Official position/designation</small>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Accountant Section -->
        <div class="card mb-3">
          <div class="card-header bg-secondary text-white">
            <strong><i class="fa fa-user"></i> Municipal Accountant</strong>
          </div>
          <div class="card-body">
            <div class="form-group row">
              <label class="col-sm-3 form-control-label">Name:</label>
              <div class="col-sm-9">
                <input type="text" name="accountant_name" id="accountant_name" class="form-control" placeholder="e.g., OFELIA V. TUPAS" />
                <small class="form-text text-muted">Full name of the Municipal Accountant</small>
              </div>
            </div>
            
            <div class="form-group row">
              <label class="col-sm-3 form-control-label">Position:</label>
              <div class="col-sm-9">
                <input type="text" name="accountant_position" id="accountant_position" class="form-control" value="Municipal Accountant" />
                <small class="form-text text-muted">Official position/designation</small>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Mayor Section -->
        <div class="card mb-3">
          <div class="card-header bg-dark text-white">
            <strong><i class="fa fa-user-tie"></i> Municipal Mayor</strong>
          </div>
          <div class="card-body">
            <div class="form-group row">
              <label class="col-sm-3 form-control-label">Name:</label>
              <div class="col-sm-9">
                <input type="text" name="mayor_name" id="mayor_name" class="form-control" placeholder="e.g., DAPH ANTHONY V. RELIQUIAS" />
                <small class="form-text text-muted">Full name of the Municipal Mayor</small>
              </div>
            </div>
            
            <div class="form-group row">
              <label class="col-sm-3 form-control-label">Position:</label>
              <div class="col-sm-9">
                <input type="text" name="mayor_position" id="mayor_position" class="form-control" value="Municipal Mayor" />
                <small class="form-text text-muted">Official position/designation</small>
              </div>
            </div>
          </div>
        </div>
        
      </div>
      
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-secondary">
          <i class="fa fa-times"></i> Cancel
        </button>
        <button type="button" onclick="saveSignatoriesSettings()" class="btn btn-primary">
          <i class="fa fa-save"></i> Save Settings
        </button>
      </div>
      </form>
    </div>
  </div>
</div>
<!-- end SIGNATORIES SETTINGS Modal -->

<script>
// Wait for jQuery to be available
(function() {
    function initSignatoriesScripts() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initSignatoriesScripts, 50);
            return;
        }
        
        var $ = jQuery;

$(document).ready(function() {
    // Load existing signatories settings when page loads
    loadSignatoriesSettings();
});

function loadSignatoriesSettings() {
    $.ajax({
        url: 'get_signatories_settings.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data) {
                $('#hrmo_name').val(response.data.hrmo_name || '');
                $('#hrmo_position').val(response.data.hrmo_position || 'Human Resource Management Officer');
                $('#recommending_name').val(response.data.recommending_name || '');
                $('#recommending_position').val(response.data.recommending_position || 'Immediate Supervisor');
                $('#approving_name').val(response.data.approving_name || '');
                $('#approving_position').val(response.data.approving_position || 'Regional Director');
                $('#monetization_constant').val(response.data.monetization_constant || '0.0481927');
                $('#budget_officer_name').val(response.data.budget_officer_name || '');
                $('#budget_officer_position').val(response.data.budget_officer_position || 'Municipal Budget Officer');
                $('#treasurer_name').val(response.data.treasurer_name || '');
                $('#treasurer_position').val(response.data.treasurer_position || 'Acting Municipal Treasurer');
                $('#accountant_name').val(response.data.accountant_name || '');
                $('#accountant_position').val(response.data.accountant_position || 'Municipal Accountant');
                $('#mayor_name').val(response.data.mayor_name || '');
                $('#mayor_position').val(response.data.mayor_position || 'Municipal Mayor');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading signatories settings:', error);
        }
    });
}

function saveSignatoriesSettings() {
    var formData = {
        hrmo_name: $('#hrmo_name').val(),
        hrmo_position: $('#hrmo_position').val(),
        recommending_name: $('#recommending_name').val(),
        recommending_position: $('#recommending_position').val(),
        approving_name: $('#approving_name').val(),
        approving_position: $('#approving_position').val(),
        monetization_constant: $('#monetization_constant').val(),
        budget_officer_name: $('#budget_officer_name').val(),
        budget_officer_position: $('#budget_officer_position').val(),
        treasurer_name: $('#treasurer_name').val(),
        treasurer_position: $('#treasurer_position').val(),
        accountant_name: $('#accountant_name').val(),
        accountant_position: $('#accountant_position').val(),
        mayor_name: $('#mayor_name').val(),
        mayor_position: $('#mayor_position').val()
    };
    
    $.ajax({
        url: 'save_signatories_settings.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Show success notification
                showNotification('success', 'Signatories settings saved successfully!');
                
                // Close modal
                $('#signatories_settings_modal').modal('hide');
                
                // Reload print preview if open
                if (currentLeaveAppId) {
                    loadLeaveApplicationForPrint(currentLeaveAppId);
                }
            } else {
                showNotification('danger', 'Error: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            showNotification('danger', 'Error saving settings: ' + error);
        }
    });
}

function showNotification(type, message) {
    var alertClass = 'alert-' + type;
    var notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade show" style="position: fixed; top: 80px; right: 20px; z-index: 9999; min-width: 300px;">' +
        '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
        '<i class="fa fa-' + (type === 'success' ? 'check-circle' : 'exclamation-triangle') + '"></i> ' + message +
        '</div>');
    
    $('body').append(notification);
    
    setTimeout(function() {
        notification.fadeOut(400, function() {
            $(this).remove();
        });
    }, 3000);
}

    // Make functions globally available
    window.saveSignatoriesSettings = saveSignatoriesSettings;
    window.loadSignatoriesSettings = loadSignatoriesSettings;
    window.showNotification = showNotification;

    } // End initSignatoriesScripts
    
    initSignatoriesScripts();
})();
</script>
