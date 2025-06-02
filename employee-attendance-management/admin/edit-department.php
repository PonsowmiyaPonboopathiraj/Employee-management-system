<?php 
session_start();
if(empty($_SESSION['name']))
{
    header('location:index.php');
}
include('header.php');
include('includes/connection.php');

$id = $_GET['id'];
$fetch_query = mysqli_query($connection, "select * from tbl_department where id='$id'");
$row = mysqli_fetch_array($fetch_query);

if(isset($_REQUEST['save-department']))
{
      $department_id = $_REQUEST['departmentid'];
      $department_name = $_REQUEST['department'];
      $status = $_REQUEST['status'];



      $update_query = mysqli_query($connection, "update tbl_department set department_id='$department_id', department_name='$department_name', status='$status' where id='$id'");
      if($update_query>0)
      {
          $msg = "Department updated successfully";
          $fetch_query = mysqli_query($connection, "select * from tbl_department where id='$id'");
          $row = mysqli_fetch_array($fetch_query);   
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
                        <h4 class="page-title">Edit Department</h4>
                    </div>
                    <div class="col-sm-8  text-right m-b-20">
                        <a href="departments.php" class="btn btn-primary btn-rounded float-right">Back</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <form method="post">
                          <div class="form-group">
                                <label>Department Id</label>
                                <input class="form-control" type="text" name="departmentid" value="<?php  echo $row['department_id'];  ?>">
                            </div>
                            <div class="form-group">
                                <label>Department Name</label>
                                <input class="form-control" type="text" name="department" value="<?php  echo $row['department_name'];  ?>">
                            </div>
                            
                            <div class="form-group">
                                <label class="display-block">Department Status</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="product_active" value="1" <?php if($row['status']==1) { echo 'checked' ; } ?>>
                                    <label class="form-check-label" for="product_active">
                                    Active
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="product_inactive" value="0" <?php if($row['status']==0) { echo 'checked' ; } ?>>
                                    <label class="form-check-label" for="product_inactive">
                                    Inactive
                                    </label>
                                </div>
                            </div>
                            <div class="m-t-20 text-center">
                                <button class="btn btn-primary submit-btn" name="save-department">Save</button>
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