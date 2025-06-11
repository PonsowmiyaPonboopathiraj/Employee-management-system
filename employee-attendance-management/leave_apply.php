<?php
session_start();
include('header.php');
include('includes/connection.php');



if (!isset($_SESSION['employee_id'])) {
    echo "<p>Please login to apply for leave.</p>";
    exit();
}

$employee_id = $_SESSION['employee_id'];
$message = "";

// Fetch leave types
$leave_types_result = mysqli_query($connection, "SELECT * FROM tbl_leave_type");

// Get logged in user's team_leader_id
$user_query = mysqli_query($connection, "SELECT team_leader_id FROM tbl_employee WHERE employee_id = '$employee_id'");
$user_data = mysqli_fetch_assoc($user_query);
$team_leader_id = $user_data['team_leader_id'] ?? null;

// Handle leave form submit
if (isset($_POST['submit'])) {
    $leave_type_id = $_POST['leave_type'];
    $leave_from = $_POST['leave_from'];
    $leave_to = $_POST['leave_to'];
    $reason = $_POST['reason'];

    // Calculate leave duration excluding Sundays
    $from = new DateTime($leave_from);
    $to = new DateTime($leave_to);
    $interval = $from->diff($to)->days + 1;

    $leave_days = 0;
    for ($i = 0; $i < $interval; $i++) {
        $day = clone $from;
        $day->modify("+$i day");
        if ($day->format('N') != 7) { // N=7 => Sunday
            $leave_days++;
        }
    }

    // Get max allowed days for selected leave type
    $max_query = mysqli_query($connection, "SELECT number_of_days FROM tbl_leave_type WHERE leave_type_id = '$leave_type_id'");
    $max_data = mysqli_fetch_assoc($max_query);
    $max_days = $max_data['number_of_days'] ?? 0;

    if ($leave_days > $max_days) {
        $message = "❌ Leave exceeds max limit of $max_days days.";
    } else {
        $insert = mysqli_query($connection, "INSERT INTO tbl_leave_requests (employee_id, leave_type, leave_from, leave_to, reason, leave_duration, team_leader_id, status) 
            VALUES ('$employee_id', '$leave_type_id', '$leave_from', '$leave_to', '$reason', '$leave_days', '$team_leader_id', 'Pending')");

        if ($insert) {
            $message = "✅ Leave applied successfully.";
        } else {
            $message = "❌ Failed to apply leave.";
        }
    }
}
?>
<div class="page-wrapper">
<!DOCTYPE html>
<html>
<head>
    <title>Apply for Leave</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f6f9;
            padding: 30px;
        }
        form {
            background: #fff;
            padding: 20px;
            width: 450px;
            margin: auto;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin-top: 15px;
        }
        input, select, textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }
        .btn {
            background: #007bff;
            color: white;
            padding: 10px;
            margin-top: 20px;
            border: none;
            width: 100%;
        }
        .message {
            text-align: center;
            margin-top: 15px;
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>📝 Apply for Leave</h2>
<form method="POST">
    <?php if (!empty($message)) echo "<div class='message'>$message</div>"; ?>

    <label>Leave Type</label>
    <select name="leave_type" required>
        <option value="">-- Select --</option>
        <?php while ($type = mysqli_fetch_assoc($leave_types_result)): ?>
            <option value="<?= $type['leave_type_id'] ?>">
                <?= htmlspecialchars($type['leave_type_name']) ?> (Max <?= $type['number_of_days'] ?> days)
            </option>
        <?php endwhile; ?>
    </select>

    <label>Leave From</label>
    <input type="date" name="leave_from" required>

    <label>Leave To</label>
    <input type="date" name="leave_to" required>

    <label>Reason</label>
    <textarea name="reason" rows="3" required></textarea>

    <button type="submit" name="submit" class="btn">Submit Leave Request</button>
</form>

</body>
</html>
