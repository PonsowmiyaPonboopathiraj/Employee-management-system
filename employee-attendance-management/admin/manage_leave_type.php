<?php
session_start();
if (empty($_SESSION['name']) || $_SESSION['role'] != 1) {
    header('location:index.php');
}
include('header.php');
include('../includes/connection.php');

// Delete Logic
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    mysqli_query($connection, "DELETE FROM tbl_leave_type WHERE leave_type_id = $id");
    $_SESSION['delete_success'] = "Leave type deleted successfully!";
    header('Location: manage_leave_type.php');
    exit();
}
?>

<div class="page-wrapper">
    <div class="content-header">
        <h2>Manage Leave Types</h2>
    </div>

    <?php
    if (isset($_SESSION['delete_success'])) {
        echo "<div class='alert success-alert'>{$_SESSION['delete_success']}</div>";
        unset($_SESSION['delete_success']); // Show only once
    }
    ?>

    <table class="styled-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Leave Type Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM tbl_leave_type";
            $result = mysqli_query($connection, $sql);
            $count = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$count}</td>
                        <td>{$row['leave_type_name']}</td>
                        <td>{$row['description']}</td>
                        <td>
                            <a href='edit_leave_type.php?id={$row['leave_type_id']}' class='action-btn edit'><i class='fas fa-edit'></i> Edit</a>
                            <a href='manage_leave_type.php?delete_id={$row['leave_type_id']}' class='action-btn delete' onclick=\"return confirm('Are you sure you want to delete this leave type?');\"><i class='fas fa-trash'></i> Delete</a>
                        </td>
                    </tr>";
                $count++;
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Styling -->
<style>

.content-header h3 {
    color: #2f4050;
    margin-bottom: 20px;
    font-weight: bold;
}

.alert.success-alert {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
    padding: 10px 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.styled-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
}

.styled-table th, .styled-table td {
    padding: 14px 18px;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
}

.styled-table th {
    background-color: #1ab394;
    color: white;
    font-size: 15px;
}

.styled-table tr:hover {
    background-color: #f1f1f1;
}

.action-btn {
    padding: 6px 12px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 14px;
    margin-right: 8px;
    display: inline-block;
}

.action-btn.edit {
    background-color: #1c84c6;
    color: white;
}

.action-btn.edit:hover {
    background-color: #0069b1;
}

.action-btn.delete {
    background-color: #ed5565;
    color: white;
}

.action-btn.delete:hover {
    background-color: #d43f4b;
}
</style>

<?php include('footer.php'); ?>
