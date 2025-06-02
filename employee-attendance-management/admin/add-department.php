<?php
session_start();
if(empty($_SESSION['name']))
{
    header('location:index.php');
}
include('header.php');
include('includes/connection.php');
    
    if(isset($_REQUEST['add-department']))
    {
      $department_id = $_REQUEST['departmentid'];
      $department_name = $_REQUEST['department'];
      $status = $_REQUEST['status'];

      
      $insert_query = mysqli_query($connection, "insert into tbl_department set department_id='$department_id', department_name='$department_name',status='$status'");

      if($insert_query>0)
      {
          $msg = "Department created successfully";
      }
      else
      {
          $msg = "Error!";
      }
    }
?>
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-sm-4 ">
                        <h4 class="page-title">Add Department</h4>
                         
                    </div>
                    <div class="col-sm-8  text-right m-b-20">
                        <a href="departments.php" class="btn btn-primary btn-rounded float-right">Back</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <form method="post">
                            <div class="form-group">
                                <label>Department ID</label>
                                <input class="form-control" type="text" name="departmentid" required>
                            </div>
                            <div class="form-group">
                                <label>Department Name</label>
                                <input class="form-control" type="text" name="department" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="display-block">Department Status</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="product_active" value="1" checked>
                                    <label class="form-check-label" for="product_active">
                                    Active
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="product_inactive" value="0">
                                    <label class="form-check-label" for="product_inactive">
                                    Inactive
                                    </label>
                                </div>
                            </div>
                            <div class="m-t-20 text-center">
                                <button class="btn btn-primary submit-btn" name="add-department">Add Department</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
		</div>
    
<?php
    include('footer.php');
?>
<script type="text/javascript">
     <?php
        if(isset($msg)) {
            echo 'swal("' . $msg . '");';
        }
    ?>
</script>