<?php
session_start();
if (empty($_SESSION['name'])) {
    header('location:index.php');
    exit();
}

include('header.php');
include('includes/connection.php');

$today = date('Y-m-d');

// Fetch employees on leave today
$query = "
SELECT e.first_name, e.last_name, l.from_date, l.to_date, l.reason
FROM tbl_employee e
JOIN tbl_leave_requests l ON l.employee_id = e.employee_id
WHERE CURDATE() BETWEEN l.from_date AND l.to_date
AND l.status = 'Approved'
";


$result = mysqli_query($connection, $query);
?>

<div class="page-wrapper">
    <div class="content">
        <h3>Employees On Leave Today (<?= $today ?>)</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Leave From</th>
                    <th>Leave To</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                            <td><?= htmlspecialchars($row['from_date']) ?></td>
                            <td><?= htmlspecialchars($row['to_date']) ?></td>
                            <td><?= htmlspecialchars($row['reason']) ?></td>

                                
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center">No employees are on leave today.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </div>
</div>

<?php include('footer.php'); ?>
