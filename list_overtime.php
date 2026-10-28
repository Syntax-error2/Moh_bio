<?php
include('session.php');
include('header.php'); 
$canReviewOvertime = in_array($session_access, ['Administrator', 'Admin'], true);
if (empty($_SESSION['overtime_csrf'])) $_SESSION['overtime_csrf'] = bin2hex(random_bytes(24));
?>
<body>
  <?php include('menu_sidebar.php'); ?>
  <div class="page">
    <?php include('navbar_header.php'); ?>
    <section class="mt-30px mb-30px">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12 col-md-12">
            <div class="card updates recent-updated">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h4>OVERTIME REQUESTS BULLETIN</h4>
                <a data-toggle="modal" data-target="#addOvertime" class="btn btn-sm btn-primary" style="color: white; font-weight: bold; cursor: pointer;">
                  <i class="fa fa-plus"></i> Add Overtime Request
                </a>
              </div>
              <div class="card-body">
                <form method="POST" action="list_overtime.php?cw=list_overtime" style="margin-bottom: 20px;">
    <div class="row">
        <div class="col-md-4">
            <label>Date From</label>
            <input type="date" name="date_from" class="form-control" value="<?php echo isset($_POST['date_from']) ? $_POST['date_from'] : date('Y-m-01'); ?>">
        </div>
        <div class="col-md-4">
            <label>Date To</label>
            <input type="date" name="date_to" class="form-control" value="<?php echo isset($_POST['date_to']) ? $_POST['date_to'] : date('Y-m-t'); ?>">
        </div>
        <div class="col-md-4">
            <label>&nbsp;</label><br>
            <button type="submit" name="filter_ot" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
            <a href="list_overtime.php?cw=list_overtime" class="btn btn-secondary">Reset</a> <button type="submit" formtarget="_blank" formaction="print_overtime_report.php" class="btn btn-info"><i class="fa fa-print"></i> Print</button>
        </div>
    </div>
</form>
<div class="table-responsive">
                  <table id="example" class="table table-striped table-hover table-bordered">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Personnel Name</th>
                        <th>Overtime Date</th>
                        <th>Remarks</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $date_from = isset($_POST['date_from']) ? $_POST['date_from'] : date('Y-m-01');
$date_to = isset($_POST['date_to']) ? $_POST['date_to'] : date('Y-m-t');
$ot_query = $conn->prepare("SELECT ot.*, p.lname, p.fname FROM personnel_overtime_requests ot LEFT JOIN personnels p ON ot.personnel_id = p.personnel_id WHERE ot.ot_date BETWEEN ? AND ? ORDER BY ot.ot_date DESC");
$ot_query->execute([$date_from, $date_to]);
                      while($row = $ot_query->fetch()){
                      ?>
                      <tr>
                        <td><?php echo $row['ot_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['lname'] . ', ' . $row['fname']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($row['ot_date'])); ?></td>
                        <td><?php echo htmlspecialchars($row['remarks']); ?></td>
                        <td><?php echo htmlspecialchars(strtoupper($row['status']), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                          <?php if ($canReviewOvertime && $row['status'] === 'pending') { ?>
                          <form method="post" action="review_overtime.php" style="display:inline">
                            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['overtime_csrf'], ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="ot_id" value="<?php echo (int)$row['ot_id']; ?>">
                            <button class="btn btn-sm btn-success" name="decision" value="approved" type="submit">Approve</button>
                            <button class="btn btn-sm btn-warning" name="decision" value="rejected" type="submit">Reject</button>
                          </form>
                          <?php } ?>
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
    <?php include('add_overtime_modal.php'); ?>
    <?php include('footer.php'); ?>
  </div>
  <?php include('scripts_files.php'); ?>
</body>
</html>
