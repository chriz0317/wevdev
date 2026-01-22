<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FurniEshop - Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="styles.css" rel="stylesheet">
</head>
<body>
  <div class="w-100 bg-dark text-white small py-1">
    <div class="container">CART</div>
  </div>
  <?php include 'navbar.php'; ?>
  <?php $isUserLoggedIn = isset($_SESSION['user_id']); ?>
  <header class="text-center text-white py-5 bg-success" style="background:url('https://picsum.photos/1200/400?cart') center/cover no-repeat;">
    <div class="container">
      <h1 class="display-4">Your Shopping Cart</h1>
      <p class="lead">Review your selected items and proceed to checkout</p>
    </div>
  </header>
  <section class="py-5 bg-light">
    <div class="container">
     <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Cart Items</h2>
    <button id="toggleEditBtn" class="btn btn-outline-primary btn-sm">Edit</button>
     </div>
      <div class="table-responsive">
        <table class="table align-middle table-bordered bg-white">
          <thead class="table-dark">
  <tr>
    <th scope="col" class="check-col" style="width: 50px;">
        <input type="checkbox" id="selectAll" class="form-check-input">
    </th>
    <th scope="col">Product</th>
    <th scope="col">Name</th>
    <th scope="col">Price</th>
    <th scope="col">Quantity</th>
    <th scope="col">Total</th>
    <th scope="col">Remove</th>
  </tr>
</thead>
          <tbody id="cartBody">
            <!-- Cart rows will be rendered here by script.js -->
          </tbody>
        </table>
      </div>
      <div class="row justify-content-end">
        <div class="col-md-4">
      <div class="card p-3 shadow-sm">
    <h4 class="mb-3">Order Summary</h4>
    <button class="btn btn-success w-100" id="checkoutBtn">Proceed to Checkout</button>
    
    <button class="btn btn-danger w-100 mt-0" id="bulkDeleteBtn" style="display: none;">
        Remove Selected
    </button>
</div>
        </div>
      </div>
    </div>
  </section>
  <footer class="bg-light py-4 border-top text-center small text-muted">
    <div class="container text-center text-muted small">
      © 2025 FurniEshop — Built with your home with amazing furnitures.
    </div>
  </footer>

  <!-- jQuery login modal (custom) -->
  <div id="loginModalOverlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1050; align-items:center; justify-content:center;">
    <div id="loginModal" role="dialog" aria-modal="true" style="width:100%; max-width:500px; margin:16px; background:#ffffff; border-radius:12px; padding:48px; box-shadow:0 25px 50px rgba(0,0,0,0.15);">
      <h2 class="mb-4" style="font-size:1.6rem; font-weight:600; color:#333;">Log in to your account</h2>
      <form id="loginForm" novalidate>
        <div class="mb-4">
          <label for="loginEmail" class="form-label" style="font-weight:500; color:#333; margin-bottom:0.75rem; display:block;">Email address</label>
          <input type="text" class="form-control" id="loginEmail" placeholder="you@example.com" style="padding:0.75rem 1rem; border-radius:6px; border:2px solid #e0e0e0; font-size:1rem;">
          <div class="invalid-feedback">Please include an '@' and '.com' in the email.</div>
        </div>
        <div class="mb-4">
          <label for="loginPassword" class="form-label" style="font-weight:500; color:#333; margin-bottom:0.75rem; display:block;">Password</label>
          <input type="password" class="form-control" id="loginPassword" placeholder="Password" style="padding:0.75rem 1rem; border-radius:6px; border:2px solid #e0e0e0; font-size:1rem;">
          <div class="invalid-feedback">Please enter your password</div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4">
          <button type="submit" class="btn" style="background:#c9a876; color:#fff; padding:0.75rem 2rem; border:none; border-radius:6px; font-weight:600; cursor:pointer; font-size:1rem;">Login</button>
          <a href="#" class="text-muted" id="cancelLogin" style="text-decoration:underline; color:#888;">Cancel</a>
        </div>
        <div id="loginError" class="text-danger mt-2" style="display:none;"></div>
        <div style="text-align:center; font-size:0.95rem; color:#666;">Don't have an account? <a href="#" id="openSignup" style="color:#0066cc; text-decoration:none; font-weight:500;">Sign Up</a></div>
      </form>
    </div>
  </div>
  <!-- Signup modal -->
  <div id="signupModalOverlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1050; align-items:center; justify-content:center;">
    <div id="signupModal" role="dialog" aria-modal="true" style="width:100%; max-width:520px; margin:16px; background:#fff; border-radius:8px; padding:22px; box-shadow:0 20px 40px rgba(0,0,0,0.2);">
      <h5 class="mb-3">Create your account</h5>
  <form id="signupForm" novalidate>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label" for="signupName">Full name</label>
            <input id="signupName" class="form-control" />
            <div class="invalid-feedback">Please enter your name</div>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label" for="signupEmail">Email</label>
            <input id="signupEmail" type="text" class="form-control" />
            <div class="invalid-feedback">Please include an '@' and '.com' in the email.</div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label" for="signupContact">Contact (phone)</label>
            <input id="signupContact" type="tel" class="form-control" placeholder="e.g. +1234567890" />
            <div class="invalid-feedback">Please enter a valid contact number</div>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label" for="signupPassword">Password</label>
          <input id="signupPassword" type="password" class="form-control" />
          <div class="invalid-feedback">Password must be at least 6 characters</div>
        </div>
        <div class="mb-3">
          <label class="form-label" for="signupPassword2">Confirm password</label>
          <input id="signupPassword2" type="password" class="form-control" />
          <div class="invalid-feedback">Passwords do not match</div>
        </div>
        <div class="d-flex justify-content-between align-items-center">
          <button class="btn btn-primary" type="submit">Sign Up</button>
          <button type="button" class="btn btn-link text-muted" id="cancelSignup">Cancel</button>
        </div>
        <div id="signupError" class="text-danger mt-2" style="display:none"></div>
      </form>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="script.js"></script>
  <script>
    // Simple client-side login using jQuery and localStorage (demo only — not secure)
    (function($){
      // Use server-side session flag to determine login state
      const SERVER_LOGGED_IN = <?php echo $isUserLoggedIn ? 'true' : 'false'; ?>;
      function getLoggedIn(){
        return SERVER_LOGGED_IN ? true : null;
      }
      function getUserInfo(){
        // If more user info is needed, add an endpoint or render into page similarly
        return null;
      }

      function updateNav(){
        const user = getLoggedIn();
        const $nav = $('#loginNav');
        $nav.off('click');
        if (user){
          $nav.text('Logout');
          $nav.on('click', function(e){ e.preventDefault(); alert('Session ended'); window.location.href = 'Cart.php'; });
        } else {
          $nav.text('Login');
          $nav.on('click', function(e){ e.preventDefault(); openLogin(); });
        }
      }

      function openLogin(){
        $('#loginError').hide();
        $('#loginForm')[0].reset();
        $('#loginModalOverlay').fadeIn(120).css('display','flex');
        $('#loginEmail').focus();
        // push a history entry so the URL reflects the modal state (no page reload)
        try{ if (!history.state || history.state.modal !== 'login') history.pushState({modal: 'login'}, '', '?modal=login'); }catch(e){}
      }
      function closeLogin(){ $('#loginModalOverlay').fadeOut(120); }

      function openSignup(){
        $('#signupError').hide();
        $('#signupForm')[0].reset();
        $('#loginModalOverlay').hide();
        $('#signupModalOverlay').fadeIn(140).css('display','flex');
        $('#signupName').focus();
      }
      function closeSignup(){ $('#signupModalOverlay').fadeOut(120); }

      // Wire sign up link
      $(document).on('click', '#openSignup', function(e){ e.preventDefault(); openSignup(); });

      // Signup handlers
      $('#cancelSignup').on('click', function(){ closeSignup(); });
      $('#signupForm').on('submit', function(e){
        e.preventDefault();
        const $name = $('#signupName');
        const $email = $('#signupEmail');
        const $p1 = $('#signupPassword');
        const $p2 = $('#signupPassword2');
        const $contact = $('#signupContact');
        const $error = $('#signupError');
        const name = ($name.val() || '').trim();
        const email = ($email.val() || '').trim().toLowerCase();
        const p1 = ($p1.val() || '');
        const p2 = ($p2.val() || '');
        const contact = ($contact.val() || '').trim();
        let ok = true;

        function markValid($el){ $el.removeClass('is-invalid').addClass('is-valid'); }
        function markInvalid($el, msg){ $el.removeClass('is-valid').addClass('is-invalid'); if (msg) $el.next('.invalid-feedback').text(msg); }

        // Name rule: at least 2 words (>=1 space) and at most 2 dots
        const spaceCount = (name.match(/ /g) || []).length;
        const dotCount = (name.match(/\./g) || []).length;
        if (!name){ markInvalid($name, 'Please enter your name'); ok = false; }
        else if (spaceCount < 1){ markInvalid($name, 'Please enter at least two words for your name'); ok = false; }
        else if (dotCount > 2){ markInvalid($name, 'Name may contain at most 2 dots'); ok = false; }
        else { markValid($name); }

        if (!email){ markInvalid($email, 'Please enter your email'); ok = false; }
        else if (!(email.indexOf('@') !== -1 && email.toLowerCase().includes('.com'))){ markInvalid($email, 'Please enter a valid email.'); ok = false; }
        else { markValid($email); }

        if (!contact){ markInvalid($contact, 'Please enter a contact number'); ok = false; }
        else { const phoneRe = /^[0-9+\-()\s]+$/; if (!phoneRe.test(contact)){ markInvalid($contact, 'Please enter a valid contact number'); ok = false; } else { markValid($contact); } }

        if (!p1){ markInvalid($p1, 'Please enter a password'); ok = false; } else if (p1.length < 4){ markInvalid($p1, 'Password must be at least 4 characters'); ok = false; } else { markValid($p1); }

        if (!p2){ markInvalid($p2, 'Please confirm your password'); ok = false; } else if (p1 !== p2){ markInvalid($p2, 'Passwords do not match'); ok = false; } else { markValid($p2); }

        if (!ok) return;

        // Get CSRF token then send registration request
        fetch('csrf_token.php')
        .then(r => r.json())
        .then(tokenData => {
          return fetch('register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              name: name,
              email: email,
              password: p1,
              contact: contact,
              csrf_token: tokenData.csrf_token
            })
          });
        })
        .then(r => r.json())
        .then(data => {
          if (data.error) {
            $error.text(data.error).show();
            return;
          }
          closeSignup();
          alert('Registration successful! Please log in.');
          openLogin();
        })
        .catch(err => {
          $error.text('Registration failed. Please try again.').show();
        });
      });

      // Close when clicking outside modal content
      $('#loginModalOverlay').on('click', function(e){ if (e.target && e.target.id === 'loginModalOverlay') closeLogin(); });

      $('#cancelLogin').on('click', function(){
        e.preventDefault();
        // close modal and try to go back if we pushed a state
        $('#loginModalOverlay').fadeOut(120, function(){
          $('#loginModalOverlay').css('display', 'none');
        });
        try{
          if (history.state && history.state.modal === 'login') history.back();
        }catch(e){}
      });

      $('#loginForm').on('submit', function(e){
        e.preventDefault();
        const $email = $('#loginEmail');
        const $pass = $('#loginPassword');
        const $error = $('#loginError');
        const email = ($email.val() || '').trim();
        const pass = ($pass.val() || '');
        let ok = true;

        function markValid($el){ $el.removeClass('is-invalid').addClass('is-valid'); }
        function markInvalid($el, msg){ $el.removeClass('is-valid').addClass('is-invalid'); if (msg) $el.next('.invalid-feedback').text(msg); }

        if (!email){ markInvalid($email, 'Please enter your email'); ok = false; }
        else if (!(email.indexOf('@') !== -1 && email.toLowerCase().includes('.com'))){ markInvalid($email, 'Please enter a valid email.'); ok = false; }
        else { markValid($email); }

        if (!pass){ markInvalid($pass, 'Please enter your password'); ok = false; }
        else if (pass.length < 4){ markInvalid($pass, 'Password must be at least 4 characters'); ok = false; }
        else { markValid($pass); }

        if (!ok) return;

        // Send login request
        fetch('login.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            email: email,
            password: pass
          })
        })
        .then(r => r.json())
        .then(data => {
          if (data.error) {
            $error.text(data.error).show();
            return;
          }
          // Login successful
          closeLogin();
          // If a pending action was stored (by script.js), resume it now.
          try{
            const pendingRaw = sessionStorage.getItem('pending_action_after_login');
            const pending = pendingRaw ? JSON.parse(pendingRaw) : null;
            if (pending && pending.add_product) {
              // attempt to POST the add-to-cart on behalf of the just-logged-in user
              try{
                fetch('cart_api.php', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/json' },
                  body: JSON.stringify({ product_id: pending.add_product, quantity: pending.add_qty || 1 })
                }).catch(()=>{});
              }catch(e){}
            }
            // clear pending action
            try{ sessionStorage.removeItem('pending_action_after_login'); }catch(e){}
            // redirect: prefer pending.redirect if present (it's a sanitized relative URL), else go to Cart.php with user params
            const params = new URLSearchParams();
            params.set('user', data.user.email);
            params.set('name', data.user.name);
            params.set('contact', data.user.contact || '');
            if (pending && pending.redirect) {
              // navigate to user's intended page (use the sanitized relative path)
              window.location.href = pending.redirect + (pending.redirect.indexOf('?') === -1 ? ('?' + params.toString()) : ('&' + params.toString()));
            } else {
              window.location.href = 'Cart.php?' + params.toString();
            }
            return;
          }catch(e){
            // fallback redirect
            const params = new URLSearchParams();
            params.set('user', data.user.email);
            params.set('name', data.user.name);
            params.set('contact', data.user.contact || '');
            window.location.href = 'Cart.php?' + params.toString();
            return;
          }
        })
        .catch(err => {
          $error.text('Login failed. Please try again.').show();
        });
      });

      // Checkout button requires login
      $('#checkoutBtn').on('click', function(e){
        const logged = getLoggedIn();
        if (!logged){
          e.preventDefault();
          openLogin();
        } else {
          // proceed to server-side checkout page
          window.location.href = 'checkout.php';
        }
      });

  // Initialize on load
  $(function(){ 
    updateNav();
    loadCartItems(); // Load cart items from database
    
    // Remove required attributes at runtime to avoid native browser tooltips
    $('form').each(function(){
      try{ this.noValidate = true; }catch(e){}
      $(this).find('[required]').prop('required', false);
    });        // Clear any browser custom validity when user types
        $('input').on('input', function(){ try{ this.setCustomValidity(''); }catch(e){} });

        // Prevent native validation bubbles globally
        document.addEventListener('invalid', function(ev){ ev.preventDefault(); }, true);

        // If URL contains ?modal=login, open the modal on load
        try{
          const params = new URLSearchParams(window.location.search);
          if (params.get('modal') === 'login'){
            $('#loginModalOverlay').show().css('display','flex');
            $('#loginEmail').focus();
            try{ history.replaceState({modal:'login'}, '', window.location.href); }catch(e){}
          }
        }catch(e){}
      });

      // Popstate: open/close modal based on history state
      window.addEventListener('popstate', function(e){
        try{
          if (e.state && e.state.modal === 'login'){
            $('#loginModalOverlay').fadeIn(120).css('display','flex');
            $('#loginEmail').focus();
          } else {
            $('#loginModalOverlay').fadeOut(120);
          }
        }catch(err){}
      });
    })(jQuery);
      // --- Bulk Edit Logic ---
$(document).ready(function() {
    let isEditing = false;

    // 1. Toggle Edit Mode
    $('#toggleEditBtn').click(function() {
        isEditing = !isEditing;
        
        // Toggle CSS class on the table
        $('table').toggleClass('edit-mode');
        
        // Toggle Button Text
        $(this).text(isEditing ? 'Cancel' : 'Edit');
        $(this).toggleClass('btn-outline-primary btn-outline-secondary');

        // Toggle Checkout vs Remove Buttons
        if (isEditing) {
            $('#checkoutBtn').hide();
            $('#bulkDeleteBtn').show();
        } else {
            $('#checkoutBtn').show();
            $('#bulkDeleteBtn').hide();
            // Uncheck everything if canceling
            $('.cart-item-check').prop('checked', false);
            $('#selectAll').prop('checked', false);
        }
    });

    // 2. Handle "Select All"
    $('#selectAll').change(function() {
        const isChecked = $(this).is(':checked');
        $('.cart-item-check').prop('checked', isChecked);
    });

    // 3. Handle Bulk Delete Click
    $('#bulkDeleteBtn').click(function() {
        const selectedIds = [];
        
        // Loop through checked boxes to get IDs
        $('.cart-item-check:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            alert("Please select items to remove.");
            return;
        }

        if (confirm(`Are you sure you want to remove ${selectedIds.length} items?`)) {
            // CALL YOUR SERVER TO DELETE HERE
            console.log("Deleting IDs:", selectedIds);

            // Example AJAX call (You need to implement delete_cart_items.php)
            /*
            $.post('delete_cart_items.php', { ids: selectedIds }, function(response) {
                loadCartItems(); // Reload the table
                $('#toggleEditBtn').click(); // Exit edit mode
            });
            */
           
           // For now, let's just reload the page to simulate
           alert("Items removed (simulated). Connect to backend to finish.");
           $('#toggleEditBtn').click(); 
        }
    });
});
  </script>
</body>
</html>
