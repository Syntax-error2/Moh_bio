<?php
$files = glob("c:/xampp/htdocs/moh_bio/print_monthly_*.php");
foreach ($files as $file) {
    $content = file_get_contents($file);
    $original_content = $content;
    
    // Pattern to fix redundant $studData_query
    $pattern = '/\$studData_query\s*=\s*\$conn->query\(\s*["\']select\s*\*\s*FROM\s*personnels\s*WHERE\s*personnel_id=[\'"]\s*\.?\$printALL_row\[[\'"]?personnel_id[\'"]?\]\.?[\'"]\s*["\']\s*\);\s*\$studData_row\s*=\s*\$studData_query->fetch\(\);/i';
    
    $content = preg_replace($pattern, '$studData_row = $printALL_row;', $content);
    
    // Pattern to fix redundant shifts query
    $pattern2 = '/\$sched_query\s*=\s*\$conn->prepare\("SELECT am_IN FROM time_schedules WHERE do_id = :do_id AND shift_id = :shift_id AND day = :day"\);\s*\$sched_query->execute\(\[\'do_id\' => \$studData_row\[\'do_id\'\], \'shift_id\' => \$studData_row\[\'shift_id\'\], \'day\' => \$dayName\]\);/i';
    // Let's not touch sched_query via regex since we don't have a replacement ready here yet.
    
    if ($content !== $original_content) {
        file_put_contents($file, $content);
        echo "Optimized: " . basename($file) . "\n";
    }
}
echo "Done.\n";
?>
