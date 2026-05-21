<?php
require_once '../includes/db.php';
require_once '../includes/admin_auth.php';
require_once '../includes/header.php';

// Fetch Statistics
$total_students = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
$total_employers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'employer'")->fetchColumn();
$total_internships = $pdo->query("SELECT COUNT(*) FROM internships")->fetchColumn();
$total_applications = $pdo->query("SELECT COUNT(*) FROM applications")->fetchColumn();

// Recent Internships
$recent_internships = $pdo->query("SELECT i.*, u.name as company_name FROM internships i JOIN users u ON i.employer_id = u.id ORDER BY i.created_at DESC LIMIT 5")->fetchAll();
?>

<div class="container mt-4">
    <div class="admin-hero">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1"><i class="fas fa-gauge-high"></i> Admin Dashboard</h2>
                <p class="mb-0">System overview, moderation, and communication in one place.</p>
            </div>
            <span class="badge bg-primary p-2">System Overview</span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-3 mb-5 justify-content-center">
        <?php
        $stats = [
            ['title' => 'Students', 'count' => $total_students, 'icon' => 'fa-user-graduate', 'link' => 'manage_students.php'],
            ['title' => 'Employers', 'count' => $total_employers, 'icon' => 'fa-building', 'link' => 'manage_companies.php'],
            ['title' => 'Internships', 'count' => $total_internships, 'icon' => 'fa-briefcase', 'link' => 'manage_internships.php'],
            ['title' => 'Applications', 'count' => $total_applications, 'icon' => 'fa-file-alt', 'link' => null]
        ];
        foreach ($stats as $stat):
        ?>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card p-3 text-center shadow-sm h-100" style="border-radius: 12px; border: none; background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: white; transition: transform 0.3s ease;">
                <i class="fas <?= $stat['icon'] ?> mb-2" style="font-size: 1.5rem;"></i>
                <h4 class="mb-1"><?= $stat['count'] ?></h4>
                <p class="mb-0" style="opacity: 0.9; font-size: 0.85rem; font-weight: 500;"><?= $stat['title'] ?></p>
                <?php if ($stat['link']): ?>
                    <a href="<?= $stat['link'] ?>" class="stretched-link"></a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm p-4 mb-4" style="border-radius: 15px; border: none;">
                <h4 class="mb-4">Recent Internship Postings</h4>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Company</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recent_internships as $intern): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($intern['title']) ?></strong></td>
                                <td><?= htmlspecialchars($intern['company_name']) ?></td>
                                <td class="text-muted"><?= date('M d, Y', strtotime($intern['created_at'])) ?></td>
                                <td><a href="manage_internships.php" class="btn btn-sm btn-outline-primary">Manage</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm p-4 h-100" style="border-radius: 15px; border: none;">
                <h4 class="mb-4">Admin Actions</h4>
                <div class="list-group list-group-flush">
                    <a href="manage_students.php" class="list-group-item list-group-item-action d-flex align-items-center py-3 mb-3 rounded shadow-sm border" style="transition: transform 0.2s;">
                        <i class="fas fa-users-cog me-3 text-primary"></i> Manage Students
                    </a>
                    <a href="manage_companies.php" class="list-group-item list-group-item-action d-flex align-items-center py-3 mb-3 rounded shadow-sm border" style="transition: transform 0.2s;">
                        <i class="fas fa-user-tie me-3 text-success"></i> Manage Employers
                    </a>
                    <a href="manage_internships.php" class="list-group-item list-group-item-action d-flex align-items-center py-3 mb-3 rounded shadow-sm border" style="transition: transform 0.2s;">
                        <i class="fas fa-tasks me-3 text-warning"></i> Audit Internships
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
