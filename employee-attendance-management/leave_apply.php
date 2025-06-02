<?php
session_start();
include('header.php');
include('includes/connection.php');

// Check DB connection
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch all leave types
$sql = "SELECT * FROM tbl_leave_type";
$result = mysqli_query($connection, $sql);

$message = "";
$success = false;

// Normalize employee_id
$employee_id_raw = $_SESSION['employee_id'];
$employee_id = trim($employee_id_raw);

if (isset($_POST['submit'])) {
    $leave_type_id = $_POST['leave_type_id'] ?? '';
    $leave_from = $_POST['leave_from'] ?? '';
    $leave_to = $_POST['leave_to'] ?? '';
    $reason = trim($_POST['reason'] ?? '');
    $leave_duration = $_POST['leave_duration'] ?? '';

    // Input validation
    if (!$leave_type_id || !$leave_from || !$leave_to || !$reason || !$leave_duration) {
        $message = "⚠️ Please fill all the required fields!";
    } elseif ($leave_from > $leave_to) {
        $message = "⚠️ Leave From date cannot be later than Leave To date!";
    } else {
        $checkEmpSql = "SELECT employee_id FROM tbl_employee WHERE TRIM(employee_id) COLLATE utf8mb4_general_ci = ? LIMIT 1";
        $checkEmp = mysqli_prepare($connection, $checkEmpSql);
        
        if ($checkEmp === false) {
            $message = "❌ Prepare failed: " . mysqli_error($connection);
        } else {
            mysqli_stmt_bind_param($checkEmp, "s", $employee_id);
            mysqli_stmt_execute($checkEmp);
            $checkResult = mysqli_stmt_get_result($checkEmp);

            if (mysqli_num_rows($checkResult) == 0) {
                $message = "❌ Invalid Employee ID: No such employee found!";
            } else {
                $status = 'Pending';
                $sql_insert = "INSERT INTO tbl_leave_requests (employee_id, leave_type_id, from_date, to_date, reason, leave_duration, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($connection, $sql_insert);
                if ($stmt === false) {
                    $message = "❌ Prepare failed: " . mysqli_error($connection);
                } else {
                    mysqli_stmt_bind_param($stmt, "sisssis", $employee_id, $leave_type_id, $leave_from, $leave_to, $reason, $leave_duration, $status);
                    if (mysqli_stmt_execute($stmt)) {
                        $message = "✅ Leave Request Submitted Successfully!";
                        $success = true;
                        $_POST = [];
                    } else {
                        $message = "❌ Execute failed: " . mysqli_stmt_error($stmt);
                    }
                    mysqli_stmt_close($stmt);
                }
            }
            mysqli_stmt_close($checkEmp);
        }
    }
}
?>

<style>
h2 {
    text-align: center;
    color: #333;
    margin-bottom: 25px;
}
.page-wrapper {
    position: relative;
    padding: 30px;
    max-width: 600px;
    margin: auto;
}
.styled-form {
    background: #f9f9f9;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
}
.styled-form label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
}
.styled-form input[type="date"],
.styled-form input[readonly],
.styled-form select,
.styled-form textarea {
    width: 100%;
    padding: 12px;
    margin-bottom: 18px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 15px;
}
.styled-form textarea {
    height: 100px;
    resize: vertical;
}
.styled-form input[type="submit"] {
    background-color: #1ab394;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    transition: 0.3s ease;
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
@keyframes fadeInOut {
    0% {opacity: 0;}
    10% {opacity: 1;}
    90% {opacity: 1;}
    100% {opacity: 0;}
}
</style>

<div class="page-wrapper">
    <div class="content">
        <h2>Apply for Leave</h2>

        <?php if ($message): ?>
            <div class="custom-toast <?php echo $success ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="styled-form" action="">
            <label for="leave_type_id">Leave Type</label>
            <select id="leave_type_id" name="leave_type_id" required>
                <option value="" disabled selected>Select Leave Type</option>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <option value="<?php echo $row['leave_type_id']; ?>"
                        <?php if (isset($_POST['leave_type_id']) && $_POST['leave_type_id'] == $row['leave_type_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($row['leave_type_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="leave_from">Leave From</label>
            <input type="date" id="leave_from" name="leave_from" required
                value="<?php echo htmlspecialchars($_POST['leave_from'] ?? ''); ?>" />

            <label for="leave_to">Leave To</label>
            <input type="date" id="leave_to" name="leave_to" required
                value="<?php echo htmlspecialchars($_POST['leave_to'] ?? ''); ?>" />

            <label for="leave_duration">Leave Duration (days)</label>
            <input type="text" id="leave_duration" name="leave_duration" readonly
                value="<?php echo htmlspecialchars($_POST['leave_duration'] ?? ''); ?>" />

            <label for="reason">Reason</label>
            <textarea id="reason" name="reason" required><?php echo htmlspecialchars($_POST['reason'] ?? ''); ?></textarea>

            <input type="submit" name="submit" value="Submit Leave Request" />
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const fromDate = document.getElementById("leave_from");
    const toDate = document.getElementById("leave_to");
    const durationField = document.getElementById("leave_duration");

    function calculateDuration() {
        const start = new Date(fromDate.value);
        const end = new Date(toDate.value);

        if (fromDate.value && toDate.value && start <= end) {
            const diffTime = Math.abs(end - start);
            const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;
            durationField.value = diffDays;
        } else {
            durationField.value = "";
        }
    }

    fromDate.addEventListener("change", calculateDuration);
    toDate.addEventListener("change", calculateDuration);
});
</script>

<?php include('footer.php'); ?>
