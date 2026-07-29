<?php
include('session.php');
include('lib/audit_logger.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($session_access !== 'Admin') {
        die("Unauthorized");
    }

    $user_id = $_POST['user_id'] ?? '';
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $username = $_POST['username'];
    $access = $_POST['access']; // Admin or Staff
    $password = $_POST['password'];
    
    // Modules
    if ($access === 'Admin') {
        $module_access = 'hris,payroll';
    } else {
        $modules = $_POST['modules'] ?? [];
        $module_access = implode(',', $modules);
    }
    
    // Fallback school_id, personnel_id, do_id for new users if not strictly linked to personnel table yet
    $school_id = '1';
    $personnel_id = '0';
    $do_id = '1';
    $email = '';

    try {
        if (empty($user_id)) {
            // INSERT NEW USER
            $query = "INSERT INTO useraccount (school_id, personnel_id, fname, lname, email, username, password, access, module_access, do_id) 
                      VALUES (:school_id, :personnel_id, :fname, :lname, :email, :username, :password, :access, :module_access, :do_id)";
            $stmt = $conn->prepare($query);
            
            // Password Hashing (using legacy method present in account_signup.php - custom salt)
            $salt = "a1Bz20ydqelm8m1wql";
            $hashed_password = $salt . md5($password);
            
            $stmt->execute([
                'school_id' => $school_id,
                'personnel_id' => $personnel_id,
                'fname' => $fname,
                'lname' => $lname,
                'email' => $email,
                'username' => $username,
                'password' => $hashed_password,
                'access' => $access,
                'module_access' => $module_access,
                'do_id' => $do_id
            ]);
            
            log_audit_action($conn, $session_id, $name, 'Create User', 'HRIS', "Created $access account for $fname $lname ($username) with modules: $module_access");
            
        } else {
            // UPDATE EXISTING USER
            if (!empty($password)) {
                $salt = "a1Bz20ydqelm8m1wql";
                $hashed_password = $salt . md5($password);
                $query = "UPDATE useraccount SET fname=:fname, lname=:lname, username=:username, password=:password, access=:access, module_access=:module_access WHERE user_id=:user_id";
                $stmt = $conn->prepare($query);
                $stmt->execute([
                    'fname' => $fname,
                    'lname' => $lname,
                    'username' => $username,
                    'password' => $hashed_password,
                    'access' => $access,
                    'module_access' => $module_access,
                    'user_id' => $user_id
                ]);
            } else {
                $query = "UPDATE useraccount SET fname=:fname, lname=:lname, username=:username, access=:access, module_access=:module_access WHERE user_id=:user_id";
                $stmt = $conn->prepare($query);
                $stmt->execute([
                    'fname' => $fname,
                    'lname' => $lname,
                    'username' => $username,
                    'access' => $access,
                    'module_access' => $module_access,
                    'user_id' => $user_id
                ]);
            }
            
            log_audit_action($conn, $session_id, $name, 'Update User', 'HRIS', "Updated $access account for $fname $lname ($username) with modules: $module_access");
        }
        
        echo "<script>alert('User saved successfully!'); window.location.href='manage_users.php';</script>";
        
    } catch (PDOException $e) {
        error_log("Error saving user: " . $e->getMessage());
        echo "<script>alert('Error saving user. Please try again.'); window.location.href='manage_users.php';</script>";
    }
}
?>
