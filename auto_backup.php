 
    
   <?php
   include('dbcon2.php');

   $table_name = "personnels";
   $backup_file  = "D:\\db_back_up/personnels.sql";

   $query = $conn2->query("SELECT * FROM `" . $table_name . "`");
   $rows = $query->fetchAll(PDO::FETCH_ASSOC);

   $result = "DROP TABLE IF EXISTS `" . $table_name . "`;\n";
   $createRow = $conn2->query("SHOW CREATE TABLE `" . $table_name . "`")->fetch(PDO::FETCH_NUM);
   $result .= $createRow[1] . ";\n\n";

   foreach($rows as $row){
      $columns = array_keys($row);
      $values = [];

      foreach($columns as $column){
         if($row[$column] === null){
            $values[] = 'NULL';
         }else{
            $values[] = $conn2->quote(str_replace("\n", "\\n", $row[$column]));
         }
      }

      $result .= 'INSERT INTO `" . $table_name . "` (`' . implode('`,`', $columns) . '`) VALUES(' . implode(',', $values) . ");\n";
   }

   $folder = dirname($backup_file);
   if (!is_dir($folder)) {
      mkdir($folder, 0777, true);
   }

   file_put_contents($backup_file, $result);
   echo "Backedup  data successfully\n";

   ?>
    
?>
