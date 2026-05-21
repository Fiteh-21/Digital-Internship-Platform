<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header("Location: ../login.php?error=Please fill all fields.");
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        $redirect = 'student/student_dashboard.php';
        if ($user['role'] === 'employer') {
            $redirect = 'company/employer_dashboard.php';
        } elseif ($user['role'] === 'admin') {
            $redirect = 'admin/dashboard.php';
        }
        header("Location: ../$redirect");
        exit;
    } else {
        header("Location: ../auth/login.php?error=Invalid email or password.");
        exit;
    }
}
?>
