<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


include('header.php');
include('includes/connection.php');

$employee_id = $_SESSION['employee_id'];

// Default query
$query = "SELECT * FROM tbl_attendance WHERE employee_id = '$employee_id'";

// Filter by date range
if (isset($_POST['search'])) {
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];

    if (!empty($startDate) && !empty($endDate)) {
        $query .= " AND date BETWEEN '$startDate' AND '$endDate'";
    }
}

// Sort by latest date
$query .= " ORDER BY date DESC";

$result = mysqli_query($connection, $query);
?>

<div class="page-wrapper">
    <div class="content">
        <h4 class="page-title" style="margin-bottom: 20px;">My Attendance History</h4>

        <!-- Filter form -->
        <form method="POST" class="row mb-4">
            <div class="col-md-3">
                <label>From Date</label>
                <input type="date" name="start_date" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label>To Date</label>
                <input type="date" name="end_date" class="form-control" required>
            </div>
            <div class="col-md-3 align-self-end">
                <button type="submit" name="search" class="btn btn-primary">Search</button>
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
                    <?php if (mysqli_num_rows($result) > 0): ?>
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
