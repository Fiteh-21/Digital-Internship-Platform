<?php
require_once '../includes/db.php';
$stmt = $pdo->query("SELECT * FROM applications");
$apps = $stmt->fetchAll();
file_put_contents('../db_dump.json', json_encode($apps, JSON_PRETTY_PRINT));
echo "Dumped " . count($apps) . " applications.";
?>
