<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
        header("Location: ../auth/login.php");
        exit;
    }

    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? 'Uncategorized');
    $location = trim($_POST['location'] ?? '');
    $stipend = trim($_POST['stipend'] ?? 'Unpaid');
    $description = trim($_POST['description'] ?? '');
    $requirements = trim($_POST['requirements'] ?? '');
    $employer_id = $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO internships (employer_id, title, category, description, location, stipend, requirements) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$employer_id, $title, $category, $description, $location, $stipend, $requirements])) {
            header("Location: ../company/employer_dashboard.php?success=Internship posted successfully.");
            exit;
        } else {
            header("Location: ../company/employer_post_new.php?error=Failed to post internship.");
            exit;
        }
    } catch (\PDOException $e) {
        // Redirect with actual error message for debugging
        header("Location: ../company/employer_post_new.php?error=" . urlencode("Database error: " . $e->getMessage()));
        exit;
    }
}
?>
