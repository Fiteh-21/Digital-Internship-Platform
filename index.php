<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Fetch category counts from internships
$stmt = $pdo->query("SELECT category, COUNT(*) as count FROM internships GROUP BY category ORDER BY count DESC");
$categories = $stmt->fetchAll();
?>

<section class="hero">
    <div class="hero-content">
        <h1>Launch Your Career with <span>Premium Internships</span></h1>
        <p>Connect with top companies, build your resume, and kickstart your professional journey today.</p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="auth/register.php" class="btn btn-primary btn-hero">Get Started
                Now</a>
        <?php endif; ?>
    </div>
</section>

<section id="about" class="about-section">
    <div class="container">
        <h2 class="text-center section-title about-title">Empowering the
            Next Generation of <span>Talent</span></h2>
        <div class="about-grid">
            <div class="about-image">
                <img src="assets/images/about_internhub.png" alt="About InternHub" class="about-img-fluid">
            </div>
            <div class="about-text">
                <p>InternHub, is a modern web-based platform designed to connect
                    students and companies through internship opportunities. Students can create profiles, explore
                    available internships, and apply directly through the system, while companies can post internship
                    opportunities and manage applications efficiently.</p>
                <div class="about-features">

                    <div class="feature-item">
                        <ul class="list-unstyled">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Curated Premium Internships</span>
                            </li>
                        </ul>
                    </div>


                    <div class="feature-item">
                        <ul class="list-unstyled">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Top Company Partnerships</span>
                            </li>
                        </ul>
                    </div>

                    <div class="feature-item">
                        <ul class="list-unstyled">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Career Growth Resources</span>
                            </li>
                        </ul>
                    </div>




                </div>
                <p class="mt-1-5rem">Join students and companies connecting through our internship portal to
                    discover opportunities, build experience, and create successful career pathways.
                </p>
            </div>
        </div>
    </div>
</section>

<div class="container" id="categories">
    <h2 class="text-center section-title categories-title">Explore Categories
    </h2>
    <?php if (empty($categories)): ?>
        <p class="text-center mt-4 text-muted">No categories available yet. Be the first to post!</p>
    <?php else: ?>
        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
            <?php foreach ($categories as $cat): ?>
                <a href="category.php?name=<?= urlencode($cat['category']) ?>" class="category-link">
                    <div class="card category-card">
                        <?php
                        $icon = 'fas fa-briefcase';
                        $c = strtolower($cat['category']);
                        if (strpos($c, 'it') !== false || strpos($c, 'software') !== false)
                            $icon = 'fas fa-laptop-code';
                        if (strpos($c, 'marketing') !== false || strpos($c, 'sales') !== false)
                            $icon = 'fas fa-bullhorn';
                        if (strpos($c, 'finance') !== false || strpos($c, 'accounting') !== false)
                            $icon = 'fas fa-chart-line';
                        if (strpos($c, 'design') !== false || strpos($c, 'creative') !== false)
                            $icon = 'fas fa-paint-brush';
                        if (strpos($c, 'engineering') !== false)
                            $icon = 'fas fa-cogs';
                        ?>
                        <i class="<?= $icon ?> category-icon"></i>
                        <h3 class="category-item-title"><?= htmlspecialchars($cat['category']) ?></h3>
                        <p class="text-muted category-item-count"><?= $cat['count'] ?> Opportunities</p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>