<?php

/**
 * Leave Deduction Engine for LGU (Civil Service Commission Rules)
 * Handles calculation of Equivalent Days from tardiness minutes/hours
 * and performs the VL -> SL cascade deduction.
 */

class LeaveDeductionEngine {
    
    // Converts minutes into Equivalent Day fraction (based on CSC 8-hour workday = 480 mins)
    public static function calculateEquivalentDays($totalMinutes) {
        if ($totalMinutes <= 0) return 0.000;
        // CSC Rubric: 1 min = 0.002, 60 min = 0.125, 8 hours (480 mins) = 1.000
        return round($totalMinutes / 480, 3);
    }
    
    // Deducts the equivalent days from the employee's balances (Cascades VL first, then SL)
    // Returns an array of exactly what was deducted
    public static function deductTardinessCascade($conn, $personnel_id, $equivalentDays, $remarks) {
        if ($equivalentDays <= 0) return ['vl_deducted' => 0, 'sl_deducted' => 0, 'lwop' => 0];

        // Get current balances
        $stmt = $conn->prepare("SELECT vl_balance, sl_balance FROM personnels WHERE personnel_id = :pid");
        $stmt->execute(['pid' => $personnel_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $vl = (float)$row['vl_balance'];
        $sl = (float)$row['sl_balance'];
        
        $vl_deducted = 0.000;
        $sl_deducted = 0.000;
        $lwop = 0.000;
        $remainingToDeduct = $equivalentDays;
        
        // 1. Deduct from VL First
        if ($vl > 0) {
            if ($vl >= $remainingToDeduct) {
                $vl_deducted = $remainingToDeduct;
                $vl -= $remainingToDeduct;
                $remainingToDeduct = 0;
            } else {
                $vl_deducted = $vl;
                $remainingToDeduct -= $vl;
                $vl = 0;
            }
        }
        
        // 2. Cascade to SL if VL is empty
        if ($remainingToDeduct > 0 && $sl > 0) {
            if ($sl >= $remainingToDeduct) {
                $sl_deducted = $remainingToDeduct;
                $sl -= $remainingToDeduct;
                $remainingToDeduct = 0;
            } else {
                $sl_deducted = $sl;
                $remainingToDeduct -= $sl;
                $sl = 0;
            }
        }
        
        // 3. Anything left is LWOP (Leave Without Pay)
        if ($remainingToDeduct > 0) {
            $lwop = $remainingToDeduct;
        }
        
        // Update balances
        $update = $conn->prepare("UPDATE personnels SET vl_balance = :vl, sl_balance = :sl WHERE personnel_id = :pid");
        $update->execute(['vl' => $vl, 'sl' => $sl, 'pid' => $personnel_id]);
        
        // Log the transaction
        $log = $conn->prepare("INSERT INTO leave_credit_logs (personnel_id, log_type, vl_amount, sl_amount, vl_wout_pay, remarks) 
                               VALUES (:pid, 'Tardiness Deduction', :vl_deduct, :sl_deduct, :lwop, :remarks)");
        
        // Log amounts as negative for deductions
        $log->execute([
            'pid' => $personnel_id,
            'vl_deduct' => -$vl_deducted,
            'sl_deduct' => -$sl_deducted,
            'lwop' => $lwop,
            'remarks' => $remarks . ($lwop > 0 ? " (Plus $lwop LWOP)" : "")
        ]);
        
        return [
            'vl_deducted' => $vl_deducted,
            'sl_deducted' => $sl_deducted,
            'lwop' => $lwop,
            'new_vl' => $vl,
            'new_sl' => $sl
        ];
    }
    
    // Add monthly accrual of 1.25
    public static function accrueMonthlyLeaves($conn, $personnel_id, $remarks = "Monthly VL/SL Accrual") {
        $amount = 1.250;
        
        $update = $conn->prepare("UPDATE personnels SET vl_balance = vl_balance + :amt1, sl_balance = sl_balance + :amt2 WHERE personnel_id = :pid");
        $update->execute(['amt1' => $amount, 'amt2' => $amount, 'pid' => $personnel_id]);
        
        // Log transaction
        $log = $conn->prepare("INSERT INTO leave_credit_logs (personnel_id, log_type, vl_amount, sl_amount, remarks) 
                               VALUES (:pid, 'Accrual', :amt1, :amt2, :remarks)");
        $log->execute(['pid' => $personnel_id, 'amt1' => $amount, 'amt2' => $amount, 'remarks' => $remarks]);
    }
}

?>
