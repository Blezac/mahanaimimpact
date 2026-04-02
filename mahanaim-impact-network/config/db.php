<?php
// === config/db.php ===

define('DB_HOST', '10.50.226.3');
define('DB_NAME', 'mahanaim_db');
define('DB_USER', 'mahanaim');
define('DB_PASS', 'Mahanaim20260227.');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>
