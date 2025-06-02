<?php
session_start();
if(empty($_SESSION['name']) || $_SESSION['role']!=1)
{
    header('location:index.php');
}
include('header.php');
include('includes/connection.php');
$fetch_query = mysqli_query($connection, "select max(id) as id from tbl_employee");
$row = mysqli_fetch_row($fetch_query);
if($row[0]==0)
{
    $emp_id = 1;
}
else
{
    $emp_id = $row[0] + 1;
}

if(isset($_REQUEST['add-employee']))
{
    $first_name = $_REQUEST['first_name'];
    $last_name = $_REQUEST['last_name'];
    $username = $_REQUEST['username'];
    $emailid = $_REQUEST['emailid'];
    $pwd = $_REQUEST['pwd'];
    $employee_id = $_REQUEST['employee_id'];
    $joining_date = $_REQUEST['joining_date'];
    $shift = $_REQUEST['shift'];
    $dob = $_REQUEST['dob'];
    $phone = $_REQUEST['phone'];
    $gender = $_REQUEST['gender'];
    $department = $_REQUEST['department'];
    $status = $_REQUEST['status'];


    $insert_query = mysqli_query($connection, "insert into tbl_employee set first_name='$first_name', last_name='$last_name', username='$username', emailid='$emailid', password='$pwd',  dob='$dob', employee_id='$employee_id', joining_date = '$joining_date', gender='$gender', phone='$phone',  shift='$shift', department='$department', status='$status'");

    if($insert_query>0)
    {
        $msg = "Employee created successfully";
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
            <div class="col-sm-4">
                <h4 class="page-title">Add Employee</h4>
            </div>
            <div class="col-sm-8 text-right m-b-20">
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
                                <input class="form-control" type="text" name="first_name" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Last Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="last_name" required> 
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Username <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="username" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input class="form-control" type="email" name="emailid" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Password</label>
                                <input class="form-control" type="password" name="pwd" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Employee ID <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="employee_id" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Joining Date <span class="text-danger">*</span></label>
                                <div class="cal-icon">
                                    <input class="form-control datetimepicker" type="text" name="joining_date" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Shift <span class="text-danger">*</span></label>
                                <select class="select" name="shift" required>
                                    <option value="">Select</option>
                                    <?php
                                     $fetch_query = mysqli_query($connection, "select start_time, end_time from tbl_shift where status=1");
                                        while($shift = mysqli_fetch_array($fetch_query)){ 
                                    ?>
                                    <option value="<?php echo $shift['start_time']; ?>-<?php echo $shift['end_time']; ?>"><?php echo $shift['start_time']; ?>-<?php echo $shift['end_time']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Date of Birth <span class="text-danger">*</span></label>
                                <div class="cal-icon">
                                    <input class="form-control datetimepicker" type="text" name="dob" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Phone </label>
                                <input class="form-control" type="text" name="phone" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group gender-select">
                                <label class="gen-label">Gender:</label>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" name="gender" class="form-check-input" value="Male" >Male
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" name="gender" class="form-check-input" value="Female">Female
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
                                     $fetch_query = mysqli_query($connection, "select department_name from tbl_department where status=1");
                                        while($dept = mysqli_fetch_array($fetch_query)){ 
                                    ?>
                                    <option value="<?php echo $dept['department_name']; ?>"><?php echo $dept['department_name']; ?> </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    <div class="col-sm-6">
                    <div class="form-group">
                        <label class="display-block">Status</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="employee_active" value="1" checked>
                            <label class="form-check-label" for="employee_active">
                            Active
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="employee_inactive" value="0">
                            <label class="form-check-label" for="employee_inactive">
                            Inactive
                            </label>
                        </div>
                    </div>
                </div>
            </div>
                    <div class="m-t-20 text-center">
                        <button class="btn btn-primary submit-btn" name="add-employee">Add Employee</button>
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
