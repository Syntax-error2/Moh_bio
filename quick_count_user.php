<?php
$selectedYYYY = $selectedYYYY ?? date('Y');
$late_in_hr = $late_in_hr ?? '00';
$late_in_min = $late_in_min ?? '00';
$uTime_in_hr = $uTime_in_hr ?? '00';
$uTime_in_min = $uTime_in_min ?? '00';
$total_yearly_present = $total_yearly_present ?? 0;
$total_yearly_present_AM = $total_yearly_present_AM ?? 0;
$total_yearly_present_PM = $total_yearly_present_PM ?? 0;
$total_yearly_late_min = $total_yearly_late_min ?? 0;
$total_yearly_late_num = $total_yearly_late_num ?? 0;
$total_yearly_late_AM = $total_yearly_late_AM ?? 0;
$total_yearly_late_PM = $total_yearly_late_PM ?? 0;
$total_yearly_uTime_min = $total_yearly_uTime_min ?? 0;
$total_yearly_uTime_num = $total_yearly_uTime_num ?? 0;
$total_yearly_uTime_AM = $total_yearly_uTime_AM ?? 0;
$total_yearly_uTime_PM = $total_yearly_uTime_PM ?? 0;
$total_yearly_absent = $total_yearly_absent ?? 0;
$total_yearly_absent_AM = $total_yearly_absent_AM ?? 0;
$total_yearly_absent_PM = $total_yearly_absent_PM ?? 0;
?>

 <section class="statistics">
         <div class="container-fluid">
          <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
            <h1 class="mb-2 mb-md-0">YEARLY SUMMARY</h1>
            <span class="badge badge-light" style="font-size: 0.9rem; padding: 8px 12px;">Year: <?php echo htmlspecialchars((string)$selectedYYYY, ENT_QUOTES, 'UTF-8'); ?></span>
          </div>

          <div class="row d-flex">
            <div class="col-xl-3 col-lg-6 mb-3">
              <div class="card user-activity h-100">
                <h2 class="display h4">PRESENT</h2>
                <div class="number"><?php echo $total_yearly_present; ?></div>
                <h3 class="h4 display">Total Days</h3>
                <div class="page-statistics d-flex justify-content-between">
                  <div class="page-statistics-left"><span>AM</span><strong><?php echo $total_yearly_present_AM; ?></strong></div>
                  <div class="page-statistics-right"><span>PM</span><strong><?php echo $total_yearly_present_PM; ?></strong></div>
                </div>
              </div>
            </div>

            <div class="col-xl-3 col-lg-6 mb-3">
              <div class="card user-activity h-100">
                <h2 class="display h4">LATE</h2>
                <div class="number"><?php echo $late_in_hr.':'.$late_in_min; ?></div>
                <h3 class="h4 display"><?php echo $total_yearly_late_min; ?> min | <?php echo $total_yearly_late_num; ?> record(s)</h3>
                <div class="page-statistics d-flex justify-content-between">
                  <div class="page-statistics-left"><span>AM</span><strong><?php echo $total_yearly_late_AM; ?></strong></div>
                  <div class="page-statistics-right"><span>PM</span><strong><?php echo $total_yearly_late_PM; ?></strong></div>
                </div>
              </div>
            </div>

            <div class="col-xl-3 col-lg-6 mb-3">
              <div class="card user-activity h-100">
                <h2 class="display h4">UNDERTIME</h2>
                <div class="number"><?php echo $uTime_in_hr.':'.$uTime_in_min; ?></div>
                <h3 class="h4 display"><?php echo $total_yearly_uTime_min; ?> min | <?php echo $total_yearly_uTime_num; ?> record(s)</h3>
                <div class="page-statistics d-flex justify-content-between">
                  <div class="page-statistics-left"><span>AM</span><strong><?php echo $total_yearly_uTime_AM; ?></strong></div>
                  <div class="page-statistics-right"><span>PM</span><strong><?php echo $total_yearly_uTime_PM; ?></strong></div>
                </div>
              </div>
            </div>

            <div class="col-xl-3 col-lg-6 mb-3">
              <div class="card user-activity h-100">
                <h2 class="display h4">ABSENT</h2>
                <div class="number"><?php echo $total_yearly_absent; ?></div>
                <h3 class="h4 display">Total Days</h3>
                <div class="page-statistics d-flex justify-content-between">
                  <div class="page-statistics-left"><span>AM</span><strong><?php echo $total_yearly_absent_AM; ?></strong></div>
                  <div class="page-statistics-right"><span>PM</span><strong><?php echo $total_yearly_absent_PM; ?></strong></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>