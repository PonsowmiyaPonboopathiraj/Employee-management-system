
<?php
$server = "localhost";
$username = "root";
$password = "";
$database = "eam_db";

$connection = mysqli_connect($server, $username, $password, $database);

if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
