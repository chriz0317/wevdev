<?php
// admin_dashboard.php - Admin dashboard with Stock AND Color columns
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

$pdo = require __DIR__ . '/db.php';

// Get users
$usersStmt = $pdo->query('SELECT id, name, email, contact, role, is_active, last_login, created_at FROM users ORDER BY created_at DESC');
$users = $usersStmt->fetchAll();

// Get products (Fetching stock AND color)
$productsStmt = $pdo->query('SELECT id, title, description, price, stock, color, image, created_at FROM products ORDER BY created_at DESC');
$products = $productsStmt->fetchAll();

// Stats
$userCount = count($users);
$productCount = count($products);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — FurniEshop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; color: white; }
        .sidebar a { color: white; text-decoration: none; display: block; padding: 10px 15px; border-radius: 8px; margin-bottom: 10px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255, 255, 255, 0.2); }
        .stat-card { background: white; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); border-left: 4px solid #667eea; }
        .stat-number { font-size: 2rem; font-weight: bold; color: #667eea; }
        .table-container { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); }
        .btn-sm-custom { padding: 5px 12px; font-size: 0.85rem; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 sidebar">
                <div class="mb-4">
                    <h3 class="fw-bold"><i class="bi bi-speedometer2 me-2"></i>Admin Panel</h3>
                    <small>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></small>
                </div>
                <hr class="bg-light">
                <a href="#dashboard" class="active" onclick="showTab('dashboard')"><i class="bi bi-graph-up me-2"></i>Dashboard</a>
                <a href="#users" onclick="showTab('users')"><i class="bi bi-people me-2"></i>Users</a>
                <a href="#products" onclick="showTab('products')"><i class="bi bi-box me-2"></i>Products</a>
                <hr class="bg-light">
                <a href="admin_add_product.php" class="btn btn-outline-light w-100 mb-2"><i class="bi bi-plus-circle me-2"></i>Add Product</a>
                <a href="logout.php" class="btn btn-outline-danger w-100"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
            </div>

            <div class="col-md-9 p-4">
                <div id="dashboard" class="tab-content">
                    <h2 class="mb-4">Dashboard Overview</h2>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="stat-card">
                                <i class="bi bi-people" style="font-size: 2rem; color: #667eea;"></i>
                                <p class="text-muted mt-2">Total Users</p>
                                <div class="stat-number"><?php echo $userCount; ?></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card">
                                <i class="bi bi-box" style="font-size: 2rem; color: #764ba2;"></i>
                                <p class="text-muted mt-2">Total Products</p>
                                <div class="stat-number"><?php echo $productCount; ?></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card">
                                <i class="bi bi-calendar" style="font-size: 2rem; color: #28a745;"></i>
                                <p class="text-muted mt-2">Today</p>
                                <div class="stat-number" style="font-size: 1.5rem;"><?php echo date('M d'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="users" class="tab-content" style="display: none;">
                    <h2 class="mb-4">Users Management</h2>
                    <div class="table-container">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Active</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : 'primary'; ?>"><?php echo $user['role']; ?></span></td>
                                    <td><span class="badge bg-<?php echo $user['is_active'] ? 'success' : 'warning'; ?>"><?php echo $user['is_active'] ? 'Yes' : 'No'; ?></span></td>
                                    <td>
                                        <a href="admin_edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-info btn-sm-custom">Edit</a>
                                        <a href="admin_api.php?action=delete_user&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger btn-sm-custom" onclick="return confirm('Delete user?')">Delete</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="products" class="tab-content" style="display: none;">
                    <h2 class="mb-4">Products Management</h2>
                    <a href="admin_add_product.php" class="btn btn-primary mb-3"><i class="bi bi-plus-circle me-2"></i>Add New Product</a>
                    <div class="table-container">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Color</th> <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td><?php echo htmlspecialchars($product['title']); ?></td>
                                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                                    <td>
                                        <?php 
                                        $stock = intval($product['stock']); 
                                        if($stock == 0) echo '<span class="badge bg-danger">0</span>';
                                        else echo '<span class="badge bg-success">' . $stock . '</span>';
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['color']); ?></td>
                                    <td>
                                        <a href="admin_edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-warning btn-sm-custom">Edit</a>
                                        <a href="admin_api.php?action=delete_product&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger btn-sm-custom" onclick="return confirm('Delete product?')">Delete</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab') || 'dashboard';
        showTab(tab);

        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.style.display = 'none');
            const target = document.getElementById(tabName);
            if(target) target.style.display = 'block';
            
            document.querySelectorAll('.sidebar a').forEach(a => a.classList.remove('active'));
            const activeLink = document.querySelector(`.sidebar a[href="#${tabName}"]`);
            if(activeLink) activeLink.classList.add('active');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>