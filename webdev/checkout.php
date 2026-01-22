<?php
// checkout.php - server-side checkout page; requires login
session_start();
if (empty($_SESSION['user_id'])) {
    // Not logged in: redirect back to cart and open login modal
    $u = 'Cart.php?modal=login';
    header('Location: ' . $u);
    exit;
}

// Minimal checkout page (expand as needed)
$userName = htmlspecialchars($_SESSION['user_name'] ?? '');

// Fetch cart items for this user
$pdo = require __DIR__ . '/db.php';
$userId = intval($_SESSION['user_id']);

// UPDATED QUERY: Now selects 'ci.selected_color'
$stmt = $pdo->prepare('SELECT ci.quantity, ci.selected_color, p.id as product_id, p.title, p.description, p.price, p.image
  FROM cart_items ci
  JOIN products p ON ci.product_id = p.id
  WHERE ci.user_id = ?');
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll();

$subtotal = 0.0;
foreach ($cartItems as $it) {
  $subtotal += floatval($it['price']) * intval($it['quantity']);
}

// Shipping policy: show a shipping fee, but if subtotal >= 200 show a shipping discount equal to fee
$shippingFee = 34.00;
$shippingDiscount = 0.00;
if ($subtotal >= 200) { $shippingDiscount = $shippingFee; }
$total = $subtotal + $shippingFee - $shippingDiscount;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Checkout — FurniEshop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="styles.css" rel="stylesheet" />
</head>
<body>
  <?php include 'navbar.php'; ?>
  <main class="container py-5">
    <h1 class="mb-4">Checkout</h1>
    <p>Hi <?php echo $userName; ?> — review your order and complete payment.</p>

    <div class="row g-4">
      <div class="col-lg-7">
        <div class="card p-4">
          <h5 class="mb-3">Shipping address</h5>
          <p class="text-muted">Use your account address or enter another address at checkout.</p>

          <hr>

          <h5 class="mb-3">Items</h5>
          <?php if (empty($cartItems)): ?>
            <div class="text-muted">Your cart is empty.</div>
          <?php else: ?>
            <?php foreach ($cartItems as $it): ?>
              <div class="d-flex mb-3 align-items-center" data-product-id="<?php echo $it['product_id']; ?>">
                <img src="<?php echo htmlspecialchars($it['image'] ?: 'https://via.placeholder.com/80'); ?>" alt="item" class="rounded" style="width:80px;height:80px;object-fit:cover;">
                <div class="ms-3 flex-grow-1">
                  <div class="fw-bold"><?php echo htmlspecialchars($it['title']); ?></div>
                  
                  <?php if (!empty($it['selected_color']) && $it['selected_color'] !== 'Standard'): ?>
                    <div class="text-muted small">Color: <?php echo htmlspecialchars($it['selected_color']); ?></div>
                  <?php endif; ?>

                  <div class="text-muted small">Price: ₱<?php echo number_format($it['price'], 2); ?></div>
                  <div class="mt-2 d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary qty-minus">−</button>
                    <input type="number" class="form-control" style="width:60px;" min="1" value="<?php echo intval($it['quantity']); ?>" class="qty-input">
                    <button type="button" class="btn btn-sm btn-outline-secondary qty-plus">+</button>
                    <button type="button" class="btn btn-sm btn-danger btn-remove">Remove</button>
                  </div>
                </div>
                <div class="text-end">
                  <div class="fw-bold">₱<?php echo number_format($it['price'] * $it['quantity'], 2); ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

          <div class="d-flex justify-content-between align-items-center mt-4">
            <a href="Cart.php" class="btn btn-outline-secondary">Edit cart</a>
            <small class="text-muted">Need help? <a href="Contact.php">Contact us</a></small>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="card p-4" style="position:sticky; top:20px;">
          <h5 class="mb-3">Order summary</h5>
          <div class="mb-3 small text-muted">Product subtotal</div>
          <div class="d-flex justify-content-between mb-2">
            <div>Subtotal</div>
            <div class="fw-bold">₱<?php echo number_format($subtotal, 2); ?></div>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <div>Shipping fee</div>
            <div class="text-muted">₱<?php echo number_format($shippingFee, 2); ?></div>
          </div>
          <div class="d-flex justify-content-between mb-3">
            <div>Shipping discount</div>
            <div class="text-danger">-₱<?php echo number_format($shippingDiscount, 2); ?></div>
          </div>
          <hr>
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="fs-5 fw-bold">Total (<?php echo array_sum(array_column($cartItems, 'quantity')) ?: 0; ?> item)</div>
            <div class="fs-5 fw-bold">₱<?php echo number_format($total, 2); ?></div>
          </div>

          <h6 class="mb-2">Payment method</h6>
          <form method="post" action="place_order.php">
            <div class="mb-2">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" id="pm_cod" value="cod" checked>
                <label class="form-check-label" for="pm_cod">Cash on delivery</label>
              </div>
            </div>
            <div class="mb-2">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" id="pm_card" value="card">
                <label class="form-check-label" for="pm_card">Add credit/debit card</label>
              </div>
              <div class="mt-2 small text-muted">Supports Visa, Mastercard</div>
            </div>
            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" id="pm_gcash" value="gcash">
                <label class="form-check-label" for="pm_gcash">GCash</label>
              </div>
            </div>

            <button type="submit" class="btn btn-danger w-100 mb-2" style="background:#ff3b6b; border-color:#ff3b6b;">Place order</button>
            <div class="text-center small text-muted">By placing an order you agree to our <a href="#">Terms</a> and <a href="#">Privacy Policy</a>.</div>
          </form>
        </div>
      </div>
    </div>
  </main>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script>
    $(function(){
      // Live cart editing
      $(document).on('click', '.qty-plus', function(){
        const $item = $(this).closest('[data-product-id]');
        const productId = $item.data('product-id');
        const $qtyInput = $item.find('input[type="number"]');
        let qty = parseInt($qtyInput.val()) || 1;
        qty++;
        updateCartItem(productId, qty);
      });

      $(document).on('click', '.qty-minus', function(){
        const $item = $(this).closest('[data-product-id]');
        const productId = $item.data('product-id');
        const $qtyInput = $item.find('input[type="number"]');
        let qty = parseInt($qtyInput.val()) || 1;
        if (qty > 1) qty--;
        updateCartItem(productId, qty);
      });

      $(document).on('change', 'input[type="number"]', function(){
        const $item = $(this).closest('[data-product-id]');
        const productId = $item.data('product-id');
        const qty = parseInt($(this).val()) || 1;
        updateCartItem(productId, qty);
      });

      $(document).on('click', '.btn-remove', function(){
        const $item = $(this).closest('[data-product-id]');
        const productId = $item.data('product-id');
        if (confirm('Remove this item from cart?')) {
          removeCartItem(productId);
        }
      });

      function updateCartItem(productId, quantity){
        $.ajax({
          url: 'cart_api.php',
          method: 'PUT',
          contentType: 'application/json',
          data: JSON.stringify({
            product_id: productId,
            quantity: quantity
          }),
          success: function(data){
            if (data.success) {
              location.reload(); // Reload page to update totals
            }
          },
          error: function(){
            alert('Failed to update cart');
          }
        });
      }

      function removeCartItem(productId){
        $.ajax({
          url: 'cart_api.php',
          method: 'DELETE',
          contentType: 'application/json',
          data: JSON.stringify({
            product_id: productId
          }),
          success: function(data){
            if (data.success) {
              location.reload(); // Reload page to update totals and items list
            }
          },
          error: function(){
            alert('Failed to remove item');
          }
        });
      }
    });
  </script>
</body>
</html>