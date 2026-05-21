<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

$category_name = $_GET['name'] ?? null;
if (!$category_name) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT i.*, u.name as employer_name FROM internships i JOIN users u ON i.employer_id = u.id WHERE i.category = ? ORDER BY i.created_at DESC");
$stmt->execute([$category_name]);
$internships = $stmt->fetchAll();
?>

<div class="container" style="min-height: 70vh;">
    <div class="category-header">
        <h2 class="category-title">Category: <?= htmlspecialchars($category_name) ?></h2>
        <a href="index.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Categories</a>
    </div>

    <?php if(empty($internships)): ?>
        <p class="text-center mt-4 text-muted">No internships posted in this category yet.</p>
    <?php else: ?>
        <div class="grid">
            <?php foreach($internships as $internship): ?>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?= htmlspecialchars($internship['title']) ?></h3>
                        <span class="badge badge-primary"><?= htmlspecialchars($internship['stipend']) ?></span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-building text-muted" style="width: 20px;"></i> <?= htmlspecialchars($internship['employer_name']) ?>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-map-marker-alt text-muted" style="width: 20px;"></i> <?= htmlspecialchars($internship['location']) ?>
                    </div>
                    <div class="card-body mt-4">
                        <?= nl2br(htmlspecialchars(mb_substr($internship['description'], 0, 150))) ?>...
                    </div>
                    <div class="card-footer" style="flex-direction: column; align-items: stretch; gap: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                            <span class="text-muted"><i class="far fa-clock"></i> <?= date('M j, Y', strtotime($internship['created_at'])) ?></span>
                        </div>
                        
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <?php if($_SESSION['role'] === 'student'): ?>
                                <a href="student/student_dashboard.php" class="btn btn-primary" style="width: 100%; justify-content: center;">
                                    <i class="fas fa-paper-plane"></i> Apply Now
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="auth/login.php" class="btn btn-primary" style="width: 100%; justify-content: center; background: var(--secondary);">
                                <i class="fas fa-sign-in-alt"></i> Login to Apply
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
