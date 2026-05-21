<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
    header("Location: ../auth/login.php");
    exit;
}

$student_id = $_GET['id'] ?? null;

if (!$student_id) {
    header("Location: employer_applications.php?error=No student specified.");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'student'");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    header("Location: employer_applications.php?error=Student not found.");
    exit;
}
?>

<div class="dashboard">
    <aside class="sidebar">
        <nav class="sidebar-nav">
            <ul>
                <li><a href="employer_dashboard.php"><i class="fas fa-briefcase"></i> My Postings</a></li>
                <li><a href="employer_applications.php" class="active"><i class="fas fa-users"></i> Applications <?php if(isset($pending_count) && $pending_count > 0): ?><span class="badge" style="background: var(--danger); color: white; border-radius: 50%; padding: 0.15rem 0.4rem; font-size: 0.75rem; margin-left: 0.25rem; vertical-align: top;"><?= $pending_count ?></span><?php endif; ?></a></li>
                <li><a href="employer_post_new.php"><i class="fas fa-plus-circle"></i> Post New</a></li>
                <li style="margin-top: 2rem; border-top: 1px solid #eee; padding-top: 1rem;">
                    <form action="../api/delete_account.php" method="POST" onsubmit="return confirm('CRITICAL WARNING: Are you sure you want to permanently delete your company account? All your postings and received applications will be permanently deleted.');">
                        <button type="submit" style="background: none; border: none; color: var(--danger); font-family: inherit; font-size: 0.95rem; cursor: pointer; padding: 0.75rem 1.5rem; display: flex; align-items: center; gap: 0.75rem; width: 100%; text-align: left;">
                            <i class="fas fa-user-slash"></i> Delete Account
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <div class="page-shell">
        <div class="dashboard-header">
            <h2 class="page-title"><i class="fas fa-user-graduate"></i> Student Profile</h2>
            <a href="employer_applications.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Applications</a>
        </div>

        <section class="mb-8">
            <div class="card" style="max-width: 800px;">
                <div style="display: flex; gap: 2rem; align-items: flex-start; margin-bottom: 2rem;">
                    <div style="flex-shrink: 0; text-align: center;">
                        <?php if(!empty($student['profile_picture'])): ?>
                            <img src="../<?= htmlspecialchars($student['profile_picture']) ?>" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary);">
                        <?php else: ?>
                            <div style="width: 150px; height: 150px; border-radius: 50%; background: #eee; display: flex; align-items: center; justify-content: center; font-size: 4rem; color: #ccc; border: 3px solid var(--primary);">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div style="flex-grow: 1;">
                        <h3 style="margin-bottom: 0.5rem; font-size: 1.8rem;"><?= htmlspecialchars($student['name']) ?></h3>
                        <?php if(!empty($student['headline'])): ?>
                            <p style="color: var(--primary); font-weight: 500; font-size: 1.1rem; margin-bottom: 1rem;"><?= htmlspecialchars($student['headline']) ?></p>
                        <?php endif; ?>
                        
                        <div class="profile-contact" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.95rem; color: #555;">
                            <div><i class="fas fa-envelope" style="width: 20px;"></i> <a href="mailto:<?= htmlspecialchars($student['email']) ?>" class="inline-link"><?= htmlspecialchars($student['email']) ?></a></div>
                            <?php if(!empty($student['phone'])): ?>
                                <div><i class="fas fa-phone" style="width: 20px;"></i> <a href="tel:<?= htmlspecialchars(preg_replace('/\s+/', '', $student['phone'])) ?>" class="inline-link"><?= htmlspecialchars($student['phone']) ?></a></div>
                            <?php endif; ?>
                            <?php if(!empty($student['location'])): ?>
                                <div><i class="fas fa-map-marker-alt" style="width: 20px;"></i> <?= htmlspecialchars($student['location']) ?></div>
                            <?php endif; ?>
                            <?php if(!empty($student['portfolio'])): ?>
                                <div><i class="fas fa-link" style="width: 20px;"></i> <a href="<?= htmlspecialchars($student['portfolio']) ?>" target="_blank" class="inline-link">Portfolio Link</a></div>
                            <?php endif; ?>
                            <?php if(!empty($student['resume_path'])): ?>
                                <div><i class="fas fa-file-pdf" style="width: 20px;"></i> <a href="../<?= htmlspecialchars($student['resume_path']) ?>" target="_blank" class="inline-link">View Resume</a></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if(!empty($student['about'])): ?>
                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="border-bottom: 2px solid #eee; padding-bottom: 0.5rem; margin-bottom: 0.5rem;">About</h4>
                        <p style="color: var(--text-main); line-height: 1.6;"><?= nl2br(htmlspecialchars($student['about'])) ?></p>
                    </div>
                <?php endif; ?>

                <?php if(!empty($student['skills'])): ?>
                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="border-bottom: 2px solid #eee; padding-bottom: 0.5rem; margin-bottom: 0.5rem;">Skills</h4>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                            <?php 
                            $skills = explode(',', $student['skills']);
                            foreach($skills as $skill): 
                                $skill = trim($skill);
                                if($skill):
                            ?>
                                <span style="background: #e9ecef; color: #495057; padding: 0.25rem 0.75rem; border-radius: 50px; font-size: 0.85rem;"><?= htmlspecialchars($skill) ?></span>
                            <?php endif; endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if(!empty($student['experience'])): ?>
                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="border-bottom: 2px solid #eee; padding-bottom: 0.5rem; margin-bottom: 0.5rem;">Experience</h4>
                        <p style="color: var(--text-main); line-height: 1.6;"><?= nl2br(htmlspecialchars($student['experience'])) ?></p>
                    </div>
                <?php endif; ?>

                <?php if(!empty($student['education'])): ?>
                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="border-bottom: 2px solid #eee; padding-bottom: 0.5rem; margin-bottom: 0.5rem;">Education</h4>
                        <p style="color: var(--text-main); line-height: 1.6;"><?= nl2br(htmlspecialchars($student['education'])) ?></p>
                    </div>
                <?php endif; ?>

            </div>
        </section>
        </div>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>
