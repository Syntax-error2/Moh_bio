<?php
include('session.php');
include('header.php');

// Check if table already exists
$table_exists = false;
try {
    $check_query = $conn->query("SHOW TABLES LIKE 'pr_tbl_personnel_income'");
    $table_exists = ($check_query->rowCount() > 0);
} catch (PDOException $e) {
    error_log("Error checking table: " . $e->getMessage());
}

// Handle table creation
if (isset($_POST['create_table'])) {
    try {
        $sql = "CREATE TABLE IF NOT EXISTS `pr_tbl_personnel_income` (
          `personnel_income_id` INT(11) NOT NULL AUTO_INCREMENT,
          `personnel_id` VARCHAR(50) NOT NULL COMMENT 'References personnels.personnel_id',
          `income_id` INT(11) NOT NULL COMMENT 'References pr_tbl_income.income_id',
          `amount_per_pay` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Amount paid per pay period',
          `is_active` TINYINT(1) NOT NULL DEFAULT 1,
          `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
          `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(),
          `user_id` INT(11) NULL COMMENT 'User who created this record',
          
          PRIMARY KEY (`personnel_income_id`),
          UNIQUE KEY `unique_personnel_income` (`personnel_id`, `income_id`),
          KEY `idx_personnel_id` (`personnel_id`),
          KEY `idx_income_id` (`income_id`),
          KEY `idx_is_active` (`is_active`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        COMMENT='Junction table: Links personnel to income types with amounts'";
        
        $conn->exec($sql);
        $success = true;
        
        // Refresh table check
        $check_query = $conn->query("SHOW TABLES LIKE 'pr_tbl_personnel_income'");
        $table_exists = ($check_query->rowCount() > 0);
        
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
        error_log("Error creating pr_tbl_personnel_income table: " . $e->getMessage());
    }
}
?>

<body>
<?php include('menu_sidebar.php'); ?>

<div class="page">
    <?php include('navbar_header.php'); ?>
    
    <!-- Breadcrumb-->
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
                <li style="color: blue"><strong style="margin-right: 4px;"><?php echo htmlspecialchars($schoolName); ?> | </strong></li>
                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                <li class="breadcrumb-item"><a href="income.php">Income Management</a></li>
                <li class="breadcrumb-item active">Setup Personnel Income Table</li>
            </ul>
        </div>
    </div>
    
    <!-- Main Section -->
    <section class="mt-30px mb-30px">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="h4 mb-0">
                                <i class="fa fa-database"></i> Personnel Income Table Setup Wizard
                            </h3>
                        </div>
                        
                        <div class="card-body">
                            <?php if (isset($success)) { ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="alert-heading"><i class="fa fa-check-circle"></i> Success!</h4>
                                <p>The <code>pr_tbl_personnel_income</code> table has been created successfully.</p>
                                <hr>
                                <p class="mb-0">
                                    <a href="list_personnel.php" class="btn btn-success">
                                        <i class="fa fa-users"></i> Go to Personnel List
                                    </a>
                                    <a href="income.php" class="btn btn-primary">
                                        <i class="fa fa-money"></i> Manage Income Types
                                    </a>
                                </p>
                            </div>
                            <?php } ?>
                            
                            <?php if (isset($error)) { ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="alert-heading"><i class="fa fa-times-circle"></i> Error!</h4>
                                <p><?php echo htmlspecialchars($error); ?></p>
                            </div>
                            <?php } ?>
                            
                            <?php if ($table_exists) { ?>
                            <div class="alert alert-info" role="alert">
                                <h4 class="alert-heading"><i class="fa fa-info-circle"></i> Table Already Exists</h4>
                                <p>The <code>pr_tbl_personnel_income</code> table is already created in your database.</p>
                                <hr>
                                <p class="mb-0">
                                    <strong>Next Steps:</strong>
                                </p>
                                <ol>
                                    <li>Go to <a href="list_personnel.php">Personnel List</a></li>
                                    <li>Select a personnel</li>
                                    <li>Click on "INCOME" tab</li>
                                    <li>Enter income amounts for that personnel</li>
                                </ol>
                            </div>
                            <?php } else { ?>
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <h5>Table Information</h5>
                                    <p>This wizard will create the <code>pr_tbl_personnel_income</code> table which stores income amounts for individual personnel.</p>
                                    
                                    <h6 class="mt-4">Table Structure:</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Column</th>
                                                    <th>Type</th>
                                                    <th>Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><code>personnel_income_id</code></td>
                                                    <td>INT(11)</td>
                                                    <td>Primary key, auto-increment</td>
                                                </tr>
                                                <tr>
                                                    <td><code>personnel_id</code></td>
                                                    <td>VARCHAR(50)</td>
                                                    <td>References personnels table</td>
                                                </tr>
                                                <tr>
                                                    <td><code>income_id</code></td>
                                                    <td>INT(11)</td>
                                                    <td>References pr_tbl_income table</td>
                                                </tr>
                                                <tr>
                                                    <td><code>amount_per_pay</code></td>
                                                    <td>DECIMAL(10,2)</td>
                                                    <td>Amount paid per pay period</td>
                                                </tr>
                                                <tr>
                                                    <td><code>is_active</code></td>
                                                    <td>TINYINT(1)</td>
                                                    <td>Active status (0 or 1)</td>
                                                </tr>
                                                <tr>
                                                    <td><code>created_at</code></td>
                                                    <td>DATETIME</td>
                                                    <td>Creation timestamp</td>
                                                </tr>
                                                <tr>
                                                    <td><code>updated_at</code></td>
                                                    <td>DATETIME</td>
                                                    <td>Last update timestamp</td>
                                                </tr>
                                                <tr>
                                                    <td><code>user_id</code></td>
                                                    <td>INT(11)</td>
                                                    <td>User who created/updated</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <h6 class="mt-4">Relationships:</h6>
                                    <ul>
                                        <li>Links to <code>personnels</code> table via <code>personnel_id</code></li>
                                        <li>Links to <code>pr_tbl_income</code> table via <code>income_id</code></li>
                                        <li>Unique constraint on <code>(personnel_id, income_id)</code> - prevents duplicates</li>
                                    </ul>
                                    
                                    <h6 class="mt-4">Features:</h6>
                                    <ul>
                                        <li>✅ Stores individual income amounts per personnel per pay period</li>
                                        <li>✅ Supports multiple income types (Basic Salary, PERA, COLA, etc.)</li>
                                        <li>✅ Active/inactive status for each income entry</li>
                                        <li>✅ Audit trail with timestamps and user tracking</li>
                                        <li>✅ Indexed for fast queries</li>
                                    </ul>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h5 class="card-title"><i class="fa fa-magic"></i> Ready to Create?</h5>
                                            <p class="card-text">Click the button below to create the table in your database.</p>
                                            
                                            <form method="POST" onsubmit="return confirm('Are you sure you want to create the pr_tbl_personnel_income table?');">
                                                <button type="submit" name="create_table" class="btn btn-primary btn-block btn-lg">
                                                    <i class="fa fa-database"></i> Create Table Now
                                                </button>
                                            </form>
                                            
                                            <hr>
                                            
                                            <h6><i class="fa fa-question-circle"></i> Alternative Method</h6>
                                            <p class="small">You can also create this table manually by:</p>
                                            <ol class="small">
                                                <li>Opening phpMyAdmin</li>
                                                <li>Selecting <code>moh_hrms</code> database</li>
                                                <li>Going to Import tab</li>
                                                <li>Importing: <code>payroll/db/personnel_income_schema.sql</code></li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <?php include('footer.php'); ?>
</div><!-- End .page -->

<?php include('scripts_files.php'); ?>

</body>
</html>
