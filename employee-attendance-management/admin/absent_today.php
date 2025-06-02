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
            <div class="col-sm-6 col-6">
                <h4 class="page-title">Today's Absent Employees</h4>
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
                        <!-- This will be dynamically loaded via AJAX -->
                    </select>
                </div>

                <div class="col-lg-2 d-flex align-items-end">
                    <button type="submit" name="search_absent" class="btn btn-primary w-100">Search</button>
                </div>
            </div>
        </form>

        <!-- Results Table -->
        <div class="table-responsive">
            <table class="datatable table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Department</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $today = date("Y-m-d");
                    $dept = $_POST['department'] ?? 'all';
                    $emp = $_POST['employee'] ?? 'all';

                    $query = "
                        SELECT e.first_name, e.last_name, e.department 
                        FROM tbl_employee e
                        LEFT JOIN tbl_attendance a 
                            ON e.employee_id = a.employee_id 
                            AND DATE(a.date) = '$today'
                        WHERE a.employee_id IS NULL
                    ";

                    if ($dept != 'all') {
                        $query .= " AND e.department = '$dept'";
                    }

                    if ($emp != 'all') {
                        $query .= " AND e.employee_id = '$emp'";
                    }

                    $result = mysqli_query($connection, $query);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_array($result)) {
                            echo "<tr>
                                <td>{$row['first_name']} {$row['last_name']}</td>
                                <td>{$row['department']}</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No absent employees found for the selected criteria!</td></tr>";
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
