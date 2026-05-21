<?php
require_once '../includes/db.php';
require_once '../includes/admin_auth.php';

// Handle deletion
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM internships WHERE id = ?");
    if ($stmt->execute([$id])) {
        header("Location: manage_internships.php?msg=Internship deleted successfully.");
        exit;
    }
}

$internships = $pdo->query("SELECT i.*, u.name as company_name FROM internships i JOIN users u ON i.employer_id = u.id ORDER BY i.created_at DESC")->fetchAll();

require_once '../includes/header.php';
?>

<div class="container mt-4 page-shell">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title"><i class="fas fa-clipboard-check"></i> Manage Internships</h2>
        <a href="dashboard.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Back to Dashboard</a>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success shadow-sm mb-4"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm border-none" style="border-radius: 15px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Title</th>
                        <th>Company</th>
                        <th>Category</th>
                        <th>Posted Date</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($internships)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">No internships found.</td></tr>
                    <?php endif; ?>
                    <?php foreach($internships as $intern): ?>
                    <tr>
                        <td class="ps-4"><strong><?= htmlspecialchars($intern['title']) ?></strong></td>
                        <td><?= htmlspecialchars($intern['company_name']) ?></td>
                        <td><span class="badge bg-info-soft text-info border"><?= htmlspecialchars($intern['category']) ?></span></td>
                        <td class="text-muted"><?= date('M d, Y', strtotime($intern['created_at'])) ?></td>
                        <td class="text-end pe-4">
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="openModalById('deleteModal<?= $intern['id'] ?>')">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modals moved outside table -->
    <?php foreach($internships as $intern): ?>
        <div class="modal fade" id="deleteModal<?= $intern['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-lg border-0" style="border-radius: 15px;">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title" style="font-weight: 700; color: var(--secondary);">Delete Internship: <?= htmlspecialchars($intern['title']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="api/send_notification.php" method="POST">
                        <div class="modal-body">
                            <p class="text-danger mb-3"><strong>Warning:</strong> This will permanently delete this internship posting.</p>
                            <input type="hidden" name="user_id" value="<?= $intern['employer_id'] ?>">
                            <input type="hidden" name="internship_id" value="<?= $intern['id'] ?>">
                            <div class="mb-3">
                                <label class="form-label text-muted">Notification Message to Company (Optional)</label>
                                <textarea name="message" class="form-control" rows="4" placeholder="Explain why the post is being deleted..." style="border-radius: 10px; border: 1px solid #eee; padding: 1rem;">Your internship posting '<?= htmlspecialchars($intern['title']) ?>' has been removed by the administrator.</textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px;">Cancel</button>
                            <button type="submit" class="btn btn-danger px-4" style="border-radius: 10px; font-weight: 600;">Confirm Delete & Notify</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
