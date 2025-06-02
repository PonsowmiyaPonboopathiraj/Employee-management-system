<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "eam_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (
    isset($_POST['employee_id']) &&
    isset($_POST['leave_type_id']) &&
    isset($_POST['from_date']) &&
    isset($_POST['to_date']) &&
    isset($_POST['reason'])
) {
    $employee_id = $_POST['employee_id'];
    $leave_type_id = $_POST['leave_type_id'];
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $reason = $_POST['reason'];

    $stmt = $conn->prepare("INSERT INTO tbl_leaves (employee_id, leave_type_id, from_date, to_date, reason) VALUES (?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sisss", $employee_id, $leave_type_id, $from_date, $to_date, $reason);

    if ($stmt->execute()) {
        echo "✅ Leave applied successfully!";
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Please fill all required fields.";
}

$conn->close();
?>
