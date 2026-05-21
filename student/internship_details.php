<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit;
}

$student_id = $_SESSION['user_id'];
$internship_id = $_GET['id'] ?? null;

if (!$internship_id) {
    header("Location: student_dashboard.php");
    exit;
}

// Fetch internship details
$stmt = $pdo->prepare("
    SELECT i.*, u.name as employer_name 
    FROM internships i 
    JOIN users u ON i.employer_id = u.id 
    WHERE i.id = ?
");
$stmt->execute([$internship_id]);
$internship = $stmt->fetch();

if (!$internship) {
    header("Location: student_dashboard.php?error=Internship+not+found");
    exit;
}

// Check if student has already applied
$stmt_app = $pdo->prepare("SELECT * FROM applications WHERE student_id = ? AND internship_id = ?");
$stmt_app->execute([$student_id, $internship_id]);
$application = $stmt_app->fetch();
?>

<div class="dashboard">
    <aside class="sidebar">
        <nav class="sidebar-nav">
            <ul>
                <li><a href="student_dashboard.php" class="active"><i class="fas fa-search"></i> Browse</a></li>
                <li><a href="student_applications.php"><i class="fas fa-file-signature"></i> My Applications</a></li>
                <li><a href="student_messages.php"><i class="fas fa-envelope"></i> Messages <?php if($unread_notif_count > 0): ?><span class="badge bg-danger rounded-pill" style="font-size: 0.7rem;"><?= $unread_notif_count ?></span><?php endif; ?></a></li>
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
            <?php if(isset($_GET['success'])): ?>
                <div class="alert text-success mb-4"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>
            <?php if(isset($_GET['error'])): ?>
                <div class="alert text-danger mb-4"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <a href="student_dashboard.php" class="btn btn-outline mb-4" style="margin-bottom: 1.5rem;">
                <i class="fas fa-arrow-left"></i> Back to Internships
            </a>

            <div class="card" style="padding: 2.5rem; border-radius: var(--radius); position: relative; overflow: hidden; margin-bottom: 2rem; border: 1px solid var(--border); box-shadow: var(--shadow-md); background: var(--surface);">
                <!-- Accent background line -->
                <div style="position: absolute; top: 0; left: 0; width: 6px; height: 100%; background: var(--primary);"></div>
                
                <div class="card-header" style="flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid #edf2f8; padding-bottom: 1.5rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: flex-start;">
                    <div style="flex: 1; min-width: 250px;">
                        <span class="badge" style="background: #eef2ff; color: #4338ca; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; margin-bottom: 0.5rem; display: inline-block;">
                            <?= htmlspecialchars($internship['category'] ?? 'General') ?>
                        </span>
                        <h2 style="font-size: 2rem; font-weight: 800; color: var(--secondary); margin-bottom: 0.5rem; line-height: 1.25;"><?= htmlspecialchars($internship['title']) ?></h2>
                        
                        <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; margin-top: 1rem; color: var(--text-muted); font-size: 0.95rem;">
                            <span><i class="fas fa-building text-primary" style="margin-right: 0.4rem;"></i> <strong><?= htmlspecialchars($internship['employer_name']) ?></strong></span>
                            <span><i class="fas fa-map-marker-alt text-danger" style="margin-right: 0.4rem;"></i> <?= htmlspecialchars($internship['location']) ?></span>
                            <span><i class="far fa-clock text-warning" style="margin-right: 0.4rem;"></i> Posted on <?= date('M j, Y', strtotime($internship['created_at'])) ?></span>
                        </div>
                    </div>
                    
                    <div style="text-align: right; min-width: 150px; display: flex; flex-direction: column; align-items: flex-end; justify-content: center;">
                        <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Stipend Amount</span>
                        <span class="badge badge-primary" style="font-size: 1.1rem; padding: 0.5rem 1rem; border-radius: 8px; margin-top: 0.25rem; background: #dbeafe; color: #1e40af;">
                            <?= htmlspecialchars($internship['stipend']) ?>
                        </span>
                    </div>
                </div>

                <div class="details-content-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 2.5rem; align-items: start;">
                    
                    <!-- Left Column: Details -->
                    <div style="display: flex; flex-direction: column; gap: 2rem;">
                        <div>
                            <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--secondary); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-align-left text-primary" style="font-size: 1.1rem;"></i> Description
                            </h3>
                            <div style="color: var(--text-main); font-size: 1.05rem; line-height: 1.7; background: #fafcff; padding: 1.5rem; border-radius: 12px; border: 1px solid #f0f4fa;">
                                <?= nl2br(htmlspecialchars($internship['description'])) ?>
                            </div>
                        </div>

                        <div>
                            <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--secondary); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-list-check text-primary" style="font-size: 1.1rem;"></i> Requirements
                            </h3>
                            <div style="color: var(--text-main); font-size: 1.05rem; line-height: 1.7; background: #fafcff; padding: 1.5rem; border-radius: 12px; border: 1px solid #f0f4fa;">
                                <?php if (!empty($internship['requirements'])): ?>
                                    <?= nl2br(htmlspecialchars($internship['requirements'])) ?>
                                <?php else: ?>
                                    <span class="text-muted">No specific requirements listed for this position.</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Application Form or Status -->
                    <div style="background: var(--surface-soft); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.5rem; box-shadow: var(--shadow-sm);">
                        <?php if ($application): ?>
                            <div style="text-align: center; padding: 1rem 0;">
                                <div style="width: 60px; height: 60px; background: #dcfce7; color: var(--success); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.75rem;">
                                    <i class="fas fa-check-double"></i>
                                </div>
                                <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--secondary); margin-bottom: 0.5rem;">Already Applied</h3>
                                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">You submitted your application on <?= date('M j, Y', strtotime($application['applied_at'])) ?>.</p>
                                
                                <div style="text-align: left; background: #fff; padding: 1rem; border-radius: 10px; border: 1px solid #eef2f7; font-size: 0.85rem;">
                                    <div style="margin-bottom: 0.5rem;">
                                        <strong>Status: </strong>
                                        <span class="badge status-<?= htmlspecialchars($application['status']) ?>" style="font-size: 0.75rem; text-transform: uppercase;">
                                            <?= htmlspecialchars(ucfirst($application['status'])) ?>
                                        </span>
                                    </div>
                                    <?php if (!empty($application['cover_letter'])): ?>
                                        <div style="margin-top: 0.5rem; word-break: break-word;">
                                            <strong>Your Cover Letter:</strong><br>
                                            <span style="color: var(--text-muted);"><?= nl2br(htmlspecialchars(mb_substr($application['cover_letter'], 0, 150))) ?><?= strlen($application['cover_letter']) > 150 ? '...' : '' ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <a href="student_applications.php" class="btn btn-outline" style="width: 100%; margin-top: 1.5rem; justify-content: center; display: inline-flex;">
                                    <i class="fas fa-file-signature"></i> Manage Applications
                                </a>
                            </div>
                        <?php else: ?>
                            <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--secondary); margin-bottom: 1rem; text-align: center; border-bottom: 1px solid #eef2f7; padding-bottom: 0.75rem;">Apply for this Position</h3>
                            
                            <form action="../api/apply.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="internship_id" value="<?= $internship['id'] ?>">
                                <input type="hidden" name="redirect_to" value="../student/internship_details.php?id=<?= $internship['id'] ?>">
                                
                                <div class="form-group" style="margin-bottom: 1.25rem;">
                                    <label class="form-label" style="font-size: 0.9rem; font-weight: 600; color: var(--secondary);">Cover Letter (Optional)</label>
                                    <textarea name="cover_letter" class="form-control" rows="4" placeholder="Briefly describe why you are a good fit for this role..." style="font-size: 0.9rem; min-height: 100px;"></textarea>
                                </div>

                                <div class="form-group" style="margin-bottom: 1.5rem;">
                                    <label class="form-label" style="font-size: 0.9rem; font-weight: 600; color: var(--secondary);">Resume (PDF format required)</label>
                                    <div class="file-upload-wrapper" style="height: 80px;">
                                        <input type="file" name="resume" class="file-upload-input" accept=".pdf" required>
                                        <div class="file-upload-text">
                                            <i class="fas fa-cloud-upload-alt" style="font-size: 1.4rem; margin-bottom: 0.25rem; color: var(--primary);"></i>
                                            <span style="font-size: 0.8rem;">Click or drag PDF here</span>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 1rem; justify-content: center; font-weight: 700; box-shadow: 0 4px 12px rgba(31, 111, 235, 0.25); display: inline-flex;">
                                    <i class="fas fa-paper-plane"></i> Submit Application
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Delete Account Confirmation Modal -->
<div id="deleteAccountModal" class="modal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h5 class="modal-title text-danger">Delete Student Account</h5>
            <button type="button" class="btn-close" onclick="closeModalElement(document.getElementById('deleteAccountModal'))"></button>
        </div>
        <div class="modal-body">
            <p><strong>CRITICAL WARNING:</strong> Are you sure you want to permanently delete your account?</p>
            <p style="margin-top: 1rem;">This action will permanently remove:</p>
            <ul style="margin-top: 0.5rem; padding-left: 1.5rem; font-size: 0.9rem; color: #64748b;">
                <li>Your profile details and professional headline</li>
                <li>All your active internship applications</li>
                <li>Your uploaded resume and certificates</li>
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

<style>
@media (max-width: 992px) {
    .details-content-grid {
        grid-template-columns: 1fr !important;
        gap: 2rem !important;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?>
