<?php 
session_start();
if(empty($_SESSION['name']))
{
    header('location:index.php');
}
include('header.php');
include('includes/connection.php');

$id = $_GET['id'];
$fetch_query = mysqli_query($connection, "SELECT * FROM tbl_employee WHERE id='$id'");
$row = mysqli_fetch_array($fetch_query);

$upload_dir = 'uploads/employees/';

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

    // Existing file paths
    $image_path = $row['image_path'];
    $resume_path = $row['resume_path'];

    // Handle photo upload
    if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0)
    {
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $file_name = $_FILES['photo']['name'];
        $file_tmp = $_FILES['photo']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if(in_array($file_ext, $allowed_types))
        {
            $new_file_name = 'emp_'.$id.'_'.time().'.'.$file_ext;
            if(!is_dir($upload_dir)){ mkdir($upload_dir, 0777, true); }
            $upload_path = $upload_dir . $new_file_name;

            if(move_uploaded_file($file_tmp, $upload_path))
            {
                if($image_path != "" && file_exists($upload_dir . $image_path)){
                    unlink($upload_dir . $image_path);
                }
                $image_path = $new_file_name;
            }
            else { $msg = "Failed to upload image."; }
        }
        else { $msg = "Invalid image format. Only JPG, JPEG, PNG, GIF allowed."; }
    }

    // Handle resume upload
    if(isset($_FILES['resume']) && $_FILES['resume']['error'] == 0)
    {
        $allowed_resume = ['pdf', 'doc', 'docx'];
        $resume_name = $_FILES['resume']['name'];
        $resume_tmp = $_FILES['resume']['tmp_name'];
        $resume_ext = strtolower(pathinfo($resume_name, PATHINFO_EXTENSION));

        if(in_array($resume_ext, $allowed_resume))
        {
            $new_resume_name = 'resume_'.$id.'_'.time().'.'.$resume_ext;
            $resume_path_full = $upload_dir . $new_resume_name;

            if(move_uploaded_file($resume_tmp, $resume_path_full))
            {
                if($resume_path != "" && file_exists($upload_dir . $resume_path)){
                    unlink($upload_dir . $resume_path);
                }
                $resume_path = $new_resume_name;
            }
            else { $msg = "Failed to upload resume."; }
        }
        else { $msg = "Invalid resume format. Only PDF, DOC, DOCX allowed."; }
    }

    // Update DB
    $update_query = mysqli_query($connection, "UPDATE tbl_employee SET 
        first_name='$first_name', last_name='$last_name', username='$username', emailid='$emailid', 
        password='$pwd', dob='$dob', joining_date='$joining_date', gender='$gender', phone='$phone', 
        shift='$shift', department='$department', status='$status', 
        image_path='$image_path', resume_path='$resume_path' 
        WHERE id='$id'");

    if($update_query)
    {
        $msg = "Employee updated successfully";
        $fetch_query = mysqli_query($connection, "SELECT * FROM tbl_employee WHERE id='$id'");
        $row = mysqli_fetch_array($fetch_query);   
    }
    else
    {
        $msg = "Error updating employee!";
    }
}
?>

<!-- HTML Start -->
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-sm-4 ">
                <h4 class="page-title">Edit Employee</h4>
            </div>
            <div class="col-sm-8 text-right m-b-20">
                <a href="employees.php" class="btn btn-primary btn-rounded float-right">Back</a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Name fields -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>First Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="first_name" value="<?php echo htmlspecialchars($row['first_name']); ?>">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Last Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="last_name" value="<?php echo htmlspecialchars($row['last_name']); ?>">
                            </div>
                        </div>

                        <!-- Other personal fields -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Username</label>
                                <input class="form-control" type="text" name="username" value="<?php echo htmlspecialchars($row['username']); ?>">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input class="form-control" type="email" name="emailid" value="<?php echo htmlspecialchars($row['emailid']); ?>">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Password</label>
                                <input class="form-control" type="password" name="pwd" value="<?php echo htmlspecialchars($row['password']); ?>">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Employee ID</label>
                                <input class="form-control" type="text" value="<?php echo htmlspecialchars($row['employee_id']); ?>" disabled>
                            </div>
                        </div>

                        <!-- Joining, DOB, Shift -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Joining Date</label>
                                <input type="text" class="form-control datetimepicker" name="joining_date" value="<?php echo htmlspecialchars($row['joining_date']); ?>">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Shift</label>
                                <select class="select" name="shift">
                                    <option value="">Select</option>
                                    <?php
                                     $fetch_shift = mysqli_query($connection, "SELECT start_time, end_time FROM tbl_shift");
                                     while($shift = mysqli_fetch_array($fetch_shift)){ 
                                        $shift_val = $shift['start_time'] . "-" . $shift['end_time'];
                                    ?>
                                    <option value="<?php echo $shift_val; ?>" <?php if($row['shift']==$shift_val){ echo 'selected'; } ?>>
                                        <?php echo $shift_val; ?>
                                    </option>
                                    <?php } ?>  
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Date of Birth</label>
                                <input class="form-control datetimepicker" type="text" name="dob" value="<?php echo htmlspecialchars($row['dob']); ?>">
                            </div>
                        </div>

                        <!-- Phone, Gender, Department -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Phone</label>
                                <input class="form-control" type="text" name="phone" value="<?php echo htmlspecialchars($row['phone']); ?>">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group gender-select">
                                <label>Gender</label><br>
                                <label><input type="radio" name="gender" value="Male" <?php if($row['gender']=='Male') echo 'checked'; ?>> Male</label>
                                <label><input type="radio" name="gender" value="Female" <?php if($row['gender']=='Female') echo 'checked'; ?>> Female</label>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Department</label>
                                <select class="select" name="department">
                                    <option value="">Select</option>
                                    <?php
                                     $fetch_dept = mysqli_query($connection, "SELECT department_name FROM tbl_department");
                                     while($dept = mysqli_fetch_array($fetch_dept)){ 
                                    ?>
                                    <option value="<?php echo $dept['department_name']; ?>" <?php if($row['department']==$dept['department_name']) echo 'selected'; ?>>
                                        <?php echo $dept['department_name']; ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <!-- Image and Resume Upload -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Photo</label><br>
                                <?php if(!empty($row['image_path']) && file_exists($upload_dir . $row['image_path'])): ?>
                                    <img src="<?php echo $upload_dir . $row['image_path']; ?>" style="width:100px;height:100px;">
                                <?php else: ?>
                                    <img src="assets/img/default-avatar.png" style="width:100px;height:100px;">
                                <?php endif; ?>
                                <input type="file" name="photo" accept="image/*" class="form-control">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Resume</label><br>
                                <?php if(!empty($row['resume_path']) && file_exists($upload_dir . $row['resume_path'])): ?>
                                    <a href="<?php echo $upload_dir . $row['resume_path']; ?>" target="_blank">View Existing Resume</a><br>
                                <?php endif; ?>
                                <input type="file" name="resume" accept=".pdf,.doc,.docx" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label>Status</label><br>
                        <label><input type="radio" name="status" value="1" <?php if($row['status']==1) echo 'checked'; ?>> Active</label>
                        <label><input type="radio" name="status" value="0" <?php if($row['status']==0) echo 'checked'; ?>> Inactive</label>
                    </div>

                    <div class="m-t-20 text-center">
                        <button class="btn btn-primary submit-btn" name="save-emp">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

<script type="text/javascript">
<?php if(isset($msg)) echo 'swal("'.$msg.'");'; ?>
</script>
