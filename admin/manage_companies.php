<?php
require_once '../includes/db.php';
require_once '../includes/admin_auth.php';

// Handle deletion
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'employer'");
    if ($stmt->execute([$id])) {
        header("Location: manage_companies.php?msg=Employer deleted successfully.");
        exit;
    }
}

$companies = $pdo->query("SELECT * FROM users WHERE role = 'employer' ORDER BY created_at DESC")->fetchAll();

require_once '../includes/header.php';
?>

<div class="container mt-4 page-shell" style="min-height: 65vh;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title"><i class="fas fa-building"></i> Manage Employers</h2>
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
                        <th class="ps-4">Company Name</th>
                        <th>Email</th>
                        <th>Location</th>
                        <th>Internships Posted</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($companies)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">No employers found.</td></tr>
                    <?php endif; ?>
                    <?php foreach($companies as $company): ?>
                    <?php
                        $job_count = $pdo->prepare("SELECT COUNT(*) FROM internships WHERE employer_id = ?");
                        $job_count->execute([$company['id']]);
                        $count = $job_count->fetchColumn();
                    ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="rounded bg-success text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                                    <?= strtoupper(substr($company['name'], 0, 1)) ?>
                                </div>
                                <strong><?= htmlspecialchars($company['name']) ?></strong>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($company['email']) ?></td>
                        <td><?= htmlspecialchars($company['location'] ?? 'Not specified') ?></td>
                        <td><span class="badge bg-light text-dark border"><?= $count ?> Postings</span></td>
                        <td class="text-end pe-4">
                            <button type="button" class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#msgModal<?= $company['id'] ?>" onclick="openModalById('msgModal<?= $company['id'] ?>'); return false;">
                                <i class="fas fa-paper-plane"></i> Message
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmAdminDelete(<?= $company['id'] ?>, '<?= addslashes(htmlspecialchars($company['name'])) ?>', 'company')">
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
    <?php foreach($companies as $company): 
        // Fetch previous messages for this company
        $stmt_history = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
        $stmt_history->execute([$company['id']]);
        $history = $stmt_history->fetchAll();
    ?>

    
        <div class="modal fade" id="msgModal<?= $company['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content shadow-lg border-0" style="border-radius: 15px;">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title" style="font-weight: 700; color: var(--secondary);">Messaging: <?= htmlspecialchars($company['name']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closeModalElement(this.closest('.modal'))"></button>
                    </div>
                    <div class="row g-0">
                        <div class="col-md-5 border-end">
                            <div class="p-4">
                                <h6 class="text-muted mb-3" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Recent Message History</h6>
                                <div style="max-height: 300px; overflow-y: auto; padding-right: 10px;">
                                    <?php if(empty($history)): ?>
                                        <p class="text-muted small">No previous messages sent.</p>
                                    <?php else: ?>
                                        <?php foreach($history as $h): ?>
                                            <div class="mb-3 p-2 bg-light rounded shadow-sm" style="font-size: 0.85rem; border-left: 3px solid #ccc;">
                                                <div class="text-muted small mb-1"><?= date('M j, Y', strtotime($h['created_at'])) ?></div>
                                                <div><?= htmlspecialchars($h['message']) ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <form action="api/send_notification.php" method="POST">
                                <div class="modal-body">
                                    <input type="hidden" name="user_id" value="<?= $company['id'] ?>">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">New Message</label>
                                        <textarea name="message" class="form-control" rows="6" placeholder="Type your message here..." style="border-radius: 10px; border: 1px solid #eee; padding: 1rem;" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px;" onclick="closeModalElement(this.closest('.modal'))">Cancel</button>
                                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 10px; font-weight: 600;">Send Message</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Delete Confirmation Modal -->
    <div id="adminDeleteModal" class="modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0" style="border-radius: 15px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" style="font-weight: 700; color: var(--secondary);">Confirm Deletion</h5>
                    <button type="button" class="btn-close" onclick="closeModalElement(document.getElementById('adminDeleteModal'))"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the company <strong id="deleteItemName"></strong>?</p>
                    <p class="text-danger mt-2" style="font-size: 0.9rem;">
                        <i class="fas fa-exclamation-triangle"></i> This will also remove all their internship postings and associated applications.
                    </p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" onclick="closeModalElement(document.getElementById('adminDeleteModal'))">Cancel</button>
                    <a id="confirmDeleteBtn" href="#" class="btn btn-danger px-4" style="font-weight: 600;">Yes, Delete Company</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmAdminDelete(id, name, type) {
    document.getElementById('deleteItemName').innerText = name;
    document.getElementById('confirmDeleteBtn').href = '?delete_id=' + id;
    openModalById('adminDeleteModal');
}
</script>

<?php require_once '../includes/footer.php'; ?>
