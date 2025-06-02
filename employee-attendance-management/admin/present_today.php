<?php
session_start();
if (empty($_SESSION['name'])) {
    header('location:index.php');
}
include('header.php');
include('includes/connection.php');

// Handle deletion request
if (isset($_POST['delete_attendance'])) {
    $delete_id = $_POST['delete_id'];
    $delete_query = "DELETE FROM tbl_attendance WHERE id = $delete_id";
    if (mysqli_query($connection, $delete_query)) {
        echo "<script>alert('Attendance record deleted successfully!'); window.location.href=window.location.href;</script>";
    } else {
        echo "<script>alert('Error deleting record.');</script>";
    }
}
?>

<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-sm-6 col-6">
                <h4 class="page-title">Today's Present Employees</h4>
            </div>
        </div>

        <!-- Search Filter -->
        <form method="post">
            <div class="form-group row" style="padding: 20px;">
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

                <div class="col-lg-3">
                    <select class="form-control" id="employee" name="employee" required>
                        <option value="all">All Employees</option>
                        <!-- AJAX will fill this dynamically -->
                    </select>
                </div>

                <div class="col-lg-2 d-flex align-items-end">
                    <button type="submit" name="search_present" class="btn btn-primary w-100">Search</button>
                </div>
            </div>
        </form>

        <!-- Present Employee Table -->
        <div class="table-responsive">
            <table class="datatable table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Date</th>
                        <th>Shift</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $today = date("Y-m-d");
                    $dept = $_POST['department'] ?? 'all';
                    $emp = $_POST['employee'] ?? 'all';

                    $where = "WHERE DATE(a.date) = '$today'";

                    if ($dept != 'all') {
                        $where .= " AND e.department = '$dept'";
                    }

                    if ($emp != 'all') {
                        $where .= " AND e.employee_id = '$emp'";
                    }

                    $query = "
                        SELECT a.id, e.first_name, e.last_name, e.department,
                               a.date, a.shift, a.check_in, a.check_out, a.in_status 
                        FROM tbl_employee e
                        INNER JOIN tbl_attendance a 
                            ON e.employee_id = a.employee_id
                        $where
                        ORDER BY a.date DESC
                    ";

                    $result = mysqli_query($connection, $query);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_array($result)) {
                            echo "<tr>
                                <td>{$row['first_name']} {$row['last_name']}</td>
                                <td>{$row['department']}</td>
                                <td>{$row['date']}</td>
                                <td>{$row['shift']}</td>
                                <td>{$row['check_in']}</td>
                                <td>{$row['check_out']}</td>
                                <td>{$row['in_status']}</td>
                                <td>
                                    <form method='post' onsubmit='return confirm(\"Are you sure you want to delete this attendance record?\");'>
                                        <input type='hidden' name='delete_id' value='{$row['id']}'>
                                        <button type='submit' name='delete_attendance' class='btn btn-danger btn-sm'>Delete</button>
                                    </form>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No present employees found for the selected criteria!</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

<!-- AJAX to load employee list dynamically -->
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
