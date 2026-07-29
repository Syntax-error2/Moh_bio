<?php
include('session.php');
include('header.php');

// Restrict to Admin only
if ($session_access !== 'Admin') {
    echo "<script>alert('Access Denied. Admin only.'); window.location.href='home_user.php';</script>";
    exit();
}

$active_manage_users = "active";
?>
<body>
    <!-- Side Navbar -->
    <nav class="side-navbar">
      <div class="side-navbar-wrapper">
        <!-- Sidebar Header    -->
        <?php include('menu_sidebar.php'); ?>
      </div>
    </nav>
    <div class="page">
      <!-- navbar-->
      <header class="header">
        <?php include('navbar_header.php'); ?>
      </header>
      
      <!-- Breadcrumb-->
      <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active">Manage Users</li>
          </ul>
        </div>
      </div>

      <section class="mt-4">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h4>User Management</h4>
                  <button class="btn btn-primary" data-toggle="modal" data-target="#addUserModal">
                    <i class="fa fa-user-plus"></i> Add New User
                  </button>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-striped table-hover" id="usersTable">
                      <thead>
                        <tr>
                          <th>Name</th>
                          <th>Username</th>
                          <th>Role</th>
                          <th>Module Access</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $users = $conn->query("SELECT * FROM useraccount ORDER BY fname ASC")->fetchAll();
                        foreach ($users as $u) {
                            $modules = $u['access'] === 'Admin' ? '<span class="badge badge-success">All Access</span>' : '';
                            if ($u['access'] === 'Staff') {
                                $mods = explode(',', $u['module_access']);
                                foreach ($mods as $m) {
                                    if(trim($m) != '') {
                                        $modules .= '<span class="badge badge-info mr-1">'.strtoupper(trim($m)).'</span>';
                                    }
                                }
                            }
                            
                            echo "<tr>
                                <td>{$u['fname']} {$u['lname']}</td>
                                <td>{$u['username']}</td>
                                <td><strong>{$u['access']}</strong></td>
                                <td>{$modules}</td>
                                <td>
                                    <button class='btn btn-sm btn-outline-primary btn-edit-user' data-id='{$u['user_id']}' data-fname='{$u['fname']}' data-lname='{$u['lname']}' data-username='{$u['username']}' data-access='{$u['access']}' data-modules='{$u['module_access']}'>
                                        <i class='fa fa-edit'></i> Edit
                                    </button>
                                </td>
                            </tr>";
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      
      <?php include('footer.php'); ?>
    </div>
    
    <!-- User Modal -->
    <div id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true" class="modal fade text-left">
      <div role="document" class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 id="addUserModalLabel" class="modal-title">Manage User</h5>
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
          </div>
          <div class="modal-body">
            <form action="save_manage_user.php" method="post">
              <input type="hidden" name="user_id" id="modal_user_id" value="">
              
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="fname" id="modal_fname" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="lname" id="modal_lname" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" id="modal_username" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Password (Leave blank to keep current)</label>
                <input type="password" name="password" class="form-control">
              </div>
              
              <div class="form-group">
                <label>Role</label>
                <select name="access" id="modal_access" class="form-control" required onchange="toggleModules()">
                    <option value="Staff">Staff</option>
                    <option value="Admin">Admin</option>
                </select>
              </div>
              
              <div class="form-group" id="module_selection_div">
                <label>Module Access (For Staff)</label><br>
                <div class="custom-control custom-checkbox custom-control-inline">
                  <input id="mod_hris" type="checkbox" name="modules[]" value="hris" class="custom-control-input">
                  <label for="mod_hris" class="custom-control-label">HRIS Encoding</label>
                </div>
                <div class="custom-control custom-checkbox custom-control-inline">
                  <input id="mod_payroll" type="checkbox" name="modules[]" value="payroll" class="custom-control-input">
                  <label for="mod_payroll" class="custom-control-label">Payroll System</label>
                </div>
              </div>
              
              <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary btn-block">Save User</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Scripts -->
    <?php include('scripts_files.php'); ?>
    <script>
        $(document).ready(function() {
            $('#usersTable').DataTable();
            
            $('.btn-edit-user').click(function() {
                $('#modal_user_id').val($(this).data('id'));
                $('#modal_fname').val($(this).data('fname'));
                $('#modal_lname').val($(this).data('lname'));
                $('#modal_username').val($(this).data('username'));
                $('#modal_access').val($(this).data('access'));
                
                // Clear checkboxes
                $('input[name="modules[]"]').prop('checked', false);
                
                // Check active modules
                let mods = $(this).data('modules').split(',');
                if (mods.includes('hris')) $('#mod_hris').prop('checked', true);
                if (mods.includes('payroll')) $('#mod_payroll').prop('checked', true);
                
                toggleModules();
                $('#addUserModalLabel').text('Edit User');
                $('#addUserModal').modal('show');
            });
            
            $('#addUserModal').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset();
                $('#modal_user_id').val('');
                $('#addUserModalLabel').text('Add New User');
                toggleModules();
            });
        });
        
        function toggleModules() {
            if ($('#modal_access').val() === 'Admin') {
                $('#module_selection_div').hide();
                $('input[name="modules[]"]').prop('checked', true); // Admin gets everything
            } else {
                $('#module_selection_div').show();
            }
        }
    </script>
  </body>
</html>
