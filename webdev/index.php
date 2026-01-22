<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Furnis — Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="styles.css" rel="stylesheet" />
</head>
<body>
  <div class="w-100 bg-dark text-white small py-1">
    <div class="container">HOME</div>
  </div>
  <?php include 'navbar.php'; ?>

  <!-- HERO: two-column with left text and right image (matches screenshot) -->
  <header class="py-5">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <h1 class="display-1 fw-bold" style="line-height:0.95;">ELEVATE<br>YOUR<br>SPACE<br>WITH OUR<br>FURNITURE</h1>
        </div>
        </div>
      </div>
    </div>
  </header>

  <main class="py-5">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Featured</h2>
        <small class="text-muted">Handpicked items for you</small>
      </div>

      <!-- Search and promo row -->
      <div class="row align-items-center mb-4">
        <div class="col-md-6">
          <div class="input-group">
            <span class="input-group-text bg-pale" style="background:#f1f1f1;border-radius:8px 0 0 8px;">🔍</span>
            <input id="searchInput" type="text" class="form-control" placeholder="search">
            <button id="searchBtn" class="btn btn-outline-secondary">Search</button>
          </div>
        </div>
        <div class="col-md-6 text-end">
          <h3 class="mb-0">shop now to elevate<br>your design</h3>
        </div>
      </div>

      <!-- Dynamic product grid -->
      <div id="productGrid" class="row g-4"></div>
      <hr class="my-5">
    </div>
      <div class="modal fade" id="productDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-0 pb-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-0">
        <div class="row">
          <div class="col-md-6 mb-3 mb-md-0">
            <img id="modalProductImage" src="" class="img-fluid rounded" alt="Product" style="width: 100%; object-fit: cover; max-height: 400px;">
          </div>
          
          <div class="col-md-6">
            <h3 id="modalProductTitle" class="fw-bold mb-2"></h3>
            <div class="mb-3">
              <span class="text-danger fw-bold fs-3" id="modalProductPrice"></span>
              <span class="badge bg-danger ms-2">Hot Item</span>
            </div>
            <p class="text-muted" id="modalProductDesc"></p>

            <hr class="text-muted my-3">

            <div class="mb-3">
               <small id="modalStockCount" class="fw-bold"></small>
            </div>

            <div class="mb-4">
              <label class="form-label small fw-bold text-muted text-uppercase">Quantity</label>
              <div class="d-flex align-items-center gap-3">
                <div class="input-group" style="width: 140px;">
                  <button class="btn btn-outline-secondary" type="button" id="modalQtyMinus"><i class="bi bi-dash"></i></button>
                  <input type="number" class="form-control text-center border-secondary" id="modalQtyInput" value="1" min="1" readonly>
                  <button class="btn btn-outline-secondary" type="button" id="modalQtyPlus"><i class="bi bi-plus"></i></button>
                </div>
              </div>
            </div>

            <div class="d-grid">
              <button type="button" class="btn btn-danger py-3 fw-bold text-uppercase" id="modalAddToCartBtn" style="background-color: #ff3b6b; border: none;">
                Add to Cart
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
  </main>

  <footer class="bg-light py-4 mt-auto border-top">
    <div class="container text-center text-muted small">
      © 2025 FurniEshop — Built with your home with amazign furnitures.
    </div>
  </footer>

  <!-- Image upload UI removed. To change images, edit the HTML or add your image files to the same folder. -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="script.js"></script>
</body>
</html>
