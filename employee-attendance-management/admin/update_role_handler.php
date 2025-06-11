<?php
include('includes/connection.php');

if (isset($_POST['update_role'])) {
    $emp_id = $_POST['emp_id'];
    $new_role = $_POST['new_role'];

    if ($new_role == '2') {
        $query = "UPDATE tbl_employee SET role = ?, team_leader_id = ? WHERE id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("iii", $new_role, $emp_id, $emp_id);
    } else {
        $query = "UPDATE tbl_employee SET role = ?, team_leader_id = NULL WHERE id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("ii", $new_role, $emp_id);
    }

    if ($stmt->execute()) {
        header("Location: employee_role.php?msg=success");
        exit();
    } else {
        header("Location: employee_role.php?msg=error");
        exit();
    }
}
?>
