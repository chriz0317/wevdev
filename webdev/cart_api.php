<?php
session_start();
require_once 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'login_required']);
    exit;
}

$user_id = intval($_SESSION['user_id']);

// POST: Add Item
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['product_id']) || !isset($data['quantity'])) {
        http_response_code(400); echo json_encode(['error' => 'Invalid request']); exit;
    }

    $product_id = intval($data['product_id']);
    $quantity = intval($data['quantity']);
    $color = isset($data['color']) ? trim($data['color']) : 'Standard';

    // 1. Check if we have enough STOCK
    $stockStmt = $pdo->prepare('SELECT stock, title FROM products WHERE id = ?');
    $stockStmt->execute([$product_id]);
    $product = $stockStmt->fetch();

    if (!$product) {
        http_response_code(404); echo json_encode(['error' => 'Product not found']); exit;
    }

    // Check how many are ALREADY in the cart
    $cartStmt = $pdo->prepare('SELECT SUM(quantity) as qty FROM cart_items WHERE user_id = ? AND product_id = ?');
    $cartStmt->execute([$user_id, $product_id]);
    $inCart = $cartStmt->fetch();
    $currentCartQty = intval($inCart['qty'] ?? 0);

    // Validate: Total wanted (Cart + New) must be <= Stock
    if (($currentCartQty + $quantity) > $product['stock']) {
        http_response_code(400); 
        echo json_encode(['error' => 'Not enough stock! Available: ' . $product['stock']]); 
        exit;
    }

    // 2. Add/Update Cart
    $stmt = $pdo->prepare('SELECT quantity FROM cart_items WHERE user_id = ? AND product_id = ? AND selected_color = ?');
    $stmt->execute([$user_id, $product_id, $color]);
    $existing = $stmt->fetch();

    if ($existing) {
        $stmt = $pdo->prepare('UPDATE cart_items SET quantity = quantity + ? WHERE user_id = ? AND product_id = ? AND selected_color = ?');
        $stmt->execute([$quantity, $user_id, $product_id, $color]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO cart_items (user_id, product_id, quantity, selected_color) VALUES (?, ?, ?, ?)');
        $stmt->execute([$user_id, $product_id, $quantity, $color]);
    }

    // Return Count
    $stmt = $pdo->prepare('SELECT SUM(quantity) as count FROM cart_items WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    echo json_encode(['success' => true, 'cart_count' => $count]);
    exit;
}

// PUT: Update Quantity
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $product_id = intval($data['product_id']);
    $quantity = intval($data['quantity']);
    $color = isset($data['color']) ? trim($data['color']) : 'Standard';

    if ($quantity <= 0) {
        $stmt = $pdo->prepare('DELETE FROM cart_items WHERE user_id = ? AND product_id = ? AND selected_color = ?');
        $stmt->execute([$user_id, $product_id, $color]);
    } else {
        // Check stock before allowing increase
        $stockStmt = $pdo->prepare('SELECT stock FROM products WHERE id = ?');
        $stockStmt->execute([$product_id]);
        $product = $stockStmt->fetch();

        // We only check against total stock here roughly; for strict checks you'd sum up all color variants in cart
        if ($product && $quantity > $product['stock']) {
             // Just limit to max stock silently or error out
             $quantity = $product['stock']; 
        }

        $stmt = $pdo->prepare('UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ? AND selected_color = ?');
        $stmt->execute([$quantity, $user_id, $product_id, $color]);
    }

    $stmt = $pdo->prepare('SELECT SUM(quantity) as count FROM cart_items WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    echo json_encode(['success' => true, 'cart_count' => $count]);
    exit;
}

// DELETE: Remove Item
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $product_id = intval($data['product_id']);
    $color = isset($data['color']) ? trim($data['color']) : 'Standard';

    $stmt = $pdo->prepare('DELETE FROM cart_items WHERE user_id = ? AND product_id = ? AND selected_color = ?');
    $stmt->execute([$user_id, $product_id, $color]);

    $stmt = $pdo->prepare('SELECT SUM(quantity) as count FROM cart_items WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    echo json_encode(['success' => true, 'cart_count' => $count]);
    exit;
}

// GET: List Items
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare('SELECT ci.*, p.title, p.price, p.image FROM cart_items ci JOIN products p ON ci.product_id = p.id WHERE ci.user_id = ? ORDER BY ci.added_at DESC');
    $stmt->execute([$user_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['items' => $items, 'cart_count' => array_sum(array_column($items, 'quantity'))]);
    exit;
}
?>