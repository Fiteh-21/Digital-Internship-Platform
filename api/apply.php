<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
        header("Location: ../auth/login.php");
        exit;
    }

    $student_id = $_SESSION['user_id'];
    $internship_id = $_POST['internship_id'];
    $redirect = $_POST['redirect_to'] ?? '../student/student_dashboard.php';
    $separator = (strpos($redirect, '?') !== false) ? '&' : '?';

    // Handle File Upload
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['resume']['tmp_name'];
        $fileName = $_FILES['resume']['name'];
        $fileSize = $_FILES['resume']['size'];
        $fileType = $_FILES['resume']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        if ($fileExtension !== 'pdf') {
            header("Location: " . $redirect . $separator . "error=Only PDF files are allowed.");
            exit;
        }

        // Sanitize file name
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $uploadFileDir = '../uploads/';
        
        // Create directory if not exists
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0755, true);
        }

        $dest_path = $uploadFileDir . $newFileName;
        $db_path = 'uploads/' . $newFileName; // Path to store in DB

        if(move_uploaded_file($fileTmpPath, $dest_path)) {
            $cover_letter = trim($_POST['cover_letter'] ?? '');
            
            // Insert application into DB
            $stmt = $pdo->prepare("INSERT INTO applications (student_id, internship_id, resume_path, cover_letter) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$student_id, $internship_id, $db_path, $cover_letter])) {
                header("Location: " . $redirect . $separator . "success=Application submitted successfully!");
                exit;
            } else {
                header("Location: " . $redirect . $separator . "error=Database error. Try again.");
                exit;
            }
        } else {
            header("Location: " . $redirect . $separator . "error=Error moving the uploaded file.");
            exit;
        }
    } else {
        header("Location: " . $redirect . $separator . "error=Please upload a resume.");
        exit;
    }
}
?>
