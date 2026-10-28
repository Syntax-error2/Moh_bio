<?php
/**
 * Script to automatically calculate and update Age and Years in Service
 * for all personnel based on their Date of Birth and Appointment Date.
 * 
 * You can set this script up in Windows Task Scheduler to run every January 1st,
 * or simply click/run it manually when needed.
 */
include('session.php');
include('header.php');

$success_count = 0;
$error_count = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stats'])) {
    
    // Fetch all personnel
    $query = $conn->query("SELECT personnel_id, bdMM, bdDD, bdYYYY, appointment_date FROM personnels");
    
    $current_year = date('Y');
    $current_month = date('m');
    $current_day = date('d');
    
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $pid = $row['personnel_id'];
        $bMonth = intval($row['bdMM']);
        $bDay = intval($row['bdDD']);
        $bYear = intval($row['bdYYYY']);
        $appointment = trim($row['appointment_date']);
        
        // --- CALCULATE AGE ---
        $age = 0;
        if ($bYear > 1900) {
            $age = $current_year - $bYear;
            // Subtract 1 if they haven't had their birthday yet this year
            if ($current_month < $bMonth || ($current_month == $bMonth && $current_day < $bDay)) {
                $age--;
            }
        }
        
        // --- CALCULATE YEARS IN SERVICE ---
        $years_in_service = 0;
        if (!empty($appointment)) {
            // Convert formats like MM/DD/YYYY or MM/DD/YY to timestamp
            // Note: PHP strtotime handles MM/DD/YYYY natively.
            $app_timestamp = strtotime($appointment);
            
            if ($app_timestamp !== false) {
                $app_year = date('Y', $app_timestamp);
                $app_month = date('m', $app_timestamp);
                $app_day = date('d', $app_timestamp);
                
                $years_in_service = $current_year - $app_year;
                if ($current_month < $app_month || ($current_month == $app_month && $current_day < $app_day)) {
                    $years_in_service--;
                }
                
                // Prevent negative years if appointment date is in the future (somehow)
                if ($years_in_service < 0) {
                    $years_in_service = 0;
                }
            }
        }
        
        // --- UPDATE DATABASE ---
        try {
            $update_stmt = $conn->prepare("UPDATE personnels SET age = :age, num_of_yrs = :num_of_yrs WHERE personnel_id = :pid");
            $update_stmt->execute([
                ':age' => $age,
                ':num_of_yrs' => $years_in_service,
                ':pid' => $pid
            ]);
            $success_count++;
        } catch (PDOException $e) {
            $error_count++;
        }
    }
    
    $message = "Successfully updated Age and Years in Service for $success_count personnel records.";
}
?>
<!DOCTYPE html>
<html>
<body>
  <?php include('menu_sidebar.php'); ?>
  <div class="page">
    <?php include('navbar_header.php'); ?>
    <div class="breadcrumb-holder">
      <div class="container-fluid">
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="home.php">Home</a></li>
          <li class="breadcrumb-item active">Update Personnel Stats</li>
        </ul>
      </div>
    </div>
    
    <section class="mt-30px mb-30px">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-8 mx-auto">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="h5 display"><i class="fa fa-refresh"></i> Batch Update Age & Years of Service</h2>
              </div>
              <div class="card-body">
                
                <?php if (isset($message)): ?>
                <div class="alert alert-success">
                    <i class="fa fa-check-circle"></i> <?php echo $message; ?>
                    <?php if ($error_count > 0) echo "<br>Failed to update $error_count records."; ?>
                </div>
                <?php endif; ?>

                <p>This script recalculates and permanently updates the <strong>Age</strong> and <strong>Years in Service</strong> for every employee in the database.</p>
                <p>Calculations are based on today's date against their recorded <strong>Date of Birth</strong> and <strong>Appointment Date</strong>.</p>
                
                <div class="alert alert-info">
                    <strong>Pro Tip:</strong> You can bookmark this page and run it every January 1st (or anytime) to ensure all personnel records dynamically reflect their exact accurate age and tenure for the current year.
                </div>
                
                <form method="POST" onsubmit="return confirm('Are you sure you want to recalculate and update all personnel records?');">
                    <button type="submit" name="update_stats" class="btn btn-primary btn-lg mt-3">
                        <i class="fa fa-cogs"></i> Run Batch Update Now
                    </button>
                </form>

              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?php include('footer.php'); ?>
  </div>
  <?php include('scripts_files.php'); ?>
</body>
</html>
