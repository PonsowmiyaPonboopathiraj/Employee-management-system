<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['employee_id']) || empty($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

include('header.php');
include('includes/connection.php');

if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

date_default_timezone_set('Asia/Kolkata');
$id = $_SESSION['employee_id'];

$fetch_emp = mysqli_query($connection, "SELECT * FROM tbl_employee WHERE employee_id='$id'");
$emp = mysqli_fetch_array($fetch_emp);

$empid = $emp['employee_id'];
$dept = $emp['department'];
$shift = isset($emp['shift']) ? $emp['shift'] : '09:00:00 - 18:00:00';

$curr_date = date('Y-m-d');
$current_time = date('H:i:s');

// Split shift
$shifttime = substr($shift, 0, 8);
$outtimeshift = substr($shift, -8);

$intime = (strtotime($current_time) > strtotime($shifttime)) ? "Late" : "On Time";
$outtime = (strtotime($current_time) > strtotime($outtimeshift)) ? "Over Time" : "Early";

// Handle Check-in
if (isset($_POST['turn-it'])) {
    $department = $dept;
    $location = $_POST['location'];
    $msg = $_POST['msg'];
    $shift_value = $shift;

    $insert_query = mysqli_query($connection, "INSERT INTO tbl_attendance 
        (employee_id, department, shift, location, message, date, check_in, in_status, check_out, out_status) 
        VALUES 
        ('$empid', '$department', '$shift_value', '$location', '$msg', '$curr_date', '$current_time', '$intime', '00:00:00', '')");

    if (!$insert_query) {
        echo "<div class='alert alert-danger'>Check-in failed: " . mysqli_error($connection) . "</div>";
    }
}

// Handle Check-out
$checkout_status = 1;
$fetch_checkin = mysqli_query($connection, "SELECT date FROM tbl_attendance WHERE check_out='00:00:00' AND employee_id='$empid' AND date='$curr_date'");
if (mysqli_num_rows($fetch_checkin) > 0) {
    $data = mysqli_fetch_array($fetch_checkin);
    $chekdate = $data['date'];

    if (isset($_POST['check-out'])) {
        $update = mysqli_query($connection, "UPDATE tbl_attendance SET check_out='$current_time', out_status='$outtime' 
            WHERE employee_id='$empid' AND date='$chekdate'");

        if ($update) {
            $checkout_status = 0;
        } else {
            echo "<div class='alert alert-danger'>Check-out failed: " . mysqli_error($connection) . "</div>";
        }
    }
}
?>

<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-sm-4">
                <h4 class="page-title">Attendance Form</h4>
            </div>
        </div>

        <div class="row">
            <?php
            $fetch_attend = mysqli_query($connection, "SELECT * FROM tbl_attendance WHERE date='$curr_date' AND employee_id='$empid'");
            if (mysqli_num_rows($fetch_attend) == 0) {
            ?>
                <!-- Check-in Form -->
                <div class="col-lg-8 offset-lg-2">
                    <form method="post">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Shift <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="shift" value="<?php echo $shift; ?>" disabled>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Location <span class="text-danger">*</span></label>
                                    <select class="select" name="location" required>
                                        <option value="">Select</option>
                                        <?php
                                        $fetch_query = mysqli_query($connection, "SELECT location FROM tbl_location");
                                        while ($loc = mysqli_fetch_array($fetch_query)) {
                                            echo "<option value='{$loc['location']}'>{$loc['location']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Message</label>
                                    <textarea class="form-control" name="msg"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="m-t-20 text-center">
                            <button class="btn btn-primary submit-btn" name="turn-it"><img src="assets/img/login.png" width="40"> Turn It!</button>
                        </div>
                    </form>
                </div>
            <?php
            } else {
            ?>
                <!-- Check-out Section -->
                <div class="col-lg-12 offset-lg-2">
                    <div class="row">
                        <div class="col-sm-6">
                            <center><h3>Thank You For Today</h3></center>
                            <form method="post">
                                <div class="m-t-20 text-center">
                                    <?php
                                    $fetch_checkout = mysqli_query($connection, "SELECT out_status FROM tbl_attendance WHERE date='$curr_date' AND employee_id='$empid'");
                                    $result = mysqli_fetch_array($fetch_checkout);
                                    $out_status = $result['out_status'];

                                    if ($out_status == '' && $checkout_status == 1) {
                                    ?>
                                        <button class="btn btn-primary submit-btn" name="check-out" onclick="return confirmDelete()"><img src="assets/img/login.png" width="40"> Check Out!</button>
                                    <?php
                                    } else {
                                    ?>
                                        <button disabled class="btn btn-primary submit-btn"><img src="assets/img/login.png" width="40"> Done!</button>
                                        <h5>See you tomorrow!</h5>
                                    <?php } ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

<script type="text/javascript">
    function confirmDelete() {
        return confirm('Are you sure you want to check out now?');
    }
</script>
