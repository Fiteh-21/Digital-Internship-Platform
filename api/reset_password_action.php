<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['reset_authorized']) || !isset($_SESSION['reset_email'])) {
    header("Location: ../auth/forgot-password.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_SESSION['reset_email'];

    if (empty($password) || empty($confirm_password)) {
        header("Location: ../auth/reset-password.php?error=All fields are required.");
        exit;
    }

    if ($password !== $confirm_password) {
        header("Location: ../auth/reset-password.php?error=Passwords do not match.");
        exit;
    }

    // Password complexity check
    if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[\W_]/', $password) || strlen($password) < 4) {
        header("Location: ../auth/reset-password.php?error=Password must be at least 4 characters and include at least one letter, one number, and one symbol.");
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    
    if ($stmt->execute([$hashedPassword, $email])) {
        // Clear reset session
        unset($_SESSION['reset_authorized']);
        unset($_SESSION['reset_email']);
        
        header("Location: ../auth/login.php?success=Password updated successfully. Please log in.");
        exit;
    } else {
        header("Location: ../auth/reset-password.php?error=Failed to update password. Try again.");
        exit;
    }
} else {
    header("Location: ../auth/reset-password.php");
    exit;
}
?>
