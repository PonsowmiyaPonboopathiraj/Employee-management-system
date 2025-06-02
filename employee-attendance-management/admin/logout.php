<?php 
session_start();
if(!empty($_SESSION['name']))
{
	unset($_SESSION['name']);
	header('location:index.php');
}
?>