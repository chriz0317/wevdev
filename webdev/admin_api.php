<?php
// admin_api.php - API for delete operations (called from dashboard)
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    exit('Unauthorized');
}

$pdo = require __DIR__ . '/db.php';
$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    http_response_code(400);
    exit('Invalid ID');
}

try {
    if ($action === 'delete_product') {
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);
        $_SESSION['info'] = 'Product deleted successfully!';
    } elseif ($action === 'delete_user') {
        // Don't allow deleting yourself
        if ($id === $_SESSION['admin_id']) {
            $_SESSION['error'] = 'Cannot delete your own account!';
        } else {
            $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
            $stmt->execute([$id]);
            $_SESSION['info'] = 'User deleted successfully!';
        }
    } else {
        http_response_code(400);
        exit('Invalid action');
    }

    header('Location: admin_dashboard.php');
    exit;
} catch (Exception $e) {
    $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    header('Location: admin_dashboard.php');
    exit;
}
?>
