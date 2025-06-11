<?php
include('includes/connection.php');

// Check for update role request
if (isset($_POST['update_role'])) {
    $emp_id = $_POST['emp_id'];
    $new_role = $_POST['new_role'];

    if ($new_role == '2') {
        // Team Leader role — set team_leader_id to employee's own id
        $query = "UPDATE tbl_employee SET role = ?, team_leader_id = ? WHERE id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("iii", $new_role, $emp_id, $emp_id);
    } else {
        // Other roles — clear team_leader_id
        $query = "UPDATE tbl_employee SET role = ?, team_leader_id = NULL WHERE id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("ii", $new_role, $emp_id);
    }

    if ($stmt->execute()) {
        // Redirect with success message in query string
        header("Location: update_role.php?msg=success");
        exit();
    } else {
        header("Location: update_role.php?msg=error");
        exit();
    }
}
?>

<!-- HTML STARTS (Put this in update_role.php where the redirect happens) -->
<!DOCTYPE html>
<html>
<head>
    <title>Update Role</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #28a745;
            color: white;
            padding: 15px 25px;
            border-radius: 6px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            animation: slideIn 0.5s ease, fadeOut 0.5s ease 3.5s forwards;
            z-index: 1000;
        }

        .toast.error {
            background-color: #dc3545;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeOut {
            to { opacity: 0; transform: translateY(-20px); }
        }

        .content {
            margin: 50px auto;
            text-align: center;
        }

        .btn-back {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn-back:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
    <div class="toast">✅ Role updated successfully!</div>
<?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'error'): ?>
    <div class="toast error">❌ Failed to update role!</div>
<?php endif; ?>

<div class="content">
    <h2>Update Role Page</h2>
    <a href="employee_role.php" class="btn-back">🔙 Back to Role Management</a>
</div>

</body>
</html>
