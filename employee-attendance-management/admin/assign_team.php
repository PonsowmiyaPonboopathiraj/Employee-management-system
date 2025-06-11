<?php
session_start();
include('../includes/connection.php'); // Correct relative path
include('header.php');

// Fetch all Team Leaders
$team_leaders = mysqli_query($connection, "SELECT id, first_name, last_name FROM tbl_employee WHERE role = '2'");

// Fetch all Employees without Team Leader
$team_members = mysqli_query($connection, "SELECT id, first_name, last_name FROM tbl_employee WHERE role = '0' AND team_leader_id IS NULL");
?>
<div class="page-wrapper">
<!DOCTYPE html>
<html>
<head>
    <title>Assign Team</title>
    <style>
        body { font-family: Arial; background-color: #f4f6f9; padding: 30px; }
        h2 { text-align: center; color: #333; }
        .form-container {
            background: white;
            max-width: 600px;
            margin: auto;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        select, input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 15px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Assign Team Members to Team Leader</h2>
    <form method="post" action="assign_team_action.php">
        <label>Choose Team Leader:</label>
        <select name="team_leader_id" required>
            <option value="" disabled selected>Select Team Leader</option>
            <?php while ($tl = mysqli_fetch_assoc($team_leaders)): ?>
                <option value="<?= $tl['id'] ?>"><?= $tl['first_name'] . ' ' . $tl['last_name'] ?></option>
            <?php endwhile; ?>
        </select>

        <label>Select Team Members:</label>
        <select name="team_member_ids[]" multiple size="6" required>
            <?php while ($tm = mysqli_fetch_assoc($team_members)): ?>
                <option value="<?= $tm['id'] ?>"><?= $tm['first_name'] . ' ' . $tm['last_name'] ?></option>
            <?php endwhile; ?>
        </select>

        <input type="submit" name="assign" value="Assign Members">
    </form>
</div>

</body>
</html>
