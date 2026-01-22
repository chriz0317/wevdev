<?php
// admin_add_product.php - Add/Edit Product with Stock & Color
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

$pdo = require __DIR__ . '/db.php';
$product = null;
$isEdit = false;

// If editing, fetch current data
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    if (!$product) {
        header('Location: admin_dashboard.php');
        exit;
    }
    $isEdit = true;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $color = trim($_POST['color'] ?? ''); // NEW: Color
    $image = trim($_POST['image'] ?? '');

    $errors = [];
    if (!$title) $errors[] = 'Title is required';
    if ($price <= 0) $errors[] = 'Price must be greater than 0';
    if ($stock < 0) $errors[] = 'Stock cannot be negative';
    if (!$color) $errors[] = 'Color is required (e.g. "Black, White")'; // NEW: Validation

    if (!$errors) {
        try {
            if ($isEdit) {
                // Update existing product
                $stmt = $pdo->prepare('UPDATE products SET title = ?, description = ?, price = ?, stock = ?, color = ?, image = ? WHERE id = ?');
                $stmt->execute([$title, $description, $price, $stock, $color, $image ?: null, $product['id']]);
                $message = 'Product updated successfully!';
            } else {
                // Insert new product
                $stmt = $pdo->prepare('INSERT INTO products (title, description, price, stock, color, image) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->execute([$title, $description, $price, $stock, $color, $image ?: null]);
                $message = 'Product added successfully!';
            }
            $_SESSION['info'] = $message;
            header('Location: admin_dashboard.php?tab=products');
            exit;
        } catch (Exception $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit' : 'Add'; ?> Product — FurniEshop Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .form-container { max-width: 700px; margin: 40px auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); }
    </style>
</head>
<body>
    <div class="form-container">
        <a href="admin_dashboard.php" class="btn btn-outline-secondary mb-3">
            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>

        <h2 class="mb-4"><?php echo $isEdit ? 'Edit Product' : 'Add New Product'; ?></h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" novalidate>
            <div class="mb-3">
                <label for="title" class="form-label">Product Title *</label>
                <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($product['title'] ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description *</label>
                <textarea name="description" id="description" class="form-control" rows="4" required><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="price" class="form-label">Price ($) *</label>
                    <input type="number" name="price" id="price" class="form-control" step="0.01" value="<?php echo htmlspecialchars($product['price'] ?? ''); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="stock" class="form-label">Stock Qty *</label>
                    <input type="number" name="stock" id="stock" class="form-control" value="<?php echo htmlspecialchars($product['stock'] ?? '0'); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="color" class="form-label">Colors *</label>
                    <input type="text" name="color" id="color" class="form-control" 
                           value="<?php echo htmlspecialchars($product['color'] ?? 'Standard'); ?>" 
                           placeholder="e.g. Black, White, Red">
                </div>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Image URL</label>
                <input type="url" name="image" id="image" class="form-control" value="<?php echo htmlspecialchars($product['image'] ?? ''); ?>">
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i><?php echo $isEdit ? 'Update Product' : 'Add Product'; ?>
                </button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>