<?php
require 'dbcon.php';

$audit = [];

// Check core tables exist and row counts
$tables = [
    'pr_tbl_payroll_profiles',
    'pr_tbl_payroll_profile_income',
    'pr_tbl_payroll_profile_deductions',
    'pr_tbl_payroll_profile_filters',
    'pr_tbl_payroll_runs',
    'pr_tbl_payroll_run_details',
    'pr_tbl_payroll_run_income',
    'pr_tbl_payroll_run_deductions',
    'pr_tbl_payroll_snapshots',
    'pr_tbl_income',
    'pr_tbl_deductions',
    'pr_tbl_personnel_income',
    'pr_tbl_personnel_deductions'
];

foreach ($tables as $t) {
    try {
        $stmt = $conn->query("SELECT COUNT(*) FROM $t");
        if ($stmt) {
            $audit['tables'][$t] = $stmt->fetchColumn() . " rows";
        }
    } catch (Exception $e) {
        $audit['tables'][$t] = "MISSING OR ERROR";
    }
}

file_put_contents('audit_results.json', json_encode($audit, JSON_PRETTY_PRINT));
echo "Audit complete";
