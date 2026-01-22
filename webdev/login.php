<?php
// login.php - serves the login/signup page on GET and handles JSON POST requests from fetch()
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isJson = strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false;
    if ($isJson) {
        header('Content-Type: application/json; charset=utf-8');
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true) ?: [];
        $email = strtolower(trim($data['email'] ?? ''));
        $password = $data['password'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            http_response_code(422);
            echo json_encode(['error' => 'Email and password are required']);
            exit;
        }

        try {
            $pdo = require __DIR__ . '/db.php';
            $stmt = $pdo->prepare('SELECT id, name, email, password, contact, is_active FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if (!$user) {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid credentials']);
                exit;
            }
            if (isset($user['is_active']) && !$user['is_active']) {
                http_response_code(403);
                echo json_encode(['error' => 'Account disabled']);
                exit;
            }
            if (!password_verify($password, $user['password'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid credentials']);
                exit;
            }
            // success: set session and return public user info
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];

            echo json_encode(['user' => ['id' => $user['id'], 'email' => $user['email'], 'name' => $user['name'], 'contact' => $user['contact']]]);
            exit;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Server error']);
            exit;
        }
    }

    // Handle non-JSON (regular form) POST: authenticate and optionally resume add-to-cart action
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        // simple fallback: set a flash via session and let the page render with message
        $_SESSION['login_error'] = 'Email and password are required.';
        // fall through to render page with message
    } else {
        try {
            $pdo = require __DIR__ . '/db.php';
            $stmt = $pdo->prepare('SELECT id, name, email, password, contact, is_active FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if (!$user || (isset($user['is_active']) && !$user['is_active']) || !password_verify($password, $user['password'])) {
                $_SESSION['login_error'] = 'Invalid credentials';
            } else {
                // success: set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['name'];

                // update last_login
                try {
                    $u = $pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = ?');
                    $u->execute([$user['id']]);
                } catch (Exception $e) { /* ignore */ }

                // If an add-to-cart was requested via query parameters, perform it now
                if (isset($_GET['add_product'])) {
                    $product_id = intval($_GET['add_product']);
                    $quantity = isset($_GET['add_qty']) ? intval($_GET['add_qty']) : 1;
                    if ($product_id > 0 && $quantity > 0) {
                        // check existing
                        $s = $pdo->prepare('SELECT quantity FROM cart_items WHERE user_id = ? AND product_id = ?');
                        $s->execute([$user['id'], $product_id]);
                        $existing = $s->fetch();
                        if ($existing) {
                            $u2 = $pdo->prepare('UPDATE cart_items SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?');
                            $u2->execute([$quantity, $user['id'], $product_id]);
                        } else {
                            $i = $pdo->prepare('INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)');
                            $i->execute([$user['id'], $product_id, $quantity]);
                        }
                        // Optionally set a flash informing user the item was added
                        $_SESSION['info'] = 'Item added to cart after login.';
                    }
                }

                // Redirect back to the original page if provided, else to Cart.php
                if (!empty($_GET['redirect'])) {
                    $redir = $_GET['redirect'];
                    // avoid open redirect by allowing only local paths
                    $u = parse_url($redir);
                    if (isset($u['host']) && $u['host'] !== ($_SERVER['HTTP_HOST'] ?? '')) {
                        header('Location: Cart.php');
                        exit;
                    }
                    header('Location: ' . $redir);
                    exit;
                }

                header('Location: Cart.php');
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['login_error'] = 'Server error';
        }
    }
    // if we get here, authentication failed or incomplete — let the page render with flash messages
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Clothery — Login / Sign up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <style>
        /* Keep only page-specific card shadow; base colors come from styles.css */
        .card { border-radius: 12px; box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06); }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4">
                    <h3 class="mb-3 text-center">Welcome to Clothery</h3>

                    <ul class="nav nav-tabs" id="authTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login"
                                type="button" role="tab">Login</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="signup-tab" data-bs-toggle="tab" data-bs-target="#signup"
                                type="button" role="tab">Sign Up</button>
                        </li>
                    </ul>

                    <div class="tab-content mt-3">
                        <div class="tab-pane fade show active" id="login" role="tabpanel">
                            <form id="loginForm" action="login.php" method="post" novalidate>
                                <div class="mb-3">
                                    <label for="loginEmail" class="form-label">Email</label>
                                    <input type="text" name="email" id="loginEmail" class="form-control"
                                        placeholder="you@example.com">
                                    <div class="invalid-feedback">Please enter your email</div>
                                </div>
                                <div class="mb-3">
                                    <label for="loginPassword" class="form-label">Password</label>
                                    <input type="password" name="password" id="loginPassword" class="form-control"
                                        placeholder="Password">
                                    <div class="invalid-feedback">Please enter your password</div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <button class="btn btn-primary" type="submit">Login</button>
                                    <a href="index.php" class="btn btn-link">Back to Home</a>
                                </div>
                                <div id="loginMsg" class="mt-2"></div>
                                <input type="hidden" name="csrf_token" id="login_csrf">
                            </form>
                        </div>

                        <div class="tab-pane fade" id="signup" role="tabpanel">
                            <form id="signupForm" action="register.php" method="post" novalidate>
                                <div class="mb-3">
                                    <label for="signupName" class="form-label">Full name</label>
                                    <input type="text" name="name" id="signupName" class="form-control" placeholder="Your name">
                                    <div class="invalid-feedback">Please enter your name</div>
                                </div>
                                <div class="mb-3">
                                    <label for="signupEmail" class="form-label">Email</label>
                                    <input type="text" name="email" id="signupEmail" class="form-control"
                                        placeholder="you@example.com">
                                    <div class="invalid-feedback">Please enter your email</div>
                                </div>
                                <div class="mb-3">
                                    <label for="signupContact" class="form-label">Contact (phone)</label>
                                    <input type="tel" name="contact" id="signupContact" class="form-control"
                                        placeholder="e.g. +1234567890">
                                    <div class="invalid-feedback">Please enter your contact number</div>
                                </div>
                                <div class="mb-3">
                                    <label for="signupPassword" class="form-label">Password</label>
                                    <input type="password" name="password" id="signupPassword" class="form-control"
                                        placeholder="Choose a password">
                                    <div class="invalid-feedback">Please enter a password</div>
                                </div>
                                <div class="mb-3">
                                    <label for="signupPassword2" class="form-label">Confirm Password</label>
                                    <input type="password" name="password2" id="signupPassword2" class="form-control"
                                        placeholder="Repeat password">
                                    <div class="invalid-feedback">Please confirm your password</div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <button class="btn btn-outline-primary" type="submit">Create account</button>
                                    <a href="Cart.html" class="btn btn-link">Go to Cart</a>
                                </div>
                                <div id="signupMsg" class="mt-2"></div>
                                                                <input type="hidden" name="csrf_token" id="signup_csrf">
                            </form>
                        </div>

                                                <div class="col-12 mt-3">
                                                    <div id="flashMessages"></div>
                                                </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple jQuery-only validation (no persistence, no redirects).
        (function ($) {
            // Clear per-field validation states on input
            $('input').on('input', function () {
                $(this).removeClass('is-invalid is-valid');
            });
                // Clear per-field validation states on input and clear browser custom validity
                $('input').on('input', function(){
                    $(this).removeClass('is-invalid is-valid');
                    // clear any browser-level custom validity messages
                    try{ this.setCustomValidity(''); }catch(e){}
                });

                // Disable native required constraints at runtime (some browsers still show bubbles despite novalidate)
                $('form').each(function(){
                    try{ this.noValidate = true; }catch(e){}
                    // remove required attributes so browser won't pop native messages
                    $(this).find('[required]').prop('required', false);
                });

                // Prevent browser-native validation bubbles by catching invalid events in capture phase on the document
                // This ensures any native bubble is suppressed and our custom UI is used.
                document.addEventListener('invalid', function(ev){ ev.preventDefault(); }, true);

            function markValid($el) { $el.removeClass('is-invalid').addClass('is-valid'); }
            function markInvalid($el, message) { $el.removeClass('is-valid').addClass('is-invalid'); if (message) $el.next('.invalid-feedback').text(message); }

            // Login validation (per-field)
            $('#loginForm').on('submit', function (e) {
                e.preventDefault();
                let ok = true;
                const $email = $('#loginEmail');
                const $pass = $('#loginPassword');

                const email = $email.val().trim();
                const passv = $pass.val();

                if (!email) { markInvalid($email, 'Please enter your email'); ok = false; } else if (!(email.indexOf('@') !== -1 && email.toLowerCase().includes('.com'))) { markInvalid($email, 'Please enter a valid email'); ok = false; } else { markValid($email); }

                if (!passv) { markInvalid($pass, 'Please enter your password'); ok = false; } else if (passv.length < 4) { markInvalid($pass, 'Password must be at least 4 characters'); ok = false; } else { markValid($pass); }

                if (ok) {
                    // All fields valid — actually submit the form
                    this.submit();
                } else {
                    $('#loginMsg').removeClass().text('');
                }
            });

            // Signup validation (per-field)
            $('#signupForm').on('submit', function (e) {
                e.preventDefault();
                let ok = true;
                const $name = $('#signupName');
                const $email = $('#signupEmail');
                const $contact = $('#signupContact');
                const $p1 = $('#signupPassword');
                const $p2 = $('#signupPassword2');

                const name = $name.val().trim();
                const email = $email.val().trim();
                const contact = $contact.val().trim();
                const p1 = $p1.val();
                const p2 = $p2.val();

                if (!name) { markInvalid($name, 'Please enter your name'); ok = false; } else { markValid($name); }

                if (!email) { markInvalid($email, 'Please enter your email'); ok = false; } else if (!(email.indexOf('@') !== -1 && email.toLowerCase().includes('.com'))) { markInvalid($email, 'Please include an "@" and ".com" in the email.'); ok = false; } else { markValid($email); }

                if (!contact) { markInvalid($contact, 'Please enter a contact number'); ok = false; } else { const phoneRe = /^[0-9+\-()\s]+$/; if (!phoneRe.test(contact)) { markInvalid($contact, 'Please enter a valid contact number'); ok = false; } else { markValid($contact); } }

                if (!p1) { markInvalid($p1, 'Please enter a password'); ok = false; } else if (p1.length < 6) { markInvalid($p1, 'Password must be at least 6 characters'); ok = false; } else { markValid($p1); }

                if (!p2) { markInvalid($p2, 'Please confirm your password'); ok = false; } else if (p1 !== p2) { markInvalid($p2, 'Passwords do not match'); ok = false; } else { markValid($p2); }

                if (ok) {
                    $('#signupMsg').removeClass().addClass('text-success mt-2').text('Submitting...');
                    // submit the form now that client-side validation passed
                    this.submit();
                } else {
                    $('#signupMsg').removeClass().text('');
                }
            });

        })(jQuery);
    </script>
        <script>
        // Fetch CSRF token and flash messages to populate forms and show server messages
        (function(){
            function showFlash(obj){
                const container = document.getElementById('flashMessages');
                if (!container) return;
                container.innerHTML = '';
                if (obj.reg_errors) {
                    const ul = document.createElement('ul'); ul.className = 'alert alert-danger';
                    (Array.isArray(obj.reg_errors)?obj.reg_errors:[obj.reg_errors]).forEach(m=>{ const li = document.createElement('li'); li.textContent = m; ul.appendChild(li); });
                    container.appendChild(ul);
                }
                if (obj.login_error) {
                    const d = document.createElement('div'); d.className='alert alert-danger'; d.textContent = obj.login_error; container.appendChild(d);
                }
                if (obj.info) {
                    const d = document.createElement('div'); d.className='alert alert-info'; d.textContent = obj.info; container.appendChild(d);
                }
            }

            fetch('csrf_token.php').then(r=>r.json()).then(data=>{
                if (data.csrf_token){
                    const l = document.getElementById('login_csrf'); if (l) l.value = data.csrf_token;
                    const s = document.getElementById('signup_csrf'); if (s) s.value = data.csrf_token;
                }
            }).catch(()=>{});

            fetch('flash.php').then(r=>r.json()).then(showFlash).catch(()=>{});
        })();
        </script>
</body>

</html>