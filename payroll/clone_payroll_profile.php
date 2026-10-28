<?php
include('session.php');

if (!isset($_GET['profile_id'])) {
    header('Location: list_payroll_profiles.php?errorMsg=' . urlencode('Missing profile ID'));
    exit;
}

$source_id = (int)$_GET['profile_id'];
$user_id = $_SESSION['id'] ?? 0;

try {
    $conn->beginTransaction();

    // 1. Get Source Profile
    $stmt = $conn->prepare("SELECT * FROM pr_tbl_payroll_profiles WHERE profile_id = :id");
    $stmt->execute([':id' => $source_id]);
    $source = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$source) {
        throw new Exception("Source profile not found.");
    }

    // 2. Clone Profile (setting is_default to 0 to prevent multiple defaults)
    $stmt = $conn->prepare("
        INSERT INTO pr_tbl_payroll_profiles (
            profile_name, profile_description, profile_type, pay_frequency, is_active, is_default, created_by, created_at, updated_at
        ) VALUES (
            :name, :desc, :type, :freq, :active, 0, :user, NOW(), NOW()
        )
    ");
    $stmt->execute([
        ':name' => $source['profile_name'] . ' (Copy)',
        ':desc' => $source['profile_description'],
        ':type' => $source['profile_type'],
        ':freq' => $source['pay_frequency'],
        ':active' => 0, // Cloned profiles are inactive by default to avoid accidental use
        ':user' => $user_id
    ]);
    
    $new_profile_id = $conn->lastInsertId();

    // 3. Clone Income Items
    $stmt = $conn->prepare("SELECT * FROM pr_tbl_payroll_profile_income WHERE profile_id = :id");
    $stmt->execute([':id' => $source_id]);
    $incomes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($incomes)) {
        $insert_inc = $conn->prepare("
            INSERT INTO pr_tbl_payroll_profile_income (
                profile_id, income_id, default_amount, amount_calculation, calculation_base, calculation_value, is_mandatory, display_order, notes, created_at
            ) VALUES (
                :pid, :iid, :def_amt, :calc, :base, :val, :man, :ord, :notes, NOW()
            )
        ");
        foreach ($incomes as $inc) {
            $insert_inc->execute([
                ':pid' => $new_profile_id,
                ':iid' => $inc['income_id'],
                ':def_amt' => $inc['default_amount'],
                ':calc' => $inc['amount_calculation'],
                ':base' => $inc['calculation_base'],
                ':val' => $inc['calculation_value'],
                ':man' => $inc['is_mandatory'],
                ':ord' => $inc['display_order'],
                ':notes' => $inc['notes']
            ]);
        }
    }

    // 4. Clone Deduction Items
    $stmt = $conn->prepare("SELECT * FROM pr_tbl_payroll_profile_deductions WHERE profile_id = :id");
    $stmt->execute([':id' => $source_id]);
    $deductions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($deductions)) {
        $insert_ded = $conn->prepare("
            INSERT INTO pr_tbl_payroll_profile_deductions (
                profile_id, deduction_id, default_employee_amt, default_employer_amt, amount_calculation, calculation_base, calculation_value, is_mandatory, display_order, notes, created_at
            ) VALUES (
                :pid, :did, :emp_amt, :empr_amt, :calc, :base, :val, :man, :ord, :notes, NOW()
            )
        ");
        foreach ($deductions as $ded) {
            $insert_ded->execute([
                ':pid' => $new_profile_id,
                ':did' => $ded['deduction_id'],
                ':emp_amt' => $ded['default_employee_amt'],
                ':empr_amt' => $ded['default_employer_amt'],
                ':calc' => $ded['amount_calculation'],
                ':base' => $ded['calculation_base'],
                ':val' => $ded['calculation_value'],
                ':man' => $ded['is_mandatory'],
                ':ord' => $ded['display_order'],
                ':notes' => $ded['notes']
            ]);
        }
    }

    // 5. Clone Filters
    $stmt = $conn->prepare("SELECT * FROM pr_tbl_payroll_profile_filters WHERE profile_id = :id");
    $stmt->execute([':id' => $source_id]);
    $filters = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($filters)) {
        $insert_fil = $conn->prepare("
            INSERT INTO pr_tbl_payroll_profile_filters (
                profile_id, filter_type, filter_value, created_at
            ) VALUES (
                :pid, :type, :val, NOW()
            )
        ");
        foreach ($filters as $fil) {
            $insert_fil->execute([
                ':pid' => $new_profile_id,
                ':type' => $fil['filter_type'],
                ':val' => $fil['filter_value']
            ]);
        }
    }

    $conn->commit();
    header('Location: list_payroll_profiles.php?successMsg=' . urlencode('Profile cloned successfully.'));

} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    header('Location: list_payroll_profiles.php?errorMsg=' . urlencode('Failed to clone profile: ' . $e->getMessage()));
}
exit;
?>
