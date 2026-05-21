<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
    header("Location: ../auth/login.php");
    exit;
}

$employer_id = $_SESSION['user_id'];

// Fetch applications for these internships
$stmt_apps = $pdo->prepare("
    SELECT a.*, u.name as student_name, u.email as student_email, i.title as internship_title 
    FROM applications a 
    JOIN users u ON a.student_id = u.id 
    JOIN internships i ON a.internship_id = i.id 
    WHERE i.employer_id = ? 
    ORDER BY a.applied_at DESC
");
$stmt_apps->execute([$employer_id]);
$applications = $stmt_apps->fetchAll();
?>

<div class="dashboard">
    <aside class="sidebar">
        <nav class="sidebar-nav">
            <ul>
                <li><a href="employer_dashboard.php"><i class="fas fa-briefcase"></i> My Postings</a></li>
                <li><a href="employer_applications.php" class="active"><i class="fas fa-users"></i> Applications <?php if(isset($pending_count) && $pending_count > 0): ?><span class="badge" style="background: var(--danger); color: white; border-radius: 50%; padding: 0.15rem 0.4rem; font-size: 0.75rem; margin-left: 0.25rem; vertical-align: top;"><?= $pending_count ?></span><?php endif; ?></a></li>
                <li><a href="employer_messages.php"><i class="fas fa-envelope"></i> Messages <?php if($unread_notif_count > 0): ?><span class="badge bg-danger rounded-pill" style="font-size: 0.7rem;"><?= $unread_notif_count ?></span><?php endif; ?></a></li>
                <li><a href="employer_post_new.php"><i class="fas fa-plus-circle"></i> Post New</a></li>
                <li style="margin-top: 2rem; border-top: 1px solid #eee; padding-top: 1rem;">
                    <button type="button" onclick="openModalById('deleteAccountModal')"
                        style="background: none; border: none; color: var(--danger); font-family: inherit; font-size: 0.95rem; cursor: pointer; padding: 0.75rem 1.5rem; display: flex; align-items: center; gap: 0.75rem; width: 100%; text-align: left;">
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

        <div class="dashboard-header">
            <div>
                <h2 class="page-title"><i class="fas fa-users"></i> Welcome, <?= htmlspecialchars($_SESSION['name']) ?></h2>
                <p class="page-subtitle">Review candidates and communicate decisions faster.</p>
            </div>
            <a href="employer_post_new.php" class="btn btn-primary">Post Internship</a>
        </div>

        <section id="applications" class="mb-8">
            <h3 class="mb-4">Recent Applications</h3>
            <?php if(empty($applications)): ?>
                <p class="text-muted">No applications received yet.</p>
            <?php else: ?>
                <table class="table-divided">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Internship</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($applications as $app): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($app['student_name']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($app['student_email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($app['internship_title']) ?></td>
                                <td>
                                    <span class="badge status-<?= htmlspecialchars($app['status']) ?>">
                                        <?= ucfirst(htmlspecialchars($app['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="flex gap-4 items-center" style="flex-wrap: wrap;">
                                        <a href="view_student_profile.php?id=<?= $app['student_id'] ?>" class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; border-color: var(--primary); color: var(--primary); border-radius: 8px;">
                                            <i class="fas fa-user"></i> Profile
                                        </a>
                                        <a href="../<?= htmlspecialchars($app['resume_path']) ?>" target="_blank" class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; border-color: var(--primary); color: var(--primary); border-radius: 8px;">
                                            <i class="fas fa-file-pdf"></i> Resume
                                        </a>
                                        
                                        <?php if (!empty($app['cover_letter'])): ?>
                                            <div class="glass-note" style="width: 100%; margin-top: 0.5rem; font-size: 0.85rem;">
                                                <strong>Student Message:</strong><br>
                                                <?= nl2br(htmlspecialchars($app['cover_letter'])) ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($app['status'] === 'pending' || $app['status'] === 'reviewed'): ?>
                                            <div style="width: 100%; margin-top: 0.5rem; border-top: 1px solid #eee; padding-top: 0.5rem;">
                                                <form action="../api/update_application_status.php" method="POST" style="display:flex; gap: 0.5rem; align-items: flex-start; flex-direction: column;">
                                                    <input type="hidden" name="application_id" value="<?= $app['id'] ?>">
                                                    <textarea name="employer_message" class="form-control" placeholder="Message to student (required for Accept, optional for Reject)" style="font-size: 0.85rem; padding: 0.4rem; min-height: 50px;"></textarea>
                                                    <div style="display: flex; gap: 0.5rem;">
                                                        <button type="submit" name="status" value="accepted" class="btn btn-outline text-success" style="padding: 0.25rem 0.5rem; font-size: 0.85rem; border-color: var(--success); color: var(--success);">Accept</button>
                                                        <button type="submit" name="status" value="rejected" class="btn btn-outline text-danger" style="padding: 0.25rem 0.5rem; font-size: 0.85rem; border-color: var(--danger); color: var(--danger);" formnovalidate>Reject</button>
                                                    </div>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
        </div>
    </main>
</div>

<!-- Delete Account Confirmation Modal -->
<div id="deleteAccountModal" class="modal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h5 class="modal-title text-danger">Delete Company Account</h5>
            <button type="button" class="btn-close" onclick="closeModalElement(document.getElementById('deleteAccountModal'))"></button>
        </div>
        <div class="modal-body">
            <p><strong>CRITICAL WARNING:</strong> Are you sure you want to permanently delete your company account?</p>
            <p style="margin-top: 1rem;">This action will permanently remove:</p>
            <ul style="margin-top: 0.5rem; padding-left: 1.5rem; font-size: 0.9rem; color: #64748b;">
                <li>All your internship postings</li>
                <li>All received student applications</li>
                <li>All your messages and notifications</li>
            </ul>
            <p class="text-danger" style="font-size: 0.9rem; margin-top: 1rem; font-weight: 600;">
                <i class="fas fa-exclamation-triangle"></i> This action cannot be undone.
            </p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="closeModalElement(document.getElementById('deleteAccountModal'))">Cancel</button>
            <form action="../api/delete_account.php" method="POST">
                <button type="submit" class="btn btn-primary" style="background: var(--danger); border-color: var(--danger);">Yes, Delete My Account</button>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
