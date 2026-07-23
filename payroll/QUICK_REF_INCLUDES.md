# Quick Reference: Standard Includes for Payroll Module

## ✅ Always Use These Includes

### 1. CSS Loading (in <head>)
```php
<?php include('header.php'); ?>
```

### 2. JavaScript Loading (after footer, before custom scripts)
```php
<?php include('scripts_files.php'); ?>
```

## ❌ Never Do This

```php
<!-- DON'T use CDN -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- DON'T manually load vendor files -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
```

## ✅ Correct Page Template

```php
<?php
include('session.php');
// PHP logic
?>
<!DOCTYPE html>
<html lang="en">

<?php include('header.php'); ?>  <!-- CSS here -->

<body>
<?php include('menu_sidebar.php'); ?>

<div class="page">
    <?php include('navbar_header.php'); ?>
    
    <section class="mt-30px mb-30px">
        <div class="container-fluid">
            <!-- Your content -->
        </div>
    </section>
    
</div>

<?php include('footer.php'); ?>

<?php include('scripts_files.php'); ?>  <!-- JS here -->

<script>
    // Your custom JavaScript
    $(document).ready(function() {
        // jQuery is available here
    });
</script>

</body>
</html>
```

## What Each File Provides

### header.php Provides:
- Bootstrap CSS
- Font Awesome 4.7.0
- DataTables CSS
- Theme CSS
- Custom CSS

### scripts_files.php Provides:
- jQuery
- Bootstrap JavaScript
- DataTables JavaScript
- Chart.js
- Validation
- All other plugins

## Updated Files

✅ list_payroll_profiles.php  
✅ list_payroll_history.php  
✅ view_payroll_profile.php  

Already compliant:  
✅ list_personnel_income.php  
✅ list_personnel_deductions.php  
✅ list_personnel_individual_details.php  

## Remember

1. ✅ header.php in <head>
2. ✅ scripts_files.php after footer
3. ✅ Custom scripts last
4. ❌ No CDN links
5. ❌ No manual vendor includes
