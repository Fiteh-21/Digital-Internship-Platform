<?php
session_start();
require_once '../../includes/db.php';
require_once '../../includes/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int)$_POST['user_id'];
    $message = trim($_POST['message']);

    if (empty($message)) {
        $connector = (strpos($_SERVER['HTTP_REFERER'], '?') === false) ? '?' : '&';
        header("Location: " . $_SERVER['HTTP_REFERER'] . $connector . "error=Message cannot be empty.");
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    if ($stmt->execute([$user_id, $message])) {
        // If coming from manage_internships deletion
        if (isset($_POST['internship_id'])) {
            $internship_id = (int)$_POST['internship_id'];
            $pdo->prepare("DELETE FROM internships WHERE id = ?")->execute([$internship_id]);
            header("Location: ../manage_internships.php?msg=Internship deleted and company notified.");
        } else {
            $connector = (strpos($_SERVER['HTTP_REFERER'], '?') === false) ? '?' : '&';
            header("Location: " . $_SERVER['HTTP_REFERER'] . $connector . "msg=Message sent successfully.");
        }
        exit;
    } else {
        $connector = (strpos($_SERVER['HTTP_REFERER'], '?') === false) ? '?' : '&';
        header("Location: " . $_SERVER['HTTP_REFERER'] . $connector . "error=Failed to send message.");
        exit;
    }
} else {
    header("Location: ../dashboard.php");
    exit;
}
?>
