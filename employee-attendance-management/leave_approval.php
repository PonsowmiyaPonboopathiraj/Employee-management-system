<?php
session_start();
include('includes/connection.php'); // ✅ FIXED: Correct path

// Optional header
include('header.php'); 

$current_user_id = $_SESSION['employee_id'] ?? '';
$current_user_role = $_SESSION['role'] ?? '';

$whereClause = ($current_user_role == '2') ? 
    "WHERE e.team_leader_id = '$current_user_id'" : "";

if (isset($_POST['update_status'])) {
    $leave_id = $_POST['leave_id'];
    $new_status = $_POST['new_status'];

    $update = mysqli_query($connection, "UPDATE tbl_leave_requests SET status = '$new_status' WHERE leave_id = '$leave_id'");
    $message = $update ? "✅ Leave status updated." : "❌ Update failed.";
}

// Fetch requests
$query = "
    SELECT lr.leave_id, lr.employee_id, e.first_name, e.last_name, e.department,
           lr.leave_type, lr.leave_from, lr.leave_to, lr.reason, lr.status, lr.applied_on
    FROM tbl_leave_requests lr
    JOIN tbl_employee e ON lr.employee_id = e.employee_id
    $whereClause
    ORDER BY lr.applied_on DESC
";
$query = "
    SELECT 
        lr.leave_id, 
        lr.employee_id, 
        e.first_name, 
        e.last_name, 
        e.department,
        lt.leave_type_name AS leave_type, 
        lr.leave_from, 
        lr.leave_to, 
        lr.reason, 
        lr.status, 
        lr.applied_on
    FROM tbl_leave_requests lr
    JOIN tbl_employee e ON lr.employee_id = e.employee_id
    JOIN tbl_leave_types lt ON lr.leave_type_id = lt.leave_type_id
    $whereClause
    ORDER BY lr.applied_on DESC
";

?>

<!DOCTYPE html>
<html>
<head>
    <title>Leave Approval</title>
    <style>
        table {
            width: 95%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }
        th {
            background: #007BFF;
            color: white;
        }
        select, button {
            padding: 5px;
        }
        .msg {
            text-align: center;
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">📝 Leave Approval Requests</h2>

<?php if (!empty($message)) echo "<p class='msg'>$message</p>"; ?>

<table>
    <tr>
        <th>Employee</th>
        <th>Department</th>
        <th>Leave Type</th>
        <th>From</th>
        <th>To</th>
        <th>Reason</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
            <td><?= htmlspecialchars($row['department']) ?></td>
            <td><?= htmlspecialchars($row['leave_type']) ?></td>
            <td><?= htmlspecialchars($row['leave_from']) ?></td>
            <td><?= htmlspecialchars($row['leave_to']) ?></td>
            <td><?= htmlspecialchars($row['reason']) ?></td>
            <td>
                <?php 
                    echo match($row['status']) {
                        '0' => '⏳ Pending',
                        '1' => '✅ Approved',
                        '2' => '❌ Rejected',
                        default => 'Unknown'
                    }; 
                ?>
            </td>
            <td>
                <?php if ($row['status'] == '0'): ?>
                    <form method="post">
                        <input type="hidden" name="leave_id" value="<?= $row['leave_id'] ?>">
                        <select name="new_status" required>
                            <option value="">--Select--</option>
                            <option value="1">Approve</option>
                            <option value="2">Reject</option>
                        </select>
                        <button type="submit" name="update_status">Update</button>
                    </form>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
