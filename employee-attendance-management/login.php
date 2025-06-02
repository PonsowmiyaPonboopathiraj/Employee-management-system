<?php
session_start();
include('includes/connection.php');

$message = "";

if (isset($_POST['login'])) {
    $employee_id = trim($_POST['employee_id'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($employee_id) || empty($password)) {
        $message = "⚠️ Please enter Employee ID and Password.";
    } else {
        $sql = "SELECT * FROM tbl_employee WHERE LOWER(TRIM(employee_id)) = LOWER(TRIM(?)) LIMIT 1";
        $stmt = mysqli_prepare($connection, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $employee_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                if ($password === $row['password']) {
                    $_SESSION['employee_id'] = $row['employee_id'];
                    $_SESSION['employee_name'] = $row['first_name'] . ' ' . $row['last_name'];

                    // Flash success notification with delay
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            setTimeout(function() {
                                alert('✅ Welcome, " . $_SESSION['employee_name'] . "! Login successful.');
                                window.location.href = 'profile.php';
                            }, 500);
                        });
                    </script>";
                    exit();
                } else {
                    $message = "❌ Incorrect password.";
                }
            } else {
                $message = "❌ Employee ID not found.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $message = "❌ Database error: " . mysqli_error($connection);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sentersoft Technologies - Employee Portal</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f0f0;
            padding: 40px;
        }
        .login-container {
            max-width: 400px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        .logo {
            max-width: 120px;
            margin-bottom: 20px;
        }
        h2 {
            margin-bottom: 10px;
            color: #1ab394;
        }
        p {
            font-weight: bold;
            margin-bottom: 20px;
            color: #555;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            text-align: left;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            margin-top: 25px;
            width: 100%;
            padding: 12px;
            background: #1ab394;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #18a689;
        }
        .message {
            margin-top: 15px;
            color: red;
            font-weight: 500;
        }
        .footer-text {
            margin-top: 30px;
            font-size: 13px;
            color: #888;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <!-- Online logo -->
        <img src="https://www.sentersoftech.com/images/logo.png" alt="Sentersoft Logo" class="logo">
        <h2>Sentersoft Technologies</h2>
        <p>Employee Portal Login</p>

        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="employee_id">Employee ID</label>
            <input type="text" id="employee_id" name="employee_id" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" name="login" value="Login">
        </form>

        <div class="footer-text">© <?php echo date("Y"); ?> Sentersoft Technologies</div>
    </div>

</body>
</html>
