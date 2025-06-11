<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('header.php');
include('includes/connection.php');

$employee_id = $_SESSION['employee_id'];
$startDate = '';
$endDate = '';
$recordCount = 0;

// Base query
$query = "
SELECT * FROM (
    SELECT * FROM tbl_attendance 
    WHERE employee_id = '$employee_id'
    ORDER BY id DESC
) AS sub
";

// Apply date filter if available
if (isset($_POST['search'])) {
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];

    if (!empty($startDate) && !empty($endDate)) {
        $query .= " WHERE date BETWEEN '$startDate' AND '$endDate'";
    }
}

// Finally, group to avoid duplicates and order
$query .= " GROUP BY date ORDER BY date DESC";

$result = mysqli_query($connection, $query);
$recordCount = mysqli_num_rows($result);
?>

<div class="page-wrapper">
    <div class="content">
        <h4 class="page-title" style="margin-bottom: 20px;">My Attendance History</h4>

        <!-- Filter form -->
        <form method="POST" class="row mb-4">
            <div class="col-md-3">
                <label>From Date</label>
                <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($startDate) ?>">
            </div>
            <div class="col-md-3">
                <label>To Date</label>
                <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($endDate) ?>">
            </div>
            <div class="col-md-3 align-self-end d-flex gap-2">
                <button type="submit" name="search" class="btn btn-primary">Search</button>
                <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-secondary">Reset</a>
            </div>
            <div class="col-md-3 align-self-end">
                <input type="text" class="form-control" value="Attendance count: <?= $recordCount ?>" readonly>
            </div>
        </form>

        <!-- Attendance table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center">
                <thead class="thead-dark">
                    <tr>
                        <th>Date</th>
                        <th>Check In</th>
                        <th>In Status</th>
                        <th>Check Out</th>
                        <th>Out Status</th>
                        <th>Shift</th>
                        <th>Location</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recordCount > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['date']) ?></td>
                                <td><?= htmlspecialchars($row['check_in']) ?></td>
                                <td><?= htmlspecialchars($row['in_status']) ?></td>
                                <td><?= htmlspecialchars($row['check_out']) ?></td>
                                <td><?= htmlspecialchars($row['out_status']) ?></td>
                                <td><?= htmlspecialchars($row['shift']) ?></td>
                                <td><?= htmlspecialchars($row['location']) ?></td>
                                <td><?= htmlspecialchars($row['message']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">No attendance records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
