<?php 
session_start();
if (!isset($_SESSION['reset_authorized']) || !isset($_SESSION['reset_email'])) {
    header("Location: forgot-password.php");
    exit;
}
require_once '../includes/header.php'; 
?>

<div class="container">
    <div class="form-container">
        <h2 class="text-center" style="color: var(--secondary); margin-bottom: 2rem;">Set New Password</h2>
        <p class="text-center text-muted mb-4">You have successfully verified your identity. Please choose a new password.</p>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="alert text-danger text-center mb-4"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <form action="../api/reset_password_action.php" method="POST">
            <div class="form-group">
                <label class="form-label">New Password</label>
                <input type="password" name="password" class="form-control" required placeholder="L3tter$123" autofocus>
                <small class="text-muted">Must include: Letter, Number, Symbol, and min. 4 chars.</small>
            </div>
            <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Update Password</button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
