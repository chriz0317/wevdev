<?php
// flash.php - return and clear session flash messages as JSON
session_start();
header('Content-Type: application/json; charset=utf-8');
$out = [
    'reg_errors' => $_SESSION['reg_errors'] ?? null,
    'login_error' => $_SESSION['login_error'] ?? null,
    'prod_error' => $_SESSION['prod_error'] ?? null,
    'prod_success' => $_SESSION['prod_success'] ?? null,
    'info' => $_SESSION['info'] ?? null,
];

// clear them
foreach (array_keys($out) as $k) {
    if (isset($_SESSION[$k])) unset($_SESSION[$k]);
}

echo json_encode($out);
exit;

?>
