<?php
$servername = "localhost";
$username = "root"; // or your DB username
$password = ""; // your DB password, usually empty for XAMPP
$dbname = "eam_db"; // replace with your DB name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
