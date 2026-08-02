<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
   
   // Sanitize and validate GET parameters BEFORE including header.php
   $get_dept = $_GET['dept'] ?? '';
   $personnel_id = $_GET['personnel_id'] ?? '';
   
   if (empty($personnel_id)) {
       header('Location: list_personnel.php?dept=' . urlencode($get_dept));
       exit();
   }
   
   include('header.php');
   
   ?>

  <body>
  
  <?php include('menu_sidebar.php'); ?>


    <div class="page">

    <?php include('navbar_header.php'); ?>
    
    <?php
    try {
        $staff_stmt = $conn->prepare("SELECT personnel_id, fname, mname, lname, suffix, img, shift_id, personnel_id_code
                                     FROM personnels
                                     WHERE personnel_id = :personnel_id
                                     LIMIT 1");
        $staff_stmt->execute([':personnel_id' => $personnel_id]);
        $staff_row = $staff_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$staff_row) {
            ?>
            <script>
            alert('Personnel not found.');
            window.location = 'list_personnel.php?dept=<?php echo urlencode($get_dept); ?>';
            </script>
            <?php
            exit();
        }

        $shift_stmt = $conn->prepare("SELECT shift_name, type FROM shifts WHERE shift_id = :shift_id LIMIT 1");
        $shift_stmt->execute([':shift_id' => $staff_row['shift_id']]);
        $shift_row = $shift_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$shift_row) {
            $shift_row = ['shift_name' => 'N/A', 'type' => 'Regular Shift'];
        }

        $tables_stmt = $conn->query("SHOW TABLES LIKE 'pr_tbl_payroll_run_details'");
        $has_run_details = ($tables_stmt && $tables_stmt->rowCount() > 0);

        $tables_stmt2 = $conn->query("SHOW TABLES LIKE 'pr_tbl_payroll_runs'");
        $has_runs = ($tables_stmt2 && $tables_stmt2->rowCount() > 0);

        $pay_history = [];
        if ($has_run_details && $has_runs) {
            $history_stmt = $conn->prepare("SELECT
                                                prd.detail_id,
                                                prd.run_id,
                                                prd.gross_pay,
                                                prd.total_deductions,
                                                prd.total_employer_share,
                                                prd.net_pay,
                                                prd.payment_status,
                                                prd.payment_method,
                                                prd.payment_reference,
                                                prd.created_at AS detail_created_at,
                                                pr.run_name,
                                                pr.run_type,
                                                pr.pay_period_start,
                                                pr.pay_period_end,
                                                pr.payment_date,
                                                pr.run_status
                                           FROM pr_tbl_payroll_run_details prd
                                           LEFT JOIN pr_tbl_payroll_runs pr ON prd.run_id = pr.run_id
                                           WHERE prd.personnel_id = :personnel_id_str
                                              OR prd.personnel_id = :personnel_id_num
                                           ORDER BY pr.pay_period_end DESC, prd.created_at DESC");
            $history_stmt->execute([
                ':personnel_id_str' => (string)$personnel_id,
                ':personnel_id_num' => (int)$personnel_id
            ]);
            $pay_history = $history_stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        error_log('Error loading payroll pay history page: ' . $e->getMessage());
        ?>
        <script>
        alert('An error occurred while loading pay history.');
        window.history.back();
        </script>
        <?php
        exit();
    }
    ?>

    <div class="breadcrumb-holder">
      <div class="container-fluid">
        <ul class="breadcrumb">
          <li style="color: blue"><strong style="margin-right: 4px;"><?php echo htmlspecialchars($schoolName); ?> | </strong></li>
          <li class="breadcrumb-item"><a href="home.php">Home</a></li>
          <?php if($session_access == 'Admin') { ?>
          <li class="breadcrumb-item"><a href="list_personnel.php?dept=<?php echo urlencode($get_dept); ?>">List of Personnel</a></li>
          <?php } ?>
          <li class="breadcrumb-item active">Pay History</li>
        </ul>
      </div>
    </div>

    <section class="mt-30px mb-30px">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12 col-md-12">

            <div id="new-updates" class="card">
              <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                  <i class="fa fa-user"></i>
                  <?php
                    $full_name = htmlspecialchars($staff_row['fname']) . ' ' .
                                 substr(htmlspecialchars($staff_row['mname']), 0, 1) . '. ' .
                                 htmlspecialchars($staff_row['lname']);

                    if ($staff_row['suffix'] != '-' && !empty($staff_row['suffix'])) {
                        $full_name .= ' ' . htmlspecialchars($staff_row['suffix']);
                    }

                    echo $full_name;
                  ?>
                  <small class="ml-2" style="color: #e6f4ea;">
                    (<?php echo htmlspecialchars($shift_row['shift_name']); ?> - <?php echo htmlspecialchars($shift_row['type']); ?>)
                  </small>
                </h5>
              </div>

              <div id="updates-boxKinder" class="card-body">

                <div class="col-lg-12 mt-2 mb-2">
                  <a class="btn btn-secondary" href="../list_personnel_individual_details.php?dept=<?php echo urlencode($get_dept); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>"> HRIS PROFILE</a>
                  <a class="btn btn-secondary" href="list_personnel_income.php?dept=<?php echo urlencode($get_dept); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>"> INCOME</a>
                  <a class="btn btn-secondary" href="list_personnel_deductions.php?dept=<?php echo urlencode($get_dept); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>"> DEDUCTIONS</a>
                  <a class="btn btn-primary" style="color: white; font-weight: bold;" href="list_personnel_pay_history.php?dept=<?php echo urlencode($get_dept); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>"> PAY HISTORY</a>
                </div>

                <div class="col-lg-12 mt-4 mb-4">

                  <?php if (!$has_run_details || !$has_runs) { ?>
                  <div class="alert alert-warning" role="alert">
                    <strong><i class="fa fa-exclamation-triangle"></i> Payroll tables not ready.</strong><br />
                    Pay History requires <code>pr_tbl_payroll_run_details</code> and <code>pr_tbl_payroll_runs</code> tables.
                  </div>
                  <?php } ?>

                  <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="payHistoryTable" style="width:100%">
                      <thead>
                        <tr>
                          <th>Run</th>
                          <th>Pay Period</th>
                          <th>Gross</th>
                          <th>Deductions</th>
                          <th>Net Pay</th>
                          <th>Payment Date</th>
                          <th>Payment Status</th>
                          <th>Run Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (count($pay_history) > 0) { ?>
                            <?php foreach ($pay_history as $row) { ?>
                            <tr>
                              <td>
                                <strong><?php echo htmlspecialchars($row['run_name'] ?? 'Payroll Run #' . ($row['run_id'] ?? '-')); ?></strong><br />
                                <small>Type: <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $row['run_type'] ?? 'regular'))); ?></small>
                              </td>
                              <td>
                                <?php
                                  $period_start = !empty($row['pay_period_start']) ? date('M d, Y', strtotime($row['pay_period_start'])) : '-';
                                  $period_end = !empty($row['pay_period_end']) ? date('M d, Y', strtotime($row['pay_period_end'])) : '-';
                                  echo htmlspecialchars($period_start . ' to ' . $period_end);
                                ?>
                              </td>
                              <td class="text-right">PHP <?php echo number_format((float)$row['gross_pay'], 2); ?></td>
                              <td class="text-right">PHP <?php echo number_format((float)$row['total_deductions'], 2); ?></td>
                              <td class="text-right"><strong>PHP <?php echo number_format((float)$row['net_pay'], 2); ?></strong></td>
                              <td>
                                <?php echo !empty($row['payment_date']) ? htmlspecialchars(date('M d, Y', strtotime($row['payment_date']))) : '-'; ?>
                              </td>
                              <td><?php echo htmlspecialchars(ucfirst($row['payment_status'] ?? 'pending')); ?></td>
                              <td><?php echo htmlspecialchars(ucfirst($row['run_status'] ?? 'draft')); ?></td>
                            </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                              <td colspan="8" class="text-center text-muted">No pay history found for this employee.</td>
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
      </div>
    </section>

    <?php include('footer.php'); ?>

    </div>

    <?php include('scripts_files.php'); ?>

    <script>
      $(document).ready(function() {
        if ($('#payHistoryTable').length) {
          $('#payHistoryTable').DataTable({
            order: [[1, 'desc']],
            pageLength: 25
          });
        }
      });
    </script>

  </body>
</html>
