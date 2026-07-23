<?php
include('dbcon.php');
include('session.php');

header('Content-Type: text/html; charset=utf-8');

// Get detail_id from request
$detail_id = isset($_GET['detail_id']) ? intval($_GET['detail_id']) : 0;

if ($detail_id <= 0) {
    echo '<div class="alert alert-danger">Invalid detail ID</div>';
    exit();
}

try {
    // Get personnel payroll details
    $detail_query = $conn->prepare("
        SELECT prd.*,
               p.fname, p.lname, p.mname, p.personnel_id_code,
               d.dept_office_name,
               des.des_name as designation_name,
               pr.run_name, pr.pay_period_start, pr.pay_period_end, pr.run_status
        FROM pr_tbl_payroll_run_details prd
        LEFT JOIN personnels p ON prd.personnel_id = p.personnel_id
        LEFT JOIN dept_offices d ON p.do_id = d.do_id
        LEFT JOIN designation des ON p.des_id = des.des_id
        LEFT JOIN pr_tbl_payroll_runs pr ON prd.run_id = pr.run_id
        WHERE prd.detail_id = :detail_id
    ");
    $detail_query->execute([':detail_id' => $detail_id]);
    $detail = $detail_query->fetch(PDO::FETCH_ASSOC);
    
    if (!$detail) {
        echo '<div class="alert alert-danger">Personnel payroll details not found</div>';
        exit();
    }
    
    // Get income breakdown
    $income_query = $conn->prepare("
        SELECT pri.*, i.income_title, i.income_type
        FROM pr_tbl_payroll_run_income pri
        LEFT JOIN pr_tbl_income i ON pri.income_id = i.income_id
        WHERE pri.detail_id = :detail_id
        ORDER BY i.income_type, i.income_title
    ");
    $income_query->execute([':detail_id' => $detail_id]);
    $income_items = $income_query->fetchAll(PDO::FETCH_ASSOC);
    
    // Get deduction breakdown
    $deduction_query = $conn->prepare("
        SELECT prd.*, d.deduction_title, d.deduction_type
        FROM pr_tbl_payroll_run_deductions prd
        LEFT JOIN pr_tbl_deductions d ON prd.deduction_id = d.deduction_id
        WHERE prd.detail_id = :detail_id
        ORDER BY d.deduction_type, d.deduction_title
    ");
    $deduction_query->execute([':detail_id' => $detail_id]);
    $deduction_items = $deduction_query->fetchAll(PDO::FETCH_ASSOC);
    
    // Format full name
    $full_name = $detail['lname'] . ', ' . $detail['fname'] . ' ' . ($detail['mname'] ? substr($detail['mname'], 0, 1) . '.' : '');
    
    // Check if run is editable (draft status only)
    $is_editable = ($detail['run_status'] ?? '') === 'draft';
    
    ?>
    
    <div class="personnel-details" data-detail-id="<?php echo $detail_id; ?>">
        
        <!-- Edit Mode Toggle -->
        <?php if ($is_editable): ?>
        <div class="alert alert-info mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <span><i class="fa fa-info-circle"></i> This payroll run is in <strong>DRAFT</strong> status. You can edit the amounts below.</span>
                <button type="button" class="btn btn-sm btn-primary" id="toggleEditMode">
                    <i class="fa fa-edit"></i> Enable Edit Mode
                </button>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Personnel Information -->
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fa fa-user"></i> Personnel Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($full_name); ?></p>
                        <p><strong>ID:</strong> <?php echo htmlspecialchars($detail['personnel_id_code'] ?? $detail['personnel_id']); ?></p>
                        <p><strong>Department:</strong> <?php echo htmlspecialchars($detail['dept_office_name'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Designation:</strong> <?php echo htmlspecialchars($detail['designation_name'] ?? 'N/A'); ?></p>
                        <p><strong>Payroll Run:</strong> <?php echo htmlspecialchars($detail['run_name']); ?></p>
                        <p><strong>Pay Period:</strong> <?php echo date('M d, Y', strtotime($detail['pay_period_start'])); ?> - <?php echo date('M d, Y', strtotime($detail['pay_period_end'])); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Income Breakdown -->
        <div class="card mb-3">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fa fa-plus-circle"></i> Income Breakdown</h5>
            </div>
            <div class="card-body">
                <?php if (count($income_items) > 0): ?>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Income Item</th>
                            <th>Type</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_income = 0;
                        foreach ($income_items as $item): 
                            $total_income += $item['amount'];
                        ?>
                        <tr data-income-id="<?php echo $item['run_income_id']; ?>">
                            <td><?php echo htmlspecialchars($item['income_title']); ?></td>
                            <td><span class="badge badge-info"><?php echo htmlspecialchars($item['income_type']); ?></span></td>
                            <td class="text-right">
                                <span class="view-mode">₱<?php echo number_format($item['amount'], 2); ?></span>
                                <input type="number" class="form-control form-control-sm text-right edit-mode" 
                                       style="display:none; width: 120px; display: inline-block;" 
                                       value="<?php echo $item['amount']; ?>" 
                                       step="0.01" 
                                       data-field="income_amount">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="table-success font-weight-bold">
                            <td colspan="2" class="text-right">Total Income:</td>
                            <td class="text-right">₱<?php echo number_format($total_income, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
                <?php else: ?>
                <p class="text-muted">No income items recorded</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Deduction Breakdown -->
        <div class="card mb-3">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fa fa-minus-circle"></i> Deduction Breakdown</h5>
            </div>
            <div class="card-body">
                <?php if (count($deduction_items) > 0): ?>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Deduction Item</th>
                            <th>Type</th>
                            <th class="text-right">Employee Share</th>
                            <th class="text-right">Employer Share</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_employee_share = 0;
                        $total_employer_share = 0;
                        foreach ($deduction_items as $item): 
                            $total_employee_share += $item['employee_amount'];
                            $total_employer_share += $item['employer_amount'];
                            $item_total = $item['employee_amount'] + $item['employer_amount'];
                        ?>
                        <tr data-deduction-id="<?php echo $item['run_deduction_id']; ?>">
                            <td><?php echo htmlspecialchars($item['deduction_title']); ?></td>
                            <td><span class="badge badge-warning"><?php echo htmlspecialchars($item['deduction_type']); ?></span></td>
                            <td class="text-right">
                                <span class="view-mode">₱<?php echo number_format($item['employee_amount'], 2); ?></span>
                                <input type="number" class="form-control form-control-sm text-right edit-mode" 
                                       style="display:none; width: 120px; display: inline-block;" 
                                       value="<?php echo $item['employee_amount']; ?>" 
                                       step="0.01" 
                                       data-field="employee_amount">
                            </td>
                            <td class="text-right">
                                <span class="view-mode">₱<?php echo number_format($item['employer_amount'], 2); ?></span>
                                <input type="number" class="form-control form-control-sm text-right edit-mode" 
                                       style="display:none; width: 120px; display: inline-block;" 
                                       value="<?php echo $item['employer_amount']; ?>" 
                                       step="0.01" 
                                       data-field="employer_amount">
                            </td>
                            <td class="text-right">
                                <span class="view-mode">₱<?php echo number_format($item_total, 2); ?></span>
                                <span class="edit-mode" style="display:none;">-</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="table-warning font-weight-bold">
                            <td colspan="2" class="text-right">Total Deductions:</td>
                            <td class="text-right">₱<?php echo number_format($total_employee_share, 2); ?></td>
                            <td class="text-right">₱<?php echo number_format($total_employer_share, 2); ?></td>
                            <td class="text-right">₱<?php echo number_format($total_employee_share + $total_employer_share, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
                <p class="text-muted small mb-0"><em>Note: Employee share is deducted from gross pay. Employer share is additional cost to employer.</em></p>
                <?php else: ?>
                <p class="text-muted">No deductions recorded</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Summary -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fa fa-calculator"></i> Payroll Summary</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td><strong>Gross Pay:</strong></td>
                        <td class="text-right">₱<?php echo number_format($detail['gross_pay'], 2); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Total Deductions (Employee):</strong></td>
                        <td class="text-right text-danger">- ₱<?php echo number_format($detail['total_deductions'], 2); ?></td>
                    </tr>
                    <tr class="table-success">
                        <td><strong>Net Pay:</strong></td>
                        <td class="text-right"><strong>₱<?php echo number_format($detail['net_pay'], 2); ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong>Employer Share:</strong></td>
                        <td class="text-right text-info">₱<?php echo number_format($detail['total_employer_share'], 2); ?></td>
                    </tr>
                    <tr class="table-info">
                        <td><strong>Total Cost to Employer:</strong></td>
                        <td class="text-right"><strong>₱<?php echo number_format($detail['gross_pay'] + $detail['total_employer_share'], 2); ?></strong></td>
                    </tr>
                </table>
                
                <hr>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Payment Status:</strong> 
                            <span class="badge badge-<?php 
                                echo $detail['payment_status'] == 'paid' ? 'success' : 
                                    ($detail['payment_status'] == 'pending' ? 'warning' : 'secondary'); 
                            ?>">
                                <?php echo ucfirst($detail['payment_status']); ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6 text-right">
                        <?php if (!empty($detail['payment_date'])): ?>
                        <p class="mb-1"><strong>Payment Date:</strong> <?php echo date('M d, Y', strtotime($detail['payment_date'])); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if (!empty($detail['notes'])): ?>
                <div class="mt-3">
                    <strong>Notes:</strong>
                    <p class="text-muted mb-0"><?php echo nl2br(htmlspecialchars($detail['notes'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Save Changes Button (Edit Mode Only) -->
        <?php if ($is_editable): ?>
        <div class="text-right mt-3" id="saveChangesSection" style="display: none;">
            <button type="button" class="btn btn-secondary" id="cancelEditMode">
                <i class="fa fa-times"></i> Cancel
            </button>
            <button type="button" class="btn btn-success" id="saveChanges">
                <i class="fa fa-save"></i> Save Changes
            </button>
        </div>
        <?php endif; ?>
    </div>
    
    <script>
    $(document).ready(function() {
        let editMode = false;
        
        // Toggle Edit Mode
        $('#toggleEditMode').click(function() {
            editMode = !editMode;
            if (editMode) {
                $(this).html('<i class="fa fa-eye"></i> View Mode').removeClass('btn-primary').addClass('btn-secondary');
                $('.view-mode').hide();
                $('.edit-mode').show();
                $('#saveChangesSection').show();
            } else {
                $(this).html('<i class="fa fa-edit"></i> Enable Edit Mode').removeClass('btn-secondary').addClass('btn-primary');
                $('.view-mode').show();
                $('.edit-mode').hide();
                $('#saveChangesSection').hide();
            }
        });
        
        // Cancel Edit Mode
        $('#cancelEditMode').click(function() {
            if (confirm('Cancel changes? Any unsaved modifications will be lost.')) {
                location.reload(); // Reload modal content
            }
        });
        
        // Save Changes
        $('#saveChanges').click(function() {
            const detailId = $('.personnel-details').data('detail-id');
            
            // Collect income changes
            const incomeChanges = [];
            $('tr[data-income-id]').each(function() {
                const incomeId = $(this).data('income-id');
                const amount = $(this).find('input[data-field="income_amount"]').val();
                incomeChanges.push({ income_id: incomeId, amount: parseFloat(amount) });
            });
            
            // Collect deduction changes
            const deductionChanges = [];
            $('tr[data-deduction-id]').each(function() {
                const deductionId = $(this).data('deduction-id');
                const employeeAmount = $(this).find('input[data-field="employee_amount"]').val();
                const employerAmount = $(this).find('input[data-field="employer_amount"]').val();
                deductionChanges.push({
                    deduction_id: deductionId,
                    employee_amount: parseFloat(employeeAmount),
                    employer_amount: parseFloat(employerAmount)
                });
            });
            
            // Show loading
            $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
            
            // Send AJAX request
            $.ajax({
                url: 'update_personnel_payroll.php',
                method: 'POST',
                data: {
                    detail_id: detailId,
                    income_items: JSON.stringify(incomeChanges),
                    deduction_items: JSON.stringify(deductionChanges)
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message || 'Changes saved successfully!');
                        // Reload the modal content
                        viewPersonnelDetails(detailId);
                        // Optionally reload the main page to update totals
                        if (typeof reloadPayrollData === 'function') {
                            reloadPayrollData();
                        }
                    } else {
                        alert('Error: ' + (response.message || 'Failed to save changes'));
                        $('#saveChanges').prop('disabled', false).html('<i class="fa fa-save"></i> Save Changes');
                    }
                },
                error: function() {
                    alert('Error: Failed to save changes. Please try again.');
                    $('#saveChanges').prop('disabled', false).html('<i class="fa fa-save"></i> Save Changes');
                }
            });
        });
    });
    </script>
    
    <?php
    
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>
