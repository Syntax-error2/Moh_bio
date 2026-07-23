<?php
include('session.php');
include('header.php');
?>

<body>
<?php include('menu_sidebar.php'); ?>

<div class="page">
    <?php include('navbar_header.php'); ?>
    
    <section class="mt-30px mb-30px">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h2>Modal Test Page</h2>
                </div>
                <div class="card-body">
                    <h4>Bootstrap Modal Test</h4>
                    <p>Click the button below to test if modals are working:</p>
                    
                    <!-- Test Button 1: Using data-toggle -->
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#testModal1">
                        Test Modal 1 (data-toggle)
                    </button>
                    
                    <!-- Test Button 2: Using JavaScript -->
                    <button type="button" class="btn btn-success" onclick="$('#testModal2').modal('show');">
                        Test Modal 2 (JavaScript)
                    </button>
                    
                    <!-- Test Button 3: From income.php style -->
                    <a style="color: white !important;" data-toggle="modal" data-target="#testModal3" href="#" class="btn btn-info">
                        Test Modal 3 (Link Style)
                    </a>
                    
                    <hr class="mt-4 mb-4">
                    
                    <h4>Diagnostic Information</h4>
                    <div id="diagnostics"></div>
                </div>
            </div>
        </div>
    </section>
    
    <?php include('footer.php'); ?>
</div><!-- End .page -->

<!-- Test Modal 1 -->
<div class="modal fade" id="testModal1" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test Modal 1</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>This modal uses <code>data-toggle="modal"</code></p>
                <p>If you can see this, Bootstrap modals are working!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Test Modal 2 -->
<div class="modal fade" id="testModal2" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test Modal 2</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>This modal uses <code>$('#modal').modal('show')</code></p>
                <p>JavaScript method works!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Test Modal 3 -->
<div class="modal fade" id="testModal3" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test Modal 3</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>This modal uses link-style button (income.php pattern)</p>
                <p>Link-style buttons work!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include('scripts_files.php'); ?>

<script>
$(document).ready(function() {
    // Diagnostic information
    let html = '<ul>';
    html += '<li><strong>jQuery loaded:</strong> ' + (typeof jQuery !== 'undefined' ? 'YES ✓' : 'NO ✗') + '</li>';
    html += '<li><strong>jQuery version:</strong> ' + (typeof jQuery !== 'undefined' ? jQuery.fn.jquery : 'N/A') + '</li>';
    html += '<li><strong>$ defined:</strong> ' + (typeof $ !== 'undefined' ? 'YES ✓' : 'NO ✗') + '</li>';
    html += '<li><strong>Bootstrap loaded:</strong> ' + (typeof $.fn.modal !== 'undefined' ? 'YES ✓' : 'NO ✗') + '</li>';
    html += '<li><strong>Modal buttons found:</strong> ' + $('[data-toggle="modal"]').length + '</li>';
    html += '<li><strong>Modals found:</strong> ' + $('.modal').length + '</li>';
    html += '</ul>';
    
    $('#diagnostics').html(html);
    
    console.log('=== DIAGNOSTIC INFO ===');
    console.log('jQuery loaded:', typeof jQuery !== 'undefined');
    console.log('jQuery version:', typeof jQuery !== 'undefined' ? jQuery.fn.jquery : 'N/A');
    console.log('Bootstrap modal:', typeof $.fn.modal !== 'undefined');
    console.log('Modal buttons:', $('[data-toggle="modal"]').length);
    console.log('Modals:', $('.modal').length);
});
</script>

</body>
</html>
