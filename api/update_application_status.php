<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
        header("Location: ../auth/login.php");
        exit;
    }

    $employer_id = $_SESSION['user_id'];
    $application_id = $_POST['application_id'] ?? null;
    $status = $_POST['status'] ?? null;

    if (!$application_id || !in_array($status, ['accepted', 'rejected'])) {
        header("Location: ../company/employer_applications.php?error=Invalid action.");
        exit;
    }

    try {
        // Verify this application belongs to an internship owned by this employer
        $stmt = $pdo->prepare("
            SELECT a.id 
            FROM applications a
            JOIN internships i ON a.internship_id = i.id
            WHERE a.id = ? AND i.employer_id = ?
        ");
        $stmt->execute([$application_id, $employer_id]);
        
        if ($stmt->fetch()) {
            $employer_message = trim($_POST['employer_message'] ?? null);
            
            // Require message if accepting
            if ($status === 'accepted' && empty($employer_message)) {
                header("Location: ../company/employer_applications.php?error=A message is required when accepting an application.");
                exit;
            }

            $updateStmt = $pdo->prepare("UPDATE applications SET status = ?, employer_message = ? WHERE id = ?");
            if ($updateStmt->execute([$status, $employer_message, $application_id])) {
                header("Location: ../company/employer_applications.php?success=Application status updated to " . htmlspecialchars($status) . ".");
                exit;
            } else {
                header("Location: ../company/employer_applications.php?error=Failed to update status.");
                exit;
            }
        } else {
            header("Location: ../company/employer_applications.php?error=Unauthorized action.");
            exit;
        }
    } catch (\PDOException $e) {
        header("Location: ../company/employer_applications.php?error=" . urlencode("Database error: " . $e->getMessage()));
        exit;
    }
}
?>
