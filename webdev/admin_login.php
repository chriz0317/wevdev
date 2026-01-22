<?php
// admin_login.php - Admin login with role verification
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if this is a login or signup request
    $isSignup = isset($_POST['signup']);
    
    if ($isSignup) {
        // Handle admin signup
        $name = trim($_POST['name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';
        $contact = trim($_POST['contact'] ?? '');

        $errors = [];
        if (!$name) $errors[] = 'Name is required';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required';
        if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters';
        if ($password !== $password2) $errors[] = 'Passwords do not match';

        if (!$errors) {
            try {
                $pdo = require __DIR__ . '/db.php';
                
                // Check if email exists
                $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $errors[] = 'Email already registered';
                } else {
                    // Create account (note: new signups get 'user' role, admins must be promoted)
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare('INSERT INTO users (name, email, password, contact, role) VALUES (?, ?, ?, ?, ?)');
                    $stmt->execute([$name, $email, $hash, $contact, 'user']);
                    $_SESSION['info'] = 'Account created! Please ask an admin to promote your account to admin access.';
                    header('Location: admin_login.php');
                    exit;
                }
            } catch (Exception $e) {
                $errors[] = 'Server error: ' . $e->getMessage();
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['signup_errors'] = $errors;
        }
    } else {
        // Handle admin login
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            $_SESSION['login_error'] = 'Email and password are required.';
        } else {
            try {
                $pdo = require __DIR__ . '/db.php';
                $stmt = $pdo->prepare('SELECT id, name, email, password, role, is_active FROM users WHERE email = ? LIMIT 1');
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if (!$user) {
                    $_SESSION['login_error'] = 'Invalid credentials';
                } elseif (!$user['is_active']) {
                    $_SESSION['login_error'] = 'Account disabled';
                } elseif ($user['role'] !== 'admin') {
                    $_SESSION['login_error'] = 'Admin access required';
                } elseif (!password_verify($password, $user['password'])) {
                    $_SESSION['login_error'] = 'Invalid credentials';
                } else {
                    // Success: set admin session
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_email'] = $user['email'];
                    $_SESSION['admin_name'] = $user['name'];
                    $_SESSION['admin_logged_in'] = true;

                    // Update last_login
                    try {
                        $u = $pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = ?');
                        $u->execute([$user['id']]);
                    } catch (Exception $e) { /* ignore */ }

                    header('Location: admin_dashboard.php');
                    exit;
                }
            } catch (Exception $e) {
                $_SESSION['login_error'] = 'Server error';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login — FurniEshop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            border: none;
            border-radius: 12px;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 10px 30px;
            font-weight: 600;
        }
        .btn-login:hover {
            opacity: 0.9;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card login-card p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold"><i class="bi bi-shield-lock me-2"></i>Admin Panel</h2>
                        <p class="text-muted">FurniEshop Administration</p>
                    </div>

                    <ul class="nav nav-tabs" id="authTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login"
                                type="button" role="tab">Login</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="signup-tab" data-bs-toggle="tab" data-bs-target="#signup"
                                type="button" role="tab">Create Account</button>
                        </li>
                    </ul>

                    <div class="tab-content mt-3">
                        <!-- Login Tab -->
                        <div class="tab-pane fade show active" id="login" role="tabpanel">
                            <?php if (isset($_SESSION['login_error'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo htmlspecialchars($_SESSION['login_error']); unset($_SESSION['login_error']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['info'])): ?>
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    <?php echo htmlspecialchars($_SESSION['info']); unset($_SESSION['info']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <form method="post" action="admin_login.php" novalidate>
                                <div class="mb-3">
                                    <label for="loginEmail" class="form-label">Email</label>
                                    <input type="email" name="email" id="loginEmail" class="form-control" placeholder="admin@example.com" required>
                                </div>
                                <div class="mb-4">
                                    <label for="loginPassword" class="form-label">Password</label>
                                    <input type="password" name="password" id="loginPassword" class="form-control" placeholder="Password" required>
                                </div>
                                <button type="submit" class="btn btn-login text-white w-100 mb-3">Login to Dashboard</button>
                                <a href="index.php" class="btn btn-outline-secondary w-100">Back to Home</a>
                            </form>
                        </div>

                        <!-- Signup Tab -->
                        <div class="tab-pane fade" id="signup" role="tabpanel">
                            <?php if (isset($_SESSION['signup_errors'])): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php 
                                        foreach ($_SESSION['signup_errors'] as $error) {
                                            echo '<li>' . htmlspecialchars($error) . '</li>';
                                        }
                                        unset($_SESSION['signup_errors']); 
                                        ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <form method="post" action="admin_login.php" novalidate>
                                <input type="hidden" name="signup" value="1">
                                
                                <div class="mb-3">
                                    <label for="signupName" class="form-label">Full Name</label>
                                    <input type="text" name="name" id="signupName" class="form-control" placeholder="Your name" required>
                                </div>

                                <div class="mb-3">
                                    <label for="signupEmail" class="form-label">Email</label>
                                    <input type="email" name="email" id="signupEmail" class="form-control" placeholder="your@example.com" required>
                                </div>

                                <div class="mb-3">
                                    <label for="signupContact" class="form-label">Contact (Phone)</label>
                                    <input type="tel" name="contact" id="signupContact" class="form-control" placeholder="+1234567890">
                                </div>

                                <div class="mb-3">
                                    <label for="signupPassword" class="form-label">Password</label>
                                    <input type="password" name="password" id="signupPassword" class="form-control" placeholder="Min 6 characters" required>
                                </div>

                                <div class="mb-4">
                                    <label for="signupPassword2" class="form-label">Confirm Password</label>
                                    <input type="password" name="password2" id="signupPassword2" class="form-control" placeholder="Repeat password" required>
                                </div>

                                <div class="alert alert-info mb-3">
                                    <small><i class="bi bi-info-circle me-2"></i>Your account will be created as a regular user. An admin will need to promote it to admin access.</small>
                                </div>

                                <button type="submit" class="btn btn-login text-white w-100 mb-3">Create Account</button>
                                <a href="index.php" class="btn btn-outline-secondary w-100">Back to Home</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
