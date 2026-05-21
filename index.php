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
                        
                        // Comprehensive category icon mapping
                        if (strpos($c, 'it') !== false || strpos($c, 'software') !== false)
                            $icon = 'fas fa-laptop-code';
                        elseif (strpos($c, 'marketing') !== false || strpos($c, 'sales') !== false)
                            $icon = 'fas fa-bullhorn';
                        elseif (strpos($c, 'finance') !== false || strpos($c, 'accounting') !== false)
                            $icon = 'fas fa-chart-line';
                        elseif (strpos($c, 'design') !== false || strpos($c, 'creative') !== false)
                            $icon = 'fas fa-paint-brush';
                        elseif (strpos($c, 'engineering') !== false)
                            $icon = 'fas fa-cogs';
                        elseif (strpos($c, 'healthcare') !== false || strpos($c, 'medical') !== false)
                            $icon = 'fas fa-stethoscope';
                        elseif (strpos($c, 'education') !== false || strpos($c, 'training') !== false)
                            $icon = 'fas fa-graduation-cap';
                        elseif (strpos($c, 'human resources') !== false || strpos($c, 'hr') !== false)
                            $icon = 'fas fa-people-arrows';
                        elseif (strpos($c, 'business') !== false || strpos($c, 'management') !== false)
                            $icon = 'fas fa-building';
                        elseif (strpos($c, 'customer service') !== false)
                            $icon = 'fas fa-headset';
                        elseif (strpos($c, 'media') !== false || strpos($c, 'communication') !== false)
                            $icon = 'fas fa-broadcast-tower';
                        elseif (strpos($c, 'legal') !== false || strpos($c, 'law') !== false)
                            $icon = 'fas fa-gavel';
                        elseif (strpos($c, 'agriculture') !== false || strpos($c, 'environmental') !== false)
                            $icon = 'fas fa-leaf';
                        elseif (strpos($c, 'architecture') !== false || strpos($c, 'construction') !== false)
                            $icon = 'fas fa-hammer';
                        elseif (strpos($c, 'logistics') !== false || strpos($c, 'supply chain') !== false)
                            $icon = 'fas fa-dolly';
                        elseif (strpos($c, 'tourism') !== false || strpos($c, 'hospitality') !== false)
                            $icon = 'fas fa-hotel';
                        elseif (strpos($c, 'data science') !== false || strpos($c, 'ai') !== false)
                            $icon = 'fas fa-brain';
                        elseif (strpos($c, 'cybersecurity') !== false || strpos($c, 'networking') !== false)
                            $icon = 'fas fa-shield-alt';
                        elseif (strpos($c, 'banking') !== false || strpos($c, 'insurance') !== false)
                            $icon = 'fas fa-university';
                        elseif (strpos($c, 'economics') !== false || strpos($c, 'statistics') !== false)
                            $icon = 'fas fa-chart-bar';
                        elseif (strpos($c, 'science') !== false || strpos($c, 'research') !== false)
                            $icon = 'fas fa-flask';
                        elseif (strpos($c, 'multimedia') !== false || strpos($c, 'animation') !== false)
                            $icon = 'fas fa-film';
                        elseif (strpos($c, 'public administration') !== false)
                            $icon = 'fas fa-landmark';
                        elseif (strpos($c, 'social sciences') !== false)
                            $icon = 'fas fa-users';
                        elseif (strpos($c, 'journalism') !== false || strpos($c, 'publishing') !== false)
                            $icon = 'fas fa-newspaper';
                        elseif (strpos($c, 'pharmacy') !== false || strpos($c, 'biotechnology') !== false)
                            $icon = 'fas fa-flask-vial';
                        elseif (strpos($c, 'telecommunications') !== false)
                            $icon = 'fas fa-phone';
                        elseif (strpos($c, 'industrial') !== false || strpos($c, 'manufacturing') !== false)
                            $icon = 'fas fa-industry';
                        elseif (strpos($c, 'project management') !== false)
                            $icon = 'fas fa-tasks';
                        elseif (strpos($c, 'entrepreneurship') !== false || strpos($c, 'startups') !== false)
                            $icon = 'fas fa-rocket';
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