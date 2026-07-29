<?php
include('session.php');
include('header.php');

// Restrict to Admin only
if ($session_access !== 'Admin') {
    echo "<script>alert('Access Denied. Admin only.'); window.location.href='home_user.php';</script>";
    exit();
}

$active_audit_logs = "active";
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
                        $logs = $conn->query("SELECT * FROM audit_trail ORDER BY timestamp DESC")->fetchAll();
                        foreach ($logs as $log) {
                            $time = date('M d, Y h:i A', strtotime($log['timestamp']));
                            echo "<tr>
                                <td>{$time}</td>
                                <td><strong>{$log['user_name']}</strong></td>
                                <td><span class='badge badge-info'>{$log['module']}</span></td>
                                <td>{$log['action']}</td>
                                <td>{$log['details']}</td>
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
