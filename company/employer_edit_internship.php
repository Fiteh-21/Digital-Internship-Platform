<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
    header("Location: ../auth/login.php");
    exit;
}

$employer_id = $_SESSION['user_id'];
$internship_id = $_GET['id'] ?? null;

if (!$internship_id) {
    header("Location: employer_dashboard.php?error=No internship specified.");
    exit;
}

// Fetch existing internship
$stmt = $pdo->prepare("SELECT * FROM internships WHERE id = ? AND employer_id = ?");
$stmt->execute([$internship_id, $employer_id]);
$internship = $stmt->fetch();

if (!$internship) {
    header("Location: employer_dashboard.php?error=Internship not found or unauthorized.");
    exit;
}
?>

<div class="dashboard">
    <aside class="sidebar">
        <nav class="sidebar-nav">
            <ul>
                <li><a href="employer_dashboard.php"><i class="fas fa-briefcase"></i> My Postings</a></li>
                <li><a href="employer_applications.php"><i class="fas fa-users"></i> Applications <?php if(isset($pending_count) && $pending_count > 0): ?><span class="badge" style="background: var(--danger); color: white; border-radius: 50%; padding: 0.15rem 0.4rem; font-size: 0.75rem; margin-left: 0.25rem; vertical-align: top;"><?= $pending_count ?></span><?php endif; ?></a></li>
                <li><a href="employer_messages.php"><i class="fas fa-envelope"></i> Messages <?php if($unread_notif_count > 0): ?><span class="badge bg-danger rounded-pill" style="font-size: 0.7rem;"><?= $unread_notif_count ?></span><?php endif; ?></a></li>
                <li><a href="employer_post_new.php"><i class="fas fa-plus-circle"></i> Post New</a></li>
                <li class="mt-2rem-pt-1rem-border">
                    <button type="button" onclick="openModalById('deleteAccountModal')" class="btn-danger-sidebar">
                        <i class="fas fa-user-slash"></i> Delete Account
                    </button>
                </li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <div class="page-shell">
        <?php if(isset($_GET['success'])): ?>
            <div class="alert text-success mb-4"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
            <div class="alert text-danger mb-4"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <div class="dashboard-header">
            <div>
                <h2 class="page-title"><i class="fas fa-pen-to-square"></i> Edit Internship</h2>
                <p class="page-subtitle">Update details to keep your listing accurate and competitive.</p>
            </div>
        </div>

        <section id="edit-internship" class="mb-8">
            <div class="form-container" style="margin: 0; max-width: 600px;">
                <form action="../api/edit_internship.php" method="POST" class="needs-validation">
                    <input type="hidden" name="internship_id" value="<?= $internship['id'] ?>">
                    
                    <div class="form-group">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($internship['title']) ?>" required>
                    </div>
                    <div class="flex gap-4">
                        <div class="form-group" style="flex: 1;">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-control" required>
                                <?php
                                $categories = ['IT & Software', 'Marketing & Sales', 'Finance & Accounting', 'Design & Creative', 'Engineering', 'Other'];
                                foreach ($categories as $cat) {
                                    $selected = ($internship['category'] === $cat) ? 'selected' : '';
                                    echo "<option value=\"$cat\" $selected>$cat</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($internship['location']) ?>" required>
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label class="form-label">Stipend</label>
                            <input type="text" name="stipend" class="form-control" value="<?= htmlspecialchars($internship['stipend']) ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" required><?= htmlspecialchars($internship['description']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Requirements</label>
                        <textarea name="requirements" class="form-control"><?= htmlspecialchars($internship['requirements']) ?></textarea>
                    </div>
                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="employer_dashboard.php" class="btn btn-outline" style="line-height: 2.2;">Cancel</a>
                    </div>
                </form>
            </div>
        </section>
        </div>
    </main>
</div>

<!-- Delete Account Confirmation Modal -->
<div id="deleteAccountModal" class="modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-danger">Delete Company Account</h5>
                <button type="button" class="btn-close" onclick="closeModalElement(document.getElementById('deleteAccountModal'))"></button>
            </div>
            <div class="modal-body">
                <p><strong>CRITICAL WARNING:</strong> Are you sure you want to permanently delete your company account?</p>
                <p class="mt-1rem">This action will permanently remove:</p>
                <ul class="warning-list">
                    <li>Your company profile and all details</li>
                    <li>All your active internship postings</li>
                    <li>All applications received from students</li>
                    <li>All your messages and notifications</li>
                </ul>
                <p class="text-danger text-danger-bold-sm">
                    <i class="fas fa-exclamation-triangle"></i> This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline" onclick="closeModalElement(document.getElementById('deleteAccountModal'))">Cancel</button>
                <form action="../api/delete_account.php" method="POST">
                    <button type="submit" class="btn btn-primary btn-danger-solid">Yes, Delete My Account</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
