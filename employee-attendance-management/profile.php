<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['employee_id']) || empty($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

include('header.php');
include('includes/connection.php');

$id = $_SESSION['employee_id'];

// Fetch employee details
$fetch_emp = mysqli_query($connection, "SELECT * FROM tbl_employee WHERE employee_id='$id'");
$emp = mysqli_fetch_array($fetch_emp);

// Fetch latest leave request
$leave_query = mysqli_query($connection, "SELECT * FROM tbl_leave_requests WHERE employee_id='$id' ORDER BY leave_id DESC LIMIT 1");

// Date range for current month
$start_date = date('Y-m-01');
$end_date = date('Y-m-t');

// Fetch present days from tbl_attendance using in_status
$attendance_sql = "SELECT COUNT(*) as present_days FROM tbl_attendance 
    WHERE employee_id='$id' 
    AND date BETWEEN '$start_date' AND '$end_date' 
    AND in_status='Present'";
$attendance_result = mysqli_query($connection, $attendance_sql);
$attendance_data = mysqli_fetch_assoc($attendance_result);
$present_days = $attendance_data['present_days'] ?? 0;

// Fetch approved leave days for this month
$leave_sql = "SELECT COUNT(*) as leave_days FROM tbl_leave_requests 
    WHERE employee_id='$id' 
    AND ((from_date BETWEEN '$start_date' AND '$end_date') OR (to_date BETWEEN '$start_date' AND '$end_date'))
    AND status='Approved'";
$leave_result = mysqli_query($connection, $leave_sql);
$leave_data = mysqli_fetch_assoc($leave_result);
$leave_days = $leave_data['leave_days'] ?? 0;
?>

<div class="page-wrapper">
    <div class="content">
        <div class="row">

            <!-- Profile Image + Attendance Summary -->
            <div class="col-lg-4">
                <div class="card text-center">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Employee Photo</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $image_path = 'admin/uploads/employees/' . $emp['image_path'];
                        if (!empty($emp['image_path']) && file_exists($image_path)) {
                            echo '<img src="' . $image_path . '" alt="Employee Image" style="width:120px; height:120px; object-fit:cover; border:2px solid #007bff;" class="rounded-circle mb-3">';
                        } else {
                            echo '<img src="assets/img/default-user.png" alt="No Image" style="width:120px; height:120px;" class="rounded-circle mb-3">';
                        }
                        ?>
                        <hr>
                        <h6>This Month</h6>
                        <p><strong>Present Days:</strong> <?= $present_days; ?></p>
                        <p><strong>Leave Days:</strong> <?= $leave_days; ?></p>
                    </div>
                </div>
            </div>

            <!-- Profile Details -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between">
                        <h4 class="mb-0">Employee Profile</h4>
                        <a href="logout.php" class="btn btn-sm btn-light text-danger">Logout</a>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Employee ID:</strong> <?= htmlspecialchars($emp['employee_id']); ?></li>
                            <li class="list-group-item"><strong>Full Name:</strong> <?= htmlspecialchars($emp['first_name']) . ' ' . htmlspecialchars($emp['last_name']); ?></li>
                            <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($emp['emailid']); ?></li>
                            <li class="list-group-item"><strong>Phone:</strong> <?= htmlspecialchars($emp['phone']); ?></li>
                            <li class="list-group-item"><strong>Gender:</strong> <?= htmlspecialchars($emp['gender']); ?></li>
                            <li class="list-group-item"><strong>Department:</strong> <?= htmlspecialchars($emp['department']); ?></li>
                            <li class="list-group-item"><strong>Shift:</strong> <?= htmlspecialchars($emp['shift']); ?></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Latest Leave Status -->
            <div class="col-lg-12 mt-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0">Latest Leave Status</h4>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>From Date</th>
                                    <th>To Date</th>
                                    <th>Leave Type</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($leave_query) > 0) {
                                    $row = mysqli_fetch_assoc($leave_query);
                                    echo "<tr class='table-info'>";
                                    echo "<td>" . date('d-m-Y', strtotime($row['from_date'])) . "</td>";
                                    echo "<td>" . date('d-m-Y', strtotime($row['to_date'])) . "</td>";
                                    echo "<td>" . (!empty($row['leave_type']) ? htmlspecialchars($row['leave_type']) : 'N/A') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['reason']) . "</td>";
                                    echo "<td>";
                                    if ($row['status'] == 'Approved') {
                                        echo '<span class="badge bg-success">Approved</span>';
                                    } elseif ($row['status'] == 'Rejected') {
                                        echo '<span class="badge bg-danger">Rejected</span>';
                                    } else {
                                        echo '<span class="badge bg-warning text-dark">Pending</span>';
                                    }
                                    echo "</td></tr>";
                                } else {
                                    echo "<tr><td colspan='5' class='text-center'>No leave record found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <small class="text-muted">* Only the most recent leave application is displayed.</small>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include('footer.php'); ?>
