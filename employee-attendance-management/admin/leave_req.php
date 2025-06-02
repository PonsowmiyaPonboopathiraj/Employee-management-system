<?php
session_start();
include('header.php'); // Assuming this includes your sidebar and top nav structure
include('includes/connection.php'); // Your database connection

$message = "";

// Update leave status
if (isset($_POST['update_status'])) {
    $request_id = $_POST['request_id'];
    $new_status = $_POST['new_status'];

    $sql = "UPDATE tbl_leave_requests SET status = ? WHERE leave_id = ?";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "si", $new_status, $request_id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['toast'] = "✅ Leave status updated successfully!";
    } else {
        $_SESSION['toast'] = "❌ Failed to update leave status. Error: " . mysqli_error($connection); // Added error for debugging
    }
    mysqli_stmt_close($stmt);
    header("Location: leave_req.php");
    exit();
}

// Fetch all leave requests
$sql = "SELECT lr.*, e.username AS employee_name, lt.leave_type_name 
        FROM tbl_leave_requests lr
        JOIN tbl_employee e ON lr.employee_id = e.employee_id
        JOIN tbl_leave_type lt ON lr.leave_type_id = lt.leave_type_id
        ORDER BY lr.leave_id DESC";

$result = mysqli_query($connection, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Leave Requests</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        /* Universal Reset & Box-Sizing */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box; /* This is crucial for consistent sizing */
}

body {
    font-family: Arial, sans-serif;
    background-color: #f4f7f6; /* Light grey background */
    line-height: 1.6;
    color: #333;
}

/* Dashboard Layout using CSS Grid */
.dashboard-layout {
    display: grid;
    grid-template-rows: 60px 1fr; /* Top Navbar height, then rest for main-container */
    height: 100vh; /* Full viewport height */
    overflow: hidden; /* Prevent body scrollbars, allow internal scrolling */
}

/* Top Navigation Bar */
.top-navbar {
    background-color: #ffffff; /* White background for top bar */
    padding: 0 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    grid-row: 1; /* Place in the first row of dashboard-layout */
    z-index: 100; /* Ensure it stays on top */
}

.top-navbar .logo {
    font-weight: bold;
    font-size: 24px;
    color: #007bff; /* Blue for logo */
}

.top-navbar .navbar-actions {
    display: flex;
    align-items: center;
    gap: 15px;
}

.top-navbar .user-profile {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: #333;
}

.top-navbar .user-profile .avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    margin-right: 8px;
}

.top-navbar .settings-icon {
    font-size: 20px;
    color: #555;
    text-decoration: none;
}

/* Main Container (Sidebar + Content Area) using CSS Grid */
.main-container {
    display: grid;
    grid-template-columns: 250px 1fr; /* Sidebar width, then content area takes remaining */
    grid-row: 2; /* Place in the second row of dashboard-layout */
    overflow: hidden; /* Important for internal scrolling */
}


/* Example specific icons (replace with actual image/svg/font-awesome) */
.dashboard-icon { background-color: lightblue; }
.employees-icon { background-color: lightcoral; }
.departments-icon { background-color: lightgreen; }
.shift-icon { background-color: lightsalmon; }
.location-icon { background-color: lightgoldenrodyellow; }
.attendance-icon { background-color: lightseagreen; }
.leave-icon { background-color: lightskyblue; }


/* Content Area */
.content-area {
    padding: 20px;
    overflow-y: auto; /* Allow content area to scroll */
    grid-column: 2; /* Place in the second column of main-container */
}

.page-header {
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee; /* A subtle line below the header */
}

.page-header h2 {
    font-size: 28px;
    color: #333;
    margin: 0;
}

/* Table Styling */
.table-wrapper {
    overflow-x: auto; /* Allow horizontal scrolling for table on small screens */
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    padding: 20px;
    /* Table centering */
    display: flex; /* Use flexbox to center the table */
    justify-content: center; /* Horizontally center the table */
    width: 100%; /* Ensure table-wrapper takes full width */
}

.data-table {
    width: 100%; /* Make table take full width of its container (table-wrapper) */
    border-collapse: collapse; /* Collapse borders for a cleaner look */
    min-width: 900px; /* Ensures table is readable on smaller content areas, allows horizontal scroll */
}

.data-table th,
.data-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd; /* Light grey border at the bottom of rows */
}

.data-table th {
    background-color: #007bff; /* Blue header background */
    color: white;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 14px;
}

.data-table tbody tr:nth-child(even) {
    background-color: #f8f8f8; /* Zebra striping for readability */
}

.data-table tbody tr:hover {
    background-color: #f0f0f0; /* Highlight row on hover */
}

/* Status Badges */
.status {
    display: inline-block; /* Allows padding and margin */
    padding: 6px 12px;
    border-radius: 20px; /* Pill shape */
    font-size: 13px;
    font-weight: 600;
    text-align: center;
}

.status.approved {
    background-color: #e6ffed; /* Light green */
    color: #28a745; /* Green text */
}

.status.pending {
    background-color: #fff3cd; /* Light yellow */
    color: #ffc107; /* Yellow text */
}

.status.rejected {
    background-color: #ffebe6; /* Light red */
    color: #dc3545; /* Red text */
}

/* Action Controls (Dropdown and Button) */
.action-controls {
    display: flex; /* Use flexbox to align dropdown and button */
    align-items: center; /* Vertically align them */
    gap: 8px; /* Space between dropdown and button */
}

.action-dropdown {
    padding: 8px 10px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 14px;
    background-color: #fff;
    cursor: pointer;
}

.btn {
    padding: 8px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    transition: background-color 0.2s ease-in-out;
}

.btn-update {
    background-color: #007bff; /* Blue */
    color: white;
}

.btn-update:hover {
    background-color: #0056b3; /* Darker blue on hover */
}
         .sidebar a {
    text-decoration: none !important;
}

.sidebar a:hover {
    text-decoration: none !important;
}
    </style>

    <script>
        function filterByEmployeeName() {
            const input = document.getElementById("searchInput").value.toUpperCase();
            const rows = document.getElementById("leaveTableBody").getElementsByTagName("tr");
            for (let i = 0; i < rows.length; i++) {
                // Employee Name is in the second <td> (index 1)
                const empNameCell = rows[i].getElementsByTagName("td")[1];
                if (empNameCell) { // Check if the cell exists
                    const nameText = empNameCell.textContent || empNameCell.innerText;
                    rows[i].style.display = nameText.toUpperCase().includes(input) ? "" : "none";
                }
            }
        }
    </script>
</head>
<body>

<div class="page-wrapper">
<div class="container-fluid mt-4"> <main class="col-12">
        <div class="page-header">
            <h2>All Leave Requests</h2>
            <input type="text" class="form-control" id="searchInput" onkeyup="filterByEmployeeName()" placeholder="Search by Employee Name">
        </div>

        <?php if (isset($_SESSION['toast'])): ?>
            <?php
            $toast_message = $_SESSION['toast'];
            $toast_class = (strpos($toast_message, '✅') !== false) ? 'bg-success' : 'bg-danger';
            unset($_SESSION['toast']); // Clear the session toast immediately after reading
            ?>
            <div class="toast-container">
                <div class="toast align-items-center text-white <?= $toast_class; ?> border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body"><?= $toast_message; ?></div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <table class="table table-hover"> <thead>
                    <tr>
                        <th>Leave ID</th>
                        <th>Employee Name</th>
                        <th>Leave Type</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="leaveTableBody">
                    <?php 
                    if (mysqli_num_rows($result) > 0):
                        while ($row = mysqli_fetch_assoc($result)): 
                            // Determine status class for badge
                            $status_class = strtolower($row['status']);
                    ?>
                        <tr>
                            <td><?= $row['leave_id']; ?></td>
                            <td><?= htmlspecialchars($row['employee_name']); ?></td>
                            <td><?= htmlspecialchars($row['leave_type_name']); ?></td>
                            <td><?= htmlspecialchars($row['from_date']); ?></td>
                            <td><?= htmlspecialchars($row['to_date']); ?></td>
                            <td><?= htmlspecialchars($row['reason']); ?></td>
                            <td>
                                <span class="status-badge <?= $status_class; ?>">
                                    <?= htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-cell-content">
                                    <form method="POST" action="" class="d-flex align-items-center" style="gap: 8px;">
                                        <input type="hidden" name="request_id" value="<?= $row['leave_id']; ?>">
                                        <select name="new_status" class="form-select"> <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="Approved" <?= $row['status'] == 'Approved' ? 'selected' : ''; ?>>Approved</option>
                                            <option value="Rejected" <?= $row['status'] == 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-primary btn-update">Update</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                    <tr>
                        <td colspan="8">No leave requests found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Initialize Bootstrap toasts manually if needed (Bootstrap 5 usually auto-initializes if HTML structure is correct)
    // var toastElList = [].slice.call(document.querySelectorAll('.toast'));
    // var toastList = toastElList.map(function (toastEl) {
    //     return new bootstrap.Toast(toastEl, { delay: 3000 }); // Auto-hide after 3 seconds
    // });
    // toastList.forEach(toast => toast.show());

    // You can also use jQuery for toasts if you prefer, but Bootstrap 5 doesn't require it.
    // Make sure your header.php does not include old jQuery if you are sticking