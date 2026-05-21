<?php require_once '../includes/header.php'; ?>

<div class="container">
    <div class="form-container">
        <h2 class="text-center" style="color: var(--secondary); margin-bottom: 2rem;">Create an Account</h2>
        <?php if(isset($_GET['error'])): ?>
            <div class="alert text-danger text-center mb-4"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
        <form action="../api/register_action.php" method="POST" enctype="multipart/form-data" class="needs-validation">
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" required placeholder="John Doe">
            </div>
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required placeholder="john@example.com">
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required placeholder="L3tter$123">
                <small class="text-muted">Must include: Letter, Number, Symbol, and min. 4 chars.</small>
            </div>
            <div class="form-group">
                <label class="form-label">I am a...</label>
                <select name="role" id="roleSelect" class="form-control" required>
                    <option value="student">Student (Looking for internships)</option>
                    <option value="employer">Employer (Posting internships)</option>
                </select>
            </div>
            <hr style="border: 0.5px solid #eee; margin: 1.5rem 0;">
            <div class="form-group">
                <label class="form-label">Security Question <span class="text-muted">(For recovery)</span></label>
                <select name="security_question" class="form-control" required>
                    <option value="What is your favorite book?">What is your favorite book?</option>
                    <option value="What is your best friend's name?">What is your best friend's name?</option>
                    <option value="What was your first pet's name?">What was your first pet's name?</option>
                    <option value="What city were you born in?">What city were you born in?</option>
                    <option value="What is your favorite color?">What is your favorite color?</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Secret Answer</label>
                <input type="text" name="security_answer" class="form-control" required placeholder="Type your secret word here...">
                <small class="text-muted">Keep this safe! You'll need it if you forget your password.</small>
            </div>
            <div class="form-group" id="resumeUploadGroup">
                <label class="form-label">Upload Resume (PDF only, Optional)</label>
                <div class="file-upload-wrapper" style="height: 80px;">
                    <input type="file" name="resume" class="file-upload-input" accept=".pdf">
                    <div class="file-upload-text">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 1.5rem;"></i>
                        <span style="font-size: 0.85rem;">Click or drag to upload</span>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Sign Up</button>
        </form>

        <script>
            document.getElementById('roleSelect').addEventListener('change', function() {
                var resumeGroup = document.getElementById('resumeUploadGroup');
                if(this.value === 'student') {
                    resumeGroup.style.display = 'block';
                } else {
                    resumeGroup.style.display = 'none';
                }
            });
        </script>
        <p class="text-center mt-4 text-muted">
            Already have an account? <a href="login.php" class="auth-link">Log in</a>
        </p>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
