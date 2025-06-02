<?php
session_start();
if (empty($_SESSION['name'])) {
    header('location:index.php');
}
include('header.php');
include('includes/connection.php');
?>

<div class="page-wrapper">
    <div class="content">
        <h4 class="page-title">Attendance History</h4>
        <form method="post" id="filterForm">
            <div class="row" style="padding: 20px;">
                <div class="col-md-3">
                    <label>From Date</label>
                    <input type="date" name="from_date" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label>To Date</label>
                    <input type="date" name="to_date" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label>Department</label>
                    <select class="form-control" name="department" id="department" required>
                        <option value="all">All Departments</option>
                        <?php
                        $dept = mysqli_query($connection, "SELECT * FROM tbl_department");
                        while ($d = mysqli_fetch_array($dept)) {
                            echo '<option value="' . $d['department_name'] . '">' . $d['department_name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Employee</label>
                    <select class="form-control" name="employee" id="employee">
                        <option value="all">All Employees</option>
                    </select>
                </div>
                <div class="col-md-2 mt-3">
                    <button type="submit" name="search" class="btn btn-primary w-100">Search</button>
                </div>
                <div class="col-md-2 mt-3">
                    <button onclick="printDiv('printArea')" class="btn btn-info w-100" type="button">Print</button>
                </div>
            </div>
        </form>

        <div id="printArea">
            <div class="table-responsive">
                <table class="datatable table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Date</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $presentCount = 0;
                        $absentCount = 0;
                        $leaveCount = 0;

                        if (isset($_POST['search'])) {
                            $from = $_POST['from_date'];
                            $to = $_POST['to_date'];
                            $dept = $_POST['department'];
                            $emp = $_POST['employee'];

                            $query = "SELECT e.first_name, e.last_name, e.department, a.date, a.check_in, a.check_out, a.in_status 
                                FROM tbl_attendance a 
                                JOIN tbl_employee e ON a.employee_id = e.employee_id 
                                WHERE DATE(a.date) BETWEEN '$from' AND '$to'";

                            if ($dept != 'all') {
                                $query .= " AND e.department = '$dept'";
                            }
                            if ($emp != 'all') {
                                $query .= " AND e.employee_id = '$emp'";
                            }

                            $query .= " ORDER BY a.date DESC";

                            $result = mysqli_query($connection, $query);

                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                    <td>{$row['first_name']} {$row['last_name']}</td>
                                    <td>{$row['department']}</td>
                                    <td>{$row['date']}</td>
                                    <td>{$row['check_in']}</td>
                                    <td>{$row['check_out']}</td>
                                    <td>{$row['in_status']}</td>
                                </tr>";

                                // Normalize status
                                $status = strtolower(trim($row['in_status']));
                                if ($status === 'present') {
                                    $presentCount++;
                                } elseif ($status === 'absent') {
                                    $absentCount++;
                                } elseif ($status === 'leave') {
                                    $leaveCount++;
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if (isset($_POST['search'])): ?>
        <div class="row mt-5">
            <div class="col-md-6">
                <canvas id="pieChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="barChart"></canvas>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include('footer.php'); ?>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    function printDiv(divId) {
        var printContents = document.getElementById(divId).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        location.reload(); // Refresh to restore content
    }

    // AJAX for employee dropdown
    document.getElementById("department").addEventListener("change", function () {
        var dept = this.value;
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "get_employees.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
            if (this.status === 200) {
                document.getElementById("employee").innerHTML = this.responseText;
            }
        };
        xhr.send("department=" + dept);
    });

    <?php if (isset($_POST['search'])): ?>
    const pieChart = document.getElementById('pieChart');
    const barChart = document.getElementById('barChart');

    new Chart(pieChart, {
        type: 'pie',
        data: {
            labels: ['Present', 'Absent', 'Leave'],
            datasets: [{
                data: [<?= $presentCount ?>, <?= $absentCount ?>, <?= $leaveCount ?>],
                backgroundColor: ['#4CAF50', '#FF5722', '#FFC107']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'Attendance Overview' }
            }
        }
    });

    new Chart(barChart, {
        type: 'bar',
        data: {
            labels: ['Present', 'Absent', 'Leave'],
            datasets: [{
                label: 'Count',
                data: [<?= $presentCount ?>, <?= $absentCount ?>, <?= $leaveCount ?>],
                backgroundColor: ['#4CAF50', '#FF5722', '#FFC107']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'Attendance Statistics' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
    <?php endif; ?>
</script>
    