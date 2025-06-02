<?php
include('../includes/connection.php');

if (isset($_POST['update_status'])) {
    $request_id = $_POST['request_id'];
    $new_status = $_POST['new_status'];

    $update_sql = "UPDATE tbl_leave_requests SET status = ? WHERE request_id = ?";
    $stmt = mysqli_prepare($connection, $update_sql);
    mysqli_stmt_bind_param($stmt, "si", $new_status, $request_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: leave_requests_admin.php?msg=updated");
        exit();
    } else {
        echo "❌ Failed to update status.";
    }
}
?>
