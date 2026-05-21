<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Determine path prefix for subfolders
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
$prefix = (in_array($current_dir, ['student', 'company', 'admin', 'auth'])) ? '../' : '';

$page_name = basename($_SERVER['PHP_SELF']);
$is_home_active = ($page_name === 'index.php' || $page_name === 'category.php') ? 'active' : '';
$is_dashboard_active = (in_array($current_dir, ['student', 'company'])) ? 'active' : '';
$is_admin_active = ($current_dir === 'admin') ? 'active' : '';

$pending_count = 0;
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'employer' && isset($pdo)) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM applications a JOIN internships i ON a.internship_id = i.id WHERE i.employer_id = ? AND a.status = 'pending'");
    $stmt->execute([$_SESSION['user_id']]);
    $pending_count = $stmt->fetchColumn();
}

// Fetch unread notification count
$unread_notif_count = 0;
if (isset($_SESSION['user_id']) && isset($pdo)) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$_SESSION['user_id']]);
    $unread_notif_count = $stmt->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternHub Internships</title>
    <link rel="stylesheet" href="<?= $prefix ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Apply saved theme before first paint to prevent flash -->
    <script>
      (function(){
        try {
          var t = localStorage.getItem('internhub_theme');
          if (t === 'violet') document.documentElement.classList.add('theme-violet-pre');
        } catch(e) {}
      })();
    </script>
    <style>
      /* Pre-paint class on <html> so body inherits immediately */
      html.theme-violet-pre body { background: #f5f3ff !important; }
    </style>
</head>

<body>
    <header class="topbar">
        <div class="container nav-wrapper">
            <a href="<?= $prefix ?>index.php" class="logo">
                <i class="fas fa-rocket"></i> Intern<span>Hub</span>
            </a>
            <button class="nav-toggle" id="navToggle" type="button" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            <nav class="nav-links">
                <a href="<?= $prefix ?>index.php" class="<?= $is_home_active ?>">Home</a>
                <?php if (!in_array($current_dir, ['company', 'student', 'admin'])): ?>
                    <a href="<?= $prefix ?>index.php#about">About</a>
                    <a href="<?= $prefix ?>index.php#categories">Category</a>
                    <a href="<?= $prefix ?>index.php#contact">Contact</a>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] === 'student'): ?>
                        <a href="<?= $prefix ?>student/student_dashboard.php" class="<?= $is_dashboard_active ?>">Dashboard</a>
                    <?php elseif ($_SESSION['role'] === 'employer'): ?>
                        <a href="<?= $prefix ?>company/employer_dashboard.php" class="<?= $is_dashboard_active ?>">Dashboard</a>
                    <?php elseif ($_SESSION['role'] === 'admin'): ?>
                        <a href="<?= $prefix ?>admin/dashboard.php" class="<?= $is_admin_active ?>">Admin Panel</a>
                    <?php endif; ?>
                    <a href="<?= $prefix ?>api/logout.php" class="btn btn-outline">Logout</a>
                    <div class="profile-chip" tabindex="0" aria-label="Current user profile">
                        <span class="avatar"><?= strtoupper(substr($_SESSION['name'] ?? 'U', 0, 1)) ?></span>
                        <span>
                            <strong><?= htmlspecialchars($_SESSION['name'] ?? 'User') ?></strong>
                            <small><?= htmlspecialchars($_SESSION['role'] ?? '') ?></small>
                        </span>
                    </div>
                <?php else: ?>
                    <a href="<?= $prefix ?>auth/login.php" class="btn btn-primary">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- ===== Theme Toggle Button ===== -->
    <button id="themeToggleBtn" type="button" title="Switch theme" aria-label="Toggle colour theme">
        <span class="theme-icon-default"><i class="fas fa-palette"></i></span>
        <span class="theme-icon-violet"><i class="fas fa-wand-magic-sparkles"></i></span>
    </button>