<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
    header("Location: ../auth/login.php");
    exit;
}

$employer_id = $_SESSION['user_id'];

// Handle Mark as Read
if (isset($_GET['read_id'])) {
    $read_id = (int) $_GET['read_id'];
    $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?")->execute([$read_id, $employer_id]);
    header("Location: employer_messages.php");
    exit;
}

// Fetch Notifications
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$employer_id]);
$notifications = $stmt->fetchAll();
?>

<div class="dashboard">
    <aside class="sidebar">
        <nav class="sidebar-nav">
            <ul>
                <li><a href="employer_dashboard.php"><i class="fas fa-briefcase"></i> My Postings</a></li>
                <li><a href="employer_applications.php"><i class="fas fa-users"></i> Applications
                        <?php if (isset($pending_count) && $pending_count > 0): ?><span class="badge"
                                style="background: var(--danger); color: white; border-radius: 50%; padding: 0.15rem 0.4rem; font-size: 0.75rem; margin-left: 0.25rem; vertical-align: top;"><?= $pending_count ?></span><?php endif; ?></a>
                </li>
                <li><a href="employer_messages.php" class="active"><i class="fas fa-envelope"></i> Messages
                        <?php if ($unread_notif_count > 0): ?><span class="badge bg-danger rounded-pill"
                                style="font-size: 0.7rem;"><?= $unread_notif_count ?></span><?php endif; ?></a></li>
                <li><a href="employer_post_new.php"><i class="fas fa-plus-circle"></i> Post New</a></li>
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
                    <h2 class="page-title"><i class="fas fa-bell"></i> Admin Notifications</h2>
                    <p class="page-subtitle">Important messages and alerts from the site administrator.</p>
                </div>
            </div>

            <section id="notifications" class="mb-8">
                <?php if (empty($notifications)): ?>
                    <div class="text-center p-5 bg-white rounded shadow-sm">
                        <i class="fas fa-bell-slash text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                        <p class="text-muted">No messages from the administrator yet.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($notifications as $notif): ?>
                        <div class="card p-4 mb-3 border-0 shadow-sm"
                            style="border-left: 5px solid <?= $notif['is_read'] ? '#dee2e6' : 'var(--primary)' ?> !important; background: <?= $notif['is_read'] ? '#ffffff' : '#f0f7ff' ?>; border-radius: 15px;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div style="flex-grow: 1;">
                                    <div class="text-muted mb-2" style="font-size: 0.85rem; font-weight: 500;">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        <?= date('F j, Y - H:i', strtotime($notif['created_at'])) ?>
                                    </div>
                                    <div style="font-size: 1.1rem; color: var(--secondary); line-height: 1.5;">
                                        <?= nl2br(htmlspecialchars($notif['message'])) ?>
                                    </div>
                                </div>
                                <?php if (!$notif['is_read']): ?>
                                    <a href="?read_id=<?= $notif['id'] ?>" class="btn btn-sm btn-primary"
                                        style="font-size: 0.8rem; text-decoration: none !important;">Mark as Read</a>
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-danger">Delete Company Account</h5>
                <button type="button" class="btn-close"
                    onclick="closeModalElement(document.getElementById('deleteAccountModal'))"></button>
            </div>
            <div class="modal-body">
                <p><strong>CRITICAL WARNING:</strong> Are you sure you want to permanently delete your company account?
                </p>
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
                <button type="button" class="btn btn-outline"
                    onclick="closeModalElement(document.getElementById('deleteAccountModal'))">Cancel</button>
                <form action="../api/delete_account.php" method="POST">
                    <button type="submit" class="btn btn-primary btn-danger-solid">Yes, Delete My Account</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>