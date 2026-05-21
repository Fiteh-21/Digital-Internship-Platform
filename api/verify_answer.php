<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $answer = trim(strtolower($_POST['answer']));

    if (empty($email) || empty($answer)) {
        header("Location: ../auth/forgot-password.php?error=All fields are required.");
        exit;
    }

    $stmt = $pdo->prepare("SELECT security_answer FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($answer, $user['security_answer'])) {
        // Success! Authorize the reset for this session
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_authorized'] = true;
        header("Location: ../auth/reset-password.php");
        exit;
    } else {
        header("Location: ../auth/forgot-password.php?error=Incorrect answer. Please try again.");
        exit;
    }
} else {
    header("Location: ../auth/forgot-password.php");
    exit;
}
?>
