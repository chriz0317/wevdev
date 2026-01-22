<?php
// csrf_token.php - issue or return CSRF token (JSON)
session_start();
header('Content-Type: application/json; charset=utf-8');
if (empty($_SESSION['csrf_token'])) {
    // 32 bytes -> 64 hex chars
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
echo json_encode(['csrf_token' => $_SESSION['csrf_token']]);
exit;

?>
