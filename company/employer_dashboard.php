<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
    header("Location: ../auth/login.php");
    exit;
}

$employer_id = $_SESSION['user_id'];

// Fetch posted internships
$stmt = $pdo->prepare("SELECT * FROM internships WHERE employer_id = ? ORDER BY created_at DESC");
$stmt->execute([$employer_id]);
$internships = $stmt->fetchAll();
?>

<div class="dashboard">
    <aside class="sidebar">
        <nav class="sidebar-nav">
            <ul>
                <li><a href="employer_dashboard.php" class="active"><i class="fas fa-briefcase"></i> My Postings</a>
                </li>
                <li><a href="employer_applications.php"><i class="fas fa-users"></i> Applications
                        <?php if (isset($pending_count) && $pending_count > 0): ?><span class="badge"
                                style="background: var(--danger); color: white; border-radius: 50%; padding: 0.15rem 0.4rem; font-size: 0.75rem; margin-left: 0.25rem; vertical-align: top;"><?= $pending_count ?></span><?php endif; ?></a>
                </li>
                <li><a href="employer_messages.php"><i class="fas fa-envelope"></i> Messages
                        <?php if ($unread_notif_count > 0): ?><span class="badge bg-danger rounded-pill"
                                style="font-size: 0.7rem;"><?= $unread_notif_count ?></span><?php endif; ?></a></li>
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
            <?php if (isset($_GET['success'])): ?>
                <div class="alert text-success mb-4"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert text-danger mb-4"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <div class="dashboard-header">
                <div>
                    <h2 class="page-title"><i class="fas fa-briefcase"></i> Welcome,
                        <?= htmlspecialchars($_SESSION['name']) ?>
                    </h2>
                    <p class="page-subtitle">Manage internship posts with a clean hiring workflow.</p>
                </div>
                <a href="employer_post_new.php" class="btn btn-primary">Post Internship</a>
            </div>

            <section id="my-postings" class="mb-8">
                <h3 class="mb-4">My Internships</h3>
                <?php if (empty($internships)): ?>
                    <p class="text-muted">You haven't posted any internships yet.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Location</th>
                                <th>Posted Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($internships as $internship): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($internship['title']) ?></strong></td>
                                    <td><?= htmlspecialchars($internship['location']) ?></td>
                                    <td><?= date('M j, Y', strtotime($internship['created_at'])) ?></td>
                                    <td>
                                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                                            <a href="employer_edit_internship.php?id=<?= $internship['id'] ?>"
                                                class="btn btn-outline"
                                                style="padding: 0.25rem 0.5rem; font-size: 0.85rem; border-color: var(--primary); color: var(--primary);">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <button type="button" 
                                                class="btn btn-outline"
                                                style="padding: 0.25rem 0.5rem; font-size: 0.85rem; border-color: var(--danger); color: var(--danger);"
                                                onclick="confirmDelete(<?= $internship['id'] ?>, '<?= addslashes(htmlspecialchars($internship['title'])) ?>')">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
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

<!-- Delete Internship Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h5 class="modal-title">Confirm Deletion</h5>
            <button type="button" class="btn-close" onclick="closeModalElement(document.getElementById('deleteModal'))"></button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete the internship "<strong id="deleteInternshipTitle"></strong>"?</p>
            <p class="text-danger" style="font-size: 0.9rem; margin-top: 0.5rem;">
                <i class="fas fa-exclamation-triangle"></i> This will also permanently delete all associated applications.
            </p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="closeModalElement(document.getElementById('deleteModal'))">Cancel</button>
            <form id="deleteForm" action="../api/delete_internship.php" method="POST">
                <input type="hidden" name="internship_id" id="deleteInternshipId">
                <button type="submit" class="btn btn-primary" style="background: var(--danger); border-color: var(--danger);">Delete Internship</button>
            </form>
        </div>
    </div>
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

<script>
function confirmDelete(id, title) {
    document.getElementById('deleteInternshipId').value = id;
    document.getElementById('deleteInternshipTitle').innerText = title;
    openModalById('deleteModal');
}
</script>

<?php require_once '../includes/footer.php'; ?>