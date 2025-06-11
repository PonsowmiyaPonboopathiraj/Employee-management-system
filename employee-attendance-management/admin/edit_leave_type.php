<?php
session_start();
if (empty($_SESSION['name']) || $_SESSION['role'] != 1) {
    header('location:index.php');
}

include('header.php');
include('../includes/connection.php');

$message = "";
$success = false;

// Get leave type details
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM tbl_leave_type WHERE leave_type_id = $id";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_assoc($result);

    if (!$row) {
        echo "<script>alert('Invalid Leave Type ID'); window.location.href='manage_leave_type.php';</script>";
        exit();
    }
} else {
    echo "<script>window.location.href='manage_leave_type.php';</script>";
    exit();
}

// Update logic
if (isset($_POST['submit'])) {
    $leave_type_name = $_POST['leave_type_name'];
    $description = $_POST['description'];
    $number_of_days = $_POST['number_of_days'];

    $update_sql = "UPDATE tbl_leave_type 
                   SET leave_type_name = '$leave_type_name', 
                       description = '$description', 
                       number_of_days = '$number_of_days' 
                   WHERE leave_type_id = $id";

    if (mysqli_query($connection, $update_sql)) {
        echo "<script>
                alert('Leave Type Updated Successfully!');
                window.location.href='manage_leave_type.php';
              </script>";
        exit();
    } else {
        $message = "❌ Error: " . mysqli_error($connection);
        $success = false;
    }
}
?>

<div class="page-wrapper">
    <h2>Edit Leave Type</h2>

    <form method="post" class="styled-form">
        <label>Leave Type Name:</label>
        <input type="text" name="leave_type_name" value="<?php echo $row['leave_type_name']; ?>" required>

        <label>Description:</label>
        <textarea name="description" required><?php echo $row['description']; ?></textarea>

        <label>Number of Days:</label>
        <input type="number" name="number_of_days" value="<?php echo $row['number_of_days']; ?>" min="1" required>

        <input type="submit" name="submit" value="Update Leave Type">
    </form>
</div>

<!-- Toast Notification if error -->
<?php if ($message): ?>
<script>
    window.onload = function() {
        const toast = document.createElement('div');
        toast.innerText = "<?php echo $message; ?>";
        toast.className = 'custom-toast error';
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
</script>
<?php endif; ?>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<!-- Styling -->
<style>
h2 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}
.styled-form {
    background: #fff;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.05);
}
.styled-form label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
}
.styled-form input[type="text"],
.styled-form textarea,
.styled-form input[type="number"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 15px;
}
.styled-form textarea {
    height: 100px;
    resize: vertical;
}
.styled-form input[type="submit"] {
    background-color: #f8ac59;
    color: white;
    padding: 10px 18px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    transition: background 0.3s;
}
.styled-form input[type="submit"]:hover {
    background-color: #f39c12;
}
.custom-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 14px 22px;
    border-radius: 8px;
    color: #fff;
    font-size: 16px;
    z-index: 9999;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
.custom-toast.error {
    background-color: #ed5565;
}
</style>

<?php include('footer.php'); ?>
