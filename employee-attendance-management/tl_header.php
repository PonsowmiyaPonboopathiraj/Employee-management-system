<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0" />
    <title>SST Staff - Team Leader Dashboard</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/font-awesome.min.css" />
    <link rel="stylesheet" href="assets/css/select2.min.css" />
    <link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        /* Sidebar submenu caret style */
        .menu-arrow {
            float: right;
            margin-top: 5px;
        }

        /* Show submenu on toggle */
        .submenu ul {
            display: none;
        }
    </style>
</head>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<body>
    <div class="main-wrapper">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <a href="#" class="logo">
                    <img class="rounded-circle" src="assets/img/eam-logo.png" width="50" alt="Admin" />
                    <span>SST</span>
                </a>
            </div>
        </div>
        <!-- /Header -->

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="active">
                            <a href="profile.php"><i class="fa fa-id-card-o"></i> <span>My Profile</span></a>
                        </li>

                        <li class="submenu">
                            <a href="#"><i class="fa fa-file-text-o"></i> <span>Attendance</span> <span class="menu-arrow">&#9660;</span></a>
                            <ul>
                                <li><a href="attendance.php">Attendance Form</a></li>
                                <li><a href="attendance_history.php">Attendance History</a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="#"><i class="fa fa-calendar"></i> <span>Leave</span> <span class="menu-arrow">&#9660;</span></a>
                            <ul>
                                <li><a href="leave_apply.php">Leave Form</a></li>
                                <li><a href="leave_history.php">Leave History</a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="#"><i class="fa fa-users"></i> <span>Team</span> <span class="menu-arrow">&#9660;</span></a>
                            <ul>

                                <!-- Leave Submenu -->
                                <li class="submenu">
                                    <a href="#">Leave <span class="menu-arrow">&#9656;</span></a>
                                    <ul>
                                        <li><a href="leave_request.php">Leave Request</a></li>
                                        <li><a href="approved_leave.php">Approved Leave</a></li>
                                        <li><a href="rejected_leave.php">Rejected Leave</a></li>
                                        <li><a href="pending_leave.php">Pending Leave</a></li>
                                    </ul>
                                </li>

                                <!-- Attendance Submenu -->
                                <li class="submenu">
                                    <a href="#">Attendance <span class="menu-arrow">&#9656;</span></a>
                                    <ul>
                                        <li><a href="team_attendance_history.php">Attendance History</a></li>
                                    </ul>
                                </li>

                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Sidebar -->
    </div>

    <!-- JS files -->
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/app.js"></script>

    <!-- Custom JS for submenu toggle -->
    <script>
        $(document).ready(function () {
            // Toggle submenu on click
            $('.submenu > a').click(function (e) {
                e.preventDefault();

                var $this = $(this);
                var $submenu = $this.next('ul');

                // Slide toggle the clicked submenu
                $submenu.slideToggle();

                // Close other open submenus at same level
                $this.parent().siblings('.submenu').find('ul').slideUp();

                // Optionally toggle the arrow direction
                $this.find('.menu-arrow').toggleClass('rotated');
                $this.parent().siblings('.submenu').find('.menu-arrow').removeClass('rotated');
            });
        });
    </script>

    <style>
        /* Arrow rotation when submenu is open */
        .menu-arrow.rotated {
            transform: rotate(90deg);
            transition: transform 0.3s ease;
        }
    </style>
</body>

</html>
