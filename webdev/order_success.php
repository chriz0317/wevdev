<?php
session_start();
$info = $_SESSION['info'] ?? null;
unset($_SESSION['info']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Order Success — FurniEshop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
  <?php include 'navbar.php'; ?>
  <main class="container py-5">
    <div class="card p-4">
      <h2>Thank you!</h2>
      <p class="lead">Your order has been received.</p>
      <?php if ($info): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($info); ?></div>
      <?php endif; ?>
      <a href="index.php" class="btn btn-primary">Continue shopping</a>
    </div>
  </main>
</body>
</html>