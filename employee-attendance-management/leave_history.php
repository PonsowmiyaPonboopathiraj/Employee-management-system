<?php
session_start();

if (!isset($_SESSION['employee_id']) || empty($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

include('header.php');
include('includes/connection.php');

$employee_id = $_SESSION['employee_id'];

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $checkSql = "SELECT status FROM tbl_leave_requests WHERE leave_id = ? AND employee_id = ?";
    $stmt = mysqli_prepare($connection, $checkSql);
    mysqli_stmt_bind_param($stmt, "is", $delete_id, $employee_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        if ($row['status'] === 'Pending') {
            $delSql = "DELETE FROM tbl_leave_requests WHERE leave_id = ? AND employee_id = ?";
            $delStmt = mysqli_prepare($connection, $delSql);
            mysqli_stmt_bind_param($delStmt, "is", $delete_id, $employee_id);
            mysqli_stmt_execute($delStmt);
            $_SESSION['msg'] = (mysqli_stmt_affected_rows($delStmt) > 0) ? 
                "Leave request deleted successfully." : "Failed to delete leave request.";
            mysqli_stmt_close($delStmt);
        } else {
            $_SESSION['msg'] = "Only pending leaves can be deleted.";
        }
    } else {
        $_SESSION['msg'] = "Leave request not found.";
    }
    mysqli_stmt_close($stmt);
    header("Location: leave_history.php");
    exit();
}

$startDate = $_GET['start_date'] ?? '';
$endDate   = $_GET['end_date'] ?? '';

$query = "SELECT r.*, t.leave_type_name 
          FROM tbl_leave_requests r 
          JOIN tbl_leave_type t ON r.leave_type_id = t.leave_type_id 
          WHERE r.employee_id = ?";

if (!empty($startDate) && !empty($endDate)) {
    $query .= " AND r.from_date >= ? AND r.to_date <= ?";
}

$query .= " ORDER BY r.leave_id DESC";

$stmt = mysqli_prepare($connection, $query);
if (!empty($startDate) && !empty($endDate)) {
    mysqli_stmt_bind_param($stmt, "sss", $employee_id, $startDate, $endDate);
} else {
    mysqli_stmt_bind_param($stmt, "s", $employee_id);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$msg = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);

// Counters
$totalApproved = 0;
$totalApprovedDuration = 0;
$totalRejected = 0;
$totalRejectedDuration = 0;

?>

<div class="page-wrapper">
    <div class="content">
        <div class="row mb-3">
            <div class="col-md-12">
                <h4 class="page-title">My Leave History</h4>
                <?php if ($msg): ?>
                    <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" class="row g-3 mb-3">
            <div class="col-md-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="<?= htmlspecialchars($startDate) ?>">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="<?= htmlspecialchars($endDate) ?>">
            </div>
            <div class="col-md-3 align-self-end d-flex gap-2">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="leave_history.php" class="btn btn-secondary">Reset</a>
            </div>
            <div class="col-md-3 align-self-end">
                <input type="text" class="form-control" id="searchInput" placeholder="Search Reason / Type">
            </div>
        </form>

        <!-- Summary Boxes -->
        <div class="row mb-3">
            <div class="col-md-3 offset-md-6">
                <div class="card border-success">
                    <div class="card-body p-2">
                        <h6 class="mb-1 text-success">Approved Summary</h6>
                        <p class="mb-0">Total Approved: <strong id="totalApproved">0</strong></p>
                        <p class="mb-0">Total Days: <strong id="totalApprovedDuration">0</strong></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-danger">
                    <div class="card-body p-2">
                        <h6 class="mb-1 text-danger">Rejected Summary</h6>
                        <p class="mb-0">Total Rejected: <strong id="totalRejected">0</strong></p>
                        <p class="mb-0">Total Days: <strong id="totalRejectedDuration">0</strong></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leave Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="leaveTable">
                <thead>
                    <tr>
                        <th>Leave Type</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Duration (Days)</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <?php
                            $from = new DateTime($row['from_date']);
                            $to = new DateTime($row['to_date']);
                            $duration = $from->diff($to)->days + 1;

                            if ($row['status'] === 'Approved') {
                                $totalApproved++;
                                $totalApprovedDuration += $duration;
                            } elseif ($row['status'] === 'Rejected') {
                                $totalRejected++;
                                $totalRejectedDuration += $duration;
                            }
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($row['leave_type_name']) ?></td>
                            <td><?= htmlspecialchars($row['from_date']) ?></td>
                            <td><?= htmlspecialchars($row['to_date']) ?></td>
                            <td><?= $duration ?></td>
                            <td><?= htmlspecialchars($row['reason']) ?></td>
                            <td>
                                <?php
                                $status = $row['status'];
                                if ($status == 'Approved') {
                                    echo "<span class='badge bg-success'>Approved</span>";
                                } elseif ($status == 'Rejected') {
                                    echo "<span class='badge bg-danger'>Rejected</span>";
                                } else {
                                    echo "<span class='badge bg-warning text-dark'>Pending</span>";
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($status === 'Pending'): ?>
                                    <a href="leave_history.php?delete_id=<?= $row['leave_id'] ?>"
                                       onclick="return confirm('Are you sure you want to delete this pending leave request?');"
                                       class="btn btn-sm btn-danger">Delete</a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No leave records found.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
document.getElementById("searchInput").addEventListener("keyup", function () {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("#leaveTable tbody tr");
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});

document.getElementById("totalApproved").innerText = "<?= $totalApproved ?>";
document.getElementById("totalApprovedDuration").innerText = "<?= $totalApprovedDuration ?>";
document.getElementById("totalRejected").innerText = "<?= $totalRejected ?>";
document.getElementById("totalRejectedDuration").innerText = "<?= $totalRejectedDuration ?>";
</script>

<?php include('footer.php'); ?>
