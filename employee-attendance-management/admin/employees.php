<?php
session_start();
if(empty($_SESSION['name']))
{
    header('location:index.php');
}
include('header.php');
include('includes/connection.php');
?>
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-sm-4 col-3">
                        <h4 class="page-title">Employees</h4>
                    </div>
                    
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="add-employee.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-plus"></i> Add Employee</a>
                    </div>
                
                </div>
                <div class="table-responsive">
                                    <table class="datatable table table-stripped ">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Mobile</th>
                                            <th>Department</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if(isset($_GET['ids'])){
                                        $id = $_GET['ids'];
                                        $delete_query = mysqli_query($connection, "delete from tbl_employee where id='$id'");
                                        }
                                        $fetch_query = mysqli_query($connection, "select * from tbl_employee where role=0");
                                        while($row = mysqli_fetch_array($fetch_query))
                                        {
                                        ?>
                                        <tr>
                                            <td><?php echo $row['first_name']." ".$row['last_name']; ?></td>
                                            <td><?php echo $row['username']; ?></td>
                                            <td><?php echo $row['emailid']; ?></td>
                                            <td><?php echo $row['phone']; ?></td>
                                            
                                            <td>
                                                <span class="custom-badge status-grey"><?php echo $row['department'];?></span>
                                            </td>
                                                <td><?php if($row['status']=="1"){ ?>
                                                    <span class="custom-badge status-green">Active</span>
                                                <?php } else{ ?>
                                                    <span class="custom-badge status-red">Inactive</span>
                                                <?php } ?>
                                                </td>
                                            <td class="text-right">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="edit-employee.php?id=<?php echo $row['id'];?>"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" href="employees.php?ids=<?php echo $row['id'];?>" onclick="return confirmDelete()"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
				
            </div>
            
        </div>


<?php include('footer.php'); ?>
<script language="JavaScript" type="text/javascript">
function confirmDelete(){
    return confirm('Are you sure want to delete this Employee?');
}
</script>
