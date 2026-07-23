<!DOCTYPE html>
<html>

<?php
include('session.php');
include('header.php'); 

$day = date("l"); //Mon-Sun

if(isset($_POST['filterDate'])){
    $filterDate = $_POST['reportDate'];
} else {
    $filterDate = date('m/d/Y');
}

// Get statistics
try {
    // Payroll statistics
    $payroll_stats = $conn->query("
        SELECT 
            COUNT(*) as total_profiles,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_profiles
        FROM pr_tbl_payroll_profiles
    ")->fetch(PDO::FETCH_ASSOC);
    
    $payroll_runs = $conn->query("
        SELECT 
            COUNT(*) as total_runs,
            SUM(CASE WHEN run_status = 'completed' THEN 1 ELSE 0 END) as completed_runs,
            SUM(CASE WHEN run_status = 'draft' THEN 1 ELSE 0 END) as draft_runs
        FROM pr_tbl_payroll_runs
    ")->fetch(PDO::FETCH_ASSOC);
    
    // Personnel statistics
    $personnel_stats = $conn->query("
        SELECT COUNT(*) as total_personnel 
        FROM personnels 
        WHERE (separation_date IS NULL)
    ")->fetch(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $payroll_stats = ['total_profiles' => 0, 'active_profiles' => 0];
    $payroll_runs = ['total_runs' => 0, 'completed_runs' => 0, 'draft_runs' => 0];
    $personnel_stats = ['total_personnel' => 0];
}
?>

<style>
    .dashboard-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    .dashboard-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }
    .quick-link-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 15px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        text-decoration: none;
        display: block;
    }
    .quick-link-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        color: white;
        text-decoration: none;
    }
    .quick-link-card i {
        font-size: 2.5rem;
        margin-bottom: 10px;
        display: block;
    }
    .quick-link-card h4 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: #ffffff;
        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }
    .quick-link-card p {
        margin: 5px 0 0 0;
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.95);
        text-shadow: 0 1px 2px rgba(0,0,0,0.15);
    }
    .stat-box {
        text-align: center;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 6px;
        margin-bottom: 10px;
    }
    .stat-box h3 {
        font-size: 2.5rem;
        margin: 0;
        color: #667eea;
        font-weight: bold;
    }
    .stat-box p {
        margin: 5px 0 0 0;
        color: #6c757d;
        font-size: 0.9rem;
    }
    .guide-step {
        background: #f8f9fa;
        border-left: 4px solid #667eea;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 4px;
    }
    .guide-step h5 {
        color: #667eea;
        margin: 0 0 10px 0;
        font-weight: 600;
    }
    .guide-step p {
        margin: 0;
        color: #495057;
    }
    .welcome-banner {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 8px;
        margin-bottom: 25px;
    }
    .welcome-banner h2 {
        margin: 0 0 10px 0;
        font-size: 2rem;
    }
    .welcome-banner p {
        margin: 0;
        opacity: 0.9;
    }
    .menu-category {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .menu-category h4 {
        color: #667eea;
        margin: 0 0 15px 0;
        padding-bottom: 10px;
        border-bottom: 2px solid #f8f9fa;
    }
    .menu-item {
        padding: 10px 15px;
        margin: 5px 0;
        background: #f8f9fa;
        border-radius: 4px;
        transition: all 0.2s ease;
        display: block;
        color: #495057;
        text-decoration: none;
    }
    .menu-item:hover {
        background: #667eea;
        color: white;
        text-decoration: none;
        transform: translateX(5px);
    }
    .menu-item i {
        width: 25px;
        text-align: center;
        margin-right: 10px;
    }
</style>

<body>

<?php include('menu_sidebar.php'); ?>

<div class="page">

<?php include('navbar_header.php');

if($session_access == "User") { ?>
    <script>
        window.location = 'list_personnel_individual_details.php?dept=<?php echo $user_dept; ?>&personnel_id=<?php echo $user_personnel_id; ?>';
    </script>
<?php } elseif($session_access == "Administrator") { } ?>

<?php if($session_access == "Administrator") { ?>

<section class="dashboard" style="padding: 20px;">
    <div class="container-fluid">
        
        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2><i class="icon-bill"></i> Payroll Management System</h2>
                    <p>Welcome back, <?php echo $name; ?>! Manage payroll templates, process payroll runs, and track payroll history all in one place.</p>
                </div>
                <div class="col-md-4 text-right">
                    <h4 style="margin: 0;"><?php echo date("l, F d, Y"); ?></h4>
                    <p style="margin: 5px 0 0 0; font-size: 0.9rem;"><?php echo date("h:i A"); ?></p>
                </div>
            </div>
        </div>

        <!-- Quick Statistics -->
        <div class="row">
            <div class="col-md-3">
                <div class="stat-box">
                    <h3><?php echo number_format($personnel_stats['total_personnel']); ?></h3>
                    <p><i class="icon-user"></i> Active Personnel</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <h3><?php echo number_format($payroll_stats['total_profiles']); ?></h3>
                    <p><i class="icon-bill"></i> Payroll Templates</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <h3><?php echo number_format($payroll_runs['total_runs']); ?></h3>
                    <p><i class="icon-clock"></i> Total Payroll Runs</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <h3><?php echo number_format($payroll_runs['completed_runs']); ?></h3>
                    <p><i class="fa fa-check-circle"></i> Completed Runs</p>
                </div>
            </div>
        </div>

        <!-- Quick Access Menu -->
        <div class="row mt-4">
            <div class="col-md-12">
                <h3 style="margin-bottom: 20px;"><i class="fa fa-bolt"></i> Quick Access</h3>
            </div>
        </div>

        <div class="row">
            <!-- Payroll Templates -->
            <div class="col-md-3">
                <a href="list_payroll_profiles.php" class="quick-link-card">
                    <i class="icon-bill"></i>
                    <h4>Payroll Templates</h4>
                    <p>Manage payroll profiles</p>
                </a>
            </div>
            
            <!-- Generate Payroll -->
            <div class="col-md-3">
                <a href="list_payroll_profiles.php" class="quick-link-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="fa fa-cogs"></i>
                    <h4>Generate Payroll</h4>
                    <p>Create new payroll run</p>
                </a>
            </div>
            
            <!-- Payroll History -->
            <div class="col-md-3">
                <a href="list_payroll_history.php" class="quick-link-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <i class="icon-clock"></i>
                    <h4>Payroll History</h4>
                    <p>View all payroll runs</p>
                </a>
            </div>
            
            <!-- Personnel -->
            <div class="col-md-3">
                <a href="list_personnel.php?dept=All" class="quick-link-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <i class="icon-user"></i>
                    <h4>Personnel</h4>
                    <p>Manage personnel data</p>
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Income Reference -->
            <div class="col-md-3">
                <a href="income.php" class="quick-link-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <i class="fa fa-money"></i>
                    <h4>Income Reference</h4>
                    <p>Manage income types</p>
                </a>
            </div>
            
            <!-- Deduction Reference -->
            <div class="col-md-3">
                <a href="deductions.php" class="quick-link-card" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                    <i class="fa fa-minus-circle"></i>
                    <h4>Deduction Reference</h4>
                    <p>Manage deduction types</p>
                </a>
            </div>
            
            <!-- Reports -->
            <div class="col-md-3">
                <a href="printReports.php" class="quick-link-card" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                    <i class="icon-page"></i>
                    <h4>Reports</h4>
                    <p>Generate reports</p>
                </a>
            </div>
        </div>

        <!-- User Guide Section -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="dashboard-card">
                    <h4 style="color: #667eea; margin-bottom: 20px;">
                        <i class="fa fa-book"></i> Quick Start Guide
                    </h4>
                    
                    <div class="guide-step">
                        <h5><i class="fa fa-check"></i> Step 1: Set Up Templates</h5>
                        <p>Create payroll templates with income and deduction items. Templates can be reused for recurring payroll processing.</p>
                        <a href="list_payroll_profiles.php" class="btn btn-sm btn-primary mt-2">
                            <i class="fa fa-arrow-right"></i> Go to Templates
                        </a>
                    </div>
                    
                    <div class="guide-step">
                        <h5><i class="fa fa-check"></i> Step 2: Configure Personnel</h5>
                        <p>Set up individual income and deductions for each personnel member.</p>
                        <a href="list_personnel.php?dept=All" class="btn btn-sm btn-primary mt-2">
                            <i class="fa fa-arrow-right"></i> View Personnel
                        </a>
                    </div>
                    
                    <div class="guide-step">
                        <h5><i class="fa fa-check"></i> Step 3: Generate Payroll</h5>
                        <p>Select a template, choose personnel, configure dates, and generate the payroll run.</p>
                        <a href="list_payroll_profiles.php" class="btn btn-sm btn-success mt-2">
                            <i class="fa fa-cogs"></i> Generate Now
                        </a>
                    </div>
                    
                    <div class="guide-step">
                        <h5><i class="fa fa-check"></i> Step 4: Review & Process</h5>
                        <p>Review generated payroll, make adjustments if needed, and mark as completed.</p>
                        <a href="list_payroll_history.php" class="btn btn-sm btn-info mt-2">
                            <i class="fa fa-eye"></i> View History
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="dashboard-card">
                    <h4 style="color: #667eea; margin-bottom: 20px;">
                        <i class="fa fa-map"></i> Payroll Workflow
                    </h4>
                    
                    <div style="text-align: center; padding: 20px;">
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
                            <i class="icon-bill" style="font-size: 2rem; color: #667eea;"></i>
                            <h5 style="margin: 10px 0 5px 0;">Create Template</h5>
                            <p style="margin: 0; font-size: 0.85rem; color: #6c757d;">Define income & deduction items</p>
                        </div>
                        
                        <div style="text-align: center; margin: 10px 0;">
                            <i class="fa fa-arrow-down" style="font-size: 1.5rem; color: #667eea;"></i>
                        </div>
                        
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
                            <i class="icon-user" style="font-size: 2rem; color: #667eea;"></i>
                            <h5 style="margin: 10px 0 5px 0;">Configure Personnel</h5>
                            <p style="margin: 0; font-size: 0.85rem; color: #6c757d;">Set individual amounts</p>
                        </div>
                        
                        <div style="text-align: center; margin: 10px 0;">
                            <i class="fa fa-arrow-down" style="font-size: 1.5rem; color: #667eea;"></i>
                        </div>
                        
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
                            <i class="fa fa-cogs" style="font-size: 2rem; color: #667eea;"></i>
                            <h5 style="margin: 10px 0 5px 0;">Generate Payroll Run</h5>
                            <p style="margin: 0; font-size: 0.85rem; color: #6c757d;">Process payroll automatically</p>
                        </div>
                        
                        <div style="text-align: center; margin: 10px 0;">
                            <i class="fa fa-arrow-down" style="font-size: 1.5rem; color: #667eea;"></i>
                        </div>
                        
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                            <i class="fa fa-check-circle" style="font-size: 2rem; color: #28a745;"></i>
                            <h5 style="margin: 10px 0 5px 0;">Review & Complete</h5>
                            <p style="margin: 0; font-size: 0.85rem; color: #6c757d;">Verify and finalize payroll</p>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-3" style="margin-bottom: 0;">
                        <i class="fa fa-lightbulb-o"></i> <strong>Tip:</strong> Use templates to save time on recurring payroll cycles!
                    </div>
                </div>
            </div>
        </div>

        <!-- All Menu Items -->
        <div class="row mt-4">
            <div class="col-md-12">
                <h3 style="margin-bottom: 20px;"><i class="fa fa-bars"></i> All Menu Items</h3>
            </div>
        </div>

        <div class="row">
            <!-- Payroll Templates -->
            <div class="col-md-4">
                <div class="menu-category">
                    <h4><i class="icon-bill"></i> Payroll Templates</h4>
                    <a href="list_payroll_profiles.php" class="menu-item">
                        <i class="fa fa-folder-open"></i> All Templates
                    </a>
                    <a href="list_payroll_profiles.php?type=regular" class="menu-item">
                        <i class="fa fa-calendar"></i> Regular Payroll
                    </a>
                    <a href="list_payroll_profiles.php?type=13th_month" class="menu-item">
                        <i class="fa fa-gift"></i> 13th Month
                    </a>
                    <a href="list_payroll_profiles.php?type=bonus" class="menu-item">
                        <i class="fa fa-star"></i> Bonus
                    </a>
                    <a href="list_payroll_profiles.php?type=special" class="menu-item">
                        <i class="fa fa-certificate"></i> Special Payroll
                    </a>
                </div>
            </div>

            <!-- Payroll History -->
            <div class="col-md-4">
                <div class="menu-category">
                    <h4><i class="icon-clock"></i> Payroll History</h4>
                    <a href="list_payroll_history.php" class="menu-item">
                        <i class="fa fa-list"></i> All Payroll Runs
                    </a>
                    <a href="list_payroll_history.php?status=draft" class="menu-item">
                        <i class="fa fa-pencil"></i> Draft Runs
                    </a>
                    <a href="list_payroll_history.php?status=pending" class="menu-item">
                        <i class="fa fa-clock-o"></i> Pending Approval
                    </a>
                    <a href="list_payroll_history.php?status=completed" class="menu-item">
                        <i class="fa fa-check-circle"></i> Completed Runs
                    </a>
                </div>
            </div>

            <!-- Income & Deductions -->
            <div class="col-md-4">
                <div class="menu-category">
                    <h4><i class="fa fa-money"></i> Income & Deductions</h4>
                    <a href="list_personnel.php?dept=All" class="menu-item">
                        <i class="fa fa-plus-circle"></i> Personnel Income
                    </a>
                    <a href="list_personnel.php?dept=All" class="menu-item">
                        <i class="fa fa-minus-circle"></i> Personnel Deductions
                    </a>
                    <a href="income.php" class="menu-item">
                        <i class="fa fa-list-alt"></i> Income Reference
                    </a>
                    <a href="deductions.php" class="menu-item">
                        <i class="fa fa-list-alt"></i> Deduction Reference
                    </a>
                </div>
            </div>
        </div>

        <!-- Additional Links -->
        <div class="row">
            <div class="col-md-4">
                <div class="menu-category">
                    <h4><i class="icon-user"></i> Personnel</h4>
                    <a href="list_personnel.php?dept=All" class="menu-item">
                        <i class="icon-user"></i> All Personnels
                    </a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="menu-category">
                    <h4><i class="icon-page"></i> Reports</h4>
                    <a href="printReports.php" class="menu-item">
                        <i class="icon-page"></i> Generate Reports
                    </a>
                </div>
            </div>
        </div>

        <!-- Help & Support -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="dashboard-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 style="color: white; margin: 0 0 10px 0;">
                                <i class="fa fa-question-circle"></i> Need Help?
                            </h4>
                            <p style="margin: 0;">Check out our comprehensive documentation and guides for detailed instructions on using the payroll system.</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="PAYROLL_SYSTEM_GUIDE.md" target="_blank" class="btn btn-light btn-lg">
                                <i class="fa fa-book"></i> View Documentation
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<?php } ?>

<?php include('footer.php'); ?>

</div>

<?php include('scripts_files.php'); ?>

</body>
</html>