<?php
// products.php - API for fetching product data
// IMPORTANT: Do not put any HTML in this file!
header('Content-Type: application/json; charset=utf-8');
require_once 'db.php';

try {
    // Fetch products
    $stmt = $pdo->query('SELECT id, title, description, price, stock, image FROM products ORDER BY created_at DESC');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ensure numbers are correct
    foreach ($rows as &$r) {
        $r['price'] = floatval($r['price']);
        $r['stock'] = intval($r['stock']);
        if (empty($r['image'])) $r['image'] = null;
    }

    echo json_encode($rows);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>