<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit;
}

$student_id = $_SESSION['user_id'];

// Fetch student's applications
$stmt_apps = $pdo->prepare("
    SELECT a.*, i.title as internship_title, u.name as employer_name 
    FROM applications a 
    JOIN internships i ON a.internship_id = i.id 
    JOIN users u ON i.employer_id = u.id 
    WHERE a.student_id = ? 
    ORDER BY a.applied_at DESC
");
$stmt_apps->execute([$student_id]);
$my_applications = $stmt_apps->fetchAll();
?>

<div class="dashboard">
    <aside class="sidebar">
        <nav class="sidebar-nav">
            <ul>
                <li><a href="student_dashboard.php"><i class="fas fa-search"></i> Browse</a></li>
                <li><a href="student_applications.php" class="active"><i class="fas fa-file-signature"></i> My
                        Applications</a></li>
                <li><a href="student_messages.php"><i class="fas fa-envelope"></i> Messages
                        <?php if ($unread_notif_count > 0): ?><span class="badge bg-danger rounded-pill"
                                style="font-size: 0.7rem;"><?= $unread_notif_count ?></span><?php endif; ?></a></li>
                <li><a href="student_profile.php"><i class="fas fa-user"></i> My Profile</a></li>
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
            <?php if (isset($_GET['success'])): ?>
                <div class="alert text-success mb-4"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert text-danger mb-4"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <div class="dashboard-header">
                <div>
                    <h2 class="page-title"><i class="fas fa-file-signature"></i> Welcome,
                        <?= htmlspecialchars($_SESSION['name']) ?>
                    </h2>
                    <p class="page-subtitle">Track your applications and keep them updated.</p>
                </div>
            </div>

            <section id="my-applications" class="mb-8">
                <h3 class="mb-4">My Applications</h3>
                <?php if (empty($my_applications)): ?>
                    <p class="text-muted">You haven't applied to any internships yet.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Internship</th>
                                <th>Company</th>
                                <th>Applied Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($my_applications as $app): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($app['internship_title']) ?></strong></td>
                                    <td><?= htmlspecialchars($app['employer_name']) ?></td>
                                    <td><?= date('M j, Y', strtotime($app['applied_at'])) ?></td>
                                    <td>
                                        <span class="badge status-<?= htmlspecialchars($app['status']) ?>">
                                            <?= ucfirst(htmlspecialchars($app['status'])) ?>
                                        </span>
                                        <?php if (!empty($app['employer_message'])): ?>
                                            <div class="glass-note"
                                                style="margin-top: 0.5rem; font-size: 0.85rem; max-width: 250px;">
                                                <strong>Employer Message:</strong><br>
                                                <?= nl2br(htmlspecialchars($app['employer_message'])) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($app['status'] === 'pending'): ?>
                                            <div style="display: flex; gap: 0.5rem; flex-direction: column;">
                                                <form action="../api/cancel_application.php" method="POST"
                                                    onsubmit="return confirm('Are you sure you want to cancel your application?');">
                                                    <input type="hidden" name="application_id" value="<?= $app['id'] ?>">
                                                    <button type="submit" class="btn btn-outline"
                                                        style="padding: 0.2rem 0.5rem; font-size: 0.8rem; border-color: var(--danger); color: var(--danger); width: 100%;">Cancel</button>
                                                </form>
                                                <form action="../api/edit_application.php" method="POST"
                                                    enctype="multipart/form-data" style="display: flex; gap: 0.25rem;">
                                                    <input type="hidden" name="application_id" value="<?= $app['id'] ?>">
                                                    <input type="file" name="resume" accept=".pdf" required
                                                        style="max-width: 150px; font-size: 0.8rem;">
                                                    <button type="submit" class="btn btn-outline"
                                                        style="padding: 0.2rem 0.5rem; font-size: 0.8rem;">Update</button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
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