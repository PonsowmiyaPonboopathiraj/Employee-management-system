<?php
include('includes/connection.php');

if (isset($_GET['type_id'])) {
    $type_id = intval($_GET['type_id']);
    $sql = "SELECT number_of_days FROM tbl_leave_type WHERE leave_type_id = ?";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "i", $type_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $number_of_days);
    mysqli_stmt_fetch($stmt);
    echo json_encode(['number_of_days' => $number_of_days]);
    mysqli_stmt_close($stmt);
}
?>
