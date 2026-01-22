<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FurniEshop - About</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="styles.css" rel="stylesheet">
</head>
<body>
  <div class="w-100 bg-dark text-white small py-1">
    <div class="container">ABOUT</div>
  </div>
  <?php include 'navbar.php'; ?>
  <header class="text-center text-white py-5 bg-info" style="background:url('https://picsum.photos/1200/400?about,shop') center/cover no-repeat;">
    <div class="container">
      <h1 class="display-4">About E-Shop</h1>
      <p class="lead">Learn more about our story, mission, and values</p>
    </div>
  </header>
  <section class="py-5 bg-light">
    <div class="container">
      <div class="row align-items-center mb-5">
        <div class="col-md-6 mb-4 mb-md-0">
          <img src="https://picsum.photos/500/350?team,shop" class="img-fluid rounded shadow" alt="Our Team">
        </div>
        <div class="col-md-6">
          <h2>Who We Are</h2>
          <p>E-Shop is a passionate team of professionals dedicated to bringing you the best in modern products for your home and business. Since our founding in 2020, we've focused on quality, innovation, and customer satisfaction.</p>
          <ul class="list-unstyled">
            <li>✔️ Wide selection of premium products</li>
            <li>✔️ Fast and reliable shipping</li>
            <li>✔️ Outstanding customer support</li>
            <li>✔️ Secure online shopping experience</li>
          </ul>
        </div>
      </div>
      <div class="row align-items-center">
        <div class="col-md-6 order-md-2 mb-4 mb-md-0">
          <img src="https://picsum.photos/500/350?mission,shop" class="img-fluid rounded shadow" alt="Our Mission">
        </div>
        <div class="col-md-6 order-md-1">
          <h2>Our Mission</h2>
          <p>Our mission is to empower customers by providing access to high-quality, affordable products with a seamless online shopping experience. We believe in making life easier, more comfortable, and more enjoyable for everyone.</p>
        </div>
      </div>
    </div>
  </section>
  <section class="py-5">
    <div class="container">
      <h2 class="text-center mb-4">Meet the Team</h2>
      <div class="row g-4 justify-content-center">
        <div class="col-md-3">
          <div class="card border-0 shadow-sm text-center">
            <img src="481013827_612351388329781_106296429567780206_n.jpg" class="card-img-top rounded-circle mx-auto mt-3" style="width:120px; height:120px; object-fit:cover;" alt="Team Member">
            <div class="card-body">
              <h5 class="card-title mb-0">Josh Waine</h5>
              <p class="text-muted">Founder & CEO</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card border-0 shadow-sm text-center">
            <img src="544924597_2146357969108146_3651894294817465983_n.jpg" class="card-img-top rounded-circle mx-auto mt-3" style="width:120px; height:120px; object-fit:cover;" alt="Team Member">
            <div class="card-body">
              <h5 class="card-title mb-0">Anna Mae</h5>
              <p class="text-muted">Head of Marketing</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card border-0 shadow-sm text-center">
           <img src="anjo.jpg" class="card-img-top rounded-circle mx-auto mt-3" style="width:120px; height:120px; object-fit:cover;" alt="Team Member">
            <div class="card-body">
              <h5 class="card-title mb-0">Anjoe Marudo</h5>
              <p class="text-muted">Lead Developer</p>
            </div>
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
