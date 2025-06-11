<?php
session_start();
include('includes/connection.php');
include('header.php');

$team_leader_id = $_SESSION['id'] ?? null;

if (!$team_leader_id) {
    echo "<p style='color:red;'>Unauthorized access. Please login first.</p>";
    exit();
}

// ✅ Added emailid to SELECT query
$query = "SELECT id, first_name, last_name, username, emailid, phone, department, shift, status 
          FROM tbl_employee 
          WHERE team_leader_id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $team_leader_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<div class="page-wrapper">
<!DOCTYPE html>
<html>
<head>
    <title>My Team</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        table {
            width: 95%;
            margin: 20px auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .status-active {
            color: green;
            font-weight: bold;
        }

        .status-inactive {
            color: red;
            font-weight: bold;
        }

        .no-team {
            text-align: center;
            font-size: 18px;
            margin-top: 50px;
            color: #666;
        }
    </style>
</head>
<body>

<h2>👥 My Team Members</h2>

<?php if ($result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>#ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th> <!-- ✅ Email column added -->
                <th>Phone</th>
                <th>Department</th>
                <th>Shift</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['emailid']) ?></td> <!-- ✅ Show email -->
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['department']) ?></td>
                    <td><?= htmlspecialchars($row['shift']) ?></td>
                    <td class="<?= $row['status'] == 1 ? 'status-active' : 'status-inactive' ?>">
                        <?= $row['status'] == 1 ? 'Active' : 'Inactive' ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="no-team">🙁 No team members are currently assigned to you.</div>
<?php endif; ?>

</body>
</html>
