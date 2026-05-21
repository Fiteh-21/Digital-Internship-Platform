<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

$step = 1;
$email = '';
$question = '';
$error = '';

if (isset($_POST['email']) && !isset($_POST['answer'])) {
    $email = trim($_POST['email']);
    $stmt = $pdo->prepare("SELECT security_question FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $step = 2;
        $question = $user['security_question'];
    } else {
        $error = "No account found with that email address.";
    }
} elseif (isset($_POST['email']) && isset($_POST['answer'])) {
    // This will be handled by verify_answer.php
    header("Location: ../api/verify_answer.php?email=" . urlencode($_POST['email']) . "&answer=" . urlencode($_POST['answer']));
    exit;
}
?>

<div class="container">
    <div class="form-container">
        <h2 class="text-center text-secondary mb-2rem">Reset Password</h2>

        <?php if ($error): ?>
            <div class="alert text-danger text-center mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($step == 1): ?>
            <p class="text-center text-muted mb-4">Enter your email to retrieve your security question.</p>
            <form action="forgot-password.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required placeholder="john@example.com"
                        value="<?= htmlspecialchars($email) ?>">
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-1rem">Next</button>
            </form>
        <?php else: ?>
            <p class="text-center text-muted mb-4">Please answer your security question to verify your identity.</p>
            <form action="../api/verify_answer.php" method="POST">
                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                <div class="form-group">
                    <label class="form-label">Security Question</label>
                    <div class="p-3 bg-light rounded mb-3 border-left-primary">
                        <strong><?= htmlspecialchars($question) ?></strong>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Your Secret Answer</label>
                    <input type="text" name="answer" class="form-control" required placeholder="Type your answer here..."
                        autofocus>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-1rem">Verify & Reset</button>
                <a href="forgot-password.php" class="btn btn-link text-center d-block mt-2 auth-link text-sm-09">Try a
                    different email</a>
            </form>
        <?php endif; ?>

        <p class="text-center mt-4 text-muted">
            Remembered your password? <a href="login.php" class="auth-link">Log in</a>
        </p>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>