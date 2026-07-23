<?php
/**
 * Generate Payroll from Profile
 * Creates a new payroll run based on a template/profile
 */

// Start output buffering
ob_start();

include('session.php');

// Get profile ID
$profile_id = $_GET['profile_id'] ?? '';

if (empty($profile_id)) {
    ob_end_clean();
    header('Location: list_payroll_profiles.php?error=' . urlencode('Profile ID is required'));
    exit();
}

// Get profile details
try {
    $profile_query = $conn->prepare("
        SELECT * FROM pr_tbl_payroll_profiles
        WHERE profile_id = :profile_id AND is_active = 1
    ");
    $profile_query->execute([':profile_id' => $profile_id]);
    $profile = $profile_query->fetch(PDO::FETCH_ASSOC);
    
    if (!$profile) {
        throw new Exception('Profile not found or inactive');
    }
    
    // Get income items in profile
    $income_query = $conn->prepare("
        SELECT ppi.*, i.income_title, i.income_type
        FROM pr_tbl_payroll_profile_income ppi
        INNER JOIN pr_tbl_income i ON ppi.income_id = i.income_id
        WHERE ppi.profile_id = :profile_id
        ORDER BY ppi.display_order ASC, i.income_title ASC
    ");
    $income_query->execute([':profile_id' => $profile_id]);
    $income_items = $income_query->fetchAll(PDO::FETCH_ASSOC);
    
    // Get deduction items in profile
    $deduction_query = $conn->prepare("
        SELECT ppd.*, d.deduction_title, d.deduction_type
        FROM pr_tbl_payroll_profile_deductions ppd
        INNER JOIN pr_tbl_deductions d ON ppd.deduction_id = d.deduction_id
        WHERE ppd.profile_id = :profile_id
        ORDER BY ppd.display_order ASC, d.deduction_title ASC
    ");
    $deduction_query->execute([':profile_id' => $profile_id]);
    $deduction_items = $deduction_query->fetchAll(PDO::FETCH_ASSOC);
    
    // Get filter criteria
    $filter_query = $conn->prepare("
        SELECT * FROM pr_tbl_payroll_profile_filters
        WHERE profile_id = :profile_id
    ");
    $filter_query->execute([':profile_id' => $profile_id]);
    $filters = $filter_query->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    ob_end_clean();
    header('Location: list_payroll_profiles.php?error=' . urlencode('Error loading profile: ' . $e->getMessage()));
    exit();
}

$page_title = "Generate Payroll from Profile";
?>

<!DOCTYPE html>
<html lang="en">

<?php include('header.php'); ?>
    
    <style>
        .profile-info-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .profile-info-box h3 {
            margin: 0 0 10px 0;
        }
        .personnel-filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .item-preview {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .item-count {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
        }
        .generate-section {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
    </style>

<body>

<?php include('menu_sidebar.php'); ?>

<div class="page">

<?php include('navbar_header.php'); ?>

<div class="container-fluid" style="padding: 20px;">
    
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>
                <i class="fa fa-cogs"></i> Generate Payroll from Template
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="list_payroll_profiles.php">Payroll Profiles</a></li>
                    <li class="breadcrumb-item active">Generate Payroll</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Profile Information -->
    <div class="profile-info-box">
        <h3><i class="icon-bill"></i> <?php echo htmlspecialchars($profile['profile_name']); ?></h3>
        <p class="mb-2"><?php echo htmlspecialchars($profile['profile_description'] ?: 'No description'); ?></p>
        <div class="row">
            <div class="col-md-4">
                <strong>Type:</strong> <?php echo strtoupper(str_replace('_', ' ', $profile['profile_type'])); ?>
            </div>
            <div class="col-md-4">
                <strong>Frequency:</strong> <?php echo ucfirst(str_replace(['_', '-'], ' ', $profile['pay_frequency'])); ?>
            </div>
            <div class="col-md-4">
                <strong>Profile ID:</strong> #<?php echo $profile['profile_id']; ?>
            </div>
        </div>
    </div>

    <!-- Generate Form -->
    <form method="POST" action="process_payroll_generation.php" id="generatePayrollForm">
        <input type="hidden" name="profile_id" value="<?php echo $profile['profile_id']; ?>">
        <input type="hidden" name="generate_payroll" value="1">
        
        <!-- Payroll Run Details -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fa fa-cog"></i> Payroll Run Configuration</h5>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="run_name"><strong>Payroll Run Name <span class="text-danger">*</span></strong></label>
                    <input type="text" class="form-control" id="run_name" name="run_name" 
                           value="<?php echo date('F Y'); ?> - <?php echo htmlspecialchars($profile['profile_name']); ?>"
                           required>
                    <small class="form-text text-muted">Give this payroll run a descriptive name</small>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="pay_period_start"><strong>Pay Period Start <span class="text-danger">*</span></strong></label>
                        <input type="date" class="form-control" id="pay_period_start" name="pay_period_start" 
                               value="<?php echo date('Y-m-01'); ?>" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="pay_period_end"><strong>Pay Period End <span class="text-danger">*</span></strong></label>
                        <input type="date" class="form-control" id="pay_period_end" name="pay_period_end" 
                               value="<?php echo date('Y-m-t'); ?>" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="payment_date"><strong>Payment Date</strong></label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" 
                               value="<?php echo date('Y-m-t'); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="notes"><strong>Notes</strong></label>
                    <textarea class="form-control" id="notes" name="notes" rows="3" 
                              placeholder="Optional notes about this payroll run..."></textarea>
                </div>
            </div>
        </div>

        <!-- Personnel Selection -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fa fa-users"></i> Personnel Selection</h5>
            </div>
            <div class="card-body">
                <div class="personnel-filter-section">
                    <div class="form-group">
                        <label><strong>Include Personnel By:</strong></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="personnel_selection" 
                                   id="sel_all" value="all" checked onchange="updatePersonnelSelection()">
                            <label class="form-check-label" for="sel_all">
                                <strong>All Active Personnel</strong> - Include everyone
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="personnel_selection" 
                                   id="sel_dept" value="department" onchange="updatePersonnelSelection()">
                            <label class="form-check-label" for="sel_dept">
                                <strong>By Department</strong> - Select specific departments
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="personnel_selection" 
                                   id="sel_des" value="designation" onchange="updatePersonnelSelection()">
                            <label class="form-check-label" for="sel_des">
                                <strong>By Designation</strong> - Select specific positions
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="personnel_selection" 
                                   id="sel_status" value="emp_status" onchange="updatePersonnelSelection()">
                            <label class="form-check-label" for="sel_status">
                                <strong>By Employment Status</strong> - Select by status
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="personnel_selection" 
                                   id="sel_custom" value="custom" onchange="updatePersonnelSelection()">
                            <label class="form-check-label" for="sel_custom">
                                <strong>Custom Selection</strong> - Manually select personnel
                            </label>
                        </div>
                    </div>
                    
                    <!-- Department Selection -->
                    <div id="dept_selection" class="filter-options" style="display:none;">
                        <label><strong>Select Departments:</strong></label>
                        <?php
                        try {
                            $dept_query = $conn->query("SELECT * FROM dept_offices ORDER BY dept_office_name ASC");
                            while ($dept = $dept_query->fetch(PDO::FETCH_ASSOC)) {
                                echo '<div class="form-check">';
                                echo '<input class="form-check-input" type="checkbox" name="departments[]" value="' . $dept['do_id'] . '" id="dept_' . $dept['do_id'] . '">';
                                echo '<label class="form-check-label" for="dept_' . $dept['do_id'] . '">' . htmlspecialchars($dept['dept_office_name']) . '</label>';
                                echo '</div>';
                            }
                        } catch (Exception $e) {
                            echo '<p class="text-danger">Error loading departments</p>';
                        }
                        ?>
                    </div>
                    
                    <!-- Designation Selection -->
                    <div id="des_selection" class="filter-options" style="display:none;">
                        <label><strong>Select Designations:</strong></label>
                        <?php
                        try {
                            $des_query = $conn->query("SELECT * FROM designation ORDER BY des_name ASC");
                            while ($des = $des_query->fetch(PDO::FETCH_ASSOC)) {
                                echo '<div class="form-check">';
                                echo '<input class="form-check-input" type="checkbox" name="designations[]" value="' . $des['des_id'] . '" id="des_' . $des['des_id'] . '">';
                                echo '<label class="form-check-label" for="des_' . $des['des_id'] . '">' . htmlspecialchars($des['des_name']) . '</label>';
                                echo '</div>';
                            }
                        } catch (Exception $e) {
                            echo '<p class="text-danger">Error loading designations</p>';
                        }
                        ?>
                    </div>
                    
                    <!-- Status Selection -->
                    <div id="status_selection" class="filter-options" style="display:none;">
                        <label><strong>Select Employment Status:</strong></label>
                        <?php
                        try {
                            $status_query = $conn->query("SELECT * FROM emp_status ORDER BY emp_stat_name ASC");
                            while ($status = $status_query->fetch(PDO::FETCH_ASSOC)) {
                                echo '<div class="form-check">';
                                echo '<input class="form-check-input" type="checkbox" name="emp_statuses[]" value="' . $status['empStat_id'] . '" id="status_' . $status['empStat_id'] . '">';
                                echo '<label class="form-check-label" for="status_' . $status['empStat_id'] . '">' . htmlspecialchars($status['emp_stat_name']) . '</label>';
                                echo '</div>';
                            }
                        } catch (Exception $e) {
                            echo '<p class="text-danger">Error loading statuses</p>';
                        }
                        ?>
                    </div>
                    
                    <!-- Custom Personnel Selection -->
                    <div id="custom_selection" class="filter-options" style="display:none;">
                        <label><strong>Select Personnel:</strong></label>
                        <div style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; padding: 10px; border-radius: 4px;">
                            <?php
                            try {
                                $personnel_query = $conn->query("
                                    SELECT p.*, d.dept_office_name, des.des_name
                                    FROM personnels p
                                    LEFT JOIN dept_offices d ON p.do_id = d.do_id
                                    LEFT JOIN designation des ON p.des_id = des.des_id
                                    ORDER BY p.lname, p.fname
                                ");
                                while ($person = $personnel_query->fetch(PDO::FETCH_ASSOC)) {
                                    $name = trim($person['fname'] . ' ' . ($person['mname'] ? $person['mname'][0] . '. ' : '') . $person['lname']);
                                    echo '<div class="form-check">';
                                    echo '<input class="form-check-input" type="checkbox" name="personnel_ids[]" value="' . $person['personnel_id'] . '" id="person_' . $person['personnel_id'] . '">';
                                    echo '<label class="form-check-label" for="person_' . $person['personnel_id'] . '">';
                                    echo htmlspecialchars($name) . ' <small class="text-muted">(' . htmlspecialchars($person['dept_office_name'] ?? 'N/A') . ')</small>';
                                    echo '</label>';
                                    echo '</div>';
                                }
                            } catch (Exception $e) {
                                echo '<p class="text-danger">Error loading personnel</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Preview -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fa fa-list"></i> Profile Items Preview</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Income Items -->
                    <div class="col-md-6">
                        <h5 class="text-success"><i class="fa fa-money"></i> Income Items</h5>
                        <p class="item-count"><?php echo count($income_items); ?></p>
                        <?php if (empty($income_items)): ?>
                            <div class="alert alert-warning">No income items defined in this profile</div>
                        <?php else: ?>
                            <?php foreach ($income_items as $item): ?>
                                <div class="item-preview">
                                    <strong><?php echo htmlspecialchars($item['income_title']); ?></strong>
                                    <br><small class="text-muted"><?php echo htmlspecialchars($item['income_type']); ?></small>
                                    <?php if ($item['default_amount']): ?>
                                        <br><span class="badge badge-info">Default: ₱<?php echo number_format($item['default_amount'], 2); ?></span>
                                    <?php endif; ?>
                                    <?php if ($item['is_mandatory']): ?>
                                        <span class="badge badge-danger">Mandatory</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Deduction Items -->
                    <div class="col-md-6">
                        <h5 class="text-danger"><i class="fa fa-minus-circle"></i> Deduction Items</h5>
                        <p class="item-count"><?php echo count($deduction_items); ?></p>
                        <?php if (empty($deduction_items)): ?>
                            <div class="alert alert-warning">No deduction items defined in this profile</div>
                        <?php else: ?>
                            <?php foreach ($deduction_items as $item): ?>
                                <div class="item-preview">
                                    <strong><?php echo htmlspecialchars($item['deduction_title']); ?></strong>
                                    <br><small class="text-muted"><?php echo htmlspecialchars($item['deduction_type']); ?></small>
                                    <?php if ($item['default_employee_amt'] || $item['default_employer_amt']): ?>
                                        <br>
                                        <?php if ($item['default_employee_amt']): ?>
                                            <span class="badge badge-warning">Employee: ₱<?php echo number_format($item['default_employee_amt'], 2); ?></span>
                                        <?php endif; ?>
                                        <?php if ($item['default_employer_amt']): ?>
                                            <span class="badge badge-info">Employer: ₱<?php echo number_format($item['default_employer_amt'], 2); ?></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if ($item['is_mandatory']): ?>
                                        <span class="badge badge-danger">Mandatory</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Generate Actions -->
        <div class="generate-section">
            <h5><i class="fa fa-exclamation-triangle"></i> Ready to Generate Payroll?</h5>
            <p>This will create a new payroll run based on this template. The system will:</p>
            <ul>
                <li>Create payroll records for all selected personnel</li>
                <li>Apply income and deduction items from the template</li>
                <li>Calculate gross pay, deductions, and net pay for each personnel</li>
                <li>Generate detailed reports and snapshots</li>
            </ul>
            <p class="mb-3"><strong>Note:</strong> This process may take a few moments depending on the number of personnel.</p>
            
            <div class="text-center">
                <a href="list_payroll_profiles.php" class="btn btn-secondary btn-lg mr-2">
                    <i class="fa fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-success btn-lg" name="generate_payroll" id="generateBtn">
                    <i class="fa fa-cogs"></i> Generate Payroll Run
                </button>
            </div>
        </div>
    </form>
</div>

<?php include('footer.php'); ?>

</div><!-- End .page -->

<?php include('scripts_files.php'); ?>

<script>
function updatePersonnelSelection() {
    // Hide all filter options
    $('.filter-options').hide();
    
    // Show selected option
    const selected = $('input[name="personnel_selection"]:checked').val();
    console.log('Personnel selection changed to:', selected);
    if (selected === 'department') {
        $('#dept_selection').show();
    } else if (selected === 'designation') {
        $('#des_selection').show();
    } else if (selected === 'emp_status') {
        $('#status_selection').show();
    } else if (selected === 'custom') {
        $('#custom_selection').show();
    }
}

// Form validation
    $('#generatePayrollForm').on('submit', function(e) {
        const runName = $('#run_name').val().trim();
        const periodStart = $('#pay_period_start').val();
        const periodEnd = $('#pay_period_end').val();
        const personnelSelection = $('input[name="personnel_selection"]:checked').val();
        
        // Validate run name
        if (!runName || runName.length < 3) {
            e.preventDefault();
            alert('Payroll run name must be at least 3 characters long');
            return false;
        }
        
        // Validate dates
        if (!periodStart || !periodEnd) {
            e.preventDefault();
            alert('Please enter both start and end dates');
            return false;
        }
        
        if (new Date(periodStart) > new Date(periodEnd)) {
            e.preventDefault();
            alert('Pay period start date must be before or equal to end date');
            return false;
        }
        
        // Validate personnel selection
        if (personnelSelection === 'department' && $('input[name="departments[]"]:checked').length === 0) {
            e.preventDefault();
            alert('Please select at least one department');
            return false;
        } else if (personnelSelection === 'designation' && $('input[name="designations[]"]:checked').length === 0) {
            e.preventDefault();
            alert('Please select at least one designation');
            return false;
        } else if (personnelSelection === 'emp_status' && $('input[name="emp_statuses[]"]:checked').length === 0) {
            e.preventDefault();
            alert('Please select at least one employment status');
            return false;
        } else if (personnelSelection === 'custom' && $('input[name="personnel_ids[]"]:checked').length === 0) {
            e.preventDefault();
            alert('Please select at least one personnel');
            return false;
        }
        
        // Disable button and show loading
        $('#generateBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Generating...');
    });// Initialize on page load
$(document).ready(function() {
    console.log('=== PAGE LOADED ===');
    console.log('jQuery version:', $.fn.jquery);
    console.log('Form exists:', $('#generatePayrollForm').length);
    console.log('Button exists:', $('#generateBtn').length);
    console.log('Form action:', $('#generatePayrollForm').attr('action'));
    
    updatePersonnelSelection();
    
    // Add direct button click handler for debugging
    $('#generateBtn').on('click', function(e) {
        console.log('=== BUTTON CLICKED ===');
        console.log('Button type:', $(this).attr('type'));
        console.log('Button name:', $(this).attr('name'));
    });
    
    // Check if form submit event is bound
    console.log('Form submit events:', $._data($('#generatePayrollForm')[0], 'events'));
});
</script>

</body>
</html>

<?php
ob_end_flush();
?>
