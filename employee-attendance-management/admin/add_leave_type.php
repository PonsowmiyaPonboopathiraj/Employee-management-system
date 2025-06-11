<?php
session_start();
if (empty($_SESSION['name']) || $_SESSION['role'] != 1) {
    header('location:index.php');
}
include('header.php');
include('../includes/connection.php');

$message = "";
$success = false;

if (isset($_POST['submit'])) {
    $leave_type_name = $_POST['leave_type_name'];
    $description = $_POST['description'];
    $number_of_days = $_POST['number_of_days'];

    $sql = "INSERT INTO tbl_leave_type (leave_type_name, description, number_of_days) 
            VALUES ('$leave_type_name', '$description', '$number_of_days')";

    if (mysqli_query($connection, $sql)) {
        $message = "✅ Leave Type Added Successfully!";
        $success = true;
    } else {
        $message = "❌ Error: " . mysqli_error($connection);
        $success = false;
    }
}
?>

<div class="page-wrapper">
    <h2>Add Leave Type</h2>

    <!-- Manage Leave Types Button -->
    <div style="margin-bottom: 20px;">
        <a href="manage_leave_type.php" class="manage-btn">
            <i class="fas fa-list-ul"></i> Manage Leave Types
        </a>
    </div>

    <!-- Form Section -->
    <form method="post" class="styled-form">
        <label>Leave Type Name:</label>
        <input type="text" name="leave_type_name" required>

        <label>Description:</label>
        <textarea name="description" required></textarea>

        <label>Number of Days:</label>
        <input type="number" name="number_of_days" min="1" required>

        <input type="submit" name="submit" value="Create Leave Type">
    </form>
</div>

<!-- Toast Notification -->
<?php if ($message): ?>
<script>
    window.onload = function() {
        const toast = document.createElement('div');
        toast.innerText = "<?php echo $message; ?>";
        toast.className = 'custom-toast <?php echo $success ? "success" : "error"; ?>';
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
    resize: horizontal;
}

.styled-form input[type="submit"] {
    background-color: #1ab394;
    color: white;
    padding: 10px 18px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    transition: background 0.3s;
}

.styled-form input[type="submit"]:hover {
    background-color: #18a689;
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
    animation: fadeInOut 5s forwards;
}

.custom-toast.success {
    background-color: #1ab394;
}

.custom-toast.error {
    background-color: #ed5565;
}

.manage-btn {
    background-color: #1c84c6;
    color: #fff;
    padding: 10px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 15px;
    display: inline-block;
    transition: background 0.3s ease;
}
.manage-btn:hover {
    background-color: #0069b1;
}
</style>

<?php include('footer.php'); ?>
