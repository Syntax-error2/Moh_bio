<?php
include('session.php');
include('header.php');

// Restrict to Admin only
if ($session_access !== 'Admin') {
    echo "<script>alert('Access Denied. Admin only.'); window.location.href='home_user.php';</script>";
    exit();
}

$active_audit_logs = "active";

$audit_table_notice = '';
$logs = [];

try {
  // Ensure audit_trail table exists before reading logs.
  $conn->exec("CREATE TABLE IF NOT EXISTS audit_trail (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) DEFAULT NULL,
    user_name VARCHAR(150) DEFAULT NULL,
    action VARCHAR(150) DEFAULT NULL,
    module VARCHAR(150) DEFAULT NULL,
    details TEXT DEFAULT NULL,
    timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_timestamp (timestamp),
    KEY idx_user_id (user_id)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

  $logs_stmt = $conn->prepare("SELECT * FROM audit_trail ORDER BY timestamp DESC");
  $logs_stmt->execute();
  $logs = $logs_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  // Keep page usable even if audit table is unavailable.
  $audit_table_notice = 'Audit logs are temporarily unavailable. ' . $e->getMessage();
}
?>
<body>
    <!-- Side Navbar -->
    <nav class="side-navbar">
      <div class="side-navbar-wrapper">
        <!-- Sidebar Header    -->
        <?php include('menu_sidebar.php'); ?>
      </div>
    </nav>
    <div class="page">
      <!-- navbar-->
      <header class="header">
        <?php include('navbar_header.php'); ?>
      </header>
      
      <!-- Breadcrumb-->
      <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active">Audit Trail</li>
          </ul>
        </div>
      </div>

      <section class="mt-4">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h4>System Audit Trail</h4>
                </div>
                <div class="card-body">
                  <?php if (!empty($audit_table_notice)) { ?>
                    <div class="alert alert-warning" role="alert">
                      <?php echo htmlspecialchars($audit_table_notice, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                  <?php } ?>
                  <div class="table-responsive">
                    <table class="table table-striped table-hover" id="auditTable">
                      <thead>
                        <tr>
                          <th>Timestamp</th>
                          <th>User</th>
                          <th>Module</th>
                          <th>Action</th>
                          <th>Details</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        foreach ($logs as $log) {
                          $time = !empty($log['timestamp']) ? date('M d, Y h:i A', strtotime($log['timestamp'])) : '-';
                            echo "<tr>
                            <td>" . htmlspecialchars($time, ENT_QUOTES, 'UTF-8') . "</td>
                            <td><strong>" . htmlspecialchars($log['user_name'] ?? '-', ENT_QUOTES, 'UTF-8') . "</strong></td>
                            <td><span class='badge badge-info'>" . htmlspecialchars($log['module'] ?? '-', ENT_QUOTES, 'UTF-8') . "</span></td>
                            <td>" . htmlspecialchars($log['action'] ?? '-', ENT_QUOTES, 'UTF-8') . "</td>
                            <td>" . htmlspecialchars($log['details'] ?? '-', ENT_QUOTES, 'UTF-8') . "</td>
                            </tr>";
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      
      <?php include('footer.php'); ?>
    </div>

    <!-- Scripts -->
    <?php include('scripts_files.php'); ?>
    <script>
        $(document).ready(function() {
            $('#auditTable').DataTable({
                "order": [[ 0, "desc" ]]
            });
        });
    </script>
  </body>
</html>
