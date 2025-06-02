<?php
session_start();
include('header.php');
include('includes/connection.php');

// Fetch approved leave requests
$sql = "SELECT lr.*, e.username AS employee_name, lt.leave_type_name 
        FROM tbl_leave_requests lr
        JOIN tbl_employee e ON lr.employee_id = e.employee_id
        JOIN tbl_leave_type lt ON lr.leave_type_id = lt.leave_type_id
        WHERE lr.status = 'Approved'
        ORDER BY lr.leave_id DESC";
$result = mysqli_query($connection, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Approved Leaves</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <style>
        body {
            background-color: #f8f9fa;
        }

        .page-header {
            margin: 30px 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        /* Container for search and print aligned in one row */
        .search-print-container {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: nowrap; /* Keep on same line */
        }

        /* Search input fixed width */
        .search-box {
            width: 280px;
            min-width: 200px;
        }

        /* Print button styling */
        .print-btn {
            white-space: nowrap;
            flex-shrink: 0; /* Prevent shrinking */
        }

        .table-container {
            background-color: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            width: 100%;
            overflow-x: auto;
        }

        table {
            min-width: 700px; /* enough width so columns don't break early */
            font-size: 1rem;
        }

        th, td {
            vertical-align: middle !important;
        }

        th:first-child, td:first-child {
            width: 60px;
            text-align: center;
        }

        @media print {
            .search-box,
            .print-btn,
            .navbar,
            .sidebar,
            .page-header {
                display: none !important;
            }
            body {
                margin: 0;
                padding: 0;
                background: #fff;
            }
            table {
                width: 100% !important;
                font-size: 14px;
            }
        }

        /* Responsive fix: On very small screens, let container wrap */
        @media (max-width: 400px) {
            .search-print-container {
                flex-wrap: wrap;
                gap: 8px;
            }
            .search-box {
                width: 100%;
            }
            .print-btn {
                width: 100%;
            }
        }
          .sidebar a {
    text-decoration: none !important;
}

.sidebar a:hover {
    text-decoration: none !important;
}
    </style>

    <script>
        function filterByName() {
            const input = document.getElementById("searchInput").value.toUpperCase();
            const rows = document.getElementById("leaveTableBody").getElementsByTagName("tr");

            for (let i = 0; i < rows.length; i++) {
                const empName = rows[i].getElementsByTagName("td")[1];
                if (empName) {
                    const nameText = empName.textContent || empName.innerText;
                    rows[i].style.display = nameText.toUpperCase().includes(input) ? "" : "none";
                }
            }
        }

        function printTable() {
            window.print();
        }
    </script>
</head>
<body>


<div class="page-wrapper">
<div class="container mt-4">
    <div class="page-header">
        <h2>Approved Leave Requests</h2>
        <div class="search-print-container">
            <input type="text" id="searchInput" class="form-control search-box" placeholder="Search by Employee Name" onkeyup="filterByName()" />
            <button onclick="printTable()" class="btn btn-outline-dark print-btn">🖨️ Print</button>
        </div>
    </div>

    <div class="table-container">
        <table class="table table-bordered table-hover text-center">
            <thead class="table-success">
                <tr>
                    <th>S.No</th>
                    <th>Employee Name</th>
                    <th>Leave Type</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Reason</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="leaveTableBody">
                <?php
                $sn = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>{$sn}</td>";
                    echo "<td>" . htmlspecialchars($row['employee_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['leave_type_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['from_date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['to_date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['reason']) . "</td>";
                    echo "<td style='color: green; font-weight: bold;'>" . htmlspecialchars($row['status']) . "</td>";
                    echo "</tr>";
                    $sn++;
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
