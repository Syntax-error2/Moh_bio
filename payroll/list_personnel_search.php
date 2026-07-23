<?php
if(isset($_POST['search'])){
$searched=$_POST['searchStudent'];
}else{
$searched="";
} ?>
 
                    <div class="col-lg-12" style="margin-bottom: 12px;">
                    
                    <form method="POST">
                    <div class="form-group row" style="margin-top: 12px;">
                            <div class="col-sm-12">
                              <div class="input-group">
                              
                              <input value="<?php echo $searched; ?>" name="searchStudent" list="search_list" placeholder="Search for personnel's ID code or lastname..." class="form-control" required="true" />
                              
                              
                              
                              <datalist id="search_list">
                                        <?php
                                        $fnameList_query = $conn->prepare("SELECT DISTINCT personnel_id_code, lname, fname, mname FROM personnels ORDER BY lname, fname, mname ASC");
                                        $fnameList_query->execute();
                                        while($fnlq_row = $fnameList_query->fetch()){ 
                                        ?>
                                        
                                        <option value="<?php echo $fnlq_row['personnel_id_code']; ?>"><?php echo $fnlq_row['personnel_id_code']; ?> | <small><?php echo $fnlq_row['lname'].', '.$fnlq_row['fname'].' '.$fnlq_row['mname']; ?></small></option>
                                        
                                        <?php } ?>
                              </datalist>
                              
                                
                                <div class="input-group-append">
                                  <button name="search" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                                </div>
                              </div>
                            </div>
                        </div>
                     </form>
                     
                    <div class="table-responsive" style="margin-top: 12px;">
                      <table id="" class="display" style="width:100%">                  
                        <thead>
                          <tr>
                            <th>Action</th>
                            <th style="text-align: center;">Image<br /><small>ID Code</small></th>
                            <th>Fullname</th>
                            <th>Reports</th>
                          </tr>
                        </thead>
                      
                        <tbody> 
                      
                        <?php
                        
                        if($searched==='')
                        {
                            
                        }else{  
                          
                          $staff_query = $conn->prepare("SELECT * FROM personnels WHERE personnel_id_code LIKE '%$searched%' OR lname LIKE '%$searched%' ORDER BY lname, fname ASC");
                          $staff_query->execute();
                          while ($staff_row = $staff_query->fetch()){
                            
                          $personnel_id=$staff_row['personnel_id'];
                        
                        ?>
           
                        <tr>
                        
                            <td style="vertical-align: middle;">
                            <a title="View complete personnel data..." style="color: white !important; margin-top: 3px;" href="list_personnel_individual_details.php?dept=<?php echo $staff_row['do_id']; ?>&personnel_id=<?php echo $personnel_id; ?>" class="btn btn-info btn-sm"><i class="fa fa-info-circle"></i> View Details</a>
                            </td>
                            
                            <td style="text-align: center;">
                            <img src="../personnelImg/<?php if($staff_row['img'] != ""){ echo $staff_row['img']; }else{ echo "nss.jpg"; } ?>" width="80" height="80" class="img-fluid rounded" style="margin-bottom: 8px;" />
                            <br />
                            <small><?php echo $staff_row['personnel_id_code']; ?></small>
                            </td>
                          
                            <td>
                            <?php
                            
                            
                            if($staff_row['suffix']=="-")
                            {
                                
                            echo $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname'];
                            
                            }else{
                                
                            echo $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname']." ".$staff_row['suffix'];
                            
                            } ?>
                            </td>
                           
                            <td>
                            
                            <button data-toggle="dropdown" type="button" class="btn btn-outline-primary dropdown-toggle"><i class="fa fa-print"></i> Reports <i class="caret"></i></button>
                            
                            <div class="dropdown-menu">
                            
                            <a title="Print Civil Service Form 48..." data-toggle="modal" data-target="#print_monthly_attendance_csf48<?php echo $staff_row['RFTag_id']; ?>" href="#" class="dropdown-item"><i class="fa fa-print"></i> CSForm 48</a>
                            <a title="Print detailed DTR..." data-toggle="modal" data-target="#print_monthly_attendance<?php echo $staff_row['RFTag_id']; ?>" href="#" class="dropdown-item"><i class="fa fa-print"></i> Detailed DTR <small>(Monthly)</small></a>
                            <a title="Print Log Validations history..." data-toggle="modal" data-target="#print_monthly_LV<?php echo $staff_row['RFTag_id']; ?>" href="#" class="dropdown-item"><i class="fa fa-image"></i> Log Validation History <small>(Monthly)</small></a>
                            <div class="dropdown-divider"></div>
                             
                            <a title="View 201 files..." data-toggle="modal" data-target="#download201Files<?php echo $personnel_id; ?>" href="#" class="dropdown-item"><i class="fa fa-search"></i>  201 File Archive</a>
                            <a title="Add/Upload 201 files..." data-toggle="modal" data-target="#add201Files<?php echo $personnel_id; ?>" href="#" class="dropdown-item"><i class="fa fa-plus"></i> 201 Files</a>
                           
                            </div>
                        
                           </td>
                           
                        </tr>
 
                        <?php } } ?>
                       
                      </tbody>
                    </table>
                    </div>
                    </div>                                        
 