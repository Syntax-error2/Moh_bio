<?php
include('session.php');
include('header.php');
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
            <li class="breadcrumb-item active">Biometric Enrollment</li>
          </ul>
        </div>
      </div>
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                  <h2 class="h5 display">Biometric Enrollment List</h2>
                  <div class="d-flex align-items-center">
                    <select id="office-filter" class="form-control form-control-sm" style="width: 250px; margin-right: 15px;">
                      <option value="">All Offices / Departments</option>
                      <?php
                      $dept_q = $conn->query("SELECT dept_office_name FROM dept_offices ORDER BY dept_office_name ASC");
                      while($d = $dept_q->fetch()) {
                          echo "<option value=\"".htmlspecialchars($d['dept_office_name'])."\">".htmlspecialchars($d['dept_office_name'])."</option>";
                      }
                      ?>
                    </select>
                    <a href="schedule_preferences.php" class="btn btn-primary btn-sm" style="white-space: nowrap;"><i class="fa fa-clock-o"></i> Manage Office Shifts</a>
                  </div>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-striped table-hover" id="datatable">
                      <thead>
                        <tr>
                          <th>Employee ID</th>
                          <th>Fullname</th>
                          <th>Department</th>
                          <th>Enrolled Fingers</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $query = $conn->query("SELECT p.personnel_id, p.personnel_id_code, p.lname, p.fname, p.mname, p.fingerprint_template, d.dept_office_name 
                                               FROM personnels p 
                                               LEFT JOIN dept_offices d ON p.do_id = d.do_id 
                                               WHERE p.separation_date IS NULL OR p.separation_date='' OR p.separation_date='  /  /    '
                                               ORDER BY p.lname ASC");
                        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                            $fingers = [];
                            if (!empty($row['fingerprint_template'])) {
                                $data = json_decode($row['fingerprint_template'], true);
                                if (is_array($data)) {
                                    $fingers = array_keys($data);
                                } else {
                                    // Legacy single template
                                    $fingers = ['Legacy'];
                                }
                            }
                            $count = count($fingers);
                            $badge = $count == 0 ? '<span class="badge badge-danger">Unenrolled</span>' : '<span class="badge badge-success">'.$count.'/4 Fingers</span>';
                            
                            $fullname = strtoupper($row['lname']).", ".$row['fname']." ".$row['mname'];
                        ?>
                        <tr>
                          <td><?php echo $row['personnel_id_code']; ?></td>
                          <td><?php echo $fullname; ?></td>
                          <td><?php echo $row['dept_office_name']; ?></td>
                          <td><?php echo $badge; ?></td>
                          <td>
                            <a href="biometric_register.php?pid=<?php echo $row['personnel_id']; ?>" class="btn btn-sm btn-info"><i class="fa fa-fingerprint"></i> Manage Biometrics</a>
                          </td>
                        </tr>
                        <?php } ?>
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
    <?php include('scripts_files.php'); ?>
    <script>
      $(document).ready(function() {
          var table = $('#datatable').DataTable();
          
          $('#office-filter').on('change', function() {
              table.column(2).search(this.value).draw();
          });
      });
    </script>
  </body>
</html>
