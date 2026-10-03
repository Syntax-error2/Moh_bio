<?php
$files = glob("c:/xampp/htdocs/moh_bio/*modal*.php");
foreach ($files as $file) {
    if (strpos($file, 'print_monthly_') !== false) {
        $content = file_get_contents($file);
        $original_content = $content;
        
        // Change modal ID from RFTag_id to personnel_id
        $content = preg_replace('/id="([^"]+)<\?php echo \$staff_row\[\'RFTag_id\'\]; \?>"/i', 'id="$1<?php echo $personnel_id; ?>"', $content);
        
        // Change hidden input value to fallback
        $fallback = '<?php echo !empty($staff_row[\'RFTag_id\']) ? $staff_row[\'RFTag_id\'] : $staff_row[\'biometric_id\']; ?>';
        $content = preg_replace('/<input name="RFTag_id" value="<\?php echo \$staff_row\[\'RFTag_id\'\]; \?>" type="hidden" \/>/i', '<input name="RFTag_id" value="'.$fallback.'" type="hidden" />', $content);
        
        // Also check if it's passed in form action URL (like in LV_modal)
        $url_fallback = '<?php echo !empty($staff_row[\'RFTag_id\']) ? $staff_row[\'RFTag_id\'] : $staff_row[\'biometric_id\']; ?>';
        $content = preg_replace('/&RFTag_id=<\?php echo \$staff_row\[\'RFTag_id\'\]; \?>/i', '&RFTag_id='.$url_fallback, $content);
        
        if ($content !== $original_content) {
            file_put_contents($file, $content);
            echo "Updated: " . basename($file) . "\n";
        }
    }
}
echo "Done.\n";
?>
