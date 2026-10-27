<?php 
// Ensure we are working in the script's directory
chdir(__DIR__);

include('dbcon3.php');

$tables = array();

$query = mysqli_query($conn3, 'SHOW TABLES');

while($row = mysqli_fetch_row($query)){
    $tables[] = $row[0];
}

$result = "";
foreach($tables as $table){
    $query = mysqli_query($conn3, 'SELECT * FROM '.$table);
    $num_fields = mysqli_num_fields($query);
    
    $result .= 'DROP TABLE IF EXISTS '.$table.';';
    $row2 = mysqli_fetch_row(mysqli_query($conn3, 'SHOW CREATE TABLE '.$table));
    $result .= "\n\n".$row2[1].";\n\n";
    
    for ($i = 0; $i < $num_fields; $i++) {
        while($row = mysqli_fetch_row($query)){
            $result .= 'INSERT INTO '.$table.' VALUES(';
            for($j=0; $j<$num_fields; $j++){
                $row[$j] = addslashes($row[$j]);
                $row[$j] = str_replace("\n","\\n",$row[$j]);
                if(isset($row[$j])){
                   $result .= '"'.$row[$j].'"' ; 
                }else{ 
                    $result .= '""';
                }
                if($j<($num_fields-1)){ 
                    $result .= ',';
                }
            }
            $result .= ");\n";
        }
    }
    $result .="\n\n";
}

//Create Folder
$folder = 'Backup_Data/';
if (!is_dir($folder)){
    mkdir($folder, 0777, true);
    chmod($folder, 0777);
}

// Add a timestamp so that automated backups don't overwrite the same day's manual backup instantly, though keeping them separate is fine
$date = date('m-d-Y_His'); 
$filename = $folder."auto_backup_Data_".$date; 
$filing="auto_backup_Data_".$date;
$handle = fopen($filename.'.sql','w+');
if ($handle) {
    fwrite($handle,$result);
    fclose($handle);

    date_default_timezone_set('Asia/Manila');
    $new = date('F j, Y g:i:a');
    
    // Auto increment ID isn't provided via POST, so we pass empty for ID or just omit it if it's auto_increment
    mysqli_query($conn3,"INSERT INTO backup_dbname(Name, Date) VALUES('$filing', '$new')");
    
    echo "Backup completed successfully: $filing.sql\n";
} else {
    echo "Error: Could not write to backup file $filename.sql\n";
}
?>
