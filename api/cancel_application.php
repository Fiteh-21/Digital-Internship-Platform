<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
        header("Location: ../auth/login.php");
        exit;
    }

    $student_id = $_SESSION['user_id'];
    $application_id = $_POST['application_id'] ?? null;

    if (!$application_id) {
        header("Location: ../student/student_applications.php?error=Invalid action.");
        exit;
    }

    try {
        // Verify this application belongs to the student and is still pending
        $stmt = $pdo->prepare("SELECT id FROM applications WHERE id = ? AND student_id = ? AND status = 'pending'");
        $stmt->execute([$application_id, $student_id]);
        
        if ($stmt->fetch()) {
            $deleteStmt = $pdo->prepare("DELETE FROM applications WHERE id = ?");
            if ($deleteStmt->execute([$application_id])) {
                header("Location: ../student/student_applications.php?success=Application cancelled successfully.");
                exit;
            } else {
                header("Location: ../student/student_applications.php?error=Failed to cancel application.");
                exit;
            }
        } else {
            header("Location: ../student/student_applications.php?error=Unauthorized action or application can no longer be cancelled.");
            exit;
        }
    } catch (\PDOException $e) {
        header("Location: ../student/student_applications.php?error=" . urlencode("Database error: " . $e->getMessage()));
        exit;
    }
}
?>
