<?php 
session_start();
if(empty($_SESSION['name']))
{
    header('location:index.php');
}
include('header.php');
include('includes/connection.php');

$id = $_GET['id'];
$fetch_query = mysqli_query($connection, "select * from tbl_location where id='$id'");
$row = mysqli_fetch_array($fetch_query);

if(isset($_REQUEST['save-location']))
{
      $location = $_REQUEST['location'];

      $update_query = mysqli_query($connection, "update tbl_location set location='$location' where id='$id'");
      if($update_query>0)
      {
          $msg = "Location updated successfully";
          $fetch_query = mysqli_query($connection, "select * from tbl_location where id='$id'");
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
                        <h4 class="page-title">Edit Location</h4>
                    </div>
                    <div class="col-sm-8  text-right m-b-20">
                        <a href="location.php" class="btn btn-primary btn-rounded float-right">Back</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <form method="post">
                          <div class="form-group">
                                <label>Location Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="location" value="<?php  echo $row['location'];  ?>">
                            </div>
                            
                           
                            <div class="m-t-20 text-center">
                                <button class="btn btn-primary submit-btn" name="save-location">Save</button>
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