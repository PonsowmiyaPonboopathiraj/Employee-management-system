<?php
session_start();
if (empty($_SESSION['name'])) {
    header('location:index.php');
    exit();
}

include('header.php');
include('includes/connection.php');

$today = date('Y-m-d');


// Total Employees (active employees with role=0)
$emp_q = mysqli_query($connection, "SELECT COUNT(*) FROM tbl_employee WHERE status=1 AND role=0");
$emp = mysqli_fetch_row($emp_q)[0];

// Total Departments
$dept_q = mysqli_query($connection, "SELECT COUNT(*) FROM tbl_department WHERE status=1");
$dept = mysqli_fetch_row($dept_q)[0];

// Total Shifts
$shift_q = mysqli_query($connection, "SELECT COUNT(*) FROM tbl_shift WHERE status=1");
$shift = mysqli_fetch_row($shift_q)[0];

// Present Today (attendance for today with in_status early or late)
$present_q = mysqli_query($connection, "SELECT COUNT(*) FROM tbl_attendance WHERE date='$today' AND (in_status='early' OR in_status='late')");
$present_count = mysqli_fetch_row($present_q)[0];

// On Leave Today (approved leaves overlapping today)
$leave_q = mysqli_query($connection, "SELECT COUNT(*) FROM tbl_leave_requests WHERE status='Approved' AND from_date <= '$today' AND to_date >= '$today'");
$leave_count = mysqli_fetch_row($leave_q)[0];


// Approved Leaves (total approved leave requests)
$approved_q = mysqli_query($connection, "SELECT COUNT(*) FROM tbl_leave_requests WHERE status=1");
$approved = mysqli_fetch_row($approved_q)[0];

// Get count of approved leaves
$approvedQuery = "SELECT COUNT(*) AS total_approved FROM tbl_leave_requests WHERE status = 'Approved'";
$approvedResult = mysqli_query($connection, $approvedQuery);
$approvedRow = mysqli_fetch_assoc($approvedResult);
$approved = $approvedRow['total_approved'];

// Pending Leaves (pending requests with from_date >= today)
$pending_q = mysqli_query($connection, "SELECT COUNT(*) FROM tbl_leave_requests WHERE status=0 AND from_date >= '$today'");
$pending = mysqli_fetch_row($pending_q)[0];

// Get count of pending leaves
$pendingQuery = "SELECT COUNT(*) AS total_pending FROM tbl_leave_requests WHERE status = 0 AND from_date >= '$today'";
$pendingResult = mysqli_query($connection, $pendingQuery);
$pendingRow = mysqli_fetch_assoc($pendingResult);
$pending = $pendingRow['total_pending'];



// Rejected Leaves (status=2)
$rejected_q = mysqli_query($connection, "SELECT COUNT(*) FROM tbl_leave_requests WHERE status=2");
$rejected = mysqli_fetch_row($rejected_q)[0];

// Count rejected leaves
$rejectedQuery = "SELECT COUNT(*) AS rejected_count FROM tbl_leave_requests WHERE status = 'Rejected'";
$rejectedResult = mysqli_query($connection, $rejectedQuery);
$rejectedRow = mysqli_fetch_assoc($rejectedResult);
$rejected = $rejectedRow['rejected_count'];

// Absent = Total active employees - (present + on leave)
$absent_count = $emp - ($present_count + $leave_count);
if ($absent_count < 0) {
    $absent_count = 0;
}
?>

<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-6 col-xl-3">
                <a href="employees.php">
                    <div class="dash-widget">
                        <span class="dash-widget-bg1"><i class="fa fa-user"></i></span>
                        <div class="dash-widget-info text-right">
                            <h3><?= $emp; ?></h3>
                            <span class="widget-title1">Employees <i class="fa fa-check"></i></span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-xl-3">
                <a href="department.php">
                    <div class="dash-widget">
                        <span class="dash-widget-bg2"><i class="fa fa-building-o"></i></span>
                        <div class="dash-widget-info text-right">
                            <h3><?= $dept; ?></h3>
                            <span class="widget-title2">Departments <i class="fa fa-check"></i></span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-xl-3">
                <a href="shift.php">
                    <div class="dash-widget">
                        <span class="dash-widget-bg3"><i class="fa fa-calendar"></i></span>
                        <div class="dash-widget-info text-right">
                            <h3><?= $shift; ?></h3>
                            <span class="widget-title3">Shifts <i class="fa fa-check"></i></span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-xl-3">
                <a href="leave_requests_admin.php">
                    <div class="dash-widget">
                        <span class="dash-widget-bg4"><i class="fa fa-user-times"></i></span>
                        <div class="dash-widget-info text-right">
                            <h3><?= $leave_count ?></h3>
                            <span class="widget-title4">Leave Today <i class="fa fa-calendar-day"></i></span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <a href="present_today.php">
                    <div class="dash-widget">
                        <span class="dash-widget-bg1"><i class="fa fa-user-check"></i></span>
                        <div class="dash-widget-info text-right">
                            <h3><?= $present_count ?></h3>
                            <span class="widget-title1">Present Today</span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="on_leave_today.php">
                    <div class="dash-widget">
                        <span class="dash-widget-bg3"><i class="fa fa-user-times"></i></span>
                        <div class="dash-widget-info text-right">
                            <h3><?= $leave_count ?></h3>
                            <span class="widget-title3">On Leave Today</span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="absent_today.php">
                    <div class="dash-widget">
                        <span class="dash-widget-bg3"><i class="fa fa-user-slash"></i></span>
                        <div class="dash-widget-info text-right">
                            <h3><?= $absent_count ?></h3>
                            <span class="widget-title3">Absent Today</span>
                        </div>
                    </div>
                </a>
            </div>

           <div class="col-md-3">
    <a href="approval.php">
        <div class="dash-widget">
            <span class="dash-widget-bg4"><i class="fa fa-check-circle"></i></span>
            <div class="dash-widget-info text-right">
                <h3><?= $approved; ?></h3>
                <span class="widget-title4">Approved Leave</span>
            </div>
        </div>
    </a>
</div>

        </div>

        <div class="row">
            <div class="col-md-3">
               <a href="pending_leaves.php">
        <div class="dash-widget">
            <span class="dash-widget-bg1"><i class="fa fa-hourglass-half"></i></span>
            <div class="dash-widget-info text-right">
                <h3><?= $pending; ?></h3>
                <span class="widget-title1">Pending Leave</span>
            </div>
        </div>
    </a>
            </div>

           <div class="col-md-3">
    <a href="rejected_leaves.php">
        <div class="dash-widget">
            <span class="dash-widget-bg2"><i class="fa fa-times-circle"></i></span>
            <div class="dash-widget-info text-right">
                <h3><?= $rejected; ?></h3>
                <span class="widget-title2">Rejected Leave</span>
            </div>
        </div>
    </a>
</div>

        </div>
    </div>
</div>

<?php include('footer.php'); ?>