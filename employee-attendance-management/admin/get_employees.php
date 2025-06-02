<?php
include('includes/connection.php');

$dept = $_POST['department'];

if ($dept === 'all') {
    $query = "SELECT * FROM tbl_employee";
} else {
    $query = "SELECT * FROM tbl_employee WHERE department = '$dept'";
}

$result = mysqli_query($connection, $query);
echo '<option value="all">All Employees</option>';
while ($row = mysqli_fetch_array($result)) {
    echo '<option value="'.$row['employee_id'].'">'.$row['first_name'].' '.$row['last_name'].'</option>';
}
?>
