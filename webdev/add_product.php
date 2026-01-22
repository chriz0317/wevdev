<?php
// add_product.php - insert product (requires login)
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// basic auth + admin role check
if (empty($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Login required');
}

$csrf = $_POST['csrf_token'] ?? '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
    $_SESSION['prod_error'] = 'Invalid CSRF token';
    header('Location: Product.html');
    exit;
}

if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    $_SESSION['prod_error'] = 'Admin access required to add products';
    header('Location: Product.html');
    exit;
}

$pdo = require __DIR__ . '/db.php';

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$price = floatval($_POST['price'] ?? 0);

if (!$title || $price <= 0) {
    $_SESSION['prod_error'] = 'Title and positive price required';
    header('Location: Product.html');
    exit;
}

$imagePath = null;
if (!empty($_FILES['image']['tmp_name'])) {
    $allowed = ['image/jpeg','image/png','image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES['image']['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed)) {
        $_SESSION['prod_error'] = 'Invalid image type';
        header('Location: Product.html');
        exit;
    }

    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $filename = bin2hex(random_bytes(8)) . '.' . $ext;
    $destDir = __DIR__ . '/uploads';
    if (!is_dir($destDir)) mkdir($destDir, 0755, true);
    $dest = $destDir . '/' . $filename;
    move_uploaded_file($_FILES['image']['tmp_name'], $dest);
    $imagePath = 'uploads/' . $filename;
}

$stmt = $pdo->prepare('INSERT INTO products (title, description, price, image) VALUES (?, ?, ?, ?)');
$stmt->execute([$title, $description, $price, $imagePath]);

header('Location: Product.html');
exit;

?>
