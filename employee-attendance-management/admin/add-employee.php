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
$resume_dir = 'uploads/resumes/';

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

            if(!is_dir($upload_dir)){
                mkdir($upload_dir, 0777, true);
            }

            $upload_path = $upload_dir . $new_file_name;

            if(move_uploaded_file($file_tmp, $upload_path))
            {
                if($image_path != "" && file_exists($upload_dir . $image_path)){
                    unlink($upload_dir . $image_path);
                }
                $image_path = $new_file_name;
            }
            else
            {
                $msg = "Failed to upload image.";
            }
        }
        else
        {
            $msg = "Invalid image format. Only JPG, JPEG, PNG, GIF allowed.";
        }
    }

    // Handle resume upload
    if(isset($_FILES['resume']) && $_FILES['resume']['error'] == 0)
    {
        $allowed_resumes = ['pdf', 'doc', 'docx'];
        $resume_name = $_FILES['resume']['name'];
        $resume_tmp = $_FILES['resume']['tmp_name'];
        $resume_ext = strtolower(pathinfo($resume_name, PATHINFO_EXTENSION));

        if(in_array($resume_ext, $allowed_resumes))
        {
            $new_resume = 'resume_'.$id.'_'.time().'.'.$resume_ext;

            if(!is_dir($resume_dir)){
                mkdir($resume_dir, 0777, true);
            }

            $resume_path_full = $resume_dir . $new_resume;

            if(move_uploaded_file($resume_tmp, $resume_path_full))
            {
                if($resume_path != "" && file_exists($resume_dir . $resume_path)){
                    unlink($resume_dir . $resume_path);
                }
                $resume_path = $new_resume;
            }
            else
            {
                $msg = "Failed to upload resume.";
            }
        }
        else
        {
            $msg = "Invalid resume format. Only PDF, DOC, DOCX allowed.";
        }
    }

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

<!-- FORM HTML INSIDE .page-wrapper -->

<div class="col-sm-6">
    <div class="form-group">
        <label>Resume</label><br>
        <?php 
        if(!empty($row['resume_path']) && file_exists($resume_dir . $row['resume_path'])): ?>
            <a href="<?php echo $resume_dir . $row['resume_path']; ?>" target="_blank">Download Current Resume</a><br>
        <?php else: ?>
            <p>No resume uploaded.</p>
        <?php endif; ?>
        <input type="file" name="resume" accept=".pdf,.doc,.docx" class="form-control">
    </div>
</div>

<?php include('footer.php'); ?>
<script type="text/javascript">
<?php if(isset($msg)) echo 'swal("' . $msg . '");'; ?>
</script>
