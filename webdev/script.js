document.addEventListener('DOMContentLoaded', function(){

  // --- HELPER FUNCTIONS ---
  function escHtml(s){ return String(s).replace(/[&<>"']/g, c=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' })[c]); }
  function formatMoney(n){ return '$' + (Number(n) || 0).toFixed(2); }

  // --- ELEMENTS ---
  const grid = document.getElementById('productGrid');
  const searchInput = document.getElementById('searchInput');
  const searchBtn = document.getElementById('searchBtn');
  const cartCountEl = document.getElementById('cartCount');
  
  let currentModalProduct = null;

  // --- 1. QUANTITY BUTTONS (Modal) ---
  const modalPlus = document.getElementById('modalQtyPlus');
  const modalMinus = document.getElementById('modalQtyMinus');
  const modalInput = document.getElementById('modalQtyInput');

  if(modalPlus && modalInput){
      modalPlus.addEventListener('click', function(){
          let val = parseInt(modalInput.value) || 1;
          let max = parseInt(modalInput.getAttribute('max')) || 100;
          if(val < max) modalInput.value = val + 1;
      });
  }

  if(modalMinus && modalInput){
      modalMinus.addEventListener('click', function(){
          let val = parseInt(modalInput.value) || 1;
          if(val > 1) modalInput.value = val - 1;
      });
  }

  // --- 2. ADD TO CART BUTTON (Modal) ---
  const modalAddBtn = document.getElementById('modalAddToCartBtn');
  if(modalAddBtn) {
      modalAddBtn.addEventListener('click', async function() {
          if (!currentModalProduct) return;

          const qty = parseInt(document.getElementById('modalQtyInput').value) || 1;
          
          // Force color to "Standard" since we removed the choices
          const selectedColor = 'Standard';

          const success = await addToCart(currentModalProduct.id, qty, selectedColor);
          
          if (success) {
              const modalEl = document.getElementById('productDetailsModal');
              if(typeof bootstrap !== 'undefined'){
                  const modal = bootstrap.Modal.getInstance(modalEl);
                  if(modal) modal.hide();
              }
              alert("Added " + qty + " item(s) to cart.");
          }
      });
  }

  // --- 3. OPEN MODAL FUNCTION ---
  window.openProductModal = function(product) {
    currentModalProduct = product; 

    // Fill Image & Text
    const imgSrc = product.image ? escHtml(product.image) : 'https://picsum.photos/400/220?placeholder';
    if(document.getElementById('modalProductImage')) document.getElementById('modalProductImage').src = imgSrc;
    if(document.getElementById('modalProductTitle')) document.getElementById('modalProductTitle').textContent = product.title;
    if(document.getElementById('modalProductPrice')) document.getElementById('modalProductPrice').textContent = formatMoney(product.price);
    if(document.getElementById('modalProductDesc')) document.getElementById('modalProductDesc').textContent = product.description;

    // Handle Stock Logic
    const stockEl = document.getElementById('modalStockCount');
    const addBtn = document.getElementById('modalAddToCartBtn');
    const qtyInput = document.getElementById('modalQtyInput');
    const stock = parseInt(product.stock) || 0;

    if (stockEl) {
        if (stock > 0) {
            stockEl.className = 'text-success fw-bold';
            stockEl.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i>${stock} In Stock`;
            if(addBtn) { addBtn.disabled = false; addBtn.textContent = 'Add to Cart'; }
            if(qtyInput) { qtyInput.value = 1; qtyInput.disabled = false; qtyInput.max = stock; }
        } else {
            stockEl.className = 'text-danger fw-bold';
            stockEl.innerHTML = `<i class="bi bi-x-circle-fill me-1"></i>Out of Stock`;
            if(addBtn) { addBtn.disabled = true; addBtn.textContent = 'Out of Stock'; }
            if(qtyInput) { qtyInput.value = 0; qtyInput.disabled = true; }
        }
    }

    // Color section removed completely.

    // Show Modal
    const modalEl = document.getElementById('productDetailsModal');
    if (modalEl && typeof bootstrap !== 'undefined') {
         const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
         modal.show();
    }
  };

  // --- 4. RENDER PRODUCTS GRID ---
  function renderProducts(list){
    if (!grid) return;
    grid.innerHTML = '';
    
    // Check if list is valid
    if (!list || !Array.isArray(list) || list.length === 0){ 
        grid.innerHTML = '<div class="col-12"><p class="text-muted">No products found.</p></div>'; 
        return;
    }
    
    list.forEach(p=>{
      const col = document.createElement('div'); 
      col.className = 'col-sm-6 col-md-4';
      const imgSrc = p.image ? escHtml(p.image) : 'https://picsum.photos/400/220?placeholder';
      const stock = parseInt(p.stock) || 0;
      
      let btnHtml = '';
      if (stock > 0) {
          btnHtml = `<button class="btn btn-primary btn-sm w-100 view-details-btn">Add to Cart</button>`;
      } else {
          btnHtml = `<button class="btn btn-secondary btn-sm w-100" disabled>Out of Stock</button>`;
      }

      col.innerHTML = `
        <div class="card product-card h-100 shadow-sm">
          <img src="${imgSrc}" class="card-img-top" alt="${escHtml(p.title)}" style="height:220px;object-fit:cover;">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">${escHtml(p.title)}</h5>
            <p class="card-text text-muted mb-2">${escHtml(p.description || '')}</p>
            <div class="mt-auto d-grid gap-2">
              <div class="d-flex gap-2">
                ${btnHtml}
              </div>
              <small class="text-muted">${formatMoney(p.price)}</small>
            </div>
          </div>
        </div>
      `;
      grid.appendChild(col);

      // Attach Click Event
      const btn = col.querySelector('.view-details-btn');
      if (btn) {
          btn.addEventListener('click', function() {
            openProductModal(p);
          });
      }
    });
  }
    // Inside script.js (inside your render function)

const rowHtml = `
  <tr>
      <td class="check-col">
          <input type="checkbox" class="cart-item-check form-check-input" value="${item.id}"> 
      </td>
      
      <td><img src="${item.image}" ...></td>
      <td>${item.name}</td>
      <td>${item.price}</td>
      ...
  </tr>
`;

  // --- 5. AUTH & CART ACTIONS ---
  window.isAuthenticated = null;
  
  async function checkAuth(){
    try{
      const r = await fetch('cart_api.php', { method: 'GET' });
      window.isAuthenticated = r.ok;
    }catch(e){ window.isAuthenticated = false; }
    return window.isAuthenticated;
  }

  async function addToCart(productId, quantity = 1, color = 'Standard') {
    try {
      if (window.isAuthenticated !== true) { requestLoginViaModal(); return false; }

      const response = await fetch('cart_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId, quantity: quantity, color: color })
      });

      if (response.status === 401) { requestLoginViaModal(); return false; }
      const data = await response.json();
      if (!response.ok) throw new Error(data.error || 'Error adding to cart');

      updateCartCount(data.cart_count);
      return true;
    } catch (err) {
      alert("Error: " + err.message);
      return false;
    }
  }

  async function updateCartCount(count = null) {
    if (count === null) {
      try {
        const response = await fetch('cart_api.php');
        if (response.ok) {
          const data = await response.json(); count = data.cart_count;
        } else { count = 0; }
      } catch (err) { count = 0; }
    }
    if (cartCountEl) {
        cartCountEl.textContent = count;
        cartCountEl.style.display = count > 0 ? 'inline-block' : 'none';
    }
  }

  // --- 6. RENDER CART PAGE ITEMS ---
  window.renderCartItems = function(items){
    const tbody = document.getElementById('cartBody');
    if (!tbody) return;
    tbody.innerHTML = '';
    let subtotal = 0;
    
    if(!items || items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">Your cart is empty</td></tr>';
        return;
    }

    items.forEach(it => {
      const price = parseFloat(it.price) || 0;
      const qty = parseInt(it.quantity) || 0;
      const lineTotal = price * qty;
      subtotal += lineTotal;
      
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td><img src="${escHtml(it.image||'https://picsum.photos/80/60?product') }" class="img-thumbnail" style="width:80px;height:60px;object-fit:cover;"></td>
        <td>${escHtml(it.title)}</td>
        <td>${formatMoney(price)}</td>
        <td>
            <div class="input-group" style="width: 120px;">
                <button class="btn btn-outline-secondary btn-sm cart-qty-minus" type="button">-</button>
                <input type="text" class="form-control form-control-sm text-center cart-qty-input" value="${qty}" readonly>
                <button class="btn btn-outline-secondary btn-sm cart-qty-plus" type="button">+</button>
            </div>
        </td>
        <td class="line-total">${formatMoney(lineTotal)}</td>
        <td><button class="btn btn-danger btn-sm btn-remove"><i class="bi bi-trash"></i> Remove</button></td>
      `;
      tr.querySelector('.cart-qty-plus').onclick = function() { updateQuantity(it.product_id, qty + 1, it.selected_color); };
      tr.querySelector('.cart-qty-minus').onclick = function() { if (qty > 1) updateQuantity(it.product_id, qty - 1, it.selected_color); };
      tr.querySelector('.btn-remove').onclick = function() { if(confirm('Remove this item?')) { removeCartItem(it.product_id, it.selected_color); } };
      tbody.appendChild(tr);
    });

    const shipping = 10; 
    const total = subtotal + shipping;
    if (document.getElementById('subtotalAmount')) document.getElementById('subtotalAmount').textContent = formatMoney(subtotal);
    if (document.getElementById('totalAmount')) document.getElementById('totalAmount').textContent = formatMoney(total);
  }

  async function updateQuantity(pid, qty, color) {
    await fetch('cart_api.php', { method: 'PUT', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ product_id: pid, quantity: qty, color: color }) });
    if(window.loadCartItems) window.loadCartItems();
    updateCartCount();
  }

  async function removeCartItem(pid, color) {
    await fetch('cart_api.php', { method: 'DELETE', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ product_id: pid, color: color }) });
    if(window.loadCartItems) window.loadCartItems();
    updateCartCount();
  }

  async function loadCartItems(){
    try{
      const res = await fetch('cart_api.php');
      if (res.ok) {
        const data = await res.json();
        renderCartItems(data.items || []);
        updateCartCount(data.cart_count || 0);
      }
    }catch(err){ console.error(err); }
  }

  window.loadCartItems = loadCartItems;
  window.updateCartCount = updateCartCount;
  window.addToCart = addToCart;

  // --- INITIALIZATION ---
  // Only try to fetch products if the Grid exists on this page
  if (grid) { 
      fetch('products.php')
        .then(r => {
            if(!r.ok) throw new Error("HTTP error " + r.status);
            return r.json();
        })
        .then(data => renderProducts(data))
        .catch(err => {
            console.error(err);
            if(grid) grid.innerHTML = '<div class="col-12 text-danger">Error loading products. Check console.</div>';
        });
  }

  checkAuth();
  updateCartCount();
  if(document.getElementById('cartBody')) loadCartItems();

  if (searchBtn){
    searchBtn.addEventListener('click', function(){
      const q = (searchInput.value||'').toLowerCase().trim();
      fetch('products.php').then(r=>r.json()).then(list=>{
        if (!q) return renderProducts(list);
        const results = list.filter(p=> (p.title + ' ' + (p.description||'')).toLowerCase().includes(q));
        renderProducts(results);
      }).catch(()=>{});
    });
  }

  function requestLoginViaModal(){
      if(document.getElementById('loginModalOverlay')) {
          const overlay = document.getElementById('loginModalOverlay');
          overlay.style.display = 'flex';
          if(window.jQuery) window.jQuery('#loginModalOverlay').fadeIn(120);
      } else { window.location.href = 'Cart.php?modal=login'; }
  }
});