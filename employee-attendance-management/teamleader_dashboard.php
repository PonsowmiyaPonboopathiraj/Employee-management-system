<?php
session_start();
include('header.php');
include('includes/connection.php');

// Session check for Team Leader
if (!isset($_SESSION['employee_id']) || $_SESSION['role'] != '2') {
    header("Location: login.php");
    exit();
}

// Fetch team info
$employeeName = $_SESSION['employee_name'];
$department = $_SESSION['department'] ?? 'Not Assigned';
$shift = $_SESSION['shift'] ?? 'Not Assigned';
?>
 <div class="page-wrapper">
<!DOCTYPE html>
<html>
<head>
    <title>TL Dashboard - Sentersoft</title>
    <style>
        body {
            font-family: Arial;
            background: #f7f9fc;
            padding: 40px;
        }
        h2 {
            color: #1ab394;
        }
        .dashboard-cards {
            display: flex;
            gap: 20px;
            margin-top: 30px;
        }
        .card {
            background: #fff;
            padding: 20px;
            flex: 1;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            font-weight: bold;
            font-size: 16px;
        }
        .shortcuts {
            margin-top: 30px;
        }
        .shortcuts a {
            display: inline-block;
            margin: 10px;
            padding: 12px 20px;
            background: #1ab394;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
        }
        .alerts {
            margin-top: 30px;
            background: #fff;
            padding: 15px;
            border-radius: 10px;
        }
        .alerts li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>
<body>

    <h2>👋 Welcome, <?php echo htmlspecialchars($employeeName); ?></h2>
    <p>Department: <strong><?php echo htmlspecialchars($department); ?></strong> | Shift: <strong><?php echo htmlspecialchars($shift); ?></strong></p>

    <div class="dashboard-cards">
        <div class="card">Team Members: 12</div>
        <div class="card">Pending Leaves: 3</div>
        <div class="card">Today's Attendance: 9/12</div>
    </div>

    <div class="shortcuts">
        <a href="view_team.php">👥 View Team</a>
        <a href="pending_leaves.php">📩 Approve Leaves</a>
        <a href="attendance_report.php">📊 Attendance Report</a>
        <a href="daily_log.php">📝 Add Work Log</a>
    </div>

    <div class="alerts">
        <ul>
            <li>🔔 John requested leave on June 10 (Pending)</li>
            <li>📌 Daily report pending for 3 team members</li>
        </ul>
    </div>

</body>
</html>
