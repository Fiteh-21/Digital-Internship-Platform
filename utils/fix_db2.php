<?php
require_once '../includes/db.php';

try {
    function addColumnSafely($pdo, $table, $column, $definition, $after = '') {
        $stmt = $pdo->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        if (!$stmt->fetch()) {
            $sql = "ALTER TABLE `$table` ADD COLUMN `$column` $definition";
            if ($after) $sql .= " AFTER `$after`";
            $pdo->exec($sql);
        }
    }

    // Modify users table
    addColumnSafely($pdo, 'users', 'phone', 'VARCHAR(50) DEFAULT NULL');
    addColumnSafely($pdo, 'users', 'location', 'VARCHAR(255) DEFAULT NULL');
    addColumnSafely($pdo, 'users', 'headline', 'VARCHAR(255) DEFAULT NULL');
    addColumnSafely($pdo, 'users', 'about', 'TEXT DEFAULT NULL');
    addColumnSafely($pdo, 'users', 'skills', 'TEXT DEFAULT NULL');
    addColumnSafely($pdo, 'users', 'experience', 'TEXT DEFAULT NULL');
    addColumnSafely($pdo, 'users', 'education', 'TEXT DEFAULT NULL');
    addColumnSafely($pdo, 'users', 'portfolio', 'VARCHAR(255) DEFAULT NULL');
    addColumnSafely($pdo, 'users', 'profile_picture', 'VARCHAR(255) DEFAULT NULL');
    addColumnSafely($pdo, 'users', 'resume_path', 'VARCHAR(255) DEFAULT NULL');

    // Modify internships table
    addColumnSafely($pdo, 'internships', 'category', "VARCHAR(255) DEFAULT 'Uncategorized'", 'title');

    // Modify applications table
    addColumnSafely($pdo, 'applications', 'employer_message', 'TEXT DEFAULT NULL', 'cover_letter');

    echo "<h3>Database successfully updated with new columns for the expansion plan!</h3>";
    echo "<p>You can now close this page and continue testing.</p>";
    echo "<a href='../index.php'>Go to Home</a>";

} catch (\PDOException $e) {
    echo "<h3>Error updating database schema:</h3><p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
