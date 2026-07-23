<?php
/**
 * Payroll History / Payroll Runs List
 * View all payroll runs with filtering and search
 */

// Start output buffering
ob_start();

include('session.php');

// Get filter parameters
$status_filter = $_GET['status'] ?? 'all';
$type_filter = $_GET['type'] ?? 'all';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$search = $_GET['search'] ?? '';

$page_title = "Payroll History";
?>

<!DOCTYPE html>
<html lang="en">

<?php include('header.php'); ?>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
    
    <style>
        .stats-row {
            margin-bottom: 20px;
        }
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
        }
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .filter-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .table-action-btn {
            padding: 4px 8px;
            font-size: 0.85rem;
        }
        .draft-row {
            background-color: #fff9e6 !important;
        }
        .draft-row:hover {
            background-color: #fff3cd !important;
        }
    </style>

<body>

<?php include('menu_sidebar.php'); ?>

<div class="page">

<?php include('navbar_header.php'); ?>

<div class="container-fluid" style="padding: 20px;">
    
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-2">
                <i class="icon-clock"></i> Payroll History
            </h2>
            <p class="text-muted">View and manage all payroll runs</p>
        </div>
        <div class="col-md-4 text-right">
            <a href="list_payroll_profiles.php" class="btn btn-success btn-lg">
                <i class="fa fa-plus"></i> New Payroll Run
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <?php
    try {
        $stats_query = $conn->query("
            SELECT 
                COUNT(*) as total_runs,
                SUM(CASE WHEN run_status = 'completed' THEN 1 ELSE 0 END) as completed_runs,
                SUM(CASE WHEN run_status = 'draft' THEN 1 ELSE 0 END) as draft_runs,
                SUM(total_net_pay) as total_paid,
                SUM(total_personnel) as total_processed
            FROM pr_tbl_payroll_runs
        ");
        $stats = $stats_query->fetch(PDO::FETCH_ASSOC);
    ?>
    <div class="row stats-row">
        <div class="col-md-2">
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['total_runs'] ?? 0; ?></div>
                <div class="stat-label">Total Runs</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card">
                <div class="stat-value" style="color: #28a745;"><?php echo $stats['completed_runs'] ?? 0; ?></div>
                <div class="stat-label">Completed</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card">
                <div class="stat-value" style="color: #ffc107;"><?php echo $stats['draft_runs'] ?? 0; ?></div>
                <div class="stat-label">Drafts</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value" style="color: #17a2b8; font-size: 1.5rem;">
                    ₱<?php echo number_format($stats['total_paid'] ?? 0, 2); ?>
                </div>
                <div class="stat-label">Total Paid</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value" style="color: #6610f2;"><?php echo number_format($stats['total_processed'] ?? 0); ?></div>
                <div class="stat-label">Personnel Processed</div>
            </div>
        </div>
    </div>
    <?php } catch (Exception $e) { ?>
        <div class="alert alert-warning">Statistics temporarily unavailable.</div>
    <?php } ?>

    <!-- Filters -->
    <div class="filter-section">
        <form method="GET" action="" class="form-row align-items-end">
            <div class="form-group col-md-2">
                <label><strong>Status:</strong></label>
                <select name="status" class="form-control">
                    <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                    <option value="draft" <?php echo $status_filter === 'draft' ? 'selected' : ''; ?>>Draft</option>
                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="processing" <?php echo $status_filter === 'processing' ? 'selected' : ''; ?>>Processing</option>
                    <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            
            <div class="form-group col-md-2">
                <label><strong>Type:</strong></label>
                <select name="type" class="form-control">
                    <option value="all" <?php echo $type_filter === 'all' ? 'selected' : ''; ?>>All Types</option>
                    <option value="regular" <?php echo $type_filter === 'regular' ? 'selected' : ''; ?>>Regular</option>
                    <option value="special" <?php echo $type_filter === 'special' ? 'selected' : ''; ?>>Special</option>
                    <option value="13th_month" <?php echo $type_filter === '13th_month' ? 'selected' : ''; ?>>13th Month</option>
                    <option value="bonus" <?php echo $type_filter === 'bonus' ? 'selected' : ''; ?>>Bonus</option>
                </select>
            </div>
            
            <div class="form-group col-md-2">
                <label><strong>Date From:</strong></label>
                <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($date_from); ?>">
            </div>
            
            <div class="form-group col-md-2">
                <label><strong>Date To:</strong></label>
                <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($date_to); ?>">
            </div>
            
            <div class="form-group col-md-3">
                <label><strong>Search:</strong></label>
                <input type="text" name="search" class="form-control" placeholder="Search run name..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            
            <div class="form-group col-md-1">
                <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-filter"></i></button>
                <a href="list_payroll_history.php" class="btn btn-secondary btn-block mt-1"><i class="fa fa-refresh"></i></a>
            </div>
        </form>
    </div>

    <!-- Payroll Runs Table -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fa fa-list"></i> Payroll Runs</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="payrollTable">
                    <thead>
                        <tr>
                            <th>Run ID</th>
                            <th>Run Name</th>
                            <th>Type</th>
                            <th>Pay Period</th>
                            <th>Personnel</th>
                            <th>Gross Pay</th>
                            <th>Deductions</th>
                            <th>Net Pay</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            // Build query with filters
                            $where_clauses = [];
                            $params = [];
                            
                            if ($status_filter !== 'all') {
                                $where_clauses[] = "pr.run_status = :status";
                                $params[':status'] = $status_filter;
                            }
                            
                            if ($type_filter !== 'all') {
                                $where_clauses[] = "pr.run_type = :type";
                                $params[':type'] = $type_filter;
                            }
                            
                            if (!empty($date_from)) {
                                $where_clauses[] = "pr.pay_period_start >= :date_from";
                                $params[':date_from'] = $date_from;
                            }
                            
                            if (!empty($date_to)) {
                                $where_clauses[] = "pr.pay_period_end <= :date_to";
                                $params[':date_to'] = $date_to;
                            }
                            
                            if (!empty($search)) {
                                $where_clauses[] = "pr.run_name LIKE :search";
                                $params[':search'] = '%' . $search . '%';
                            }
                            
                            $where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";
                            
                            $runs_query = $conn->prepare("
                                SELECT pr.*
                                FROM pr_tbl_payroll_runs pr
                                $where_sql
                                ORDER BY pr.created_at DESC
                            ");
                            $runs_query->execute($params);
                            
                            while ($run = $runs_query->fetch(PDO::FETCH_ASSOC)) {
                                // Status badge colors
                                $status_colors = [
                                    'draft' => 'secondary',
                                    'pending' => 'warning',
                                    'approved' => 'info',
                                    'processing' => 'primary',
                                    'completed' => 'success',
                                    'cancelled' => 'danger'
                                ];
                                $status_color = $status_colors[$run['run_status']] ?? 'secondary';
                                
                                $type_colors = [
                                    'regular' => 'primary',
                                    'special' => 'info',
                                    '13th_month' => 'success',
                                    'bonus' => 'warning'
                                ];
                                $type_color = $type_colors[$run['run_type']] ?? 'secondary';
                                
                                ?>
                                <tr class="<?php echo $run['run_status'] === 'draft' ? 'draft-row' : ''; ?>">
                                    <td><strong>#<?php echo $run['run_id']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($run['run_name']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $type_color; ?>">
                                            <?php echo strtoupper(str_replace('_', ' ', $run['run_type'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small>
                                            <?php echo date('M d, Y', strtotime($run['pay_period_start'])); ?><br>
                                            to <?php echo date('M d, Y', strtotime($run['pay_period_end'])); ?>
                                        </small>
                                    </td>
                                    <td class="text-center"><strong><?php echo number_format($run['total_personnel']); ?></strong></td>
                                    <td class="text-right text-success">₱<?php echo number_format($run['total_gross'], 2); ?></td>
                                    <td class="text-right text-danger">₱<?php echo number_format($run['total_deductions'], 2); ?></td>
                                    <td class="text-right"><strong>₱<?php echo number_format($run['total_net_pay'], 2); ?></strong></td>
                                    <td>
                                        <span class="status-badge badge-<?php echo $status_color; ?>">
                                            <?php echo strtoupper($run['run_status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="view_payroll_run.php?run_id=<?php echo $run['run_id']; ?>" 
                                           class="btn btn-sm btn-info table-action-btn" title="View Details">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <?php if ($run['run_status'] === 'draft'): ?>
                                            <a href="edit_payroll_run.php?run_id=<?php echo $run['run_id']; ?>" 
                                               class="btn btn-sm btn-warning table-action-btn" title="Edit">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <button onclick="deletePayrollRun(<?php echo $run['run_id']; ?>, '<?php echo addslashes($run['run_name']); ?>')" 
                                                    class="btn btn-sm btn-danger table-action-btn" title="Delete Draft">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                        <a href="print_payroll_run.php?run_id=<?php echo $run['run_id']; ?>" 
                                           class="btn btn-sm btn-secondary table-action-btn" title="Print" target="_blank">
                                            <i class="fa fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }
                        } catch (Exception $e) {
                            echo '<tr><td colspan="10" class="text-center text-danger">Error loading payroll runs: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

</div><!-- End .page -->

<?php include('scripts_files.php'); ?>

<script>
$(document).ready(function() {
    $('#payrollTable').DataTable({
        "order": [[0, "desc"]],
        "pageLength": 25,
        "language": {
            "search": "Quick Search:",
            "lengthMenu": "Show _MENU_ runs per page"
        }
    });
});

function deletePayrollRun(runId, runName) {
    if (!confirm('Are you sure you want to DELETE this draft payroll run?\n\nRun: ' + runName + '\n\nThis will permanently delete:\n- The payroll run\n- All personnel payroll details\n- All income and deduction records\n\nThis action CANNOT be undone!')) {
        return;
    }
    
    // Show loading state
    const btn = event.target.closest('button');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
    
    $.ajax({
        url: 'delete_payroll_run.php',
        type: 'POST',
        data: { run_id: runId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Payroll run deleted successfully!');
                location.reload();
            } else {
                alert('Error: ' + response.message);
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        },
        error: function(xhr, status, error) {
            alert('An error occurred while deleting the payroll run.\n\n' + error);
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    });
}
</script>

</body>
</html>

<?php
ob_end_flush();
?>
