<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit;
}

$student_id = $_SESSION['user_id'];

// Handle Mark as Read
if (isset($_GET['read_id'])) {
    $read_id = (int) $_GET['read_id'];
    $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?")->execute([$read_id, $student_id]);
    header("Location: student_messages.php");
    exit;
}

// Fetch Notifications
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$student_id]);
$notifications = $stmt->fetchAll();
?>

<div class="dashboard">
    <aside class="sidebar">
        <nav class="sidebar-nav">
            <ul>
                <li><a href="student_dashboard.php"><i class="fas fa-search"></i> Browse</a></li>
                <li><a href="student_applications.php"><i class="fas fa-file-signature"></i> My Applications</a></li>
                <li><a href="student_messages.php" class="active"><i class="fas fa-envelope"></i> Messages
                        <?php if ($unread_notif_count > 0): ?><span
                                class="badge bg-danger rounded-pill text-xs"><?= $unread_notif_count ?></span><?php endif; ?></a>
                </li>
                <li><a href="student_profile.php"><i class="fas fa-user"></i> My Profile</a></li>
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
            <div class="dashboard-header">
                <div>
                    <h2 class="page-title"><i class="fas fa-envelope-open-text"></i> System Messages</h2>
                    <p class="page-subtitle">Direct communications from the platform administrator.</p>
                </div>
            </div>

            <section id="notifications" class="mb-8">
                <?php if (empty($notifications)): ?>
                    <div class="text-center p-5 bg-white rounded shadow-sm">
                        <i class="fas fa-envelope-open text-muted mb-3 icon-3rem-opacity"></i>
                        <p class="text-muted">You have no messages at the moment.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($notifications as $notif): ?>
                        <div
                            class="card p-4 mb-3 border-0 shadow-sm notif-card <?= $notif['is_read'] ? 'notif-read' : 'notif-unread' ?>">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="text-muted mb-2 text-sm-085-fw-500">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        <?= date('F j, Y - H:i', strtotime($notif['created_at'])) ?>
                                    </div>
                                    <div class="notif-message-text"><?= nl2br(htmlspecialchars($notif['message'])) ?></div>
                                </div>
                                <?php if (!$notif['is_read']): ?>
                                    <a href="?read_id=<?= $notif['id'] ?>" class="btn btn-sm btn-primary text-sm-08-no-ul">Mark as
                                        Read</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
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