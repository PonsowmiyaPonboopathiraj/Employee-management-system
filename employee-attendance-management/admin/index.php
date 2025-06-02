<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Sentersoft Technologies - Employee Login</title>

    <!-- CSS Links -->
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">

    <style>
        body {
            background-color: #f7f7f7;
            font-family: 'Segoe UI', sans-serif;
        }

        .account-logo img {
            max-width: 150px;
            margin-bottom: 20px;
        }

        .account-logo h3 {
            margin-top: 10px;
            color: #1ab394;
        }

        .account-box {
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .account-btn {
            background-color: #1ab394;
            border: none;
        }

        .account-btn:hover {
            background-color: #18a689;
        }

        .error-message {
            color: red;
            font-weight: 500;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>

<?php
session_start();
include('includes/connection.php');

$msg = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($connection, $_POST['username']);
    $pwd = mysqli_real_escape_string($connection, $_POST['pwd']);

    $fetch_query = mysqli_query($connection, "SELECT * FROM tbl_employee WHERE username ='$username' AND password = '$pwd' AND role=1");
    if (mysqli_num_rows($fetch_query) > 0) {
        $data = mysqli_fetch_array($fetch_query);
        $_SESSION['name'] = $data['first_name'] . ' ' . $data['last_name'];
        $_SESSION['role'] = $data['role'];
        header('location:dashboard.php');
        exit();
    } else {
        $msg = "❌ Incorrect username or password.";
    }
}
?>

<body>
    <div class="main-wrapper account-wrapper">
        <div class="account-page">
            <div class="account-center">
                <div class="account-box">

                    <form method="post" class="form-signin">
                        <div class="account-logo text-center">
                            <img src="https://www.sentersoftech.com/images/logo.png" alt="Sentersoft Logo">
                            <h3>Sentersoft Technologies</h3>
                            <p><strong>Employee Login Portal</strong></p>
                        </div>

                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>

                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="pwd" required>
                        </div>

                        <?php if (!empty($msg)): ?>
                            <div class="error-message"><?php echo $msg; ?></div>
                        <?php endif; ?>

                        <div class="form-group text-center">
                            <button type="submit" name="login" class="btn btn-primary account-btn">Login</button>
                        </div>

                        <div class="text-center mt-3">
                            <small>© <?php echo date("Y"); ?> Sentersoft Technologies</small>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- JS Scripts -->
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
