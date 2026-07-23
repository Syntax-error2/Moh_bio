<?php
/**
 * View/Edit Payroll Profile
 * Display and edit payroll profile/template details
 */

// Start output buffering
ob_start();

include('session.php');

// Get profile ID and mode
$profile_id = $_GET['profile_id'] ?? '';
$mode = $_GET['mode'] ?? 'view'; // view or edit
$success_msg = $_GET['success'] ?? '';
$error_msg = $_GET['error'] ?? '';

if (empty($profile_id)) {
    ob_end_clean();
    header('Location: list_payroll_profiles.php?error=' . urlencode('Profile ID is required'));
    exit();
}

// Get profile details
try {
    $profile_query = $conn->prepare("
        SELECT * FROM pr_tbl_payroll_profiles
        WHERE profile_id = :profile_id
    ");
    $profile_query->execute([':profile_id' => $profile_id]);
    $profile = $profile_query->fetch(PDO::FETCH_ASSOC);
    
    if (!$profile) {
        throw new Exception('Profile not found');
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
    
    // Get all available income types (for adding)
    $all_income_query = $conn->prepare("
        SELECT * FROM pr_tbl_income 
        WHERE is_deleted = 0 
        ORDER BY income_title ASC
    ");
    $all_income_query->execute();
    $all_income = $all_income_query->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all available deduction types (for adding)
    $all_deductions_query = $conn->prepare("
        SELECT * FROM pr_tbl_deductions 
        WHERE is_deleted = 0 
        ORDER BY deduction_title ASC
    ");
    $all_deductions_query->execute();
    $all_deductions = $all_deductions_query->fetchAll(PDO::FETCH_ASSOC);
    
    // Get departments
    $dept_query = $conn->prepare("SELECT do_id as dept_id, dept_office_name as dept_title FROM dept_offices ORDER BY dept_office_name ASC");
    $dept_query->execute();
    $departments = $dept_query->fetchAll(PDO::FETCH_ASSOC);
    
    // Get employment statuses
    $emp_status_query = $conn->prepare("SELECT empStat_id, emp_stat_name FROM emp_status ORDER BY emp_stat_name ASC");
    $emp_status_query->execute();
    $employment_statuses = $emp_status_query->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    ob_end_clean();
    header('Location: list_payroll_profiles.php?error=' . urlencode('Error loading profile: ' . $e->getMessage()));
    exit();
}

$page_title = $mode === 'edit' ? "Edit Payroll Profile" : "View Payroll Profile";
?>

<!DOCTYPE html>
<html lang="en">

<?php include('header.php'); ?>
    
    <style>
        .profile-header-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .profile-header-card h2 {
            margin: 0 0 10px 0;
            font-size: 2rem;
        }
        .profile-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-right: 10px;
            background: rgba(255,255,255,0.2);
        }
        .profile-badge.active {
            background: #28a745;
        }
        .profile-badge.inactive {
            background: #dc3545;
        }
        .section-card {
            background: white;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        .info-row {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .info-value {
            color: #2c3e50;
            font-size: 1.05rem;
        }
        .item-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }
        .item-card:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transform: translateX(5px);
        }
        .item-title {
            font-weight: 600;
            color: #2c3e50;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }
        .item-details {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .badge-custom {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .badge-income {
            background: #d4edda;
            color: #155724;
        }
        .badge-deduction {
            background: #f8d7da;
            color: #721c24;
        }
        .badge-taxable {
            background: #fff3cd;
            color: #856404;
        }
        .filter-card {
            background: #e7f3ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        .action-buttons {
            position: sticky;
            top: 10px;
            z-index: 100;
        }
        .btn-action {
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .section-title .btn {
            transition: all 0.3s ease;
        }
        .section-title .btn:hover {
            transform: scale(1.1);
        }
        .add-more-btn-container {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px dashed #dee2e6;
        }
        .add-more-btn-container .btn {
            min-width: 200px;
            font-weight: 600;
        }
    </style>

<body>

<?php include('menu_sidebar.php'); ?>

<div class="page">

    <?php include('navbar_header.php'); ?>
    
    <!-- Breadcrumb -->
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
                <li style="color: blue"><strong style="margin-right: 4px;"><?php echo $schoolName; ?>&nbsp;</strong></li>
                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                <li class="breadcrumb-item"><a href="list_payroll_profiles.php">Payroll Profiles</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($profile['profile_name']); ?></li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <section class="mt-30px mb-30px">
        <div class="container-fluid">
            
            <?php if ($success_msg): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle"></i> <?php echo htmlspecialchars($success_msg); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>
            
            <?php if ($error_msg): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_msg); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-12">
                    
                    <!-- Profile Header -->
                    <div class="profile-header-card">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2><i class="icon-bill"></i> <?php echo htmlspecialchars($profile['profile_name']); ?></h2>
                                <p style="margin: 0; opacity: 0.95;">
                                    <span class="profile-badge <?php echo $profile['is_active'] ? 'active' : 'inactive'; ?>">
                                        <?php echo $profile['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                    <span class="profile-badge">
                                        <?php echo ucfirst(str_replace('_', ' ', $profile['profile_type'])); ?>
                                    </span>
                                    <?php if ($profile['is_default']): ?>
                                    <span class="profile-badge" style="background: #ffc107;">
                                        <i class="fa fa-star"></i> Default
                                    </span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-4 text-right action-buttons">
                                <?php if ($mode === 'view'): ?>
                                    <a href="view_payroll_profile.php?profile_id=<?php echo $profile_id; ?>&mode=edit" 
                                       class="btn btn-warning btn-lg btn-action">
                                        <i class="fa fa-pencil"></i> Edit Profile
                                    </a>
                                    <a href="generate_payroll_from_profile.php?profile_id=<?php echo $profile_id; ?>" 
                                       class="btn btn-success btn-lg btn-action">
                                        <i class="fa fa-cogs"></i> Generate Payroll
                                    </a>
                                <?php else: ?>
                                    <a href="view_payroll_profile.php?profile_id=<?php echo $profile_id; ?>" 
                                       class="btn btn-secondary btn-lg btn-action">
                                        <i class="fa fa-eye"></i> View Mode
                                    </a>
                                    <button type="submit" form="editProfileForm" class="btn btn-primary btn-lg btn-action">
                                        <i class="fa fa-save"></i> Save Changes
                                    </button>
                                <?php endif; ?>
                                <a href="list_payroll_profiles.php" class="btn btn-secondary btn-lg btn-action">
                                    <i class="fa fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="row">
                
                <!-- Left Column -->
                <div class="col-md-6">
                    
                    <!-- Basic Information -->
                    <div class="section-card">
                        <h3 class="section-title"><i class="fa fa-info-circle"></i> Basic Information</h3>
                        
                        <?php if ($mode === 'edit'): ?>
                        <form method="POST" action="save_payroll_profile.php" id="editProfileForm">
                            <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                            
                            <div class="form-group">
                                <label class="info-label">Profile Name <span class="text-danger">*</span></label>
                                <input type="text" name="profile_name" class="form-control" 
                                       value="<?php echo htmlspecialchars($profile['profile_name']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="info-label">Profile Type <span class="text-danger">*</span></label>
                                <select name="profile_type" class="form-control" required>
                                    <option value="regular" <?php echo $profile['profile_type'] === 'regular' ? 'selected' : ''; ?>>Regular Payroll</option>
                                    <option value="13th_month" <?php echo $profile['profile_type'] === '13th_month' ? 'selected' : ''; ?>>13th Month Pay</option>
                                    <option value="bonus" <?php echo $profile['profile_type'] === 'bonus' ? 'selected' : ''; ?>>Bonus</option>
                                    <option value="special" <?php echo $profile['profile_type'] === 'special' ? 'selected' : ''; ?>>Special Payroll</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="info-label">Description</label>
                                <textarea name="profile_description" class="form-control" rows="3"><?php echo htmlspecialchars($profile['profile_description'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="info-label">
                                            <input type="checkbox" name="is_active" value="1" <?php echo $profile['is_active'] ? 'checked' : ''; ?>>
                                            Active
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="info-label">
                                            <input type="checkbox" name="is_default" value="1" <?php echo $profile['is_default'] ? 'checked' : ''; ?>>
                                            Set as Default
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                        <?php else: ?>
                        
                        <div class="info-row">
                            <div class="info-label">Profile Name</div>
                            <div class="info-value"><?php echo htmlspecialchars($profile['profile_name']); ?></div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label">Profile Type</div>
                            <div class="info-value"><?php echo ucfirst(str_replace('_', ' ', $profile['profile_type'])); ?></div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label">Description</div>
                            <div class="info-value"><?php echo nl2br(htmlspecialchars($profile['profile_description'] ?? '')); ?></div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label">Status</div>
                            <div class="info-value">
                                <span class="badge badge-<?php echo $profile['is_active'] ? 'success' : 'secondary'; ?>">
                                    <?php echo $profile['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                                <?php if ($profile['is_default']): ?>
                                <span class="badge badge-warning">
                                    <i class="fa fa-star"></i> Default Profile
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php endif; ?>
                        
                        <div class="info-row">
                            <div class="info-label">Created</div>
                            <div class="info-value">
                                <?php echo date('F d, Y g:i A', strtotime($profile['created_at'])); ?>
                            </div>
                        </div>
                        
                        <?php if ($profile['updated_at']): ?>
                        <div class="info-row">
                            <div class="info-label">Last Updated</div>
                            <div class="info-value">
                                <?php echo date('F d, Y g:i A', strtotime($profile['updated_at'])); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Personnel Filters -->
                    <div class="section-card">
                        <h3 class="section-title">
                            <i class="fa fa-filter"></i> Personnel Filters (<?php echo count($filters); ?>)
                            <?php if ($mode === 'edit'): ?>
                            <button type="button" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#addFilterModal" style="margin-top: -3px;">
                                <i class="fa fa-plus"></i> Add
                            </button>
                            <?php endif; ?>
                        </h3>
                        
                        <?php if (count($filters) > 0): ?>
                            <?php foreach ($filters as $filter): ?>
                            <div class="filter-card" style="position: relative; padding-right: 100px;">
                                <?php 
                                // Display filter based on type
                                $filter_type = $filter['filter_type'];
                                $filter_value = $filter['filter_value'];
                                
                                switch($filter_type) {
                                    case 'department':
                                        echo '<div><strong>Department Filter:</strong> ';
                                        $dept_ids = explode(',', $filter_value);
                                        $dept_names = array_filter($departments, function($d) use ($dept_ids) {
                                            return in_array($d['dept_id'], $dept_ids);
                                        });
                                        echo implode(', ', array_column($dept_names, 'dept_title'));
                                        echo '</div>';
                                        break;
                                        
                                    case 'emp_status':
                                        echo '<div><strong>Employment Status Filter:</strong> ';
                                        $emp_ids = explode(',', $filter_value);
                                        $emp_names = array_filter($employment_statuses, function($e) use ($emp_ids) {
                                            return in_array($e['empStat_id'], $emp_ids);
                                        });
                                        echo implode(', ', array_column($emp_names, 'emp_stat_name'));
                                        echo '</div>';
                                        break;
                                        
                                    case 'designation':
                                        echo '<div><strong>Position/Designation:</strong> ';
                                        echo htmlspecialchars($filter_value);
                                        echo '</div>';
                                        break;
                                        
                                    case 'personnel':
                                        echo '<div><strong>Personnel Filter:</strong> ';
                                        echo htmlspecialchars($filter_value);
                                        echo '</div>';
                                        break;
                                        
                                    case 'all':
                                        echo '<div><strong>Filter:</strong> All Personnel</div>';
                                        break;
                                        
                                    default:
                                        echo '<div><strong>Filter:</strong> ';
                                        echo htmlspecialchars($filter_type . ': ' . $filter_value);
                                        echo '</div>';
                                }
                                ?>
                                
                                <?php if ($mode === 'edit'): ?>
                                <div style="position: absolute; top: 10px; right: 10px;">
                                    <button class="btn btn-danger btn-xs" 
                                            onclick="deleteFilter(<?php echo $filter['filter_id']; ?>)"
                                            title="Remove filter">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                            
                            <?php if ($mode === 'edit'): ?>
                            <div class="add-more-btn-container text-center">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addFilterModal">
                                    <i class="fa fa-plus-circle"></i> Add Another Filter
                                </button>
                                <p class="text-muted mt-2 mb-0"><small>You can add multiple personnel filters to narrow down who receives this payroll</small></p>
                            </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fa fa-filter"></i>
                                <p>No personnel filters defined. All active personnel will be included.</p>
                                <?php if ($mode === 'edit'): ?>
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addFilterModal">
                                    <i class="fa fa-plus"></i> Add Filter
                                </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    
                    <!-- Income Items -->
                    <div class="section-card">
                        <h3 class="section-title">
                            <i class="fa fa-plus-circle"></i> Income Items (<?php echo count($income_items); ?>)
                            <?php if ($mode === 'edit'): ?>
                            <button type="button" class="btn btn-success btn-xs pull-right" data-toggle="modal" data-target="#addIncomeModal" style="margin-top: -3px;">
                                <i class="fa fa-plus"></i> Add
                            </button>
                            <?php endif; ?>
                        </h3>
                        
                        <?php if (count($income_items) > 0): ?>
                            <?php foreach ($income_items as $item): ?>
                            <div class="item-card" style="position: relative; padding-right: 100px;">
                                <div class="item-title">
                                    <?php echo htmlspecialchars($item['income_title']); ?>
                                    <span class="badge-custom badge-income"><?php echo ucfirst($item['income_type']); ?></span>
                                </div>
                                <div class="item-details">
                                    <small>
                                        <?php if (isset($item['is_mandatory']) && $item['is_mandatory']): ?>
                                        <i class="fa fa-check-circle text-success"></i> Required |
                                        <?php endif; ?>
                                        Display Order: <?php echo $item['display_order']; ?>
                                        <?php if (isset($item['default_amount']) && $item['default_amount']): ?>
                                        | Default: ₱<?php echo number_format($item['default_amount'], 2); ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                                
                                <?php if ($mode === 'edit'): ?>
                                <div style="position: absolute; top: 10px; right: 10px;">
                                    <button class="btn btn-warning btn-xs" 
                                            onclick="editIncome(<?php echo $item['profile_income_id']; ?>, '<?php echo addslashes($item['income_title']); ?>', <?php echo $item['default_amount'] ?? 0; ?>, <?php echo $item['display_order']; ?>, <?php echo isset($item['is_mandatory']) && $item['is_mandatory'] ? 1 : 0; ?>, <?php echo isset($item['is_active']) && $item['is_active'] ? 1 : 0; ?>)"
                                            title="Edit income item">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    <button class="btn btn-danger btn-xs" 
                                            onclick="deleteIncomeItem(<?php echo $item['profile_income_id']; ?>)"
                                            title="Remove income item">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                            
                            <?php if ($mode === 'edit'): ?>
                            <div class="add-more-btn-container text-center">
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addIncomeModal">
                                    <i class="fa fa-plus-circle"></i> Add Another Income Item
                                </button>
                                <p class="text-muted mt-2 mb-0"><small>Add salary, allowances, bonuses, and other earnings</small></p>
                            </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fa fa-plus-circle"></i>
                                <p>No income items configured for this profile.</p>
                                <?php if ($mode === 'edit'): ?>
                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addIncomeModal">
                                    <i class="fa fa-plus"></i> Add Income Item
                                </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Deduction Items -->
                    <div class="section-card">
                        <h3 class="section-title">
                            <i class="fa fa-minus-circle"></i> Deduction Items (<?php echo count($deduction_items); ?>)
                            <?php if ($mode === 'edit'): ?>
                            <button type="button" class="btn btn-warning btn-xs pull-right" data-toggle="modal" data-target="#addDeductionModal" style="margin-top: -3px;">
                                <i class="fa fa-plus"></i> Add
                            </button>
                            <?php endif; ?>
                        </h3>
                        
                        <?php if (count($deduction_items) > 0): ?>
                            <?php foreach ($deduction_items as $item): ?>
                            <div class="item-card" style="position: relative; padding-right: 100px;">
                                <div class="item-title">
                                    <?php echo htmlspecialchars($item['deduction_title']); ?>
                                    <span class="badge-custom badge-deduction"><?php echo ucfirst($item['deduction_type']); ?></span>
                                </div>
                                <div class="item-details">
                                    <small>
                                        <?php if (isset($item['is_mandatory']) && $item['is_mandatory']): ?>
                                        <i class="fa fa-check-circle text-success"></i> Required |
                                        <?php endif; ?>
                                        Display Order: <?php echo $item['display_order']; ?>
                                        <?php if (isset($item['default_employee_amt']) && $item['default_employee_amt']): ?>
                                        | Employee: ₱<?php echo number_format($item['default_employee_amt'], 2); ?>
                                        <?php endif; ?>
                                        <?php if (isset($item['default_employer_amt']) && $item['default_employer_amt']): ?>
                                        | Employer: ₱<?php echo number_format($item['default_employer_amt'], 2); ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                                
                                <?php if ($mode === 'edit'): ?>
                                <div style="position: absolute; top: 10px; right: 10px;">
                                    <button class="btn btn-warning btn-xs" 
                                            onclick="editDeduction(<?php echo $item['profile_deduction_id']; ?>, '<?php echo addslashes($item['deduction_title']); ?>', <?php echo $item['default_employee_amt'] ?? 0; ?>, <?php echo $item['default_employer_amt'] ?? 0; ?>, <?php echo $item['display_order']; ?>, <?php echo isset($item['is_mandatory']) && $item['is_mandatory'] ? 1 : 0; ?>, <?php echo isset($item['is_active']) && $item['is_active'] ? 1 : 0; ?>)"
                                            title="Edit deduction item">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    <button class="btn btn-danger btn-xs" 
                                            onclick="deleteDeductionItem(<?php echo $item['profile_deduction_id']; ?>)"
                                            title="Remove deduction item">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                            
                            <?php if ($mode === 'edit'): ?>
                            <div class="add-more-btn-container text-center">
                                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#addDeductionModal">
                                    <i class="fa fa-plus-circle"></i> Add Another Deduction Item
                                </button>
                                <p class="text-muted mt-2 mb-0"><small>Add taxes, SSS, PhilHealth, Pag-IBIG, loans, and other deductions</small></p>
                            </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fa fa-minus-circle"></i>
                                <p>No deduction items configured for this profile.</p>
                                <?php if ($mode === 'edit'): ?>
                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#addDeductionModal">
                                    <i class="fa fa-plus"></i> Add Deduction Item
                                </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>

            </div>

            <!-- Action Buttons Bottom -->
            <div class="row mt-4">
                <div class="col-md-12 text-center">
                    <a href="list_payroll_profiles.php" class="btn btn-secondary btn-lg">
                        <i class="fa fa-arrow-left"></i> Back to List
                    </a>
                    <a href="generate_payroll_from_profile.php?profile_id=<?php echo $profile_id; ?>" 
                       class="btn btn-success btn-lg">
                        <i class="fa fa-cogs"></i> Generate Payroll from this Profile
                    </a>
                    <button onclick="cloneProfile(<?php echo $profile_id; ?>)" class="btn btn-info btn-lg">
                        <i class="fa fa-files-o"></i> Clone this Profile
                    </button>
                    <?php if ($mode === 'view'): ?>
                    <button onclick="deleteProfile(<?php echo $profile_id; ?>)" class="btn btn-danger btn-lg">
                        <i class="fa fa-trash"></i> Delete Profile
                    </button>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>

<!-- Add Personnel Filter Modal -->
<div class="modal fade" id="addFilterModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title">
                    <i class="fa fa-filter"></i> Add Personnel Filter
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addFilterForm">
                    <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Filter Type <span class="text-danger">*</span></label>
                                <select class="form-control" name="filter_type" id="filter_type" required>
                                    <option value="">Select Filter Type</option>
                                    <option value="department">Department/Office</option>
                                    <option value="employment_status">Employment Status</option>
                                    <option value="position">Position/Designation</option>
                                    <option value="salary_grade">Salary Grade</option>
                                    <option value="gender">Gender</option>
                                    <option value="age_range">Age Range</option>
                                    <option value="custom">Custom SQL Condition</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Filter Operator</label>
                                <select class="form-control" name="filter_operator" id="filter_operator">
                                    <option value="equals">Equals (=)</option>
                                    <option value="not_equals">Not Equals (!=)</option>
                                    <option value="in">In (Multiple Values)</option>
                                    <option value="not_in">Not In</option>
                                    <option value="like">Contains (LIKE)</option>
                                    <option value="greater_than">Greater Than (&gt;)</option>
                                    <option value="less_than">Less Than (&lt;)</option>
                                    <option value="between">Between</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Department Filter Options -->
                    <div class="filter-options" id="department_options" style="display:none;">
                        <div class="form-group">
                            <label>Select Department(s)</label>
                            <select class="form-control" name="department_ids[]" multiple size="8">
                                <?php
                                $dept_modal_query = $conn->prepare("SELECT do_id, dept_office_name FROM dept_offices ORDER BY dept_office_name");
                                $dept_modal_query->execute();
                                $dept_modal_results = $dept_modal_query->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($dept_modal_results as $dept) {
                                    echo "<option value='{$dept['do_id']}'>{$dept['dept_office_name']}</option>";
                                }
                                ?>
                            </select>
                            <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                        </div>
                    </div>
                    
                    <!-- Employment Status Filter Options -->
                    <div class="filter-options" id="employment_status_options" style="display:none;">
                        <div class="form-group">
                            <label>Select Employment Status</label>
                            <select class="form-control" name="employment_status[]" multiple size="8">
                                <?php
                                $emp_status_query = $conn->prepare("SELECT empStat_id, emp_stat_name FROM emp_status ORDER BY emp_stat_name");
                                $emp_status_query->execute();
                                $emp_status_results = $emp_status_query->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($emp_status_results as $emp_stat) {
                                    echo "<option value='{$emp_stat['empStat_id']}'>{$emp_stat['emp_stat_name']}</option>";
                                }
                                ?>
                            </select>
                            <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                        </div>
                    </div>
                    
                    <!-- Position Filter Options -->
                    <div class="filter-options" id="position_options" style="display:none;">
                        <div class="form-group">
                            <label>Position/Designation</label>
                            <input type="text" class="form-control" name="position_value" 
                                   placeholder="Enter position name or use % as wildcard">
                            <small class="text-muted">Example: "Nurse%" for all nurse positions</small>
                        </div>
                    </div>
                    
                    <!-- Salary Grade Filter Options -->
                    <div class="filter-options" id="salary_grade_options" style="display:none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Salary Grade From</label>
                                    <input type="number" class="form-control" name="salary_grade_from" 
                                           min="1" max="30" placeholder="e.g., 1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Salary Grade To</label>
                                    <input type="number" class="form-control" name="salary_grade_to" 
                                           min="1" max="30" placeholder="e.g., 15">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Gender Filter Options -->
                    <div class="filter-options" id="gender_options" style="display:none;">
                        <div class="form-group">
                            <label>Select Gender</label>
                            <select class="form-control" name="gender_value">
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Age Range Filter Options -->
                    <div class="filter-options" id="age_range_options" style="display:none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Age From</label>
                                    <input type="number" class="form-control" name="age_from" 
                                           min="18" max="100" placeholder="e.g., 25">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Age To</label>
                                    <input type="number" class="form-control" name="age_to" 
                                           min="18" max="100" placeholder="e.g., 60">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Custom SQL Filter Options -->
                    <div class="filter-options" id="custom_options" style="display:none;">
                        <div class="form-group">
                            <label>Custom SQL Condition</label>
                            <textarea class="form-control" name="custom_condition" rows="3" 
                                      placeholder="Example: date_hired >= '2020-01-01' AND designation LIKE '%Manager%'"></textarea>
                            <small class="text-danger">
                                <i class="fa fa-warning"></i> Advanced users only. Invalid SQL will cause errors.
                            </small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Description (Optional)</label>
                        <input type="text" class="form-control" name="filter_description" 
                               placeholder="e.g., All permanent employees in HR department">
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="is_active" value="1" checked>
                                Active (Apply this filter when generating payroll)
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" onclick="saveFilter()">
                    <i class="fa fa-save"></i> Save Filter
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Income Item Modal -->
<div class="modal fade" id="addIncomeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title">
                    <i class="fa fa-plus-circle"></i> Add Income Item
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addIncomeForm">
                    <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                    
                    <div class="form-group">
                        <label>Select Income Item <span class="text-danger">*</span></label>
                        <select class="form-control" name="income_id" id="income_select" required>
                            <option value="">Choose Income Item</option>
                            <?php
                            $income_modal_query = $conn->prepare("SELECT income_id, income_title, income_type 
                                           FROM pr_tbl_income 
                                           WHERE is_deleted = 0 
                                           ORDER BY income_title");
                            $income_modal_query->execute();
                            $income_modal_results = $income_modal_query->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($income_modal_results as $inc) {
                                $type_badge = $inc['income_type'] == 'fixed' ? 
                                    '<span class="label label-success">Fixed</span>' : 
                                    '<span class="label label-info">Variable</span>';
                                echo "<option value='{$inc['income_id']}' data-type='{$inc['income_type']}'>";
                                echo htmlspecialchars($inc['income_title']) . " - " . ucfirst($inc['income_type']);
                                echo "</option>";
                            }
                            ?>
                        </select>
                        <small class="text-muted">
                            <strong>Fixed:</strong> Same amount for all personnel | 
                            <strong>Variable:</strong> Amount varies per personnel
                        </small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" id="default_amount_group">
                                <label>Default Amount</label>
                                <div class="input-group">
                                    <span class="input-group-addon">₱</span>
                                    <input type="number" class="form-control" name="default_amount" 
                                           step="0.01" min="0" placeholder="0.00">
                                </div>
                                <small class="text-muted">
                                    Leave blank for variable amounts or use as default value
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Display Order</label>
                                <input type="number" class="form-control" name="sort_order" 
                                       value="0" min="0" max="999">
                                <small class="text-muted">Lower numbers appear first</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Calculation Method</label>
                        <select class="form-control" name="calculation_method">
                            <option value="fixed">Fixed Amount</option>
                            <option value="percentage">Percentage of Basic Salary</option>
                            <option value="formula">Custom Formula</option>
                            <option value="manual">Manual Entry Per Personnel</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="formula_group" style="display:none;">
                        <label>Formula</label>
                        <input type="text" class="form-control" name="formula" 
                               placeholder="e.g., {basic_salary} * 0.10">
                        <small class="text-muted">
                            Available variables: {basic_salary}, {daily_rate}, {monthly_rate}, {days_worked}
                        </small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="is_mandatory" value="1">
                                        <strong>Mandatory</strong> (Always include in payroll)
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="is_active" value="1" checked>
                                        <strong>Active</strong> (Enable this income item)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> 
                        <strong>Note:</strong> After adding income items, you can assign specific amounts 
                        to individual personnel from their profile page.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" onclick="saveIncomeItem()">
                    <i class="fa fa-save"></i> Add Income Item
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Deduction Item Modal -->
<div class="modal fade" id="addDeductionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title">
                    <i class="fa fa-minus-circle"></i> Add Deduction Item
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addDeductionForm">
                    <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                    
                    <div class="form-group">
                        <label>Select Deduction Item <span class="text-danger">*</span></label>
                        <select class="form-control" name="deduction_id" id="deduction_select" required>
                            <option value="">Choose Deduction Item</option>
                            <?php
                            $deduction_modal_query = $conn->prepare("SELECT deduction_id, deduction_title, deduction_type 
                                              FROM pr_tbl_deductions 
                                              WHERE is_deleted = 0 
                                              ORDER BY deduction_title");
                            $deduction_modal_query->execute();
                            $deduction_modal_results = $deduction_modal_query->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($deduction_modal_results as $ded) {
                                $type_badge = $ded['deduction_type'] == 'fixed' ? 
                                    '<span class="label label-warning">Fixed</span>' : 
                                    '<span class="label label-info">Variable</span>';
                                echo "<option value='{$ded['deduction_id']}' data-type='{$ded['deduction_type']}'>";
                                echo htmlspecialchars($ded['deduction_title']) . " - " . ucfirst($ded['deduction_type']);
                                echo "</option>";
                            }
                            ?>
                        </select>
                        <small class="text-muted">
                            <strong>Fixed:</strong> Same amount for all personnel | 
                            <strong>Variable:</strong> Amount varies per personnel
                        </small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Employee Amount</label>
                                <div class="input-group">
                                    <span class="input-group-addon">₱</span>
                                    <input type="number" class="form-control" name="default_amount" 
                                           step="0.01" min="0" placeholder="0.00">
                                </div>
                                <small class="text-muted">Amount deducted from employee's pay</small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Employer Amount</label>
                                <div class="input-group">
                                    <span class="input-group-addon">₱</span>
                                    <input type="number" class="form-control" name="employer_amount" 
                                           step="0.01" min="0" placeholder="0.00">
                                </div>
                                <small class="text-muted">Employer's contribution (e.g., SSS)</small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Display Order</label>
                                <input type="number" class="form-control" name="sort_order" 
                                       value="0" min="0" max="999">
                                <small class="text-muted">Lower numbers appear first</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Calculation Method</label>
                        <select class="form-control" name="calculation_method">
                            <option value="fixed">Fixed Amount</option>
                            <option value="percentage">Percentage of Gross Income</option>
                            <option value="formula">Custom Formula</option>
                            <option value="manual">Manual Entry Per Personnel</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="deduction_formula_group" style="display:none;">
                        <label>Formula</label>
                        <input type="text" class="form-control" name="formula" 
                               placeholder="e.g., {gross_income} * 0.05">
                        <small class="text-muted">
                            Available variables: {gross_income}, {basic_salary}, {total_income}, {taxable_income}
                        </small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="is_mandatory" value="1">
                                        <strong>Mandatory</strong> (Always deduct in payroll)
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="is_active" value="1" checked>
                                        <strong>Active</strong> (Enable this deduction)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Deduction Priority</label>
                        <select class="form-control" name="priority">
                            <option value="high">High (Deduct first, e.g., Government mandatories)</option>
                            <option value="medium" selected>Medium (Standard deductions)</option>
                            <option value="low">Low (Deduct last, e.g., Optional deductions)</option>
                        </select>
                        <small class="text-muted">
                            Determines the order of deductions when processing payroll
                        </small>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i> 
                        <strong>Important:</strong> Government-mandated deductions (SSS, PhilHealth, Pag-IBIG, Tax) 
                        should be marked as <strong>Mandatory</strong> and set to <strong>High Priority</strong>.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" onclick="saveDeductionItem()">
                    <i class="fa fa-save"></i> Add Deduction Item
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Filter Modal -->
<div class="modal fade" id="editFilterModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h4 class="modal-title">
                    <i class="fa fa-pencil"></i> Edit Personnel Filter
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editFilterForm">
                    <input type="hidden" name="filter_id" id="edit_filter_id">
                    <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                    
                    <div class="form-group">
                        <label>Filter Description</label>
                        <input type="text" class="form-control" name="filter_description" id="edit_filter_description">
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="is_active" id="edit_filter_active" value="1">
                                Active (Apply this filter)
                            </label>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> 
                        To change filter type or conditions, please delete and create a new filter.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="updateFilter()">
                    <i class="fa fa-save"></i> Update Filter
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Income Item Modal -->
<div class="modal fade" id="editIncomeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h4 class="modal-title">
                    <i class="fa fa-pencil"></i> Edit Income Item
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editIncomeForm">
                    <input type="hidden" name="profile_income_id" id="edit_income_id">
                    <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                    
                    <div class="form-group">
                        <label>Income Item</label>
                        <input type="text" class="form-control" id="edit_income_name" disabled>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Default Amount</label>
                                <div class="input-group">
                                    <span class="input-group-addon">₱</span>
                                    <input type="number" class="form-control" name="default_amount" 
                                           id="edit_income_amount" step="0.01" min="0">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Display Order</label>
                                <input type="number" class="form-control" name="sort_order" 
                                       id="edit_income_sort" min="0" max="999">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="is_mandatory" id="edit_income_mandatory" value="1">
                                        Mandatory
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="is_active" id="edit_income_active" value="1">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="updateIncomeItem()">
                    <i class="fa fa-save"></i> Update Income
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Deduction Item Modal -->
<div class="modal fade" id="editDeductionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h4 class="modal-title">
                    <i class="fa fa-pencil"></i> Edit Deduction Item
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editDeductionForm">
                    <input type="hidden" name="profile_deduction_id" id="edit_deduction_id">
                    <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                    
                    <div class="form-group">
                        <label>Deduction Item</label>
                        <input type="text" class="form-control" id="edit_deduction_name" disabled>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Employee Amount</label>
                                <div class="input-group">
                                    <span class="input-group-addon">₱</span>
                                    <input type="number" class="form-control" name="default_amount" 
                                           id="edit_deduction_employee_amount" step="0.01" min="0"
                                           placeholder="Amount deducted from employee">
                                </div>
                                <small class="form-text text-muted">Amount deducted from employee's pay</small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Employer Amount</label>
                                <div class="input-group">
                                    <span class="input-group-addon">₱</span>
                                    <input type="number" class="form-control" name="employer_amount" 
                                           id="edit_deduction_employer_amount" step="0.01" min="0"
                                           placeholder="Employer's share">
                                </div>
                                <small class="form-text text-muted">Employer's contribution (e.g., SSS)</small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Display Order</label>
                                <input type="number" class="form-control" name="sort_order" 
                                       id="edit_deduction_sort" min="0" max="999">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="is_mandatory" id="edit_deduction_mandatory" value="1">
                                        Mandatory
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="is_active" id="edit_deduction_active" value="1">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="updateDeductionItem()">
                    <i class="fa fa-save"></i> Update Deduction
                </button>
            </div>
        </div>
    </div>
</div>

    <?php include('footer.php'); ?>

</div><!-- End .page -->

<?php include('scripts_files.php'); ?>

<script>
// Filter Type Change Handler
$(document).ready(function() {
    $('#filter_type').change(function() {
        $('.filter-options').hide();
        var filterType = $(this).val();
        if (filterType) {
            $('#' + filterType + '_options').show();
        }
    });
    
    // Income calculation method handler
    $('select[name="calculation_method"]').change(function() {
        if ($(this).val() === 'formula') {
            $('#formula_group').show();
        } else {
            $('#formula_group').hide();
        }
    });
});

// Save Personnel Filter
function saveFilter() {
    // Get basic form data
    var filterType = $('#filter_type').val();
    var filterOperator = $('#filter_operator').val();
    var profileId = $('input[name="profile_id"]').val();
    
    if (!filterType) {
        alert('Please select a filter type');
        return;
    }
    
    // Build form data object
    var formData = {
        profile_id: profileId,
        filter_type: filterType,
        filter_operator: filterOperator
    };
    
    // Get values based on filter type
    switch(filterType) {
        case 'department':
            var deptIds = $('select[name="department_ids[]"]').val();
            if (!deptIds || deptIds.length === 0) {
                alert('Please select at least one department');
                return;
            }
            formData['department_ids[]'] = deptIds;
            break;
            
        case 'employment_status':
            var empStatus = $('select[name="employment_status[]"]').val();
            if (!empStatus || empStatus.length === 0) {
                alert('Please select at least one employment status (Hold Ctrl/Cmd to select multiple)');
                return;
            }
            formData['employment_status[]'] = empStatus;
            break;
            
        case 'position':
            var posValue = $('input[name="position_value"]').val();
            if (!posValue) {
                alert('Please enter a position value');
                return;
            }
            formData.position_value = posValue;
            break;
            
        case 'salary_grade':
            var sgFrom = $('input[name="salary_grade_from"]').val();
            var sgTo = $('input[name="salary_grade_to"]').val();
            if (!sgFrom && !sgTo) {
                alert('Please specify salary grade range');
                return;
            }
            if (sgFrom) formData.salary_grade_from = sgFrom;
            if (sgTo) formData.salary_grade_to = sgTo;
            break;
            
        case 'gender':
            var gender = $('select[name="gender_value"]').val();
            if (!gender) {
                alert('Please select a gender');
                return;
            }
            formData.gender_value = gender;
            break;
            
        case 'age_range':
            var ageFrom = $('input[name="age_from"]').val();
            var ageTo = $('input[name="age_to"]').val();
            if (!ageFrom && !ageTo) {
                alert('Please specify age range');
                return;
            }
            if (ageFrom) formData.age_from = ageFrom;
            if (ageTo) formData.age_to = ageTo;
            break;
            
        case 'custom':
            var customCond = $('textarea[name="custom_condition"]').val();
            if (!customCond) {
                alert('Please enter a custom SQL condition');
                return;
            }
            formData.custom_condition = customCond;
            break;
    }
    
    console.log('Sending filter data:', formData);
    
    $.ajax({
        url: 'save_profile_filter.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        traditional: true, // Important for array parameters
        success: function(response) {
            if (response.success) {
                alert('Filter added successfully!');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('An error occurred while saving the filter');
        }
    });
}

// Save Income Item
function saveIncomeItem() {
    var formData = $('#addIncomeForm').serialize();
    
    $.ajax({
        url: 'save_profile_income_item.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Income item added successfully!');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('An error occurred while saving the income item');
        }
    });
}

// Save Deduction Item
function saveDeductionItem() {
    var formData = $('#addDeductionForm').serialize();
    
    $.ajax({
        url: 'save_profile_deduction_item.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Deduction item added successfully!');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('An error occurred while saving the deduction item');
        }
    });
}

// Edit Filter
function editFilter(filterId, description, isActive) {
    $('#edit_filter_id').val(filterId);
    $('#edit_filter_description').val(description);
    $('#edit_filter_active').prop('checked', isActive == 1);
    $('#editFilterModal').modal('show');
}

// Update Filter
function updateFilter() {
    var formData = $('#editFilterForm').serialize();
    
    $.ajax({
        url: 'update_profile_filter.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Filter updated successfully!');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('An error occurred while updating the filter');
        }
    });
}

// Edit Income Item
function editIncome(id, name, amount, sortOrder, isMandatory, isActive) {
    $('#edit_income_id').val(id);
    $('#edit_income_name').val(name);
    $('#edit_income_amount').val(amount);
    $('#edit_income_sort').val(sortOrder);
    $('#edit_income_mandatory').prop('checked', isMandatory == 1);
    $('#edit_income_active').prop('checked', isActive == 1);
    $('#editIncomeModal').modal('show');
}

// Update Income Item
function updateIncomeItem() {
    var formData = $('#editIncomeForm').serialize();
    
    $.ajax({
        url: 'update_profile_income_item.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Income item updated successfully!');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('An error occurred while updating the income item');
        }
    });
}

// Edit Deduction Item
function editDeduction(id, name, employeeAmount, employerAmount, sortOrder, isMandatory, isActive) {
    $('#edit_deduction_id').val(id);
    $('#edit_deduction_name').val(name);
    $('#edit_deduction_employee_amount').val(employeeAmount);
    $('#edit_deduction_employer_amount').val(employerAmount);
    $('#edit_deduction_sort').val(sortOrder);
    $('#edit_deduction_mandatory').prop('checked', isMandatory == 1);
    $('#edit_deduction_active').prop('checked', isActive == 1);
    $('#editDeductionModal').modal('show');
}

// Update Deduction Item
function updateDeductionItem() {
    var formData = $('#editDeductionForm').serialize();
    
    $.ajax({
        url: 'update_profile_deduction_item.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Deduction item updated successfully!');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('An error occurred while updating the deduction item');
        }
    });
}

// Delete Filter
function deleteFilter(filterId) {
    if (confirm('Are you sure you want to remove this filter?')) {
        $.ajax({
            url: 'delete_profile_filter.php',
            type: 'POST',
            data: { filter_id: filterId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Filter removed successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while deleting the filter');
            }
        });
    }
}

// Delete Income Item
function deleteIncomeItem(itemId) {
    if (confirm('Are you sure you want to remove this income item from the profile?')) {
        $.ajax({
            url: 'delete_profile_income_item.php',
            type: 'POST',
            data: { profile_income_id: itemId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Income item removed successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while deleting the income item');
            }
        });
    }
}

// Delete Deduction Item
function deleteDeductionItem(itemId) {
    if (confirm('Are you sure you want to remove this deduction item from the profile?')) {
        $.ajax({
            url: 'delete_profile_deduction_item.php',
            type: 'POST',
            data: { profile_deduction_id: itemId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Deduction item removed successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while deleting the deduction item');
            }
        });
    }
}

function cloneProfile(profileId) {
    if (confirm('Clone this payroll profile? A copy will be created with all income, deductions, and filters.')) {
        window.location.href = 'clone_payroll_profile.php?profile_id=' + profileId;
    }
}

function deleteProfile(profileId) {
    if (confirm('Are you sure you want to delete this payroll profile? This action cannot be undone.')) {
        if (confirm('This will permanently delete the profile and all associated configurations. Continue?')) {
            $.post('delete_payroll_profile.php', {
                profile_id: profileId
            }, function(response) {
                if (response.success) {
                    alert('Profile deleted successfully');
                    window.location.href = 'list_payroll_profiles.php';
                } else {
                    alert('Error: ' + response.message);
                }
            }, 'json').fail(function() {
                alert('An error occurred while deleting the profile');
            });
        }
    }
}
</script>

</body>
</html>
