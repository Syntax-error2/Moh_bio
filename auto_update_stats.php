<?php
// Note: This script assumes the database connection ($conn) is already established
// since it is included inside session.php.

$current_year = (int)date('Y');
$tracker_file = __DIR__ . '/last_update_year.txt';

// Read the last updated year
$last_update_year = 0;
if (file_exists($tracker_file)) {
    $last_update_year = (int)trim(file_get_contents($tracker_file));
}

// If the stored year is less than current year, execute the update
if ($last_update_year < $current_year) {
    try {
        $stmt = $conn->query("SELECT personnel_id, bdMM, bdDD, bdYYYY, appointment_date FROM personnels");
        $personnels = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $update_stmt = $conn->prepare("UPDATE personnels SET age = :age, num_of_yrs = :num_of_yrs WHERE personnel_id = :personnel_id");

        foreach ($personnels as $row) {
            $age = 0;
            $num_of_yrs = 0;
            
            // Calculate Age
            $bdMM = trim((string)$row['bdMM']);
            $bdDD = trim((string)$row['bdDD']);
            $bdYYYY = trim((string)$row['bdYYYY']);
            if (!empty($bdMM) && !empty($bdDD) && !empty($bdYYYY) && is_numeric($bdYYYY) && strlen($bdYYYY) == 4) {
                $dob_str = sprintf('%04d-%02d-%02d', (int)$bdYYYY, (int)$bdMM, (int)$bdDD);
                try {
                    $dob_dt = new DateTime($dob_str);
                    $now_dt = new DateTime();
                    $age = $dob_dt->diff($now_dt)->y;
                } catch (Exception $e) {
                    $age = 0;
                }
            }
            
            // Calculate Years in Service
            $app_date = trim((string)$row['appointment_date']);
            if (!empty($app_date) && $app_date !== '/  /' && $app_date !== '  /  /    ') {
                // Format is MM/DD/YYYY
                $parts = explode('/', $app_date);
                if (count($parts) === 3 && is_numeric(trim($parts[2])) && strlen(trim($parts[2])) == 4) {
                    $app_str = trim($parts[2]) . '-' . trim($parts[0]) . '-' . trim($parts[1]);
                    try {
                        $app_dt = new DateTime($app_str);
                        $now_dt = new DateTime();
                        $num_of_yrs = $app_dt->diff($now_dt)->y;
                    } catch (Exception $e) {
                        $num_of_yrs = 0;
                    }
                }
            }

            // Update record
            $update_stmt->execute([
                ':age' => $age,
                ':num_of_yrs' => $num_of_yrs,
                ':personnel_id' => $row['personnel_id']
            ]);
        }
        
        // Update the tracker file
        file_put_contents($tracker_file, $current_year);
    } catch (PDOException $e) {
        error_log("Error in auto_update_stats.php: " . $e->getMessage());
    }
}
?>
