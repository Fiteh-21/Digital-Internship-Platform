<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $security_question = $_POST['security_question'];
    $security_answer = trim(strtolower($_POST['security_answer']));

    // Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($security_answer) || !in_array($role, ['student', 'employer'])) {
        header("Location: ../auth/register.php?error=All fields are required.");
        exit;
    }

    // Password complexity check
    if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[\W_]/', $password) || strlen($password) < 4) {
        header("Location: ../auth/register.php?error=Password must be at least 4 characters and include at least one letter, one number, and one symbol.");
        exit;
    }

    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        header("Location: ../auth/register.php?error=Email already registered.");
        exit;
    }

    // Handle resume upload
    $db_path = null;
    if ($role === 'student' && isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['resume']['tmp_name'];
        $fileName = $_FILES['resume']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        if ($fileExtension === 'pdf') {
            $newFileName = md5(time() . $fileName) . '.pdf';
            $uploadFileDir = '../uploads/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            if(move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
                $db_path = 'uploads/' . $newFileName;
            }
        }
    }

    // Insert user
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $hashedAnswer = password_hash($security_answer, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, resume_path, security_question, security_answer) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $hashedPassword, $role, $db_path, $security_question, $hashedAnswer])) {
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['role'] = $role;
        $_SESSION['name'] = $name;
        
        $redirect = $role === 'student' ? 'student/student_dashboard.php' : 'company/employer_dashboard.php';
        header("Location: ../$redirect");
        exit;
    } else {
        header("Location: ../auth/register.php?error=Registration failed. Try again.");
        exit;
    }
}
?>
