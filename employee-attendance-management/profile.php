<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['employee_id']) || empty($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

include('header.php');
include('includes/connection.php');

$id = $_SESSION['employee_id'];
$fetch_emp = mysqli_query($connection, "SELECT * FROM tbl_employee WHERE employee_id='$id'");
$emp = mysqli_fetch_array($fetch_emp);

// Fetch only the latest leave application
$leave_query = mysqli_query($connection, "SELECT * FROM tbl_leave_requests WHERE employee_id='$id' ORDER BY leave_id DESC LIMIT 1");
?>

<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <!-- Profile Section -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Employee Profile</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Employee ID:</strong> <?php echo $emp['employee_id']; ?></li>
                            <li class="list-group-item"><strong>Full Name:</strong> <?php echo $emp['first_name'] . ' ' . $emp['last_name']; ?></li>
                            <li class="list-group-item"><strong>Email:</strong> <?php echo $emp['emailid']; ?></li>
                            <li class="list-group-item"><strong>Phone:</strong> <?php echo $emp['phone']; ?></li>
                            <li class="list-group-item"><strong>Gender:</strong> <?php echo $emp['gender']; ?></li>
                            <li class="list-group-item"><strong>Department:</strong> <?php echo $emp['department']; ?></li>
                            <li class="list-group-item"><strong>Shift:</strong> <?php echo $emp['shift']; ?></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Latest Leave Section -->
            <div class="col-lg-12 mt-5">
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
                                    echo "</td>";
                                    echo "</tr>";
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
