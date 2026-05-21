<?php require_once '../includes/header.php'; ?>

<div class="container">
    <div class="form-container">
        <h2 class="text-center" style="color: var(--secondary); margin-bottom: 2rem;">Welcome Back</h2>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert text-danger text-center mb-4"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="alert text-success text-center mb-4"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>
        <form action="../api/login_action.php" method="POST" class="needs-validation">
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required placeholder="john@example.com">
            </div>
            <div class="form-group">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label">Password</label>
                    <a href="forgot-password.php" style="font-size: 0.85rem; color: var(--accent);">Forgot Password?</a>
                </div>
                <input type="password" name="password" class="form-control" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Log In</button>
        </form>
        <p class="text-center mt-4 text-muted">
            Don't have an account? <a href="register.php" class="auth-link">Sign up</a>
        </p>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>