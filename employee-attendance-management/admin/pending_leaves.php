<?php
session_start();
if (empty($_SESSION['name']) || $_SESSION['role'] != 1) {
    header('Location: index.php');
    exit();
}

include('header.php');
include('includes/connection.php');

// Handle status update
if (isset($_POST['update_status'])) {
    $leave_id = intval($_POST['leave_id']);
    $new_status = $_POST['new_status'] === 'Approved' ? 'Approved' : 'Rejected';

    $stmt = mysqli_prepare($connection, "UPDATE tbl_leave_requests SET status = ? WHERE leave_id = ?");
    mysqli_stmt_bind_param($stmt, "si", $new_status, $leave_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch pending leave requests
$sql = "SELECT lr.*, e.username AS employee_name, lt.leave_type_name 
        FROM tbl_leave_requests lr
        JOIN tbl_employee e ON lr.employee_id = e.employee_id
        JOIN tbl_leave_type lt ON lr.leave_type_id = lt.leave_type_id
        WHERE lr.status = 'Pending'
        ORDER BY lr.leave_id DESC";
$result = mysqli_query($connection, $sql);

// Function to calculate duration excluding Sundays
function calculateDaysExcludingSunday($from, $to) {
    $start = new DateTime($from);
    $end = new DateTime($to);
    $end->modify('+1 day'); // include end date
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($start, $interval, $end);
    $count = 0;
    foreach ($period as $dt) {
        if ($dt->format('w') != 0) { // Exclude Sundays
            $count++;
        }
    }
    return $count;
}
?>
<div class="page-wrapper">
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Pending Leave Requests</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        body { background-color: #f8f9fa; }
        .sidebar a {
            text-decoration: none !important;
        }
        .sidebar a:hover {
            text-decoration: none !important;
        }
        .page-header {
            margin: 30px 0 20px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
        }
        .search-box { width: 280px; }
        .table-container {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            overflow-x: auto;
        }
        table { min-width: 800px; }
        .status-pending { color: orange; font-weight: bold; }
        @media print {
            .search-box, .print-btn, .navbar, .sidebar, .page-header, form button {
                display: none !important;
            }
        }
    </style>
    <script>
        function filterByName() {
            const input = document.getElementById("searchInput").value.toUpperCase();
            const rows = document.getElementById("leaveTableBody").getElementsByTagName("tr");
            for (let i = 0; i < rows.length; i++) {
                const name = rows[i].getElementsByTagName("td")[1];
                if (name) {
                    const text = name.textContent || name.innerText;
                    rows[i].style.display = text.toUpperCase().includes(input) ? "" : "none";
                }
            }
        }
        function printTable() {
            window.print();
        }
    </script>
</head>
<body>
<div class="container mt-4">
    <div class="page-header">
        <h2>Pending Leave Requests</h2>
        <div class="d-flex gap-2">
            <input
                type="text"
                id="searchInput"
                class="form-control search-box"
                placeholder="Search by Employee Name"
                onkeyup="filterByName()"
            />
            <button onclick="printTable()" class="btn btn-outline-warning print-btn">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>
    <div class="table-container">
        <table class="table table-bordered table-hover text-center">
            <thead class="table-warning">
                <tr>
                    <th>S.No</th>
                    <th>Employee Name</th>
                    <th>Leave Type</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Duration (Days)</th>
                    <th>Day</th>
                    <th>Reason</th>
                    <th>Status / Action</th>
                </tr>
            </thead>
            <tbody id="leaveTableBody">
                <?php
                $sn = 1;
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $from = $row['from_date'];
                        $to = $row['to_date'];
                        $duration = calculateDaysExcludingSunday($from, $to);

                        $fromDay = date('l', strtotime($from));
                        $toDay = date('l', strtotime($to));
                        $dayRange = $fromDay . " to " . $toDay;

                        echo "<tr>";
                        echo "<td>{$sn}</td>";
                        echo "<td>" . htmlspecialchars($row['employee_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['leave_type_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($from) . "</td>";
                        echo "<td>" . htmlspecialchars($to) . "</td>";
                        echo "<td>" . $duration . "</td>";
                        echo "<td>" . $dayRange . "</td>";
                        echo "<td>" . htmlspecialchars($row['reason']) . "</td>";
                        echo "<td>
                            <span class='status-pending'>" . htmlspecialchars($row['status']) . "</span><br/><br/>
                            <form method='POST' style='display:inline-block; margin-right:5px;'>
                                <input type='hidden' name='leave_id' value='" . (int)$row['leave_id'] . "' />
                                <input type='hidden' name='new_status' value='Approved' />
                                <button type='submit' name='update_status' class='btn btn-success btn-sm' onclick='return confirm(\"Approve this leave request?\")'>Approve</button>
                            </form>
                            <form method='POST' style='display:inline-block;'>
                                <input type='hidden' name='leave_id' value='" . (int)$row['leave_id'] . "' />
                                <input type='hidden' name='new_status' value='Rejected' />
                                <button type='submit' name='update_status' class='btn btn-danger btn-sm' onclick='return confirm(\"Reject this leave request?\")'>Reject</button>
                            </form>
                        </td>";
                        echo "</tr>";
                        $sn++;
                    }
                } else {
                    echo "<tr><td colspan='9'>No pending leave requests found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
