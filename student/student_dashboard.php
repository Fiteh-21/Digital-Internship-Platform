<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit;
}

$student_id = $_SESSION['user_id'];

// Handle search functionality
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_not_found = false;

// Fetch all available internships not yet applied to by this student
$query = "
    SELECT i.*, u.name as employer_name 
    FROM internships i 
    JOIN users u ON i.employer_id = u.id 
    WHERE i.id NOT IN (SELECT internship_id FROM applications WHERE student_id = ?)
    ORDER BY i.created_at DESC
";
$stmt = $pdo->prepare($query);
$stmt->execute([$student_id]);
$available_internships = $stmt->fetchAll();

// Filter by search query if provided
if (!empty($search_query)) {
    $filtered_internships = array_filter($available_internships, function($internship) use ($search_query) {
        return stripos($internship['title'], $search_query) !== false || 
               stripos($internship['employer_name'], $search_query) !== false ||
               stripos($internship['category'], $search_query) !== false;
    });
    
    if (empty($filtered_internships)) {
        $search_not_found = true;
        $available_internships = [];
    } else {
        $available_internships = $filtered_internships;
    }
}
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

        <div class="dashboard-header">
            <div>
                <h2 class="page-title"><i class="fas fa-compass"></i> Welcome, <?= htmlspecialchars($_SESSION['name']) ?></h2>
                <p class="page-subtitle">Discover matching opportunities and apply with confidence.</p>
            </div>
        </div>

        <section id="browse" class="mb-8">
            <h3 class="mb-4">Available Internships</h3>
            
            <!-- Search Bar -->
            <div class="search-bar-container" style="margin-bottom: 2rem;">
                <form method="GET" action="" style="display: flex; gap: 0.5rem;">
                    <div style="flex: 1; display: flex; gap: 0.5rem;">
                        <input type="text" name="search" class="form-control" placeholder="Search internships by title, company, or category..." value="<?= htmlspecialchars($search_query) ?>" style="flex: 1;">
                        <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem;">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                    <?php if (!empty($search_query)): ?>
                        <a href="student_dashboard.php" class="btn btn-outline" style="padding: 0.75rem 1.5rem;">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <?php if($search_not_found): ?>
                <div class="alert text-warning" style="text-align: center; padding: 2rem;">
                    <i class="fas fa-info-circle"></i> <strong>Not Available</strong> - No internships found matching "<strong><?= htmlspecialchars($search_query) ?></strong>". Try a different search term.
                </div>
            <?php endif; ?>

            <?php if(empty($available_internships)): ?>
                <p class="text-muted">No new internships available at the moment.</p>
            <?php else: ?>
                <!-- Category Filter Row -->
                <div class="category-filter-row">
                    <button type="button" class="category-chip active" onclick="filterByCardCategory('all', this)">
                        <i class="fas fa-th"></i> All
                    </button>
                    <?php 
                        $all_categories = array_unique(array_column($available_internships, 'category'));
                        foreach($all_categories as $cat_name): 
                    ?>
                        <button type="button" class="category-chip" onclick="filterByCardCategory('<?= htmlspecialchars($cat_name) ?>', this)">
                            <i class="fas fa-tag"></i> <?= htmlspecialchars($cat_name) ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="grid" id="internshipGrid" style="padding-top: 1rem;">
                    <?php foreach($available_internships as $internship): ?>
                        <div class="card internship-card" data-category="<?= htmlspecialchars($internship['category'] ?? 'General') ?>" style="display: flex; flex-direction: column;">
                            <div class="card-header">
                                <h4 class="card-title"><?= htmlspecialchars($internship['title']) ?></h4>
                                <span class="badge badge-primary"><?= htmlspecialchars($internship['stipend']) ?></span>
                            </div>
                            <div style="margin-bottom: 0.5rem;">
                                <span class="badge" style="background: #eef2ff; color: #4338ca; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                                    <?= htmlspecialchars($internship['category'] ?? 'General') ?>
                                </span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-building text-muted" style="width: 20px;"></i> <?= htmlspecialchars($internship['employer_name']) ?>
                            </div>
                            <div class="detail-item mb-4">
                                <i class="fas fa-map-marker-alt text-muted" style="width: 20px;"></i> <?= htmlspecialchars($internship['location']) ?>
                            </div>
                            
                            <div class="card-body">
                                <div style="margin-bottom: 1rem;">
                                    <strong>Description:</strong><br>
                                    <span style="color: var(--text-main);">
                                        <?php if (strlen($internship['description']) > 100): ?>
                                            <?= nl2br(htmlspecialchars(mb_substr($internship['description'], 0, 100))) ?>... <a href="internship_details.php?id=<?= $internship['id'] ?>" class="read-more-link">Read More</a>
                                        <?php else: ?>
                                            <?= nl2br(htmlspecialchars($internship['description'])) ?> <a href="internship_details.php?id=<?= $internship['id'] ?>" class="read-more-link">Read More</a>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                            <form action="../api/apply.php" method="POST" enctype="multipart/form-data" class="mt-4" style="margin-top: auto;">
                                <input type="hidden" name="internship_id" value="<?= $internship['id'] ?>">
                                
                                <div class="form-group">
                                    <label class="form-label" style="font-size: 0.85rem;">Cover Letter (Optional)</label>
                                    <textarea name="cover_letter" class="form-control" rows="2" placeholder="Why are you a good fit?" style="font-size: 0.85rem;"></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="form-label" style="font-size: 0.85rem;">Resume (PDF)</label>
                                    <div class="file-upload-wrapper" style="height: 60px;">
                                        <input type="file" name="resume" class="file-upload-input" accept=".pdf" required>
                                        <div class="file-upload-text">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <span style="font-size: 0.75rem;">Upload</span>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.5rem;">Apply Now</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
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

<?php require_once '../includes/footer.php'; ?>
