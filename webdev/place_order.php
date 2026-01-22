<?php
// place_order.php - Handles order placement and updates stock
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: Cart.php?modal=login');
    exit;
}

$pdo = require __DIR__ . '/db.php';
$userId = intval($_SESSION['user_id']);
$method = $_POST['payment_method'] ?? 'cod';

try {
    // 1. Start a "Transaction" (Safety feature)
    // This ensures that if something fails, no stock is lost.
    $pdo->beginTransaction();

    // 2. Get all items currently in the user's cart
    $stmt = $pdo->prepare('SELECT product_id, quantity FROM cart_items WHERE user_id = ?');
    $stmt->execute([$userId]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($cartItems)) {
        throw new Exception("Your cart is empty.");
    }

    // 3. Loop through items and subtract stock
    $updateStock = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?');

    foreach ($cartItems as $item) {
        $pid = intval($item['product_id']);
        $qty = intval($item['quantity']);

        // Run the update: Subtract Qty from Stock, BUT only if there is enough stock
        $updateStock->execute([$qty, $pid, $qty]);
        
        // Optional: Check if the update actually happened (row count > 0)
        // If it didn't, it means stock was too low.
        if ($updateStock->rowCount() === 0) {
            throw new Exception("One of your items is out of stock or quantity is too high.");
        }
    }

    // 4. Empty the Cart (Delete items)
    $clearCart = $pdo->prepare('DELETE FROM cart_items WHERE user_id = ?');
    $clearCart->execute([$userId]);

    // 5. Save changes
    $pdo->commit();

    $_SESSION['info'] = 'Order placed successfully! Payment method: ' . htmlspecialchars($method);
    header('Location: order_success.php');
    exit;

} catch (Exception $e) {
    // If error, undo changes
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Show error message
    echo "<h1>Order Failed</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo '<a href="Cart.php">Return to Cart</a>';
    exit;
}
?>