<?php
session_start();
if (empty($_SESSION['name'])) {
    header('location:index.php');
    exit();
}

include('header.php');
include('includes/connection.php');

$today = date('Y-m-d');

// Fetch employees who are on leave today
$query = "
    SELECT e.employee_id, e.first_name, e.last_name, e.emailid, l.from_date, l.to_date
    FROM tbl_employee e
    JOIN tbl_leave_requests l ON e.id = l.employee_id
    WHERE l.status = 1
    AND '$today' BETWEEN l.from_date AND l.to_date
    AND e.status = 1
    AND e.role = 0
    ORDER BY l.from_date ASC
";
$result = mysqli_query($connection, $query);
?>

<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="page-title">Employees on Leave Today (<?= $today ?>)</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <div class="card-block">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Employee ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>
                                                    <td>{$count}</td>
                                                    <td>{$row['employee_id']}</td>
                                                    <td>{$row['first_name']} {$row['last_name']}</td>
                                                    <td>{$row['emailid']}</td>
                                                    <td>{$row['from_date']}</td>
                                                    <td>{$row['to_date']}</td>
                                                </tr>";
                                            $count++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center'>No employees on leave today.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
