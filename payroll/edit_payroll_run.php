<?php
/**
 * Edit Payroll Run
 * Modify payroll run details and personnel amounts
 */

include('session.php');

// Get run_id from URL
$run_id = isset($_GET['run_id']) ? intval($_GET['run_id']) : 0;

if (!$run_id) {
    header('Location: list_payroll_history.php?error=' . urlencode('Invalid payroll run ID'));
    exit();
}

// Fetch payroll run details
try {
    $run_query = $conn->prepare("
        SELECT pr.*, pp.profile_name
        FROM pr_tbl_payroll_runs pr
        LEFT JOIN pr_tbl_payroll_profiles pp ON pr.profile_id = pp.profile_id
        WHERE pr.run_id = :run_id
    ");
    $run_query->execute([':run_id' => $run_id]);
    $run = $run_query->fetch(PDO::FETCH_ASSOC);
    
    if (!$run) {
        header('Location: list_payroll_history.php?error=' . urlencode('Payroll run not found'));
        exit();
    }
    
    // Only allow editing of draft runs
    if ($run['run_status'] !== 'draft') {
        header('Location: view_payroll_run.php?run_id=' . $run_id . '&error=' . urlencode('Cannot edit payroll run with status: ' . $run['run_status']));
        exit();
    }
    
} catch (Exception $e) {
    die("Error loading payroll run: " . $e->getMessage());
}

$page_title = "Edit Payroll Run";
include('header.php');
include('menu.php');
?>

<div class="page">
    <div class="page-content">
        <div class="container-fluid">
            
            <!-- Page Header -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <h1 class="page-title">
                        <i class="fa fa-edit"></i> Edit Payroll Run: <?php echo htmlspecialchars($run['run_name']); ?>
                    </h1>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="list_payroll_history.php">Payroll History</a></li>
                        <li class="breadcrumb-item"><a href="view_payroll_run.php?run_id=<?php echo $run_id; ?>">View Run</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fa fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fa fa-exclamation-triangle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Edit Run Info Form -->
            <form method="POST" action="update_payroll_run.php" id="editRunForm">
                <input type="hidden" name="run_id" value="<?php echo $run_id; ?>">
                
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fa fa-info-circle"></i> Payroll Run Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Run Name <span class="text-danger">*</span></strong></label>
                                    <input type="text" class="form-control" name="run_name" 
                                           value="<?php echo htmlspecialchars($run['run_name']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Run Type</strong></label>
                                    <select class="form-control" name="run_type">
                                        <option value="regular" <?php echo $run['run_type'] === 'regular' ? 'selected' : ''; ?>>Regular</option>
                                        <option value="special" <?php echo $run['run_type'] === 'special' ? 'selected' : ''; ?>>Special</option>
                                        <option value="13th_month" <?php echo $run['run_type'] === '13th_month' ? 'selected' : ''; ?>>13th Month</option>
                                        <option value="bonus" <?php echo $run['run_type'] === 'bonus' ? 'selected' : ''; ?>>Bonus</option>
                                        <option value="adjustment" <?php echo $run['run_type'] === 'adjustment' ? 'selected' : ''; ?>>Adjustment</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><strong>Pay Period Start <span class="text-danger">*</span></strong></label>
                                    <input type="date" class="form-control" name="pay_period_start" 
                                           value="<?php echo $run['pay_period_start']; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><strong>Pay Period End <span class="text-danger">*</span></strong></label>
                                    <input type="date" class="form-control" name="pay_period_end" 
                                           value="<?php echo $run['pay_period_end']; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><strong>Payment Date</strong></label>
                                    <input type="date" class="form-control" name="payment_date" 
                                           value="<?php echo $run['payment_date']; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label><strong>Notes</strong></label>
                            <textarea class="form-control" name="notes" rows="3"><?php echo htmlspecialchars($run['notes']); ?></textarea>
                        </div>
                        
                        <div class="text-right">
                            <a href="view_payroll_run.php?run_id=<?php echo $run_id; ?>" class="btn btn-secondary">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                            <button type="submit" name="update_run_info" class="btn btn-primary">
                                <i class="fa fa-save"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Personnel Payroll Details -->
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fa fa-users"></i> Personnel Payroll Details</h5>
                    <div>
                        <button type="button" class="btn btn-light btn-sm" onclick="recalculateAll()">
                            <i class="fa fa-refresh"></i> Recalculate All
                        </button>
                        <button type="button" class="btn btn-info btn-sm" onclick="saveAllChanges()">
                            <i class="fa fa-save"></i> Save All Changes
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="searchPersonnel" placeholder="Search by name or ID...">
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="personnelTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="5%">
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th class="text-right">Gross Pay</th>
                                    <th class="text-right">Deductions</th>
                                    <th class="text-right">Net Pay</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $details_query = $conn->prepare("
                                    SELECT prd.*,
                                           p.fname, p.lname, p.mname,
                                           d.dept_office_name
                                    FROM pr_tbl_payroll_run_details prd
                                    LEFT JOIN personnels p ON prd.personnel_id = p.personnel_id
                                    LEFT JOIN dept_offices d ON p.do_id = d.do_id
                                    WHERE prd.run_id = :run_id
                                    ORDER BY p.lname, p.fname
                                ");
                                $details_query->execute([':run_id' => $run_id]);
                                
                                while ($detail = $details_query->fetch(PDO::FETCH_ASSOC)):
                                    $full_name = htmlspecialchars($detail['lname'] . ', ' . $detail['fname'] . ' ' . ($detail['mname'] ? substr($detail['mname'], 0, 1) . '.' : ''));
                                ?>
                                <tr data-detail-id="<?php echo $detail['detail_id']; ?>">
                                    <td>
                                        <input type="checkbox" class="personnel-checkbox" value="<?php echo $detail['detail_id']; ?>">
                                    </td>
                                    <td><?php echo htmlspecialchars($detail['personnel_id']); ?></td>
                                    <td><?php echo $full_name; ?></td>
                                    <td><?php echo htmlspecialchars($detail['dept_office_name'] ?? 'N/A'); ?></td>
                                    <td class="text-right">
                                        <span class="gross-display">₱<?php echo number_format($detail['gross_pay'], 2); ?></span>
                                    </td>
                                    <td class="text-right">
                                        <span class="deduction-display">₱<?php echo number_format($detail['total_deductions'], 2); ?></span>
                                    </td>
                                    <td class="text-right">
                                        <strong class="net-display">₱<?php echo number_format($detail['net_pay'], 2); ?></strong>
                                    </td>
                                    <td>
                                        <select class="form-control form-control-sm payment-status">
                                            <option value="pending" <?php echo $detail['payment_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="paid" <?php echo $detail['payment_status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                                            <option value="hold" <?php echo $detail['payment_status'] === 'hold' ? 'selected' : ''; ?>>Hold</option>
                                            <option value="cancelled" <?php echo $detail['payment_status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="editPersonnelPayroll(<?php echo $detail['detail_id']; ?>)">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="removePersonnel(<?php echo $detail['detail_id']; ?>)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <strong>Selected: <span id="selectedCount">0</span> personnel</strong>
                        <div class="btn-group ml-3">
                            <button class="btn btn-sm btn-warning" onclick="bulkUpdateStatus('pending')">
                                Set to Pending
                            </button>
                            <button class="btn btn-sm btn-success" onclick="bulkUpdateStatus('paid')">
                                Set to Paid
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="bulkUpdateStatus('hold')">
                                Set to Hold
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include('footer.php'); ?>
</div>

<!-- Edit Personnel Payroll Modal -->
<div class="modal fade" id="editPersonnelModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fa fa-edit"></i> Edit Personnel Payroll</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="editPersonnelContent">
                <div class="text-center">
                    <i class="fa fa-spinner fa-spin fa-3x"></i>
                    <p>Loading...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('scripts_files.php'); ?>

<script>
let changedPersonnel = new Set();

$(document).ready(function() {
    // Select all checkbox
    $('#selectAll').on('change', function() {
        $('.personnel-checkbox').prop('checked', this.checked);
        updateSelectedCount();
    });
    
    $('.personnel-checkbox').on('change', updateSelectedCount);
    
    // Track status changes
    $('.payment-status').on('change', function() {
        const detailId = $(this).closest('tr').data('detail-id');
        changedPersonnel.add(detailId);
    });
    
    // Search functionality
    $('#searchPersonnel').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('#personnelTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});

function updateSelectedCount() {
    const count = $('.personnel-checkbox:checked').length;
    $('#selectedCount').text(count);
}

function editPersonnelPayroll(detailId) {
    $('#editPersonnelModal').modal('show');
    $('#editPersonnelContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i><p>Loading...</p></div>');
    
    $.ajax({
        url: 'ajax_edit_personnel_payroll.php',
        method: 'GET',
        data: { detail_id: detailId, run_id: <?php echo $run_id; ?> },
        success: function(response) {
            $('#editPersonnelContent').html(response);
        },
        error: function() {
            $('#editPersonnelContent').html('<div class="alert alert-danger">Error loading payroll details</div>');
        }
    });
}

function removePersonnel(detailId) {
    if (!confirm('Remove this personnel from the payroll run?')) return;
    
    $.ajax({
        url: 'ajax_remove_personnel_from_run.php',
        method: 'POST',
        data: { detail_id: detailId, run_id: <?php echo $run_id; ?> },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('tr[data-detail-id="' + detailId + '"]').fadeOut(function() {
                    $(this).remove();
                });
                alert('Personnel removed successfully');
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error removing personnel');
        }
    });
}

function bulkUpdateStatus(status) {
    const selected = $('.personnel-checkbox:checked').map(function() {
        return this.value;
    }).get();
    
    if (selected.length === 0) {
        alert('Please select personnel first');
        return;
    }
    
    $.ajax({
        url: 'ajax_bulk_update_payment_status.php',
        method: 'POST',
        data: { 
            detail_ids: selected,
            status: status,
            run_id: <?php echo $run_id; ?>
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        }
    });
}

function saveAllChanges() {
    const changes = [];
    
    $('.payment-status').each(function() {
        const detailId = $(this).closest('tr').data('detail-id');
        if (changedPersonnel.has(detailId)) {
            changes.push({
                detail_id: detailId,
                payment_status: $(this).val()
            });
        }
    });
    
    if (changes.length === 0) {
        alert('No changes to save');
        return;
    }
    
    $.ajax({
        url: 'ajax_save_payroll_changes.php',
        method: 'POST',
        data: { 
            changes: JSON.stringify(changes),
            run_id: <?php echo $run_id; ?>
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Changes saved successfully');
                changedPersonnel.clear();
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        }
    });
}

function recalculateAll() {
    if (!confirm('Recalculate all personnel payroll amounts? This will update gross pay, deductions, and net pay based on current profile settings.')) {
        return;
    }
    
    $.ajax({
        url: 'ajax_recalculate_payroll_run.php',
        method: 'POST',
        data: { run_id: <?php echo $run_id; ?> },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Payroll recalculated successfully');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        }
    });
}
</script>

</body>
</html>
