<?php
session_start();
include('includes/connection.php');

// Get logged-in employee info
$employee_id = $_SESSION['employee_id'] ?? '';
$emp_query = mysqli_query($connection, "SELECT id, team_leader_id FROM tbl_employee WHERE employee_id = '$employee_id'");
$emp_data = mysqli_fetch_assoc($emp_query);
$employee_db_id = $emp_data['id'];
$team_leader_id = $emp_data['team_leader_id']; // ✅ Fetch TL ID

$message = "";

if (isset($_POST['apply_leave'])) {
    $leave_type = $_POST['leave_type'];
    $leave_from = $_POST['leave_from'];
    $leave_to = $_POST['leave_to'];
    $reason = $_POST['reason'];
    $status = "Pending";

    // Optional: validate dates
    if ($leave_from > $leave_to) {
        $message = "❌ Leave From date cannot be after Leave To.";
    } else {
        $insert = mysqli_query($connection, 
            "INSERT INTO tbl_leave_requests 
            (employee_id, leave_type, leave_from, leave_to, reason, status, team_leader_id) 
            VALUES 
            ('$employee_db_id', '$leave_type', '$leave_from', '$leave_to', '$reason', '$status', '$team_leader_id')"
        );

        if ($insert) {
            $message = "✅ Leave request sent to your Team Leader.";
        } else {
            $message = "❌ Error submitting leave request.";
        }
    }
}
?>

<!-- Leave Apply Form HTML -->
<form method="POST">
    <h3>Apply for Leave</h3>
    <?php if (!empty($message)) echo "<p style='color:red;'>$message</p>"; ?>
    
    <label>Leave Type:</label>
    <select name="leave_type" required>
        <option value="">--Select--</option>
        <option value="Casual Leave">Casual Leave</option>
        <option value="Sick Leave">Sick Leave</option>
        <option value="Paid Leave">Paid Leave</option>
    </select><br><br>

    <label>From:</label>
    <input type="date" name="leave_from" required><br><br>

    <label>To:</label>
    <input type="date" name="leave_to" required><br><br>

    <label>Reason:</label>
    <textarea name="reason" rows="4" required></textarea><br><br>

    <input type="submit" name="apply_leave" value="Apply Leave">
</form>
