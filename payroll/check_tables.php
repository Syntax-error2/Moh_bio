<?php
require 'dbcon.php';

echo "=== PR_TBL_INCOME TABLE STRUCTURE ===\n";
try {
    $result = $conn->query('DESCRIBE pr_tbl_income');
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . "\n";
    }
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== PR_TBL_DEDUCTIONS TABLE STRUCTURE ===\n";
try {
    $result = $conn->query('DESCRIBE pr_tbl_deductions');
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . "\n";
    }
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== PR_TBL_PERSONNEL_INCOME TABLE STRUCTURE ===\n";
try {
    $result = $conn->query('DESCRIBE pr_tbl_personnel_income');
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . "\n";
    }
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== PR_TBL_PERSONNEL_DEDUCTIONS TABLE STRUCTURE ===\n";
try {
    $result = $conn->query('DESCRIBE pr_tbl_personnel_deductions');
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . "\n";
    }
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
