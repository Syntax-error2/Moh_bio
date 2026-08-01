<?php
/**
 * Configure Official Registry Print
 */
include('session.php');

$run_id = isset($_GET['run_id']) ? intval($_GET['run_id']) : 0;

if (!$run_id) {
    header("Location: list_payroll_history.php?error=" . urlencode("Invalid run ID"));
    exit();
}

try {
    // Check if run exists
    $run_check = $conn->prepare("SELECT * FROM pr_tbl_payroll_runs WHERE run_id = :run_id");
    $run_check->execute([':run_id' => $run_id]);
    $run = $run_check->fetch(PDO::FETCH_ASSOC);
    
    if (!$run) {
        throw new Exception("Payroll run not found.");
    }

    // Get all distinct incomes for this run
    $income_q = $conn->prepare("SELECT DISTINCT income_title, income_id FROM pr_tbl_payroll_run_income WHERE run_id = :run_id ORDER BY income_title");
    $income_q->execute([':run_id' => $run_id]);
    $incomes = $income_q->fetchAll(PDO::FETCH_ASSOC);

    // Get all distinct deductions for this run
    $deduc_q = $conn->prepare("SELECT DISTINCT deduction_title, deduction_id FROM pr_tbl_payroll_run_deductions WHERE run_id = :run_id ORDER BY deduction_title");
    $deduc_q->execute([':run_id' => $run_id]);
    $deductions = $deduc_q->fetchAll(PDO::FETCH_ASSOC);

    // Get signatory templates
    $tpl_q = $conn->query("SELECT * FROM pr_tbl_signatory_templates ORDER BY is_default DESC, template_name ASC");
    $templates = $tpl_q->fetchAll(PDO::FETCH_ASSOC);

    // Get default template items
    $default_items = [];
    if (!empty($templates)) {
        $first_tpl = $templates[0]['template_id'];
        $item_q = $conn->prepare("SELECT * FROM pr_tbl_signatory_items WHERE template_id = :tid ORDER BY display_order");
        $item_q->execute([':tid' => $first_tpl]);
        $default_items = $item_q->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get Employment Statuses
    $emp_stat_q = $conn->query("SELECT * FROM emp_status ORDER BY emp_stat_name");
    $emp_statuses = $emp_stat_q->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("Error loading configuration: " . $e->getMessage());
}

$page_title = "Configure Official Registry";
include('header.php');
include('menu_sidebar.php');
?>

<div class="page">
    <div class="page-content">
        <div class="container-fluid">
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <h1 class="page-title mt-4"><i class="fa fa-cogs"></i> Configure Official Registry</h1>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="list_payroll_history.php">Payroll Runs</a></li>
                        <li class="breadcrumb-item"><a href="view_payroll_run.php?run_id=<?php echo $run_id; ?>">Run #<?php echo $run_id; ?></a></li>
                        <li class="breadcrumb-item active">Configure Registry</li>
                    </ol>
                </div>
            </div>

            <form action="print_official_registry.php" method="POST" target="_blank" id="registryForm">
                <input type="hidden" name="run_id" value="<?php echo $run_id; ?>">
                
                <div class="row">
                    <!-- Left Column: Columns & Filters -->
                    <div class="col-md-6">
                        
                        <!-- Print Options -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fa fa-filter"></i> Grouping & Filtering</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label><strong>Filter by Employment Status / Role:</strong></label>
                                    <select name="emp_status_filter" class="form-control">
                                        <option value="all">-- All Statuses --</option>
                                        <?php foreach ($emp_statuses as $stat): ?>
                                            <option value="<?php echo $stat['empStat_id']; ?>"><?php echo htmlspecialchars($stat['emp_stat_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label><strong>Filter by Membership:</strong></label>
                                    <select name="membership_filter" class="form-control">
                                        <option value="all">-- No Filter --</option>
                                        <option value="gsis">GSIS Members Only</option>
                                        <option value="sss">SSS Members Only</option>
                                    </select>
                                    <small class="form-text text-muted">Will filter based on which deduction was applied in this run.</small>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="group_by_dept" name="group_by_dept" value="1">
                                        <label class="custom-control-label" for="group_by_dept">Group by Department (Page break per dept)</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Column Selection -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fa fa-columns"></i> Report Columns</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Select which components should appear as distinct columns on the printed registry. (Rate, Gross, Total Deductions, and Net are always included).</p>
                                
                                <h6>Income Columns</h6>
                                <div class="row mb-3">
                                    <?php foreach ($incomes as $inc): ?>
                                    <div class="col-md-6">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="inc_<?php echo $inc['income_id']; ?>" name="income_cols[]" value="<?php echo htmlspecialchars($inc['income_title']); ?>" checked>
                                            <label class="custom-control-label" for="inc_<?php echo $inc['income_id']; ?>"><?php echo htmlspecialchars($inc['income_title']); ?></label>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <?php if (empty($incomes)) echo "<div class='col-12'><em class='text-muted'>No dynamic income items found.</em></div>"; ?>
                                </div>

                                <h6>Deduction Columns</h6>
                                <div class="row">
                                    <?php foreach ($deductions as $ded): ?>
                                    <div class="col-md-6">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="ded_<?php echo $ded['deduction_id']; ?>" name="deduction_cols[]" value="<?php echo htmlspecialchars($ded['deduction_title']); ?>" checked>
                                            <label class="custom-control-label" for="ded_<?php echo $ded['deduction_id']; ?>"><?php echo htmlspecialchars($ded['deduction_title']); ?></label>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <?php if (empty($deductions)) echo "<div class='col-12'><em class='text-muted'>No deduction items found.</em></div>"; ?>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Signatories -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fa fa-pencil"></i> Signatories</h5>
                            </div>
                            <div class="card-body">
                                
                                <div class="form-group">
                                    <label><strong>Signatory Template:</strong></label>
                                    <select id="signatoryTemplate" name="template_id" class="form-control" onchange="loadTemplate(this.value)">
                                        <?php foreach ($templates as $tpl): ?>
                                            <option value="<?php echo $tpl['template_id']; ?>"><?php echo htmlspecialchars($tpl['template_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <hr>

                                <div id="signatoryList">
                                    <?php foreach ($default_items as $index => $item): ?>
                                    <div class="signatory-row mb-3 p-3 border rounded">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="small">Certification</label>
                                                <input type="text" name="sig_role[]" class="form-control form-control-sm" value="<?php echo htmlspecialchars($item['role_title']); ?>" placeholder="e.g. Prepared By" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="small">Person Name</label>
                                                <input type="text" name="sig_name[]" class="form-control form-control-sm" value="<?php echo htmlspecialchars($item['person_name']); ?>" placeholder="e.g. JOHN DOE">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="small">Title / Position</label>
                                                <input type="text" name="sig_title[]" class="form-control form-control-sm" value="<?php echo htmlspecialchars($item['person_title'] ?? ''); ?>" placeholder="e.g. Municipal Mayor">
                                            </div>
                                            <div class="col-md-1 pt-4 text-right">
                                                <button type="button" class="btn btn-sm btn-danger" onclick="removeSignatory(this)"><i class="fa fa-trash"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <button type="button" class="btn btn-secondary btn-sm" onclick="addSignatory()"><i class="fa fa-plus"></i> Add Signatory Row</button>
                                
                                <?php if (strtolower($session_access) === 'administrator' || strtolower($session_access) === 'admin'): ?>
                                <button type="button" class="btn btn-warning btn-sm float-right" onclick="saveSignatoryTemplate()">
                                    <i class="fa fa-save"></i> Save as Default Template
                                </button>
                                <?php endif; ?>

                            </div>
                        </div>

                        <!-- Accounting Entries -->
                        <div class="card mb-4">
                            <div class="card-header bg-dark text-white">
                                <h5 class="mb-0"><i class="fa fa-book"></i> Accounting Entries (Optional)</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small">Add accounting entries to print at the bottom of the registry. Leave blank to print empty tables.</p>
                                
                                <div id="accountingList">
                                    <!-- Initial Empty Row -->
                                    <div class="acc-row mb-2 p-2 border rounded bg-light">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input type="text" name="acc_particulars[]" class="form-control form-control-sm" placeholder="Particulars">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" name="acc_code[]" class="form-control form-control-sm" placeholder="Account Code">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" step="0.01" name="acc_debit[]" class="form-control form-control-sm" placeholder="Debit">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" step="0.01" name="acc_credit[]" class="form-control form-control-sm" placeholder="Credit">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-sm btn-danger" onclick="$(this).closest('.acc-row').remove()"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="addAccountingRow()"><i class="fa fa-plus"></i> Add Entry Row</button>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card">
                            <div class="card-body text-center">
                                <a href="view_payroll_run.php?run_id=<?php echo $run_id; ?>" class="btn btn-secondary mr-2">Cancel</a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fa fa-print"></i> Generate Official Registry
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

            </form>

        </div>
    </div>
    <?php include('footer.php'); ?>
</div>

<?php include('scripts_files.php'); ?>
<script>
function addSignatory() {
    var html = `
    <div class="signatory-row mb-3 p-3 border rounded bg-light">
        <div class="row">
            <div class="col-md-4">
                <label class="small">Certification</label>
                <input type="text" name="sig_role[]" class="form-control form-control-sm" placeholder="e.g. Prepared By" required>
            </div>
            <div class="col-md-4">
                <label class="small">Person Name</label>
                <input type="text" name="sig_name[]" class="form-control form-control-sm" placeholder="e.g. JOHN DOE">
            </div>
            <div class="col-md-3">
                <label class="small">Title / Position</label>
                <input type="text" name="sig_title[]" class="form-control form-control-sm" placeholder="e.g. Municipal Mayor">
            </div>
            <div class="col-md-1 pt-4 text-right">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeSignatory(this)"><i class="fa fa-trash"></i></button>
            </div>
        </div>
    </div>`;
    $('#signatoryList').append(html);
}

function removeSignatory(btn) {
    $(btn).closest('.signatory-row').remove();
}

function loadTemplate(templateId) {
    // In a real scenario, this would use AJAX to fetch template items from pr_tbl_signatory_items
    // For now, since they just approved the DB structure, we will stick to the default loaded on page load
    // or just let them edit the UI directly.
    console.log("Template selected: " + templateId);
}
function addAccountingRow() {
    const html = `
    <div class="acc-row mb-2 p-2 border rounded bg-light">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="acc_particulars[]" class="form-control form-control-sm" placeholder="Particulars">
            </div>
            <div class="col-md-3">
                <input type="text" name="acc_code[]" class="form-control form-control-sm" placeholder="Account Code">
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" name="acc_debit[]" class="form-control form-control-sm" placeholder="Debit">
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" name="acc_credit[]" class="form-control form-control-sm" placeholder="Credit">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-danger" onclick="$(this).closest('.acc-row').remove()"><i class="fa fa-times"></i></button>
            </div>
        </div>
    </div>`;
    $('#accountingList').append(html);
}

function saveSignatoryTemplate() {
    if (!confirm("Are you sure you want to save these signatories as the default template? This will overwrite the current template.")) {
        return;
    }
    
    $.ajax({
        url: 'save_signatory_template_ajax.php',
        method: 'POST',
        data: $('#registryForm').serialize(),
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                alert(res.message);
            } else {
                alert("Error: " + res.message);
            }
        },
        error: function() {
            alert("An error occurred while saving.");
        }
    });
}
</script>
</body>
</html>
