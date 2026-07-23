    <?php

    include('session.php');
    
    
    if(isset($_POST['updateAccountInfo'])){
    
    $fname=strtoupper($_POST['fname']);
    $lname=strtoupper($_POST['lname']);
    $email=$_POST['email'];
    $contact_num=$_POST['contact_num'];
 
    $safe_vPass=md5($_POST['verify_pass']);
    $saltV="a1Bz20ydqelm8m1wql";
    $final_verify_pass=$saltV.$safe_vPass;
        
    if($check_pass===$final_verify_pass){
        
        $updateUser = 'UPDATE useraccount SET fname = :fname, lname = :lname, email = :email, contact_num = :contact_num WHERE user_id = :user_id';
        $conn->prepare($updateUser)->execute(['fname' => $fname, 'lname' => $lname, 'email' => $email, 'contact_num' => $contact_num, 'user_id' => $session_id]);
        
        $conn=null;
    ?>
    
    <script>
    window.alert('Data successfully updated...');
    window.location='user_profile.php';
    </script>
    
    <?php }else{ ?>
    
    <script>
    window.alert('Verification password did not match...');
    window.location='user_profile.php';
    </script>
    
    <?php } } ?>
    
    
    
    <?php 
    
    if(isset($_POST['updateAccountUname'])){
    $username=$_POST['username'];
    
    $safe_vPass=md5($_POST['verify_pass']);
    $saltV="a1Bz20ydqelm8m1wql";
    $final_verify_pass=$saltV.$safe_vPass;
        
    if($check_pass===$final_verify_pass){
        
        $updateUser = 'UPDATE useraccount SET username = :username WHERE user_id = :user_id';
        $conn->prepare($updateUser)->execute(['username' => $username, 'user_id' => $session_id]);
        
        $conn=null;
    ?>
    
    <script>
    window.alert('Username successfully updated...');
    window.location='user_profile.php';
    </script>
    
    <?php }else{ ?>
    
    <script>
    window.alert('Verification password did not match...');
    window.location='user_profile.php';
    </script>
    
    <?php } } ?>
    
    
    <?php 
    
    if(isset($_POST['updateAccountSecurity'])){
   
    $new_pass=$_POST['new_pass'];
    $new_pass2=$_POST['new_pass2'];
    
    $safe_vPass=md5($_POST['verify_pass']);
    $saltV="a1Bz20ydqelm8m1wql";
    $final_verify_pass=$saltV.$safe_vPass;
    
    
    $safe_newPass=md5($new_pass);
    $saltNew="a1Bz20ydqelm8m1wql";
    $final_newPass=$saltNew.$safe_newPass;
    
    
    if($check_pass===$final_verify_pass){
    
    if($new_pass===$new_pass2){
        
        $updateUser = 'UPDATE useraccount SET password = :password WHERE user_id = :user_id';
        $conn->prepare($updateUser)->execute(['password' => $final_newPass, 'user_id' => $session_id]);
        $conn=null;
        
    ?>
    
    <script>
    window.alert('Password successfully updated...');
    window.location='user_profile.php';
    </script>
    
    <?php }else{ ?>
    
    <script>
    window.alert('New password and retyped password did not match...');
    window.location='user_profile.php';
    </script>
    
    <?php } }else{ ?>
    
    <script>
    window.alert('Verification password did not match...');
    window.location='user_profile.php';
    </script>
    
    <?php } } ?>
    
    
    
    <?php if(isset($_POST['update_data_src_sy'])) {

        $selectedSY = $_POST['data_src_sy'] ?? $activeSchoolYear;
        $submittedTerm = $_POST['data_src_sem'] ?? '';
        if (!in_array($submittedTerm, valid_term_inputs($selectedSY), true)) {
            $submittedTerm = $activeSemester;
        }

        $selectedSem = term_to_legacy_semester($submittedTerm, $selectedSY);
        if ($selectedSem === null) {
            $selectedSem = $activeSemester;
        }

        $selectedTermDisplay = term_to_trimester_label($selectedSem, $selectedSY);
    
        $conn->query("UPDATE useraccount SET selected_SY='$selectedSY', selected_sem='$selectedSem' WHERE user_id='$session_id'");
    ?>
    
        <script>
            window.alert('SY <?php echo $selectedSY; ?> | <?php echo $selectedTermDisplay; ?> has been activated as datasource...');
        window.location='home.php';
        </script>
                
    <?php } ?>
    
    
    <?php if(isset($_POST['reset_default'])) {

        $activeTermDisplay = term_to_trimester_label($activeSemester, $activeSchoolYear);
    
    $conn->query("UPDATE useraccount SET selected_SY='$activeSchoolYear', selected_sem='$activeSemester' WHERE user_id='$session_id'");
     ?>
    
        <script>
            window.alert('Reset success to default datasource SY <?php echo $activeSchoolYear; ?> | <?php echo $activeTermDisplay; ?>.');
        window.location='home.php';
        </script>
                
    <?php } ?>
