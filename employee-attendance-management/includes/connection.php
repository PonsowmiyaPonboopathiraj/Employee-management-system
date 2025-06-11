<?php
$host = "localhost";
$user = "root";
$pass = ""; // usually blank for XAMPP
$dbname = "eam_db";

$connection = mysqli_connect($host, $user, $pass, $dbname);

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
