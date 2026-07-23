<?php
/**
 * View Payroll Run
 * Display detailed information about a specific payroll run
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
        SELECT pr.*, 
               pp.profile_name, pp.profile_type,
               creator.fname as creator_fname, creator.lname as creator_lname,
               approver.fname as approver_fname, approver.lname as approver_lname
        FROM pr_tbl_payroll_runs pr
        LEFT JOIN pr_tbl_payroll_profiles pp ON pr.profile_id = pp.profile_id
        LEFT JOIN useraccount creator ON pr.created_by = creator.user_id
        LEFT JOIN useraccount approver ON pr.approved_by = approver.user_id
        WHERE pr.run_id = :run_id
    ");
    $run_query->execute([':run_id' => $run_id]);
    $run = $run_query->fetch(PDO::FETCH_ASSOC);
    
    if (!$run) {
        header('Location: list_payroll_history.php?error=' . urlencode('Payroll run not found'));
        exit();
    }
    
    // Get personnel count by status
    $status_query = $conn->prepare("
        SELECT payment_status, COUNT(*) as count
        FROM pr_tbl_payroll_run_details
        WHERE run_id = :run_id
        GROUP BY payment_status
    ");
    $status_query->execute([':run_id' => $run_id]);
    $status_counts = $status_query->fetchAll(PDO::FETCH_KEY_PAIR);
    
} catch (Exception $e) {
    die("Error loading payroll run: " . $e->getMessage());
}

$page_title = "View Payroll Run";
include('header.php');
include('menu_sidebar.php');
?>

<div class="page">
    <div class="page-content">
        <div class="container-fluid">
            
            <!-- Page Header -->
            <div class="row mb-3">
                <div class="col-md-8">
                    <h1 class="page-title">
                        <i class="fa fa-file-text-o"></i> <?php echo htmlspecialchars($run['run_name']); ?>
                    </h1>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="list_payroll_history.php">Payroll History</a></li>
                        <li class="breadcrumb-item active">View Run</li>
                    </ol>
                </div>
                <div class="col-md-4 text-right">
                    <span class="badge badge-<?php 
                        echo $run['run_status'] === 'completed' ? 'success' : 
                            ($run['run_status'] === 'approved' ? 'info' : 
                            ($run['run_status'] === 'cancelled' ? 'danger' : 'warning')); 
                    ?> badge-lg">
                        <?php echo strtoupper($run['run_status']); ?>
                    </span>
                </div>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fa fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="btn-toolbar" role="toolbar">
                        <div class="btn-group mr-2">
                            <a href="list_payroll_history.php" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                        <div class="btn-group mr-2">
                            <?php if ($run['run_status'] === 'draft'): ?>
                                <a href="edit_payroll_run.php?run_id=<?php echo $run_id; ?>" class="btn btn-primary">
                                    <i class="fa fa-edit"></i> Edit Run
                                </a>
                            <?php endif; ?>
                            <a href="print_payroll_run.php?run_id=<?php echo $run_id; ?>" target="_blank" class="btn btn-info">
                                <i class="fa fa-print"></i> Print
                            </a>
                            <button type="button" class="btn btn-success" onclick="exportToExcel()">
                                <i class="fa fa-file-excel-o"></i> Export Excel
                            </button>
                        </div>
                        <?php if ($run['run_status'] === 'draft'): ?>
                        <div class="btn-group">
                            <button type="button" class="btn btn-warning" onclick="submitForApproval()">
                                <i class="fa fa-check"></i> Submit for Approval
                            </button>
                        </div>
                        <?php elseif ($run['run_status'] === 'pending' && $session_role === 'admin'): ?>
                        <div class="btn-group">
                            <button type="button" class="btn btn-success" onclick="approveRun()">
                                <i class="fa fa-check-circle"></i> Approve
                            </button>
                            <button type="button" class="btn btn-danger" onclick="rejectRun()">
                                <i class="fa fa-times-circle"></i> Reject
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Run Summary -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fa fa-info-circle"></i> Payroll Run Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Run ID:</strong></td>
                                    <td><?php echo $run['run_id']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Profile:</strong></td>
                                    <td>
                                        <a href="view_payroll_profile.php?profile_id=<?php echo $run['profile_id']; ?>">
                                            <?php echo htmlspecialchars($run['profile_name']); ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Run Type:</strong></td>
                                    <td><span class="badge badge-info"><?php echo ucwords(str_replace('_', ' ', $run['run_type'])); ?></span></td>
                                </tr>
                                <tr>
                                    <td><strong>Pay Period:</strong></td>
                                    <td>
                                        <?php echo date('M d, Y', strtotime($run['pay_period_start'])); ?> - 
                                        <?php echo date('M d, Y', strtotime($run['pay_period_end'])); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Date:</strong></td>
                                    <td><?php echo $run['payment_date'] ? date('M d, Y', strtotime($run['payment_date'])) : 'Not set'; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Created By:</strong></td>
                                    <td><?php echo htmlspecialchars($run['creator_fname'] . ' ' . $run['creator_lname']); ?> on <?php echo date('M d, Y h:i A', strtotime($run['created_at'])); ?></td>
                                </tr>
                                <?php if ($run['approved_by']): ?>
                                <tr>
                                    <td><strong>Approved By:</strong></td>
                                    <td><?php echo htmlspecialchars($run['approver_fname'] . ' ' . $run['approver_lname']); ?> on <?php echo date('M d, Y h:i A', strtotime($run['approved_at'])); ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($run['notes']): ?>
                                <tr>
                                    <td><strong>Notes:</strong></td>
                                    <td><?php echo nl2br(htmlspecialchars($run['notes'])); ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Financial Summary -->
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fa fa-money"></i> Financial Summary</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Total Personnel:</strong></td>
                                    <td class="text-right"><h4><?php echo number_format($run['total_personnel']); ?></h4></td>
                                </tr>
                                <tr>
                                    <td><strong>Gross Pay:</strong></td>
                                    <td class="text-right text-success"><h5>₱<?php echo number_format($run['total_gross'], 2); ?></h5></td>
                                </tr>
                                <tr>
                                    <td><strong>Deductions:</strong></td>
                                    <td class="text-right text-danger">₱<?php echo number_format($run['total_deductions'], 2); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Employer Share:</strong></td>
                                    <td class="text-right text-info">₱<?php echo number_format($run['total_employer_share'], 2); ?></td>
                                </tr>
                                <tr class="table-active">
                                    <td><strong>Net Pay:</strong></td>
                                    <td class="text-right"><h4 class="text-primary">₱<?php echo number_format($run['total_net_pay'], 2); ?></h4></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Payment Status Breakdown -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fa fa-pie-chart"></i> Payment Status</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><i class="fa fa-circle text-warning"></i> Pending:</td>
                                    <td class="text-right"><strong><?php echo $status_counts['pending'] ?? 0; ?></strong></td>
                                </tr>
                                <tr>
                                    <td><i class="fa fa-circle text-success"></i> Paid:</td>
                                    <td class="text-right"><strong><?php echo $status_counts['paid'] ?? 0; ?></strong></td>
                                </tr>
                                <tr>
                                    <td><i class="fa fa-circle text-danger"></i> Hold:</td>
                                    <td class="text-right"><strong><?php echo $status_counts['hold'] ?? 0; ?></strong></td>
                                </tr>
                                <tr>
                                    <td><i class="fa fa-circle text-muted"></i> Cancelled:</td>
                                    <td class="text-right"><strong><?php echo $status_counts['cancelled'] ?? 0; ?></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personnel Details Table -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fa fa-users"></i> Personnel Payroll Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="personnelTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th class="text-right">Gross Pay</th>
                                    <th class="text-right">Deductions</th>
                                    <th class="text-right">Net Pay</th>
                                    <th>Payment Status</th>
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
                                <tr>
                                    <td><?php echo htmlspecialchars($detail['personnel_id']); ?></td>
                                    <td><?php echo $full_name; ?></td>
                                    <td><?php echo htmlspecialchars($detail['dept_office_name'] ?? 'N/A'); ?></td>
                                    <td class="text-right">₱<?php echo number_format($detail['gross_pay'], 2); ?></td>
                                    <td class="text-right">₱<?php echo number_format($detail['total_deductions'], 2); ?></td>
                                    <td class="text-right"><strong>₱<?php echo number_format($detail['net_pay'], 2); ?></strong></td>
                                    <td>
                                        <span class="badge badge-<?php 
                                            echo $detail['payment_status'] === 'paid' ? 'success' : 
                                                ($detail['payment_status'] === 'hold' ? 'danger' : 
                                                ($detail['payment_status'] === 'cancelled' ? 'secondary' : 'warning')); 
                                        ?>">
                                            <?php echo ucfirst($detail['payment_status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewPersonnelDetails(<?php echo $detail['detail_id']; ?>)">
                                            <i class="fa fa-eye"></i> Details
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include('footer.php'); ?>
</div>

<!-- Personnel Details Modal -->
<div class="modal fade" id="personnelDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fa fa-user"></i> Personnel Payroll Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="personnelDetailsContent">
                <div class="text-center">
                    <i class="fa fa-spinner fa-spin fa-3x"></i>
                    <p>Loading details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('scripts_files.php'); ?>

<script>
$(document).ready(function() {
    $('#personnelTable').DataTable({
        "pageLength": 25,
        "order": [[1, "asc"]],
        "dom": 'Bfrtip',
        "buttons": ['copy', 'csv', 'excel', 'pdf']
    });
});

function viewPersonnelDetails(detailId) {
    $('#personnelDetailsModal').modal('show');
    $('#personnelDetailsContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i><p>Loading...</p></div>');
    
    $.ajax({
        url: 'ajax_get_personnel_payroll_details.php',
        method: 'GET',
        data: { detail_id: detailId },
        success: function(response) {
            $('#personnelDetailsContent').html(response);
        },
        error: function() {
            $('#personnelDetailsContent').html('<div class="alert alert-danger">Error loading details</div>');
        }
    });
}

function submitForApproval() {
    if (confirm('Submit this payroll run for approval?')) {
        window.location.href = 'update_payroll_status.php?run_id=<?php echo $run_id; ?>&action=submit';
    }
}

function approveRun() {
    if (confirm('Approve this payroll run?')) {
        window.location.href = 'update_payroll_status.php?run_id=<?php echo $run_id; ?>&action=approve';
    }
}

function rejectRun() {
    const reason = prompt('Enter reason for rejection:');
    if (reason) {
        window.location.href = 'update_payroll_status.php?run_id=<?php echo $run_id; ?>&action=reject&reason=' + encodeURIComponent(reason);
    }
}

function exportToExcel() {
    window.location.href = 'export_payroll_run.php?run_id=<?php echo $run_id; ?>&format=excel';
}
</script>

</body>
</html>
