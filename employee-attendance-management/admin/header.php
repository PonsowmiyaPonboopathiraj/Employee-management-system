<?php
// Start session or include any PHP logic at the top

// Do NOT echo or output anything here
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>SenterSoft Technologies</title>

    <!-- Fonts and CSS -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/select2.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
<div class="main-wrapper">
    <div class="header">
        <div class="header-left">
            <a href="dashboard.php" class="logo">
                <img class="rounded-circle" src="assets/img/eam-logo.png" width="50" alt="Admin">
                <span>SST</span>
            </a>
        </div>
        <a id="toggle_btn" href="javascript:void(0);"><i class="fa fa-bars"></i></a>
        <a id="mobile_btn" class="mobile_btn float-left" href="#sidebar"><i class="fa fa-bars"></i></a>

        <ul class="nav user-menu float-right">
            <li class="nav-item dropdown has-arrow">
                <a href="#" class="dropdown-toggle nav-link user-link" data-toggle="dropdown">
                    <span class="user-img">
                        <img class="rounded-circle" src="assets/img/user.jpg" width="24" alt="Admin">
                        <span class="status online"></span>
                    </span>
                    <span>Admin</span>
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
            </li>
        </ul>

        <div class="dropdown mobile-user-menu float-right">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="logout.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-inner slimscroll">
            <div id="sidebar-menu" class="sidebar-menu">
                <ul>
                    <li class="active"><a href="dashboard.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
                    <li><a href="employees.php"><i class="fa fa-user"></i> <span>Employees</span></a></li>
                    <li><a href="departments.php"><i class="fa fa-hospital-o"></i> <span>Departments</span></a></li>
                    <li><a href="shift.php"><i class="fa fa-calendar"></i> <span>Shift</span></a></li>
                    <li><a href="location.php"><i class="fa fa-map-marker"></i> <span>Location</span></a></li>
                    <li><a href="attendance_history.php"><i class="fa fa-file-o"></i> <span>Attendance</span></a></li>
                    <li><a href="add_leave_type.php"><i class="fa fa-calendar-check-o"></i> <span>Leave</span></a></li>
                </ul>
            </div>
        </div>
    </div>
