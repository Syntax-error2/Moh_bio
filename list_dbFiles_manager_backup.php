<?php 

include('dbcon2.php');

$tables = [];

$query = $conn2->query('SHOW TABLES');

while($row = $query->fetch(PDO::FETCH_NUM)){
  $tables[] = $row[0];
}

$result = "";
foreach($tables as $table){
  $query = $conn2->query('SELECT * FROM `' . $table . '`');

  $result .= 'DROP TABLE IF EXISTS `' . $table . '`;';
  $row2 = $conn2->query('SHOW CREATE TABLE `' . $table . '`')->fetch(PDO::FETCH_NUM);
  $result .= "\n\n" . $row2[1] . ";\n\n";

  while($row = $query->fetch(PDO::FETCH_ASSOC)){
    $columns = array_keys($row);
    $values = [];

    foreach($columns as $column){
      if($row[$column] === null){
        $values[] = 'NULL';
      }else{
        $values[] = $conn2->quote(str_replace("\n", "\\n", $row[$column]));
      }
    }

    $result .= 'INSERT INTO `' . $table . '` (`' . implode('`,`', $columns) . '`) VALUES(' . implode(',', $values) . ");\n";
  }

  $result .= "\n\n";
}

//Create Folder
$folder = 'Backup_Data/';
if (!is_dir($folder))
mkdir($folder, 0777, true);
chmod($folder, 0777);

$date = date('m-d-Y'); 
$filename = $folder."backup_Data_".$date; 
$filing="backup_Data_".$date;
$handle = fopen($filename.'.sql','w+');
fwrite($handle,$result);
fclose($handle);

$backup = $conn2->query("SELECT * FROM backup_dbname ORDER BY ID DESC LIMIT 1");
$row = $backup->fetch(PDO::FETCH_ASSOC);
$Name = $row['Name'] ?? '';

if($filing == $Name){
  ?>
<script>
alert("Database Overwritten!");
window.location="list_dbFiles_manager.php";
</script>

<?php }else{

    $ID=$_POST['ID'];
    date_default_timezone_set('Asia/Manila');
    $new =date('F j, Y g:i:a  ');
    $insertStmt = $conn2->prepare("INSERT INTO backup_dbname(ID,Name,Date) VALUES(:ID, :Name, :Date)");
    $insertStmt->execute([
      ':ID' => $ID,
      ':Name' => $filing,
      ':Date' => $new
    ]);

?>
    
<script>
alert("Database Backed Up Successfully! The Database saved in Folder: Backup_Data");
window.location="list_dbFiles_manager.php";
</script>
    <?php
}

?>