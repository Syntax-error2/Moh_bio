<!-- Add Overtime Modal -->
<div id="addOvertime" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">ADD OVERTIME REQUEST</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">?</span></button>
            </div>
            
            <form action="save_add_overtime.php" method="POST">
                <div class="modal-body">
                    <p>Submit an overtime request for administrator review. Recorded OT punches remain pending until approval.</p>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Personnel Name</label>
                                <input list="personnel_list" name="personnel_info" class="form-control" autocomplete="off" required>
                                <datalist id="personnel_list">
                                    <?php
                                    $p_query = $conn->query("SELECT personnel_id, lname, fname FROM personnels ORDER BY lname ASC");
                                    while($p = $p_query->fetch()){
                                        echo "<option value='".$p['personnel_id']." | ".$p['lname'].", ".$p['fname']."'></option>";
                                    }
                                    ?>
                                </datalist>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" name="ot_date" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Remarks / Reason</label>
                                <input type="text" name="remarks" class="form-control" required placeholder="e.g. Election preparation">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-secondary">Close</button>
                    <button type="submit" name="add_ot" class="btn btn-primary">SUBMIT REQUEST</button>
                </div>
            </form>
        </div>
    </div>
</div>
