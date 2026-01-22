<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>FurniEShop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="styles.css" rel="stylesheet" />
</head>
<body>
  <div class="w-100 bg-dark text-white small py-1">
    <div class="container">PRODUCTS</div>
  </div>
  <?php include 'navbar.php'; ?>

  <header class="hero-small text-white d-flex align-items-center" style="background-image:url('https://www.bria.com.ph/wp-content/uploads/2022/10/Furniture-Shopping-AllHome.jpg')">
    <div class="container">
      <div class="row">
        <div class="col-md-8">
          <h1 class="fw-bold">Shop — New Arrivals</h1>
          <p class="mb-0">Explore our handpicked clothing and accessories.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
        </div>
      </div>
    </div>
  </header>

  <main class="py-5">
    <div class="container">
      <!-- Dynamic product grid -->
      <div id="productGrid" class="row g-4">
        <!-- Products will be loaded here by script.js -->
      </div>
    </div>
  </main>
<div class="modal fade" id="productDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg"> <div class="modal-content border-0 shadow-lg">
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
                <input type="radio" class="btn-check" name="modalColor" id="color1" checked>
                <label class="btn btn-outline-dark btn-sm px-3" for="color1">Black</label>

                <input type="radio" class="btn-check" name="modalColor" id="color2">
                <label class="btn btn-outline-dark btn-sm px-3" for="color2">White</label>
                
                <input type="radio" class="btn-check" name="modalColor" id="color3">
                <label class="btn btn-outline-dark btn-sm px-3" for="color3">Wood</label>
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label small fw-bold text-muted text-uppercase">Quantity</label>
              <div class="d-flex align-items-center gap-3">
                <div class="input-group" style="width: 140px;">
                  <button class="btn btn-outline-secondary" type="button" id="modalQtyMinus"><i class="bi bi-dash"></i></button>
                  <input type="number" class="form-control text-center border-secondary" id="modalQtyInput" value="1" min="1" readonly>
                  <button class="btn btn-outline-secondary" type="button" id="modalQtyPlus"><i class="bi bi-plus"></i></button>
                </div>
                <small id="modalStockCount" class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i>In Stock</small>
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
  <footer class="bg-light py-4 border-top text-center small text-muted">
    © 2025 FurniEshop
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="script.js"></script>
</body>
</html>
