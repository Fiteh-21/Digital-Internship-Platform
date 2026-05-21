<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../auth/login.php");
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];

    try {
        // First, fetch user info to delete files if any
        $stmt = $pdo->prepare("SELECT profile_picture, resume_path FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if ($user) {
            // Delete files from disk if they exist
            if ($user['profile_picture'] && file_exists('../' . $user['profile_picture'])) {
                unlink('../' . $user['profile_picture']);
            }
            if ($user['resume_path'] && file_exists('../' . $user['resume_path'])) {
                unlink('../' . $user['resume_path']);
            }
        }

        // Also delete resumes from applications if any
        if ($role === 'student') {
            $stmt = $pdo->prepare("SELECT resume_path FROM applications WHERE student_id = ?");
            $stmt->execute([$user_id]);
            $apps = $stmt->fetchAll();
            foreach ($apps as $app) {
                if ($app['resume_path'] && file_exists('../' . $app['resume_path'])) {
                    unlink('../' . $app['resume_path']);
                }
            }
        }

        // Delete the user (cascade will handle applications and internships)
        $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        if ($deleteStmt->execute([$user_id])) {
            // Log out and redirect
            session_destroy();
            header("Location: ../index.php?success=Account deleted successfully.");
            exit;
        } else {
            $redirect = ($role === 'student') ? '../student/student_profile.php' : '../company/employer_dashboard.php';
            header("Location: $redirect?error=Failed to delete account.");
            exit;
        }
    } catch (\PDOException $e) {
        $redirect = ($role === 'student') ? '../student/student_profile.php' : '../company/employer_dashboard.php';
        header("Location: $redirect?error=" . urlencode("Database error: " . $e->getMessage()));
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
?>
