<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
        header("Location: ../auth/login.php");
        exit;
    }

    $internship_id = $_POST['internship_id'] ?? null;
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? 'Uncategorized');
    $location = trim($_POST['location'] ?? '');
    $stipend = trim($_POST['stipend'] ?? 'Unpaid');
    $description = trim($_POST['description'] ?? '');
    $requirements = trim($_POST['requirements'] ?? '');
    $employer_id = $_SESSION['user_id'];

    if (!$internship_id) {
        header("Location: ../company/employer_dashboard.php?error=Invalid action.");
        exit;
    }

    try {
        // Verify this internship belongs to the current employer
        $checkStmt = $pdo->prepare("SELECT id FROM internships WHERE id = ? AND employer_id = ?");
        $checkStmt->execute([$internship_id, $employer_id]);
        
        if ($checkStmt->fetch()) {
            $updateStmt = $pdo->prepare("
                UPDATE internships 
                SET title = ?, category = ?, description = ?, location = ?, stipend = ?, requirements = ? 
                WHERE id = ?
            ");
            
            if ($updateStmt->execute([$title, $category, $description, $location, $stipend, $requirements, $internship_id])) {
                header("Location: ../company/employer_dashboard.php?success=Internship updated successfully.");
                exit;
            } else {
                header("Location: ../company/employer_edit_internship.php?id=$internship_id&error=Failed to update internship.");
                exit;
            }
        } else {
            header("Location: ../company/employer_dashboard.php?error=Unauthorized action.");
            exit;
        }
    } catch (\PDOException $e) {
        header("Location: ../company/employer_edit_internship.php?id=$internship_id&error=" . urlencode("Database error: " . $e->getMessage()));
        exit;
    }
}
?>
