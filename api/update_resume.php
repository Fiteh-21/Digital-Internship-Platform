<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
        header("Location: ../auth/login.php");
        exit;
    }

    $student_id = $_SESSION['user_id'];

    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['resume']['tmp_name'];
        $fileName = $_FILES['resume']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($fileExtension !== 'pdf') {
            header("Location: ../student/student_dashboard.php?error=Only PDF files are allowed.");
            exit;
        }

        $newFileName = md5(time() . $fileName) . '.pdf';
        $uploadFileDir = '../uploads/';
        
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0755, true);
        }

        $dest_path = $uploadFileDir . $newFileName;
        $db_path = 'uploads/' . $newFileName;

        if(move_uploaded_file($fileTmpPath, $dest_path)) {
            $stmt = $pdo->prepare("UPDATE users SET resume_path = ? WHERE id = ?");
            if ($stmt->execute([$db_path, $student_id])) {
                header("Location: ../student/student_dashboard.php?success=Resume updated successfully!");
                exit;
            } else {
                header("Location: ../student/student_dashboard.php?error=Database error. Try again.");
                exit;
            }
        } else {
            header("Location: ../student/student_dashboard.php?error=Error moving the uploaded file.");
            exit;
        }
    } else {
        header("Location: ../student/student_dashboard.php?error=Please upload a valid PDF resume.");
        exit;
    }
}
?>
