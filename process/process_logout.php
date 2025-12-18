<?php
/**
 * Process Logout
 * Destroy session dan redirect ke login
 */

session_start();

// Hapus semua session variables
$_SESSION = array();

// Destroy session
session_destroy();

// Redirect ke login page
header("Location: ../admin/admin_login.php");
exit();
?>