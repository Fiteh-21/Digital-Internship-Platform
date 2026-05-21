<?php
require_once '../includes/db.php';

try {
    // Alter the database default character set
    $pdo->exec("ALTER DATABASE digital_internship CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci");

    // Alter the tables
    $pdo->exec("ALTER TABLE users CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("ALTER TABLE internships CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("ALTER TABLE applications CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    echo "<h3>Database successfully updated to support Emojis (utf8mb4)!</h3>";
    echo "<p>You can now close this page and try posting your internship again.</p>";
    echo "<a href='../company/employer_dashboard.php'>Go back to Dashboard</a>";

} catch (\PDOException $e) {
    echo "<h3>Error updating database:</h3><p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
