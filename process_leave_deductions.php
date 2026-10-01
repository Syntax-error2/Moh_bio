<?php
// DISABLED BY USER REQUEST: No auto-deductions from DTR absences.
exit;
?>
<?php
include('session.php');
include('dbcon.php');
include('leave_deduction_engine.php');

if(isset($_POST['processLeaveDeductions'])) {
    $RFTag_id = $_POST['RFTag_id'];
    $dateFrom = $_POST['dateFrom']; // Format YYYY-MM
    $dept = isset($_GET['dept']) ? $_GET['dept'] : '';
    
    if(empty($dateFrom)) {
        echo "<script>alert('Please select a month first!'); window.history.back();</script>";
        exit;
    }

    $selectedYYYY = (int)substr($dateFrom, 0, 4);
    $selectedMM = (int)substr($dateFrom, 5, 2);
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $selectedMM, $selectedYYYY);

    $studData_query = $conn->prepare("SELECT * FROM personnels WHERE RFTag_id = :rftag");
    $studData_query->execute(['rftag' => $RFTag_id]);
    $studData_row = $studData_query->fetch();
    $personnel_id = $studData_row['personnel_id'];
    $biometric_id = $studData_row['biometric_id'];

    $grandTotalLateMin = 0;
    $totalAbsentDays = 0;

    for ($i = 1; $i <= $daysInMonth; $i++) {
        $dayDate = sprintf("%04d-%02d-%02d", $selectedYYYY, $selectedMM, $i);
        $dayName = date('l', strtotime($dayDate));

        // Skip weekends for basic calculation unless schedule exists
        $sched_query = $conn->prepare("SELECT am_IN, pm_IN FROM time_schedules WHERE do_id=:do_id AND shift_id=:shift_id AND day=:day");
        $sched_query->execute([
            'do_id' => $studData_row['do_id'], 
            'shift_id' => $studData_row['shift_id'], 
            'day' => $dayName
        ]);
        $sq_row = $sched_query->fetch();
        
        if (!$sq_row || empty($sq_row['am_IN'])) {
            continue; // No schedule for this day
        }

        // Check if there is an approved leave for this day (from personnel_logs or leave_applicants)
        // If there's an approved leave, they are not absent or late, because the leave was ALREADY deducted when filed!
        $leave_query = $conn->prepare("SELECT remarks FROM personnel_logs WHERE RFTag_id=:rftag AND logDate=:ldate AND (remarks='Vacation Leave' OR remarks='Sick Leave' OR travel_leave_code != '')");
        $leave_query->execute(['rftag' => $RFTag_id, 'ldate' => $dayDate]);
        if ($leave_query->rowCount() > 0) {
            continue; // Leave already filed and deducted
        }

        // Get actual logs from bio_dtr (stitching logic similar to preview)
        $am_in_log = null;
        $pm_in_log = null;

        $bio_query = $conn->prepare("SELECT time_in, time_out FROM bio_dtr WHERE personnel_id=:pid AND log_date=:ldate ORDER BY time_in ASC");
        $bio_query->execute(['pid' => $personnel_id, 'ldate' => $dayDate]);
        $logs = $bio_query->fetchAll(PDO::FETCH_ASSOC);

        if (count($logs) == 0) {
            // Absent
            // Check holiday first (simplified)
            $hol_q = $conn->prepare("SELECT activity_id FROM activity_calendar WHERE completeDate=:ac_date AND status='Approved'");
            $hol_q->execute(['ac_date' => $dayDate]);
            if ($hol_q->rowCount() == 0) {
                $totalAbsentDays += 1;
            }
            continue;
        }

        // For simplicity in this LGU engine, we take the earliest log as AM IN, and if they have an afternoon schedule, we find PM IN.
        // Let's implement the EXACT logic from print_monthly_preview for AM IN tardiness:
        $am_in_log = $logs[0]['time_in'];
        if ($am_in_log) {
            $time_am_in = strtotime(date("H:i:s", strtotime($am_in_log)));
            $time_sched_am_in = strtotime(date("H:i:s", strtotime($sq_row['am_IN'])));
            
            $late_mins = ($time_am_in - $time_sched_am_in) / 60;
            if ($late_mins > 15) { // 15 min grace period
                $grandTotalLateMin += $late_mins;
            }
        }

        // PM IN tardiness
        // If there are multiple logs, the one around noon is PM IN.
        if (count($logs) > 1 && !empty($sq_row['pm_IN'])) {
            $pm_in_log = $logs[1]['time_in']; // Second log is usually PM IN in bio_dtr
            $time_pm_in = strtotime(date("H:i:s", strtotime($pm_in_log)));
            $time_sched_pm_in = strtotime(date("H:i:s", strtotime($sq_row['pm_IN'])));
            
            $late_mins = ($time_pm_in - $time_sched_pm_in) / 60;
            if ($late_mins > 15) {
                $grandTotalLateMin += $late_mins;
            }
        }
    }

    // Now convert late minutes to equivalent days
    $equivalentDaysLate = LeaveDeductionEngine::calculateEquivalentDays($grandTotalLateMin);
    
    // Total equivalent days to deduct = absent days + equivalent days late
    $totalEquivalentDays = $totalAbsentDays + $equivalentDaysLate;

    if ($totalEquivalentDays > 0) {
        $remarks = "Auto-deduction for $dateFrom: $totalAbsentDays Absent Days + $grandTotalLateMin mins late ($equivalentDaysLate days)";
        LeaveDeductionEngine::deductTardinessCascade($conn, $personnel_id, $totalEquivalentDays, $remarks);
        
        $msg = "Deducted $totalEquivalentDays equivalent days from Leave Balances for $dateFrom.";
    } else {
        $msg = "No tardiness or unexcused absences found for $dateFrom. Balances unchanged.";
    }

    echo "<script>
        alert('$msg');
        window.location = 'list_personnel.php?dept=$dept';
    </script>";
}
?>
