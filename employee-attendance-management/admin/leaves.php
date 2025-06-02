<?php 
session_start();
if(empty($_SESSION['name'])) {
    header('location:index.php');
}
include('header.php');
include('includes/connection.php');
?>

<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-sm-4">
                <h4 class="page-title">Leave List</h4>
            </div>
            <div class="col-sm-8 text-right m-b-20">
                <a href="add-leave.php" class="btn btn-primary btn-rounded"><i class="fa fa-plus"></i> Add Leave</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped custom-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Employee</th>
                                <th>Leave Type</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Reason</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $i = 1;
                                $fetch = mysqli_query($connection, "
                                    SELECT 
                                        lr.*, 
                                        e.employee_name, 
                                        lt.leave_type 
                                    FROM 
                                        tbl_leave_requests lr
                                    JOIN 
                                        tbl_employee e ON lr.employee_id = e.employee_id
                                    JOIN 
                                        tbl_leave_type lt ON lr.leave_type_id = lt.leave_type_id
                                ");
                                while($row = mysqli_fetch_assoc($fetch)) {
                            ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo $row['employee_name']; ?></td>
                                <td><?php echo $row['leave_type']; ?></td>
                                <td><?php echo $row['from_date']; ?></td>
                                <td><?php echo $row['to_date']; ?></td>
                                <td><?php echo $row['reason']; ?></td>
                                <td>
                                    <?php 
                                        if($row['status'] == 1){
                                            echo '<span class="badge badge-success">Approved</span>';
                                        } else {
                                            echo '<span class="badge badge-warning">Pending</span>';
                                        }
                                    ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
