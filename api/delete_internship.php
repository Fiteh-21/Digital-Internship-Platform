<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
        header("Location: ../auth/login.php");
        exit;
    }

    $employer_id = $_SESSION['user_id'];
    $internship_id = $_POST['internship_id'];

    if (empty($internship_id)) {
        header("Location: ../company/employer_dashboard.php?error=Invalid internship ID.");
        exit;
    }

    try {
        // First verify this internship belongs to the current employer
        $checkStmt = $pdo->prepare("SELECT id FROM internships WHERE id = ? AND employer_id = ?");
        $checkStmt->execute([$internship_id, $employer_id]);
        
        if ($checkStmt->fetch()) {
            // Delete the internship (due to ON DELETE CASCADE, applications will also be deleted)
            $deleteStmt = $pdo->prepare("DELETE FROM internships WHERE id = ?");
            if ($deleteStmt->execute([$internship_id])) {
                header("Location: ../company/employer_dashboard.php?success=Internship deleted successfully.");
                exit;
            } else {
                header("Location: ../company/employer_dashboard.php?error=Failed to delete internship.");
                exit;
            }
        } else {
            header("Location: ../company/employer_dashboard.php?error=Unauthorized action or internship not found.");
            exit;
        }
    } catch (\PDOException $e) {
        header("Location: ../company/employer_dashboard.php?error=" . urlencode("Database error: " . $e->getMessage()));
        exit;
    }
} else {
    header("Location: ../company/employer_dashboard.php");
    exit;
}
?>
