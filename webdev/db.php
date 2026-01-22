<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// db.php - PDO connection for Infinityfree
$host = 'sql101.infinityfree.com';
$db   = 'if0_40860683_eshop';
$user = 'if0_40860683';
$pass = 'RLIS58Mafpm';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opts = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $opts);
} catch (PDOException $e) {
    // This stops the 500 error and shows the real problem if one exists
    http_response_code(500);
    echo 'Database connection error: ' . $e->getMessage();
    exit;
}

return $pdo;