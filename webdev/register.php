<?php
// register.php - register a new user
session_start();

// 1. Check Method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// 2. Check content type
$isJson = strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false;

// 3. Connect to Database
try {
    $pdo = require __DIR__ . '/db.php';
} catch (Exception $e) {
    if ($isJson) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }
    exit('Database connection failed');
}

// 4. Get Data
if ($isJson) {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true) ?: [];
    $name = trim($data['name'] ?? '');
    $email = strtolower(trim($data['email'] ?? ''));
    $password = $data['password'] ?? '';
    $contact = trim($data['contact'] ?? '');
} else {
    // Form fallback
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $contact = trim($_POST['contact'] ?? '');
}

// 5. Validate
$errors = [];
if (!$name) $errors[] = 'Name required';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required';
if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters';

if ($errors) {
    if ($isJson) {
        http_response_code(422);
        header('Content-Type: application/json');
        echo json_encode(['error' => implode('; ', $errors)]);
        exit;
    }
    exit(implode('; ', $errors));
}

// 6. Register User
try {
    // Check if email exists
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        if ($isJson) {
            http_response_code(409);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Email already registered']);
            exit;
        }
        exit('Email already registered');
    }

    // Insert user
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password, contact, role) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$name, $email, $hash, $contact, 'user']);

    $userId = $pdo->lastInsertId();
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = $name;

    if ($isJson) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    header('Location: Cart.php');
    exit;

} catch (Exception $e) {
    if ($isJson) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
        exit;
    }
    exit('Server error');
}