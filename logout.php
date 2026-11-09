<?php
/**
 * Halaman Logout
 * 
 * Menghancurkan session dan redirect ke halaman login.
 */

require_once __DIR__ . '/init.php';

$userModel = new User($db);
$userModel->logout();

// Redirect ke halaman login
header("Location: " . BASE_URL . "/login.php");
exit();

