<?php
session_start();
include('includes/connection.php');

$message = "";

if (isset($_POST['login'])) {
    $employee_id = trim($_POST['employee_id'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($employee_id) || empty($password)) {
        $message = "⚠️ Please enter both Employee ID and Password.";
    } else {
        // Prepare and execute query
        $sql = "SELECT * FROM tbl_employee 
                WHERE LOWER(TRIM(employee_id)) = LOWER(TRIM(?)) 
                AND role IN ('0', '2') 
                LIMIT 1";
        $stmt = mysqli_prepare($connection, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $employee_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                if ($password === $row['password']) {
                    // ✅ Set session
                    $_SESSION['employee_id'] = $row['employee_id'];
                    $_SESSION['employee_name'] = $row['first_name'] . ' ' . $row['last_name'];
                    $_SESSION['role'] = $row['role']; // 0 = Employee, 2 = Team Leader
                    $_SESSION['id'] = $row['id'];

                    // ✅ Redirect based on role
                    if ($row['role'] == '2') {
                        header("Location: teamleader_dashboard.php");
                    } elseif ($row['role'] == '0') {
                        header("Location: profile.php");
                    } else {
                        $message = "❌ Unauthorized role.";
                    }
                    exit();
                } else {
                    $message = "❌ Incorrect password.";
                }
            } else {
                $message = "❌ Invalid Employee ID or not authorized.";
            }

            mysqli_stmt_close($stmt);
        } else {
            $message = "❌ Database error: " . mysqli_error($connection);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employee Login - Sentersoft</title>
    <style>
        .login-container {
            width: 300px;
            margin: 80px auto;
            padding: 30px;
            border: 1px solid #ccc;
            border-radius: 12px;
            font-family: Arial;
        }
        .login-container input {
            width: 100%;
            margin-bottom: 15px;
            padding: 8px;
        }
        .message {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Employee / TL Login</h2>
        <?php if ($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>
        <form method="POST">
            <label>Employee ID</label>
            <input type="text" name="employee_id" required>
            <label>Password</label>
            <input type="password" name="password" required>
            <input type="submit" name="login" value="Login">
        </form>
    </div>
</body>
</html>
