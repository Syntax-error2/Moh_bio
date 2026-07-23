<?php
include('session.php');
include('header.php');

// Check if table already exists
$table_exists = false;
try {
    $check_query = $conn->query("SHOW TABLES LIKE 'pr_tbl_personnel_deductions'");
    $table_exists = ($check_query->rowCount() > 0);
} catch (PDOException $e) {
    error_log("Error checking table: " . $e->getMessage());
}

// Handle table creation
if (isset($_POST['create_table'])) {
    try {
        // Read the SQL file
        $sql_file = __DIR__ . '/db/personnel_deductions_schema.sql';
        
        if (!file_exists($sql_file)) {
            throw new Exception("SQL schema file not found: $sql_file");
        }
        
        $sql = file_get_contents($sql_file);
        
        // Execute the SQL
        $conn->exec($sql);
        $success = true;
        
        // Refresh table check
        $check_query = $conn->query("SHOW TABLES LIKE 'pr_tbl_personnel_deductions'");
        $table_exists = ($check_query->rowCount() > 0);
        
    } catch (Exception $e) {
        $error = $e->getMessage();
        error_log("Error creating pr_tbl_personnel_deductions table: " . $e->getMessage());
    }
}
?>

<body>
<?php include('menu_sidebar.php'); ?>

<div class="page">
    <?php include('navbar_header.php'); ?>

    <!-- Breadcrumb -->
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
                <li style="color: blue"><strong style="margin-right: 4px;"><?php echo $schoolName; ?> | </strong></li>
                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                <li class="breadcrumb-item"><a href="list_personnel.php">Personnel</a></li>
                <li class="breadcrumb-item active">Setup Personnel Deductions</li>
            </ul>
        </div>
    </div>

    <section class="mt-30px mb-30px">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h2>Personnel Deductions - Database Setup</h2>
                </div>
                <div class="card-body">
                    
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success">
                            <h4><i class="fa fa-check-circle"></i> Success!</h4>
                            <p>The <strong>pr_tbl_personnel_deductions</strong> table has been created successfully.</p>
                            <p>You can now use the Personnel Deductions module.</p>
                            <br>
                            <a href="list_personnel.php" class="btn btn-success">
                                <i class="fa fa-users"></i> Go to Personnel List
                            </a>
                        </div>
                    <?php elseif (isset($error)): ?>
                        <div class="alert alert-danger">
                            <h4><i class="fa fa-exclamation-circle"></i> Error</h4>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($table_exists): ?>
                        <div class="alert alert-info">
                            <h4><i class="fa fa-info-circle"></i> Table Already Exists</h4>
                            <p>The <strong>pr_tbl_personnel_deductions</strong> table is already created in your database.</p>
                            <br>
                            <a href="list_personnel.php" class="btn btn-primary">
                                <i class="fa fa-users"></i> Go to Personnel List
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <h4><i class="fa fa-info-circle"></i> Setup Required</h4>
                            <p>The Personnel Deductions module requires a database table to store deduction information.</p>
                            <p>Click the button below to create the required table:</p>
                        </div>
                        
                        <h4>What will be created:</h4>
                        <ul>
                            <li><strong>Table:</strong> pr_tbl_personnel_deductions</li>
                            <li><strong>Purpose:</strong> Store employer and employee deduction amounts per personnel</li>
                            <li><strong>Features:</strong> 
                                <ul>
                                    <li>Links personnel to their specific deduction amounts</li>
                                    <li>Tracks employer vs employee contributions</li>
                                    <li>Maintains audit trail (created_by, created_at)</li>
                                    <li>Prevents duplicate entries</li>
                                </ul>
                            </li>
                        </ul>
                        
                        <h4>Table Structure:</h4>
                        <pre class="bg-light p-3">CREATE TABLE pr_tbl_personnel_deductions (
  personnel_deduction_id INT(11) PRIMARY KEY AUTO_INCREMENT,
  personnel_id VARCHAR(50) NOT NULL,
  deduction_id INT(11) NOT NULL,
  employer_amt_per_pay DECIMAL(10,2) DEFAULT 0.00,
  employee_amt_per_pay DECIMAL(10,2) DEFAULT 0.00,
  is_active TINYINT(1) DEFAULT 1,
  created_by INT(11),
  created_at DATETIME,
  updated_at DATETIME,
  UNIQUE KEY (personnel_id, deduction_id)
);</pre>
                        
                        <form method="POST" style="margin-top: 30px;">
                            <button type="submit" name="create_table" class="btn btn-success btn-lg" 
                                    onclick="return confirm('Create the pr_tbl_personnel_deductions table?');">
                                <i class="fa fa-rocket"></i> Create Table Now
                            </button>
                        </form>
                        
                        <div class="alert alert-warning mt-30px">
                            <strong><i class="fa fa-exclamation-triangle"></i> Note:</strong> This operation is safe and will not affect existing data. 
                            If the table already exists, the CREATE TABLE IF NOT EXISTS statement will simply be skipped.
                        </div>
                    <?php endif; ?>
                    
                    <hr class="mt-30px">
                    
                    <h4>Manual Setup (Alternative)</h4>
                    <p>If you prefer to create the table manually, you can run the following command in your MySQL console:</p>
                    <pre class="bg-light p-3">SOURCE <?php echo __DIR__ . '/db/personnel_deductions_schema.sql'; ?></pre>
                    <p><small>Or import the file using phpMyAdmin</small></p>
                    
                </div>
            </div>
        </div>
    </section>
    
    <?php include('footer.php'); ?>
</div><!-- End .page -->

<?php include('scripts_files.php'); ?>

</body>
</html>
