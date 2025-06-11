<?php
$user_role = $_SESSION['role'] ?? '0';
if ($user_role == '2') {
    // Team Leader Menu
} elseif ($user_role == '0') {
    // Employee Menu
}
?>
