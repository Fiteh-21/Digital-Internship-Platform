<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit;
}
?>

<div class="dashboard">
    <aside class="sidebar">
        <nav class="sidebar-nav">
            <ul>
                <li><a href="student_dashboard.php"><i class="fas fa-search"></i> Browse</a></li>
                <li><a href="student_applications.php"><i class="fas fa-file-signature"></i> My Applications</a></li>
                <li><a href="student_messages.php"><i class="fas fa-envelope"></i> Messages
                        <?php if ($unread_notif_count > 0): ?><span
                                class="badge bg-danger rounded-pill text-xs"><?= $unread_notif_count ?></span><?php endif; ?></a>
                </li>
                <li><a href="student_profile.php" class="active"><i class="fas fa-user"></i> My Profile</a></li>
                <li class="mt-2rem-pt-1rem-border">
                    <button type="button" onclick="openModalById('deleteAccountModal')"
                        style="background: none; border: none; color: var(--danger) !important; font-family: inherit; font-size: 0.95rem; cursor: pointer; padding: 0.75rem 1.5rem; display: flex; align-items: center; gap: 0.75rem; width: 100%; text-align: left;">
                        <i class="fas fa-user-slash"></i> Delete Account
                    </button>
                </li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <div class="page-shell">
            <?php if (isset($_GET['success'])): ?>
                <div class="alert text-success mb-4"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert text-danger mb-4"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <div class="dashboard-header">
                <div>
                    <h2 class="page-title"><i class="fas fa-user-circle"></i> Welcome,
                        <?= htmlspecialchars($_SESSION['name']) ?>
                    </h2>
                    <p class="page-subtitle">Manage your personal profile and resume details.</p>
                </div>
            </div>

            <?php
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            ?>
            <section id="my-profile" class="mb-8">
                <h3 class="mb-4">My Profile</h3>
                <div class="card max-w-800">
                    <form action="../api/update_profile.php" method="POST" enctype="multipart/form-data"
                        class="needs-validation">
                        <div class="profile-header-layout">
                            <div class="profile-img-container">
                                <?php if (!empty($user['profile_picture'])): ?>
                                    <img src="../<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile Picture"
                                        class="profile-img">
                                <?php else: ?>
                                    <div class="profile-img-placeholder">
                                        <i class="fas fa-user"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="form-group mb-0">
                                    <label class="form-label text-sm-08">Change Photo</label>
                                    <input type="file" name="profile_picture" class="form-control file-input-sm"
                                        accept="image/*">
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="flex gap-4">
                                    <div class="form-group flex-1">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" name="name" class="form-control"
                                            value="<?= htmlspecialchars($user['name']) ?>" required>
                                    </div>
                                    <div class="form-group flex-1">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control bg-light-gray"
                                            value="<?= htmlspecialchars($user['email']) ?>" readonly>
                                    </div>
                                </div>
                                <div class="flex gap-4">
                                    <div class="form-group flex-1">
                                        <label class="form-label">Phone</label>
                                        <input type="text" name="phone" class="form-control"
                                            value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                                    </div>
                                    <div class="form-group flex-1">
                                        <label class="form-label">Location</label>
                                        <input type="text" name="location" class="form-control"
                                            value="<?= htmlspecialchars($user['location'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Professional Headline</label>
                            <input type="text" name="headline" class="form-control"
                                value="<?= htmlspecialchars($user['headline'] ?? '') ?>"
                                placeholder="e.g. Computer Science Student at XYZ University">
                        </div>

                        <div class="form-group">
                            <label class="form-label">About</label>
                            <textarea name="about" class="form-control"
                                rows="3"><?= htmlspecialchars($user['about'] ?? '') ?></textarea>
                        </div>

                        <div class="flex gap-4">
                            <div class="form-group" style="flex: 1;">
                                <label class="form-label">Skills (comma separated)</label>
                                <input type="text" name="skills" class="form-control"
                                    value="<?= htmlspecialchars($user['skills'] ?? '') ?>">
                            </div>
                            <div class="form-group" style="flex: 1;">
                                <label class="form-label">Portfolio Link</label>
                                <input type="text" name="portfolio" class="form-control"
                                    value="<?= htmlspecialchars($user['portfolio'] ?? '') ?>" placeholder="https://...">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Experience</label>
                            <textarea name="experience" class="form-control"
                                rows="3"><?= htmlspecialchars($user['experience'] ?? '') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Education</label>
                            <textarea name="education" class="form-control"
                                rows="3"><?= htmlspecialchars($user['education'] ?? '') ?></textarea>
                        </div>

                        <hr class="divider">

                        <div class="form-group">
                            <label class="form-label">Update Resume (PDF only)</label>
                            <?php if (!empty($user['resume_path'])): ?>
                                <p class="text-sm-085">Current: <a href="../<?= htmlspecialchars($user['resume_path']) ?>"
                                        target="_blank" class="inline-link">View Resume</a></p>
                            <?php endif; ?>
                            <div class="file-upload-wrapper h-100px">
                                <input type="file" name="resume" class="file-upload-input" accept=".pdf">
                                <div class="file-upload-text">
                                    <i class="fas fa-cloud-upload-alt text-xl-15"></i>
                                    <span>Click or drag to upload new resume</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Save Profile</button>
                    </form>

                    <div class="soft-danger mt-2-2rem">
                        <h4 class="text-danger-mb">Danger Zone</h4>
                        <p class="text-muted-sm-mb">Once you delete your account, there is no going back. All your
                            applications will be permanently removed.</p>
                        <button type="button" class="btn btn-outline btn-outline-danger-full"
                            onclick="openModalById('deleteAccountModal')">Delete My Account</button>
                    </div>
                </div>
            </section>
        </div>
    </main>
</div>

<!-- Delete Account Confirmation Modal -->
<div id="deleteAccountModal" class="modal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h5 class="modal-title text-danger">Delete Student Account</h5>
            <button type="button" class="btn-close"
                onclick="closeModalElement(document.getElementById('deleteAccountModal'))"></button>
        </div>
        <div class="modal-body">
            <p><strong>CRITICAL WARNING:</strong> Are you sure you want to permanently delete your account?</p>
            <p class="mt-1rem">This action will permanently remove:</p>
            <ul class="warning-list">
                <li>Your profile details and professional headline</li>
                <li>All your active internship applications</li>
                <li>Your uploaded resume and certificates</li>
                <li>All your messages and notifications</li>
            </ul>
            <p class="text-danger text-danger-bold-sm">
                <i class="fas fa-exclamation-triangle"></i> This action cannot be undone.
            </p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline"
                onclick="closeModalElement(document.getElementById('deleteAccountModal'))">Cancel</button>
            <form action="../api/delete_account.php" method="POST">
                <button type="submit" class="btn btn-primary btn-danger-solid">Yes, Delete My Account</button>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>