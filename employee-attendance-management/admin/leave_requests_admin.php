<?php
session_start();
include('header.php');
include('includes/connection.php');

$message = "";

// Update leave status
if (isset($_POST['update_status'])) {
    $request_id = $_POST['request_id'];
    $new_status = $_POST['new_status'];

    $sql = "UPDATE tbl_leave_requests SET status = ? WHERE leave_id = ?";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "si", $new_status, $request_id);
    if (mysqli_stmt_execute($stmt)) {
        $message = "✅ Status updated successfully!";
    } else {
        $message = "❌ Failed to update status!";
    }
    mysqli_stmt_close($stmt);
}

// Delete leave request
if (isset($_POST['delete_request'])) {
    $request_id = $_POST['request_id'];
    $sql = "DELETE FROM tbl_leave_requests WHERE leave_id = ?";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "i", $request_id);
    if (mysqli_stmt_execute($stmt)) {
        $message = "🗑️ Leave request deleted!";
    } else {
        $message = "❌ Failed to delete leave request!";
    }
    mysqli_stmt_close($stmt);
}

// Filter logic
$today = date('Y-m-d');
$filter = $_GET['filter'] ?? 'all';

if ($filter === 'today') {
    $sql = "SELECT lr.*, e.username AS employee_name, lt.leave_type_name 
            FROM tbl_leave_requests lr
            JOIN tbl_employee e ON lr.employee_id = e.employee_id
            JOIN tbl_leave_type lt ON lr.leave_type_id = lt.leave_type_id
            WHERE lr.status = 'Approved' AND lr.from_date <= ? AND lr.to_date >= ?
            ORDER BY lr.leave_id DESC";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $today, $today);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $sql = "SELECT lr.*, e.username AS employee_name, lt.leave_type_name 
            FROM tbl_leave_requests lr
            JOIN tbl_employee e ON lr.employee_id = e.employee_id
            JOIN tbl_leave_type lt ON lr.leave_type_id = lt.leave_type_id
            ORDER BY lr.leave_id DESC";
    $result = mysqli_query($connection, $sql);
}

// Function to calculate duration excluding Sundays
function calculateDurationExcludeSundays($fromDateStr, $toDateStr) {
    $fromDate = new DateTime($fromDateStr);
    $toDate = new DateTime($toDateStr);
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($fromDate, $interval, $toDate->add($interval)); // include end date
    
    $daysCount = 0;
    foreach ($period as $date) {
        // Skip Sundays (0 = Sunday)
        if ($date->format('w') != 0) {
            $daysCount++;
        }
    }
    return $daysCount;
}

// Function to get day name from date string
function getDayName($dateStr) {
    $date = new DateTime($dateStr);
    return $date->format('l'); // Full day name, e.g. Monday
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave Requests - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background-color: #f8f9fa; }

        .content-wrapper {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        .header-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            text-align: center;
        }

        .search-print-container {
            margin-top: 20px;
            display: flex;
            justify-content: flex-start;
            gap: 10px;
            flex-wrap: wrap;
        }

        .search-print-container input[type="text"] {
            width: 250px;
        }

        .table-container {
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            padding: 20px;
            overflow-x: auto;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #dee2e6;
            vertical-align: middle;
        }

        th {
            background-color: #1ab394;
            color: white;
        }

        .success { color: green; }
        .error { color: red; }

        @media (max-width: 576px) {
            .header-section h2 { font-size: 20px; }
            .search-print-container {
                flex-direction: column;
                align-items: flex-start;
            }
            .search-print-container input[type="text"] {
                width: 100%;
            }
            .table-container table {
                font-size: 13px;
            }
        }

        @media print {
            body *:not(#leaveTable):not(#leaveTable *) {
                visibility: hidden;
            }
            #leaveTable, #leaveTable * {
                visibility: visible;
            }
            #leaveTable {
                position: absolute;
                left: 0;
                top: 0;
            }
        }
        
        .sidebar a {
            text-decoration: none !important;
        }

        .sidebar a:hover {
            text-decoration: none !important;
        }
    </style>

    <script>
        function filterByEmployeeName() {
            const input = document.getElementById("searchInput").value.toUpperCase();
            const rows = document.getElementById("leaveTableBody").getElementsByTagName("tr");
            for (let i = 0; i < rows.length; i++) {
                const empName = rows[i].getElementsByTagName("td")[1];
                const nameText = empName?.textContent || empName?.innerText || '';
                rows[i].style.display = nameText.toUpperCase().includes(input) ? "" : "none";
            }
        }

        function confirmDelete() {
            return confirm("Are you sure you want to delete this leave request?");
        }

        function printTable() {
            window.print();
        }
    </script>
</head>
<body>
<div class="page-wrapper">
    <div class="content-wrapper">
        <div class="header-section">
            <h2>📝 Leave Requests</h2>
        </div>

        <div class="search-print-container">
            <input type="text" class="form-control" id="searchInput" onkeyup="filterByEmployeeName()" placeholder="🔍 Search by Employee Name">
            <button class="btn btn-outline-dark" onclick="printTable()">🖨️ Print Table</button>
        </div>

        <?php if ($message): ?>
            <p class="mt-3 text-center <?= strpos($message, '✅') !== false || strpos($message, '🗑️') !== false ? 'success' : 'error'; ?>">
                <?= $message; ?>
            </p>
        <?php endif; ?>

        <div class="table-container" id="leaveTable">
            <table>
                <thead>
                <tr>
                    <th>Leave ID</th>
                    <th>Employee Name</th>
                    <th>Leave Type</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Duration (Days)</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Update</th>
                    <th>Delete</th>
                </tr>
                </thead>
                <tbody id="leaveTableBody">
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)):

                        // Calculate duration excluding Sundays
                        $duration = calculateDurationExcludeSundays($row['from_date'], $row['to_date']);

                        // Get day names for from and to dates
                        $fromDayName = getDayName($row['from_date']);
                        $toDayName = getDayName($row['to_date']);
                    ?>
                        <tr>
                            <td><?= $row['leave_id']; ?></td>
                            <td><?= htmlspecialchars($row['employee_name']); ?></td>
                            <td><?= htmlspecialchars($row['leave_type_name']); ?></td>
                            <td><?= htmlspecialchars($row['from_date']) . " <br><small>(" . $fromDayName . ")</small>"; ?></td>
                            <td><?= htmlspecialchars($row['to_date']) . " <br><small>(" . $toDayName . ")</small>"; ?></td>
                            <td><?= $duration; ?></td>
                            <td><?= htmlspecialchars($row['reason']); ?></td>
                            <td style="font-weight:bold; color:
                                <?php
                                    if ($row['status'] == 'Pending') echo 'orange';
                                    else if ($row['status'] == 'Approved') echo 'green';
                                    else if ($row['status'] == 'Rejected') echo 'red';
                                    else echo 'black';
                                ?>">
                                <?= htmlspecialchars($row['status']); ?>
                            </td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="request_id" value="<?= $row['leave_id']; ?>">
                                    <select name="new_status" required class="form-select form-select-sm mb-1">
                                        <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Approved" <?= $row['status'] == 'Approved' ? 'selected' : ''; ?>>Approved</option>
                                        <option value="Rejected" <?= $row['status'] == 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                                    </select>
                                    <input type="submit" name="update_status" value="Update" class="btn btn-sm btn-primary">
                                </form>
                            </td>
                            <td>
                                <form method="POST" action="" onsubmit="return confirmDelete();">
                                    <input type="hidden" name="request_id" value="<?= $row['leave_id']; ?>">
                                    <input type="submit" name="delete_request" value="Delete" class="btn btn-sm btn-danger">
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="10" class="text-center">No leave requests found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
