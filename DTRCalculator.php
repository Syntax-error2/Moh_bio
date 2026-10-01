<?php
// DTRCalculator.php
// Centralized engine for bulk-fetching DTR logs and calculating tardiness/undertime

class DTRCalculator {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    /**
     * Fetch all relevant logs and schedules for a list of personnel IDs for a specific month.
     * This avoids the N+1 query problem by doing bulk selects.
     */
    public function getMonthlySummary($personnel_id, $biometric_id, $do_id, $shift_id, $year, $month) {
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $summary = [
            'daily' => [],
            'grand_total_late_mins' => 0,
            'total_absent_days' => 0
        ];

        // 1. Bulk Fetch Schedules
        $sched_stmt = $this->conn->prepare("SELECT day, am_IN, am_OUT, pm_IN, pm_OUT FROM time_schedules WHERE do_id = ? AND shift_id = ?");
        $sched_stmt->execute([$do_id, $shift_id]);
        $schedules = [];
        while ($row = $sched_stmt->fetch(PDO::FETCH_ASSOC)) {
            $schedules[$row['day']] = $row;
        }

        // 2. Bulk Fetch Bio Logs
        $startDate = sprintf("%04d-%02d-01", $year, $month);
        $endDate = sprintf("%04d-%02d-%02d", $year, $month, $daysInMonth);
        
        $bio_stmt = $this->conn->prepare("SELECT log_date, time_in, time_out FROM bio_dtr WHERE personnel_id = ? AND log_date BETWEEN ? AND ? ORDER BY time_in ASC");
        $bio_stmt->execute([$personnel_id, $startDate, $endDate]);
        $bioLogs = [];
        while ($row = $bio_stmt->fetch(PDO::FETCH_ASSOC)) {
            $bioLogs[$row['log_date']][] = $row;
        }
        // 2.5. Bulk Fetch Approved Leaves
        $leave_stmt = $this->conn->prepare("SELECT logDate FROM personnel_logs WHERE RFTag_id = (SELECT RFTag_id FROM personnels WHERE personnel_id = ?) AND logDate BETWEEN ? AND ? AND (remarks='Vacation Leave' OR remarks='Sick Leave' OR travel_leave_code != '')");
        $leave_stmt->execute([$personnel_id, $startDate, $endDate]);
        $leaves = [];
        while ($row = $leave_stmt->fetch(PDO::FETCH_ASSOC)) {
            $leaves[$row['logDate']] = true;
        }
        // 3. Bulk Fetch Holidays/Activity Calendar
        $hol_stmt = $this->conn->prepare("SELECT completeDate, event_title FROM activity_calendar WHERE status='Approved' AND completeDate BETWEEN ? AND ?");
        $hol_stmt->execute([$startDate, $endDate]);
        $holidays = [];
        while ($row = $hol_stmt->fetch(PDO::FETCH_ASSOC)) {
            $holidays[$row['completeDate']] = $row['event_title'];
        }

        // 4. Calculate Days
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = sprintf("%04d-%02d-%02d", $year, $month, $i);
            $dayName = date('l', strtotime($date));
            $isHoliday = isset($holidays[$date]);
            $isOnLeave = isset($leaves[$date]);
            
            $dayData = [
                'date' => $date,
                'day_name' => $dayName,
                'is_holiday' => $isHoliday,
                'is_on_leave' => $isOnLeave,
                'schedule' => $schedules[$dayName] ?? null,
                'logs' => $bioLogs[$date] ?? [],
                'late_mins' => 0
            ];

            if ($dayData['schedule'] && !$isOnLeave) {
                $sched = $dayData['schedule'];
                $logs = $dayData['logs'];
                
                if (count($logs) == 0 && !$isHoliday) {
                    $summary['total_absent_days']++;
                } else {
                    // AM IN
                    if (count($logs) > 0 && !empty($sched['am_IN'])) {
                        $actual_in = strtotime($logs[0]['time_in']);
                        $sched_in = strtotime($sched['am_IN']);
                        $late = ($actual_in - $sched_in) / 60;
                        if ($late > 15) {
                            $dayData['late_mins'] += $late;
                        }
                    }
                    // PM IN
                    if (count($logs) > 1 && !empty($sched['pm_IN'])) {
                        $actual_pm_in = strtotime($logs[1]['time_in']);
                        $sched_pm_in = strtotime($sched['pm_IN']);
                        $late = ($actual_pm_in - $sched_pm_in) / 60;
                        if ($late > 15) {
                            $dayData['late_mins'] += $late;
                        }
                    }
                }
            }

            $summary['grand_total_late_mins'] += $dayData['late_mins'];
            $summary['daily'][$date] = $dayData;
        }

        return $summary;
    }
}
?>
