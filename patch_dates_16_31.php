<?php
$files = [
    'print_monthly_preview_csf48_16_31.php',
    'print_monthly_preview_csf48_16_31_2.php',
    'print_monthly_preview_csf48_16_31_all.php'
];

foreach ($files as $f) {
    if (!file_exists($f)) continue;
    $c = file_get_contents($f);
    
    // Pattern for 16_31 where if($d<10) was removed
    // We expect: $logDateCtr=$_GET['dateFrom'].'-'.$d; $logDateLog = date('m/d/Y', strtotime($logDateCtr));
    // OR $logDateCtr=$selectedMM.'/'.$d.'/'.$selectedYYYY;
    
    // Let's just find exactly that line
    $pattern1 = "/\\\$logDateCtr=\\\$_GET\['dateFrom'\]\.'-'\.\\\$d;\\s*\\\$logDateLog = date\('m\/d\/Y', strtotime\(\\\$logDateCtr\)\);/";
    $pattern2 = "/\\\$logDateCtr=\\\$selectedMM\.'\/'\.\\\$d\.'\/'\.\\\$selectedYYYY;/";
    $pattern3 = "/\\\$logDateCtr=\\\$_GET\['dateFrom'\]\.'-'\.\\\$d;/";

    $replacement = "\$d_str=\$d;\n\$logDateCtr=\$selectedYYYY.'-'.\$selectedMM.'-'.\$d_str;\n\$logDateLog=\$selectedMM.'/'.\$d_str.'/'.\$selectedYYYY;";
    
    $c = preg_replace($pattern1, $replacement, $c);
    $c = preg_replace($pattern2, $replacement, $c);
    
    $c = str_replace("logDate='\$logDateCtr'", "logDate='\$logDateLog'", $c);
    $c = str_replace("'logDate' => \$logDateCtr", "'logDate' => \$logDateLog", $c);
    
    $c = preg_replace("/echo substr\(\\\$logDateCtr, \d+, \d+\);/", "echo \$d_str;", $c);
    
    file_put_contents($f, $c);
    echo "Patched $f\n";
}
echo "Done patching 16_31 logic.\n";
?>
