<?php
session_start();
if (empty($_SESSION['name'])) {
    header('location:index.php');
}
include('header.php');
include('includes/connection.php');
?>
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-sm-4 col-3">
                <h4 class="page-title">Leave Report</h4>
            </div>
            <form method="post">
                <div class="form-group row" style="padding: 20px;">
                    <label class="col-lg-0 col-form-label" for="from">From</label>
                    <div class="col-lg-3">
                        <input type="text" class="form-control" id="datetimepicker5" name="from_date" placeholder="Select Date" required>
                    </div>

                    <label class="col-lg-0 col-form-label" for="to">To</label>
                    <div class="col-lg-3">
                        <input type="text" class="form-control" id="datetimepicker6" name="to_date" placeholder="Select Date" required>
                    </div>

                    <div class="col-lg-3">
                        <select class="form-control" id="leave_type" name="leave_type" required>
                            <option value="">Select Leave Type</option>
                            <?php 
                            $fetch_leave_types = mysqli_query($connection, "SELECT * FROM tbl_leave_types");
                            while ($row = mysqli_fetch_array($fetch_leave_types)) {
                            ?>
                                <option value="<?php echo $row['leave_type']; ?>">
                                    <?php echo $row['leave_type']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-lg-3" style="margin-top: 15px;">
                        <select class="form-control" name="employee" required>
                            <option value="">Select Employee</option>
                            <option value="all">All Employees</option>
                            <?php 
                            $fetch_employee = mysqli_query($connection, "SELECT * FROM tbl_employee");
                            while ($row = mysqli_fetch_array($fetch_employee)) {
                                echo '<option value="'.$row['employee_id'].'">'.$row['first_name'].'</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-lg-2 d-flex align-items-end">
                        <button type="submit" name="srh-btn" class="btn btn-primary w-100">Search</button>
                    </div>

                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="datatable table table-stripped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Leave Type</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Leave Status</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($_POST['srh-btn'])) {
                        $from_date = date('Y-m-d', strtotime($_POST['from_date']));
                        $to_date = date('Y-m-d', strtotime($_POST['to_date']));
                        $leave_type = $_POST['leave_type'];
                        $emp_id = $_POST['employee'];

                        if ($emp_id == "all") {
                            $search_query = mysqli_query($connection, "
                                SELECT 
                                    e.first_name, 
                                    e.last_name, 
                                    l.leave_type, 
                                    l.from_date, 
                                    l.to_date, 
                                    l.leave_status, 
                                    l.message 
                                FROM tbl_employee e
                                INNER JOIN tbl_leaves l ON l.employee_id = e.employee_id 
                                WHERE l.leave_type = '$leave_type' 
                                AND DATE(l.from_date) BETWEEN '$from_date' AND '$to_date'
                            ");
                        } else {
                            $search_query = mysqli_query($connection, "
                                SELECT 
                                    e.first_name, 
                                    e.last_name, 
                                    l.leave_type, 
                                    l.from_date, 
                                    l.to_date, 
                                    l.leave_status, 
                                    l.message 
                                FROM tbl_employee e
                                INNER JOIN tbl_leaves l ON l.employee_id = e.employee_id 
                                WHERE l.leave_type = '$leave_type' 
                                AND l.employee_id = '$emp_id' 
                                AND DATE(l.from_date) BETWEEN '$from_date' AND '$to_date'
                            ");
                        }

                        while ($row = mysqli_fetch_array($search_query)) {
                    ?>
                            <tr>
                                <td><?php echo $row['first_name'] . " " . $row['last_name']; ?></td>
                                <td><?php echo $row['leave_type']; ?></td>
                                <td><?php echo $row['from_date']; ?></td>
                                <td><?php echo $row['to_date']; ?></td>
                                <td><?php echo $row['leave_status']; ?></td>
                                <td><?php echo $row['message']; ?></td>
                            </tr>
                    <?php 
                        }
                    } else {
                        $fetch_query = mysqli_query($connection, "
                            SELECT 
                                e.first_name, 
                                e.last_name, 
                                l.leave_type, 
                                l.from_date, 
                                l.to_date, 
                                l.leave_status, 
                                l.message 
                            FROM tbl_employee e 
                            INNER JOIN tbl_leaves l ON l.employee_id = e.employee_id
                        ");
                        while ($row = mysqli_fetch_array($fetch_query)) {
                    ?>
                            <tr>
                                <td><?php echo $row['first_name'] . " " . $row['last_name']; ?></td>
                                <td><?php echo $row['leave_type']; ?></td>
                                <td><?php echo $row['from_date']; ?></td>
                                <td><?php echo $row['to_date']; ?></td>
                                <td><?php echo $row['leave_status']; ?></td>
                                <td><?php echo $row['message']; ?></td>
                            </tr>
                    <?php 
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

<script language="JavaScript" type="text/javascript">
function confirmDelete() {
    return confirm('Are you sure want to delete this Leave record?');
}
</script> 
