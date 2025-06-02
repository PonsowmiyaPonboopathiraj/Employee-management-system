<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['employee_id']) || empty($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

include('header.php');
include('includes/connection.php');

$employee_id = $_SESSION['employee_id'];

// Handle Delete action
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    // Check if leave belongs to employee and is pending before deleting
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
            if (mysqli_stmt_affected_rows($delStmt) > 0) {
                $_SESSION['msg'] = "Leave request deleted successfully.";
            } else {
                $_SESSION['msg'] = "Failed to delete leave request.";
            }
            mysqli_stmt_close($delStmt);
        } else {
            $_SESSION['msg'] = "Only pending leaves can be deleted.";
        }
    } else {
        $_SESSION['msg'] = "Leave request not found.";
    }
    mysqli_stmt_close($stmt);

    // Redirect to avoid resubmission and clear GET params
    header("Location: leave_history.php");
    exit();
}

// Date filter handling
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate   = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Base query with JOIN to get leave_type_name
$query = "SELECT r.*, t.leave_type_name 
          FROM tbl_leave_requests r 
          JOIN tbl_leave_type t ON r.leave_type_id = t.leave_type_id 
          WHERE r.employee_id = ?";

// Filter by date range if selected
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

// Show message if any
$msg = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);
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
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="<?= htmlspecialchars($startDate) ?>">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="<?= htmlspecialchars($endDate) ?>">
            </div>
            <div class="col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="leave_history.php" class="btn btn-secondary">Reset</a>
            </div>
            <div class="col-md-3 align-self-end">
                <input type="text" class="form-control" id="searchInput" placeholder="Search Reason / Type">
            </div>
        </form>

        <!-- Leave History Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="leaveTable">
                <thead>
                    <tr>
                        <th>Leave Type</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Action</th> <!-- New Delete column -->
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['leave_type_name']) ?></td>
                                <td><?= htmlspecialchars($row['from_date']) ?></td>
                                <td><?= htmlspecialchars($row['to_date']) ?></td>
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
                            <td colspan="6" class="text-center">No leave records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Search Filter Script -->
<script>
document.getElementById("searchInput").addEventListener("keyup", function () {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("#leaveTable tbody tr");

    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});
</script>

<?php include('footer.php'); ?>
