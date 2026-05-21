<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
        header("Location: ../auth/login.php");
        exit;
    }

    $student_id = $_SESSION['user_id'];
    
    // Fetch current user data to keep existing paths if no new file is uploaded
    $stmt = $pdo->prepare("SELECT resume_path, profile_picture FROM users WHERE id = ?");
    $stmt->execute([$student_id]);
    $user = $stmt->fetch();
    
    $resume_path = $user['resume_path'];
    $profile_picture = $user['profile_picture'];

    // Handle Text Fields
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $headline = trim($_POST['headline'] ?? '');
    $about = trim($_POST['about'] ?? '');
    $skills = trim($_POST['skills'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    $education = trim($_POST['education'] ?? '');
    $portfolio = trim($_POST['portfolio'] ?? '');

    $uploadFileDir = '../uploads/';
    if (!is_dir($uploadFileDir)) {
        mkdir($uploadFileDir, 0755, true);
    }

    // Handle Resume Upload
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $fileName = $_FILES['resume']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if ($fileExtension === 'pdf') {
            $newFileName = md5(time() . $fileName . 'resume') . '.pdf';
            $dest_path = $uploadFileDir . $newFileName;
            if(move_uploaded_file($_FILES['resume']['tmp_name'], $dest_path)) {
                $resume_path = 'uploads/' . $newFileName;
            } else {
                header("Location: ../student/student_profile.php?error=Failed to save uploaded resume file to disk.");
                exit;
            }
        } else {
            header("Location: ../student/student_profile.php?error=Invalid resume format. Only PDF files are allowed.");
            exit;
        }
    }

    // Handle Profile Picture Upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $fileName = $_FILES['profile_picture']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedImageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($fileExtension, $allowedImageTypes)) {
            $newFileName = md5(time() . $fileName . 'pic') . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;
            if(move_uploaded_file($_FILES['profile_picture']['tmp_name'], $dest_path)) {
                $profile_picture = 'uploads/' . $newFileName;
            } else {
                header("Location: ../student/student_profile.php?error=Failed to save uploaded profile picture to disk.");
                exit;
            }
        } else {
            header("Location: ../student/student_profile.php?error=Invalid profile picture format. Only JPG, PNG, GIF, and WebP are allowed.");
            exit;
        }
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE users SET 
            name = ?, phone = ?, location = ?, headline = ?, about = ?, 
            skills = ?, experience = ?, education = ?, portfolio = ?, 
            resume_path = ?, profile_picture = ? 
            WHERE id = ?
        ");
        if ($stmt->execute([
            $name, $phone, $location, $headline, $about, 
            $skills, $experience, $education, $portfolio, 
            $resume_path, $profile_picture, $student_id
        ])) {
            $_SESSION['name'] = $name; // Update session name in case it changed
            header("Location: ../student/student_profile.php?success=Profile updated successfully!");
            exit;
        } else {
            header("Location: ../student/student_profile.php?error=Database error. Try again.");
            exit;
        }
    } catch (\PDOException $e) {
        header("Location: ../student/student_profile.php?error=" . urlencode("Database error: " . $e->getMessage()));
        exit;
    }
}
?>
