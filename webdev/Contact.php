<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FurniEshop - Contact</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="styles.css" rel="stylesheet">
  <style>
    /* Validation styles: make errors clearly red */
    label.error {
      color: #e53935; /* red */
      font-size: 0.95rem;
      margin-top: 0.25rem;
      display: block;
    }
    input.error, textarea.error, select.error {
      border-color: #e53935 !important;
      box-shadow: 0 0 0 0.2rem rgba(229,57,53,0.12) !important;
    }
    input.valid, textarea.valid { border-color: #28a745; }
    .form-control.error::placeholder { color: #e57373; }
  </style>
</head>
<body>
  <div class="w-100 bg-dark text-white small py-1">
    <div class="container">CONTACT</div>
  </div>
  <?php include 'navbar.php'; ?>
    </div>
  </nav>
  <header class="text-center text-white py-5 bg-primary" style="background:url('https://picsum.photos/1200/400?contact,shop') center/cover no-repeat;">
    <div class="container">
      <h1 class="display-4">Contact Us</h1>
      <p class="lead">We're here to help! Reach out with any questions or feedback.</p>
    </div>
  </header>
  <section class="py-5 bg-light">
    <div class="container">
      <div class="row g-5">
        <div class="col-md-6">
          <h2>Get in Touch</h2>
          <form class="eme" id="contactForm" method="get">
            <div class="mb-3">
              <label for="name" class="form-label">Name</label>
              <input type="text" class="form-control" id="name" name="name" placeholder="Your Name" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" required >
            </div>
            <div class="mb-3">
              <label for="message" class="form-label">Message</label>
              <textarea class="form-control" id="message" name="message" rows="5" placeholder="How can we help you?" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Message</button>
          </form>

          <!-- jQuery and jQuery Validate -->
          <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
          <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
          <script>
            // Custom validation rules
            $.validator.addMethod('emailCustom', function(value, element) {
              return this.optional(element) || /.+@.+\.com$/i.test(value);
            }, 'Please enter a valid email that includes "@" and ends with ".com".');

            $.validator.addMethod('lettersOnly', function(value, element) {
              return this.optional(element) || /^[A-Za-z\s]+$/.test(value);
            }, 'Please enter a valid name.');

            // limit number of space characters in name to at most 3
            $.validator.addMethod('maxSpaces', function(value, element) {
              if (this.optional(element)) return true;
              const spaces = (value.match(/\s/g) || []).length;
              return spaces <= 3;
            }, 'Please enter a valid name.');

            $.validator.addMethod('messageValid', function(value, element) {
              return this.optional(element) || /[A-Za-z]/.test(value) && value.trim().length >= 10;
            }, 'Please enter a message (at least 10 words).');

            // Initialize validation
            $(function(){
              $('#contactForm').validate({
                rules: {
                  name: { required: true, lettersOnly: true, minlength: 5, maxSpaces: true },
                  email: { required: true, emailCustom: true },
                  message: { required: true, messageValid: true }
                },
                messages: {
                  name: { required: 'Please enter your name', minlength: 'Please enter a valid name', maxSpaces: 'Please use a valid name' },
                  email: { required: 'Please enter your email', emailCustom: 'Enter a valid email with @ and .com' },
                  message: { required: 'Please enter a message', messageValid: 'Please enter a message (at least 10 words)'}
                },
                submitHandler: function(form) {
                  // Demo: show confirmation and reset form
                  alert('Message sent (demo). Thank you!');
                  form.reset();
                  return false;
                }
              });
            });
          </script>
        </div>
        <div class="col-md-6">
          <h2>Contact Information</h2>
          <ul class="list-unstyled mb-4">
            <li class="mb-2"><strong>Email:</strong> support@eshop.com</li>
            <li class="mb-2"><strong>Phone:</strong> +1 (555) 123-4567</li>
            <li class="mb-2"><strong>Address:</strong> 123 E-Shop Ave, Commerce City, USA</li>
          </ul>
          <div class="ratio ratio-16x9 rounded shadow">
            <iframe src="https://www.openstreetmap.org/export/embed.html?bbox=-104.995,39.764,-104.985,39.774&amp;layer=mapnik" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="script.js"></script>
</body>
</html>
