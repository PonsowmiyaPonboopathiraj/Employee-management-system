<?php 
if(!empty($_SESSION['name']))
{
	unset($_SESSION['name']);
	header('location:login.php');
}
?>