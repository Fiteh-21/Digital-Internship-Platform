<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Not an admin, redirect to login or show error
    header("Location: ../auth/login.php?error=Access denied. Admin privileges required.");
    exit;
}
?>
