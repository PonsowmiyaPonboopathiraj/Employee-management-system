<?php 
session_start();
if(empty($_SESSION['name']))
{
    header('location:index.php');
}
include('header.php');
include('includes/connection.php');

$id = $_GET['id'];
$fetch_query = mysqli_query($connection, "select * from tbl_employee where id='$id'");
$row = mysqli_fetch_array($fetch_query);

if(isset($_REQUEST['save-emp']))
{
      $first_name = $_REQUEST['first_name'];
      $last_name = $_REQUEST['last_name'];
      $username = $_REQUEST['username'];
      $emailid = $_REQUEST['emailid'];
      $pwd = $_REQUEST['pwd'];
      $joining_date = $_REQUEST['joining_date'];
      $shift = $_REQUEST['shift'];
      $dob = $_REQUEST['dob'];
      $phone = $_REQUEST['phone'];
      $gender = $_REQUEST['gender'];
      $department = $_REQUEST['department'];
      $status = $_REQUEST['status'];


      $update_query = mysqli_query($connection, "update tbl_employee set first_name='$first_name', last_name='$last_name', username='$username', emailid='$emailid', password='$pwd',  dob='$dob', joining_date = '$joining_date', gender='$gender', phone='$phone',  shift='$shift', department='$department', status='$status' where id='$id'");
      if($update_query>0)
      {
          $msg = "Employee updated successfully";
          $fetch_query = mysqli_query($connection, "select * from tbl_employee where id='$id'");
          $row = mysqli_fetch_array($fetch_query);   
      }
      else
      {
          $msg = "Error!";
      }
  }

?>
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-sm-4 ">
                        <h4 class="page-title">Edit Employee</h4>
                    </div>
                    <div class="col-sm-8  text-right m-b-20">
                        <a href="employees.php" class="btn btn-primary btn-rounded float-right">Back</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <form method="post">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>First Name <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="first_name" value="<?php  echo $row['first_name'];  ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Last Name <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="last_name" value="<?php echo $row['last_name']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Username <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="username" value="<?php echo $row['username']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Email <span class="text-danger">*</span></label>
                                        <input class="form-control" type="email" name="emailid" value="<?php echo $row['emailid']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input class="form-control" type="password" name="pwd" value="<?php echo $row['password']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Employee ID <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="employee_id" value="<?php echo $row['employee_id']; ?>" disabled>
                                    </div>
                                </div>
								<div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Joining Date <span class="text-danger">*</span></label>
                                        <div class="cal-icon">
                                            <input type="text" class="form-control datetimepicker" name="joining_date" value="<?php echo $row['joining_date']; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Shift <span class="text-danger">*</span></label>
                                        <select class="select" name="shift" required>
                                            <option value="">Select</option>
                                            <?php
                                             $fetch_query = mysqli_query($connection, "select start_time, end_time from tbl_shift");
                                                while($shift = mysqli_fetch_array($fetch_query)){ 
                                            ?>
                                            <option <?php if($row['shift']==$shift['start_time']."-".$shift['end_time']){?> selected="selected"; <?php } ?>><?php echo $shift['start_time']; ?>-<?php echo $shift['end_time']; ?></option>
                                          <?php } ?>  
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Date of Birth <span class="text-danger">*</span></label>
                                        <div class="cal-icon">
                                            <input class="form-control datetimepicker" type="text" name="dob" required value="<?php echo $row['dob']; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Phone </label>
                                        <input class="form-control" type="text" name="phone" value="<?php echo $row['phone']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group gender-select">
                                        <label class="gen-label">Gender:</label>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" name="gender" class="form-check-input" value="Male" <?php if($row['gender']=='Male') { echo 'checked' ; } ?>>Male
                                            </label>
                                        </div>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" name="gender" class="form-check-input" value="Female" <?php if($row['gender']=='Female') { echo 'checked' ; } ?>>Female
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Department</label>
                                        <select class="select" name="department" required>
                                            <option value="">Select</option>
                                            <?php
                                             $fetch_query = mysqli_query($connection, "select department_name from tbl_department");
                                                while($dept = mysqli_fetch_array($fetch_query)){ 
                                            ?>
                                            <option <?php if($row['department']==$dept['department_name']) { ?>selected="selected";<?php } ?>><?php echo $dept['department_name']; ?> </option>
                                            <?php } ?>
                                            
                                        </select>
                                    </div>
                                </div>

                                </div>
							
                            <div class="form-group">
                                <label class="display-block">Status</label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="status" id="emp_active" value="1" <?php if($row['status']==1) { echo 'checked' ; } ?>>
									<label class="form-check-label" for="employee_active">
									Active
									</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="status" id="emp_inactive" value="0" <?php if($row['status']==0) { echo 'checked' ; } ?>>
									<label class="form-check-label" for="employee_inactive">
									Inactive
									</label>
								</div>
                            </div>
                            <div class="m-t-20 text-center">
                                <button class="btn btn-primary submit-btn" name="save-emp">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
			
        </div>
    
<?php 
include('footer.php');
?>
<script type="text/javascript">
     <?php
        if(isset($msg)) {

              echo 'swal("' . $msg . '");';
          }
     ?>
</script> 