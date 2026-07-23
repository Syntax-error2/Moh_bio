<?php
/**
 * Payroll Profile Management
 * Manage payroll templates for easy cloning and reuse
 */

// Start output buffering
ob_start();

include('session.php');

// Get filter parameters
$profile_type = $_GET['type'] ?? 'all';
$status = $_GET['status'] ?? 'active';

// Page title
$page_title = "Payroll Profiles (Templates)";
?>

<!DOCTYPE html>
<html lang="en">

<?php include('header.php'); ?>
    
    <style>
        .profile-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            background: #fff;
        }
        .profile-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 10px;
        }
        .profile-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }
        .profile-type-badge {
            font-size: 0.85rem;
            padding: 4px 12px;
            border-radius: 12px;
        }
        .profile-body {
            color: #6c757d;
            font-size: 0.95rem;
        }
        .profile-stats {
            display: flex;
            gap: 20px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #e9ecef;
        }
        .stat-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .stat-item i {
            color: #6c757d;
        }
        .profile-actions {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        .filter-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .default-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .stats-card h3 {
            font-size: 2rem;
            margin: 0;
        }
        .stats-card p {
            margin: 5px 0 0 0;
            opacity: 0.9;
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
                <i class="icon-bill"></i> Payroll Profiles (Templates)
            </h2>
            <p class="text-muted">Create reusable payroll templates for easy payroll processing</p>
        </div>
        <div class="col-md-4 text-right">
            <button class="btn btn-success btn-lg" data-toggle="modal" data-target="#addProfileModal">
                <i class="fa fa-plus"></i> Create New Profile
            </button>
        </div>
    </div>

    <!-- Statistics Summary -->
    <?php
    try {
        // Get statistics
        $stats_query = $conn->query("
            SELECT 
                COUNT(*) as total_profiles,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_profiles,
                SUM(CASE WHEN is_default = 1 THEN 1 ELSE 0 END) as default_profiles,
                SUM(CASE WHEN profile_type = 'regular' THEN 1 ELSE 0 END) as regular_count
            FROM pr_tbl_payroll_profiles
        ");
        $stats = $stats_query->fetch(PDO::FETCH_ASSOC);
    ?>
    <div class="row">
        <div class="col-md-3">
            <div class="stats-card">
                <h3><?php echo $stats['total_profiles']; ?></h3>
                <p><i class="fa fa-folder-open"></i> Total Profiles</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <h3><?php echo $stats['active_profiles']; ?></h3>
                <p><i class="fa fa-check-circle"></i> Active Profiles</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);">
                <h3><?php echo $stats['default_profiles']; ?></h3>
                <p><i class="fa fa-star"></i> Default Profiles</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #283c86 0%, #45a247 100%);">
                <h3><?php echo $stats['regular_count']; ?></h3>
                <p><i class="fa fa-calendar"></i> Regular Payrolls</p>
            </div>
        </div>
    </div>
    <?php } catch (Exception $e) { ?>
        <div class="alert alert-warning">Statistics temporarily unavailable.</div>
    <?php } ?>

    <!-- Filter Section -->
    <div class="filter-section personnel-form-shell">
        <form method="GET" action="" class="form-inline standardized-form">
            <label class="mr-2"><strong>Filter by:</strong></label>
            <select name="type" class="form-control mr-2" onchange="this.form.submit()">
                <option value="all" <?php echo $profile_type === 'all' ? 'selected' : ''; ?>>All Types</option>
                <option value="regular" <?php echo $profile_type === 'regular' ? 'selected' : ''; ?>>Regular</option>
                <option value="special" <?php echo $profile_type === 'special' ? 'selected' : ''; ?>>Special</option>
                <option value="13th_month" <?php echo $profile_type === '13th_month' ? 'selected' : ''; ?>>13th Month</option>
                <option value="bonus" <?php echo $profile_type === 'bonus' ? 'selected' : ''; ?>>Bonus</option>
                <option value="custom" <?php echo $profile_type === 'custom' ? 'selected' : ''; ?>>Custom</option>
            </select>
            
            <select name="status" class="form-control mr-2" onchange="this.form.submit()">
                <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>All Status</option>
                <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active Only</option>
                <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inactive Only</option>
            </select>
            
            <button type="submit" class="btn btn-primary mr-2">
                <i class="fa fa-filter"></i> Apply
            </button>
            <a href="list_payroll_profiles.php" class="btn btn-secondary">
                <i class="fa fa-refresh"></i> Reset
            </a>
        </form>
    </div>

    <!-- Profiles List -->
    <div class="row">
        <?php
        try {
            // Build query based on filters
            $where_clauses = [];
            $params = [];
            
            if ($profile_type !== 'all') {
                $where_clauses[] = "profile_type = :profile_type";
                $params[':profile_type'] = $profile_type;
            }
            
            if ($status === 'active') {
                $where_clauses[] = "is_active = 1";
            } elseif ($status === 'inactive') {
                $where_clauses[] = "is_active = 0";
            }
            
            $where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";
            
            // Get profiles
            $profiles_query = $conn->prepare("
                SELECT 
                    pp.*,
                    COUNT(DISTINCT ppi.profile_income_id) as income_count,
                    COUNT(DISTINCT ppd.profile_deduction_id) as deduction_count,
                    (SELECT COUNT(*) 
                     FROM pr_tbl_payroll_runs pr 
                     WHERE pr.profile_id = pp.profile_id) as usage_count
                FROM pr_tbl_payroll_profiles pp
                LEFT JOIN pr_tbl_payroll_profile_income ppi ON pp.profile_id = ppi.profile_id
                LEFT JOIN pr_tbl_payroll_profile_deductions ppd ON pp.profile_id = ppd.profile_id
                $where_sql
                GROUP BY pp.profile_id
                ORDER BY pp.is_default DESC, pp.profile_name ASC
            ");
            $profiles_query->execute($params);
            $profiles = $profiles_query->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($profiles)) {
                echo '<div class="col-12">';
                echo '<div class="alert alert-info">';
                echo '<i class="fa fa-info-circle"></i> No payroll profiles found. Click "Create New Profile" to get started.';
                echo '</div>';
                echo '</div>';
            }
            
            foreach ($profiles as $profile) {
                // Type badge colors
                $type_colors = [
                    'regular' => 'primary',
                    'special' => 'info',
                    '13th_month' => 'success',
                    'bonus' => 'warning',
                    'custom' => 'secondary'
                ];
                $badge_color = $type_colors[$profile['profile_type']] ?? 'secondary';
                
                // Frequency labels
                $frequency_labels = [
                    'monthly' => 'Monthly',
                    'semi-monthly' => 'Semi-Monthly',
                    'bi-weekly' => 'Bi-Weekly',
                    'weekly' => 'Weekly',
                    'one-time' => 'One-Time'
                ];
                $frequency_label = $frequency_labels[$profile['pay_frequency']] ?? $profile['pay_frequency'];
                ?>
                
                <div class="col-md-6 col-lg-4">
                    <div class="profile-card">
                        <div class="profile-header">
                            <div>
                                <h5 class="profile-title"><?php echo htmlspecialchars($profile['profile_name']); ?></h5>
                                <?php if ($profile['is_default']): ?>
                                    <span class="badge default-badge">
                                        <i class="fa fa-star"></i> DEFAULT
                                    </span>
                                <?php endif; ?>
                            </div>
                            <span class="badge badge-<?php echo $badge_color; ?> profile-type-badge">
                                <?php echo strtoupper(str_replace('_', ' ', $profile['profile_type'])); ?>
                            </span>
                        </div>
                        
                        <div class="profile-body">
                            <p class="mb-2">
                                <?php echo htmlspecialchars($profile['profile_description'] ?: 'No description provided'); ?>
                            </p>
                            <p class="mb-0">
                                <i class="fa fa-calendar"></i> <strong>Frequency:</strong> <?php echo $frequency_label; ?>
                            </p>
                        </div>
                        
                        <div class="profile-stats">
                            <div class="stat-item" title="Income Items">
                                <i class="fa fa-money text-success"></i>
                                <strong><?php echo $profile['income_count']; ?></strong> Income
                            </div>
                            <div class="stat-item" title="Deduction Items">
                                <i class="fa fa-minus-circle text-danger"></i>
                                <strong><?php echo $profile['deduction_count']; ?></strong> Deductions
                            </div>
                            <div class="stat-item" title="Times Used">
                                <i class="icon-clock text-info"></i>
                                <strong><?php echo $profile['usage_count']; ?></strong> Runs
                            </div>
                        </div>
                        
                        <div class="profile-actions mt-3">
                            <button class="btn btn-success btn-sm" 
                                    onclick="generatePayroll(<?php echo $profile['profile_id']; ?>)"
                                    title="Generate payroll from this template">
                                <i class="fa fa-play"></i> Generate Payroll
                            </button>
                            
                            <a href="view_payroll_profile.php?profile_id=<?php echo $profile['profile_id']; ?>" 
                               class="btn btn-info btn-sm" title="View details">
                                <i class="fa fa-eye"></i> View
                            </a>
                            
                            <button class="btn btn-warning btn-sm" 
                                    onclick="editProfile(<?php echo $profile['profile_id']; ?>)"
                                    title="Edit profile">
                                <i class="fa fa-pencil"></i> Edit
                            </button>
                            
                            <button class="btn btn-secondary btn-sm" 
                                    onclick="cloneProfile(<?php echo $profile['profile_id']; ?>)"
                                    title="Clone this profile">
                                <i class="fa fa-files-o"></i> Clone
                            </button>
                            
                            <?php if ($profile['is_active']): ?>
                                <button class="btn btn-warning btn-sm" 
                                        onclick="toggleStatus(<?php echo $profile['profile_id']; ?>, 0)"
                                        title="Deactivate">
                                    <i class="fa fa-toggle-on"></i>
                                </button>
                            <?php else: ?>
                                <button class="btn btn-success btn-sm" 
                                        onclick="toggleStatus(<?php echo $profile['profile_id']; ?>, 1)"
                                        title="Activate">
                                    <i class="fa fa-toggle-off"></i>
                                </button>
                            <?php endif; ?>
                            
                            <?php if (!$profile['is_default']): ?>
                                <button class="btn btn-danger btn-sm" 
                                        onclick="deleteProfile(<?php echo $profile['profile_id']; ?>)"
                                        title="Delete profile">
                                    <i class="fa fa-trash"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <?php
            }
        } catch (Exception $e) {
            echo '<div class="col-12">';
            echo '<div class="alert alert-danger">';
            echo '<i class="fa fa-exclamation-triangle"></i> Error loading profiles: ' . htmlspecialchars($e->getMessage());
            echo '</div>';
            echo '</div>';
        }
        ?>
    </div>
</div>

<!-- Add Profile Modal -->
<div class="modal fade" id="addProfileModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="save_payroll_profile.php" id="addProfileForm" class="standardized-form">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fa fa-plus-circle"></i> Create New Payroll Profile
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="personnel-category-card p-3 mb-3">
                    <div class="form-group">
                        <label for="profile_name"><strong>Profile Name <span class="text-danger">*</span></strong></label>
                        <input type="text" class="form-control" id="profile_name" name="profile_name" 
                               placeholder="e.g., October 2025 Regular Payroll" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="profile_type"><strong>Profile Type <span class="text-danger">*</span></strong></label>
                            <select class="form-control" id="profile_type" name="profile_type" required>
                                <option value="regular">Regular Payroll</option>
                                <option value="special">Special Payroll</option>
                                <option value="13th_month">13th Month Pay</option>
                                <option value="bonus">Bonus</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                        
                        <div class="form-group col-md-6">
                            <label for="pay_frequency"><strong>Pay Frequency <span class="text-danger">*</span></strong></label>
                            <select class="form-control" id="pay_frequency" name="pay_frequency" required>
                                <option value="monthly">Monthly</option>
                                <option value="semi-monthly">Semi-Monthly</option>
                                <option value="bi-weekly">Bi-Weekly</option>
                                <option value="weekly">Weekly</option>
                                <option value="one-time">One-Time</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="profile_description"><strong>Description</strong></label>
                        <textarea class="form-control" id="profile_description" name="profile_description" 
                                  rows="3" placeholder="Describe this payroll profile..."></textarea>
                    </div>
                    </div>
                    
                    <div class="personnel-category-card p-3 mb-0">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">
                            <strong>Active</strong> (can be used for payroll generation)
                        </label>
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_default" name="is_default" value="1">
                        <label class="form-check-label" for="is_default">
                            <strong>Set as Default Profile</strong> (will be selected automatically)
                        </label>
                    </div>
                    
                    <hr>
                    <p class="text-muted small">
                        <i class="fa fa-info-circle"></i> After creating the profile, you can add income and deduction items from the profile details page.
                    </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success" name="save_profile">
                        <i class="fa fa-save"></i> Create Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

</div><!-- End .page -->

<?php include('scripts_files.php'); ?>

<script>
function generatePayroll(profileId) {
    if (confirm('Generate a new payroll run from this profile?')) {
        window.location.href = 'generate_payroll_from_profile.php?profile_id=' + profileId;
    }
}

function editProfile(profileId) {
    window.location.href = 'view_payroll_profile.php?profile_id=' + profileId + '&mode=edit';
}

function cloneProfile(profileId) {
    if (confirm('Clone this profile to create a new one?')) {
        window.location.href = 'clone_payroll_profile.php?profile_id=' + profileId;
    }
}

function toggleStatus(profileId, newStatus) {
    const action = newStatus === 1 ? 'activate' : 'deactivate';
    if (confirm(`Are you sure you want to ${action} this profile?`)) {
        $.post('update_profile_status.php', {
            profile_id: profileId,
            is_active: newStatus
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        }, 'json').fail(function() {
            alert('Failed to update profile status');
        });
    }
}

function deleteProfile(profileId) {
    if (confirm('WARNING: This will permanently delete this profile. Continue?')) {
        $.post('delete_payroll_profile.php', {
            profile_id: profileId
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        }, 'json').fail(function() {
            alert('Failed to delete profile');
        });
    }
}

// Form validation
$('#addProfileForm').on('submit', function(e) {
    const profileName = $('#profile_name').val().trim();
    if (profileName.length < 3) {
        e.preventDefault();
        alert('Profile name must be at least 3 characters long');
        return false;
    }
});
</script>

</div><!-- End .page -->

</body>
</html>

<?php
ob_end_flush();
?>
