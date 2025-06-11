<?php
session_start();
include('includes/connection.php');
include('header.php');

if ($_SESSION['role'] != '1') {
    header("Location: index.php");
    exit();
}

// Department list
$departments = ['Admin', 'HR', 'Digital Marketing', 'Web Development', 'Machine Learning', 'Accounts'];
?>

<div class="page-wrapper">
    <h2><center>Department-wise Employee Role Update</center></h2>

    <center>
    <table border="1" cellpadding="10" cellspacing="0" width="90%">
        <?php foreach ($departments as $dept) {
            $query = "SELECT * FROM tbl_employee WHERE department = '$dept' AND role != '1'";
            $result = mysqli_query($connection, $query);

            if (mysqli_num_rows($result) > 0) {
        ?>
            <!-- Department Header Row -->
            <tr style="background-color: #f0f0f0;">
                <th colspan="7" style="text-align:left; padding:10px; font-size: 18px;"><?php echo $dept; ?> Department</th>
            </tr>
            <!-- Table Headings -->
            <tr style="background-color: #d1e0e0;">
                <th>ID</th>
                <th>Employee Name</th>
                <th>Email</th>
                <th>Department</th>
                <th>Shift</th>
                <th>Current Role</th>
                <th>Update Role</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['first_name'] . " " . $row['last_name']; ?></td>
                    <td><?php echo $row['emailid']; ?></td>
                    <td><?php echo $row['department']; ?></td>
                    <td><?php echo $row['shift']; ?></td>
                    <td>
                        <?php
                            if ($row['role'] == '0') echo "Employee";
                            elseif ($row['role'] == '2') echo "Team Leader";
                        ?>
                    </td>
                    <td>
                        <form method="POST" action="update_role.php">
                            <input type="hidden" name="emp_id" value="<?php echo $row['id']; ?>">
                            <select name="new_role" required>
                                <option value="0" <?php if($row['role']=='0') echo "selected"; ?>>Employee</option>
                                <option value="2" <?php if($row['role']=='2') echo "selected"; ?>>Team Leader</option>
                                <option value="1" <?php if($row['role']=='1') echo "selected"; ?>>Admin</option>
                            </select>
                            <input type="submit" name="update_role" value="Update">
                        </form>
                    </td>
                </tr>
            <?php } ?>
        <?php } } ?>
    </table>
    </center>
            </div >