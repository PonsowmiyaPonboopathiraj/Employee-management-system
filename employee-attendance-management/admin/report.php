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
        <div class="row align-items-center">
            <div class="col-sm-6 col-6">
                <h4 class="page-title">Attendance Report</h4>
            </div>
            <div class="col-sm-6 col-6 text-right">
                <a href="attendance_history.php" class="btn btn-outline-primary">
                    <i class="fa fa-history"></i> History
                </a>
            </div>
        </div>
        <div class="col-sm-8 col-8 text-right m-b-20">
    <a href="present_today.php" class="btn btn-success float-right">
        <i class="fa fa-users"></i> Today Present
    </a>
</div>


        <form method="post">
            <div class="form-group row" style="padding: 20px;">
                <label class="col-lg-0 col-form-label-report" for="from">From</label>
                <div class="col-lg-3">
                    <input type="date" class="form-control" name="from_date" required>
                </div>

                <label class="col-lg-0 col-form-label" for="to">To</label>
                <div class="col-lg-3">
                    <input type="date" class="form-control" name="to_date" required>
                </div>

                <div class="col-lg-3">
                    <select class="form-control" id="department" name="department" required>
                        <option value="all">All Departments</option>
                        <?php 
                        $fetch_department = mysqli_query($connection, "SELECT * FROM tbl_department");
                        while ($row = mysqli_fetch_array($fetch_department)) {
                            echo "<option value='{$row['department_name']}'>{$row['department_id']} - {$row['department_name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-lg-3" style="margin-top: 15px;">
                    <select class="form-control" id="employee" name="employee" required>
                        <option value="all">All Employees</option>
                    </select>
                </div>

                <div class="col-lg-2 d-flex align-items-end">
                    <button type="submit" name="srh-btn" class="btn btn-primary w-100">Search</button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="datatable table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Date</th>
                        <th>Shift</th>
                        <th>Check In</th>
                        <th>Notes</th>
                        <th>In Status</th>
                        <th>Check Out</th>
                        <th>Out Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $today = date("Y-m-d");

                    if (isset($_POST['srh-btn'])) {
                        $from = $_POST['from_date'];
                        $to = $_POST['to_date'];
                        $dept = $_POST['department'];
                        $emp = $_POST['employee'];

                        $where = "WHERE DATE(a.date) BETWEEN '$from' AND '$to'";

                        if ($dept != 'all') {
                            $where .= " AND e.department = '$dept'";
                        }

                        if ($emp != 'all') {
                            $where .= " AND e.employee_id = '$emp'";
                        }

                        $query = "
                            SELECT 
                                e.first_name, e.last_name, e.department, 
                                a.date, a.shift, a.check_in, a.message, 
                                a.in_status, a.check_out, a.out_status 
                            FROM tbl_employee e 
                            INNER JOIN tbl_attendance a ON a.employee_id = e.employee_id 
                            $where
                            ORDER BY 
                                CASE WHEN DATE(a.date) = '$today' THEN 0 ELSE 1 END,
                                a.date DESC
                        ";
                    } else {
                        $query = "
                            SELECT 
                                e.first_name, e.last_name, e.department, 
                                a.date, a.shift, a.check_in, a.message, 
                                a.in_status, a.check_out, a.out_status 
                            FROM tbl_employee e 
                            INNER JOIN tbl_attendance a ON a.employee_id = e.employee_id 
                            ORDER BY 
                                CASE WHEN DATE(a.date) = '$today' THEN 0 ELSE 1 END,
                                a.date DESC
                        ";
                    }

                    $result = mysqli_query($connection, $query);
                    while ($row = mysqli_fetch_array($result)) {
                        echo "<tr>
                                <td>{$row['first_name']} {$row['last_name']}</td>
                                <td>{$row['department']}</td>
                                <td>{$row['date']}</td>
                                <td>{$row['shift']}</td>
                                <td>{$row['check_in']}</td>
                                <td>{$row['message']}</td>
                                <td>{$row['in_status']}</td>
                                <td>{$row['check_out']}</td>
                                <td>{$row['out_status']}</td>
                            </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

<!-- AJAX for Dynamic Employee List -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#department').on('change', function () {
    var department = $(this).val();
    $.ajax({
        url: 'get_employees.php',
        type: 'POST',
        data: { department: department },
        success: function (data) {
            $('#employee').html(data);
        }
    });
});
</script>
