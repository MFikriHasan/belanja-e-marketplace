<?php
require 'koneksi.php';
include 'login_check.php';

check_access_control('buyer');


if (isset($_POST['remove'])) {
    $_SESSION['cart'] = [];
}


if (isset($_POST['btn_add'])) {
    $key = isset($_POST['add']) ? $_POST['add'] : '';
    if ($key !== '' && isset($_SESSION['cart'][$key])) {
        $_SESSION['cart'][$key]['qty'] += 1;
    }
}


if (isset($_POST['btn_reduce'])) {
    $key = isset($_POST['reduce']) ? $_POST['reduce'] : '';
    
    if ($key !== '' && isset($_SESSION['cart'][$key])) {
        
        if ($_SESSION['cart'][$key]['qty'] > 1) {
            $_SESSION['cart'][$key]['qty'] -= 1;
        } else {
            
            unset($_SESSION['cart'][$key]);
        }
    }
}

$total = 0; 
$total_quantity = 0; 
$grandtotal = 0;

foreach ($_SESSION['cart'] as $key => $item) {
    
    $subtotal_per_item = $item['price'] * $item['qty'];
    
    
    $total += $subtotal_per_item;
    $total_quantity += $item['qty'];
    $grandtotal = ($total + 15) - 19;
}


$carts = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total_items = array_sum(array_column($carts, 'qty'));


?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Cart - Belanja</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js" onload="window.lucideLoaded=true; if(window.initLucide) window.initLucide();"></script>
<script>
  window.initLucide = function() { if(window.lucide) lucide.createIcons(); };
  document.addEventListener('DOMContentLoaded', function() { if(window.lucideLoaded) window.initLucide(); });
</script>
<style type="text/tailwindcss">
  :root {
    --primary: #2563EB; /* Blue 600 */
    --primary-hover: #1D4ED8; /* Blue 700 */
    --foreground: #0F172A; /* Slate-900 */
    --primary-light: #EFF6FF; /* Blue 50 */
    --background: #F8FAFC; /* Slate 50 */
    --surface: #FFFFFF;
    --muted: #F1F5F9; /* Slate-100 */
    --card-grey: #F8FAFC; /* Slate-50 */
    --secondary: #64748B; /* Slate-500 */
    --text-main: #0F172A; /* Slate 900 */
    --text-muted: #64748B; /* Slate 500 */
    --border-color: #E2E8F0; /* Slate 200 */
    --danger: #EF4444; /* Red 500 */
    --success: #10B981; /* Emerald 500 */
  }
  
  @theme inline {
    --color-primary: var(--primary);
    --color-primary-hover: var(--primary-hover);
    --color-card-grey: var(--card-grey);
    --color-foreground: var(--foreground);
    --color-primary-light: var(--primary-light);
    --color-background: var(--background);
    --color-secondary: var(--secondary);
    --color-surface: var(--surface);
    --color-muted: var(--muted);
    --color-text-main: var(--text-main);
    --color-text-muted: var(--text-muted);
    --color-border-color: var(--border-color);
    --color-danger: var(--danger);
    --color-success: var(--success);
    --font-sans: 'Poppins', sans-serif;
  }

  body {
    font-family: 'Poppins', sans-serif;
    background-color: var(--background);
    color: var(--text-main);
  }

  /* Custom Checkbox Styling */
  .custom-checkbox {
    appearance: none;
    background-color: #fff;
    margin: 0;
    font: inherit;
    color: currentColor;
    width: 1.25em;
    height: 1.25em;
    border: 2px solid var(--border-color);
    border-radius: 0.25em;
    display: grid;
    place-content: center;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
  }

  .custom-checkbox::before {
    content: "";
    width: 0.65em;
    height: 0.65em;
    transform: scale(0);
    transition: 120ms transform ease-in-out;
    box-shadow: inset 1em 1em white;
    background-color: white;
    transform-origin: center;
    clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
  }

  .custom-checkbox:checked {
    background-color: var(--primary);
    border-color: var(--primary);
  }

  .custom-checkbox:checked::before {
    transform: scale(1);
  }

  /* Hide number input arrows */
  input[type="number"]::-webkit-inner-spin-button,
  input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }
  input[type="number"] {
    -moz-appearance: textfield;
  }
</style>
</head>
<body class="font-sans bg-white text-foreground min-h-screen flex flex-col">

<!-- Navbar -->
<nav class="sticky top-0 z-40 bg-white border-b border-border-color shadow-sm">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16 items-center gap-4">
      
      <!-- Logo -->
      <a href="home.php" class="flex-shrink-0 flex items-center gap-2 text-primary">
        <i data-lucide="shopping-bag" class="h-8 w-8"></i>
        <span class="font-bold text-xl tracking-tight hidden sm:block">Belanja</span>
      </a>

      <!-- Desktop Search -->
      <div class="hidden md:flex flex-1 max-w-2xl mx-4">
        <div class="relative w-full group">
          <input type="text" id="inputSearchDesktop" class="w-full bg-muted border border-transparent rounded-full py-2.5 pl-12 pr-4 text-sm focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all" placeholder="Search products, brands and categories...">
          <i data-lucide="search" class="absolute left-4 top-3 h-5 w-5 text-secondary group-focus-within:text-primary transition-colors"></i>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex items-center gap-2 sm:gap-4">
        <!-- Mobile Search Toggle -->
        <button onclick="openSearchModal()" class="md:hidden p-2 text-secondary hover:text-primary hover:bg-muted rounded-full transition-colors cursor-pointer">
          <i data-lucide="search" class="h-6 w-6"></i>
        </button>
        
        <!-- Cart -->
        <a href="/cart.php" class="p-2 text-primary bg-muted rounded-full transition-colors relative cursor-pointer">
          <i data-lucide="shopping-cart" class="h-6 w-6"></i>
        </a>
        
        <!-- Notifications -->
        <a href="/history_transaction.html" class="p-2 text-secondary hover:text-primary hover:bg-muted rounded-full transition-colors relative cursor-pointer">
          <i data-lucide="bell" class="h-6 w-6"></i>
          <span class="absolute top-1 right-1 bg-red-500 text-white text-[10px] font-bold h-2 w-2 rounded-full border-2 border-white"></span>
        </a>
        
        <!-- User Profile -->
         <a href="/buyer_profile.php">
           <div class="h-9 w-9 rounded-full bg-muted overflow-hidden border border-border-color cursor-pointer ml-2 hover:ring-2 hover:ring-primary hover:ring-offset-2 transition-all" onclick="showToast('Profile menu clicked', 'success')">
             <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop" alt="User" class="w-full h-full object-cover">
           </div>
         </a>
      </div>

    </div>
  </div>
</nav>

<!-- Main Content -->
  <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
    
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl sm:text-3xl font-bold text-text-main">Shopping Cart</h1>
      <span class="text-text-muted font-medium bg-background px-3 py-1 rounded-full text-sm border border-border-color"><span data-total-items-count><?= $total_items ?></span> Items</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      
      <!-- Left Column: Cart Items -->
      <div class="lg:col-span-8 space-y-6">
        
        <!-- Global Select All -->
        <div class="bg-surface rounded-2xl p-4 border border-border-color shadow-sm flex items-center justify-between">
          <label class="flex items-center gap-3 cursor-pointer group">
            <input type="checkbox" class="custom-checkbox" data-global-checkbox checked>
            <span class="font-medium text-text-main group-hover:text-primary transition-colors">Select All Items</span>
          </label>
          <button class="text-sm font-medium text-danger hover:text-red-700 transition-colors cursor-pointer hidden sm:block" onclick="showDeleteModal('all')">
            Delete Selected
          </button>
        </div>

        <!-- Cart Items dari Session -->
        <div class="bg-surface rounded-2xl border border-border-color shadow-sm overflow-hidden" data-shop-group="my-cart">
          <!-- Shop Header -->
          <div class="bg-background/50 px-5 py-3 border-b border-border-color flex items-center gap-3">
            <input type="checkbox" class="custom-checkbox" data-shop-checkbox="my-cart" checked>
            <div class="flex items-center gap-2">
              <i data-lucide="shopping-cart" class="w-5 h-5 text-primary"></i>
              <span class="font-semibold text-text-main">My Cart</span>
            </div>
          </div>

          <!-- Items List -->
          <div class="divide-y divide-border-color">
            <?php foreach ($carts as $key => $item): ?>
            <div class="p-5 flex gap-4 sm:gap-6 transition-colors hover:bg-background/30" data-item-id="<?= htmlspecialchars($key) ?>">
              <div class="pt-1">
                <input type="checkbox" class="custom-checkbox"
                  data-item-checkbox="<?= htmlspecialchars($key) ?>"
                  data-price="<?= htmlspecialchars($item['price']) ?>"
                  checked>
              </div>

              <div class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 rounded-xl border border-border-color overflow-hidden bg-white p-2">
                <img src="<?= htmlspecialchars('storage/image/'.$item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-full h-full object-contain">
              </div>

              <div class="flex-1 flex flex-col justify-between min-w-0">
                <div>
                  <div class="flex justify-between items-start gap-2">
                    <h3 class="font-medium text-text-main text-sm sm:text-base line-clamp-2 leading-tight"><?= htmlspecialchars($item['name']) ?></h3>
                    <p class="font-bold text-lg text-text-main whitespace-nowrap">$<?= number_format($item['price'], 2) ?></p>
                  </div>
                  <p class="text-sm text-text-muted mt-1">Variant: <?= htmlspecialchars($item['color']) ?></p>
                </div>

                <div class="flex items-center justify-between mt-4">
                  <div class="flex items-center gap-4">
                    <button class="text-text-muted hover:text-danger transition-colors cursor-pointer flex items-center gap-1 text-sm font-medium"
                      onclick="showDeleteModal('<?= htmlspecialchars($key) ?>')">
                      <i data-lucide="trash-2" class="w-4 h-4"></i>
                      <span class="hidden sm:inline">Remove</span>
                    </button>
                    <div class="w-px h-4 bg-border-color hidden sm:block"></div>
                    <button class="text-text-muted hover:text-primary transition-colors cursor-pointer flex items-center gap-1 text-sm font-medium hidden sm:flex">
                      <i data-lucide="heart" class="w-4 h-4"></i>
                      <span>Move to Wishlist</span>
                    </button>
                  </div>

                  <!-- Quantity Control -->
                  <div class="flex items-center border border-border-color rounded-lg bg-surface shadow-sm">
                      <form action="" method="post">
                        <input type="hidden" name="reduce" value="<?= $key ?>">
                        <button type="submit" name="btn_reduce" class="w-8 h-8 flex items-center justify-center text-text-muted hover:text-primary hover:bg-primary-light rounded-l-lg transition-colors cursor-pointer">
                          <i data-lucide="minus" class="w-4 h-4"></i>
                        </button>
                      </form>
                      <input type="number"
                        class="w-10 h-8 text-center text-sm font-medium border-x border-border-color focus:outline-none bg-transparent"
                        value="<?= (int)$item['qty'] ?>"
                        >
                      <form action="" method="post">
                        <input type="hidden" name="add" value="<?= $key ?>">
                        <button type="submit" name="btn_add" class="w-8 h-8 flex items-center justify-center text-text-muted hover:text-primary hover:bg-primary-light rounded-r-lg transition-colors cursor-pointer">
                          <i data-lucide="plus" class="w-4 h-4"></i>
                        </button>
                      </form>
                    </div>
                  
                </div>
              </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($carts)): ?>
                <p class="px-2 py-2 text-center">Your cart is Lonely...</p>
            <?php endif; ?>
          </div>
        </div>

      </div>

      <!-- Right Column: Order Summary -->
      <div class="lg:col-span-4">
        <div class="bg-surface rounded-2xl border border-border-color shadow-md sticky top-24 overflow-hidden">
          
          <div class="p-6 border-b border-border-color bg-background/30">
            <h2 class="text-lg font-bold text-text-main">Order Summary</h2>
          </div>

          <div class="p-6 space-y-4">
            
            <!-- Promo Code -->
            <div class="flex gap-2">
              <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <i data-lucide="ticket" class="h-4 w-4 text-text-muted"></i>
                </div>
                <input type="text" class="block w-full pl-9 pr-3 py-2.5 border border-border-color rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all uppercase" placeholder="Promo Code">
              </div>
              <button class="px-4 py-2.5 bg-background border border-border-color text-text-main text-sm font-medium rounded-xl hover:bg-border-color transition-colors cursor-pointer">
                Apply
              </button>
            </div>

            <div class="h-px w-full bg-border-color my-2"></div>

            <!-- Breakdown -->
            <div class="space-y-3 text-sm">
              <div class="flex justify-between text-text-muted">
                <span>Subtotal (<span><?= $total_items ?></span> items)</span>
                <span class="font-medium text-text-main">$<?= number_format($total, 0, ".", ",") ?></span>
              </div>
              <div class="flex justify-between text-text-muted">
                <span>Shipping Estimate</span>
                <span class="font-medium text-text-main">$15.00</span>
              </div>
              <div class="flex justify-between text-success">
                <span>Discount</span>
                <span class="font-medium">-$19.00</span>
              </div>
            </div>

            <div class="h-px w-full bg-border-color border-dashed my-2"></div>

            <!-- Total -->
            <div class="flex justify-between items-end pt-2">
              <div>
                <p class="text-base font-bold text-text-main">Total</p>
                <p class="text-xs text-text-muted">Including VAT</p>
              </div>
              <div class="text-right">
                <p class="text-2xl font-bold text-primary">$<?= number_format($grandtotal, 0, ".", ",") ?></p>
              </div>
            </div>

            <!-- Checkout Button -->
            <a href="payment.php" class="w-full py-3.5 px-4 bg-primary text-white rounded-xl font-bold text-base hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/30 transition-all cursor-pointer flex items-center justify-center gap-2 mt-6 group">
              Proceed to Checkout
              <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
            </a>
            
            <p class="text-xs text-center text-text-muted mt-4 flex items-center justify-center gap-1">
              <i data-lucide="shield-check" class="w-4 h-4"></i>
              Secure Checkout Guarantee
            </p>

          </div>
        </div>
      </div>

    </div>
  </main>

<!-- Search Modal -->
<div id="search-modal" class="fixed inset-0 bg-foreground/50 backdrop-blur-sm z-[100] hidden items-start justify-center pt-20 px-4">
  <div class="bg-white rounded-3xl w-full max-w-2xl max-h-[80vh] overflow-hidden shadow-2xl transform transition-all">
    <div class="p-4 border-b border-border-color">
      <div class="flex items-center gap-3 bg-muted rounded-2xl px-4 border border-transparent focus-within:border-primary focus-within:bg-white transition-colors">
        <i data-lucide="search" class="size-5 text-secondary"></i>
        <input type="text" id="search-input" placeholder="Search for products, brands, categories..." class="flex-1 py-4 bg-transparent outline-none text-foreground placeholder:text-secondary">
        <kbd class="hidden sm:inline-flex px-2 py-1 bg-card-grey rounded-lg text-[10px] font-semibold text-secondary border border-border">ESC</kbd>
      </div>
    </div>
    <div class="p-4 overflow-y-auto max-h-[60vh]">
      <p class="text-xs font-semibold text-secondary uppercase tracking-wider mb-3 px-2">Trending Searches</p>
      <div class="flex flex-wrap gap-2 mb-6 px-2">
        <button class="px-4 py-2 rounded-full bg-muted text-sm font-medium hover:bg-primary/10 hover:text-primary transition-colors cursor-pointer">Wireless Earbuds</button>
        <button class="px-4 py-2 rounded-full bg-muted text-sm font-medium hover:bg-primary/10 hover:text-primary transition-colors cursor-pointer">Sony WH-1000XM5</button>
        <button class="px-4 py-2 rounded-full bg-muted text-sm font-medium hover:bg-primary/10 hover:text-primary transition-colors cursor-pointer">Mechanical Keyboard</button>
        <button class="px-4 py-2 rounded-full bg-muted text-sm font-medium hover:bg-primary/10 hover:text-primary transition-colors cursor-pointer">4K Monitor</button>
      </div>
      
      <p class="text-xs font-semibold text-secondary uppercase tracking-wider mb-3 px-2">Suggested Products</p>
      <div id="search-results" class="flex flex-col gap-1">
        <a href="#" class="flex items-center gap-4 p-3 rounded-2xl hover:bg-muted transition-all group">
          <div class="size-12 bg-card-grey rounded-xl flex items-center justify-center overflow-hidden border border-border">
            <img src="https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=100&q=80&fit=crop" class="w-full h-full object-contain">
          </div>
          <div class="flex-1 min-w-0">
            <p class="font-medium text-foreground truncate group-hover:text-primary transition-colors">Sony WH-1000XM5 Wireless Headphones</p>
            <p class="text-sm text-secondary truncate">Audio > Headphones</p>
          </div>
          <span class="font-semibold text-foreground">$348.00</span>
        </a>
        <a href="#" class="flex items-center gap-4 p-3 rounded-2xl hover:bg-muted transition-all group">
          <div class="size-12 bg-card-grey rounded-xl flex items-center justify-center overflow-hidden border border-border">
            <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop" class="w-full h-full object-contain">
          </div>
          <div class="flex-1 min-w-0">
            <p class="font-medium text-foreground truncate group-hover:text-primary transition-colors">Nike Air Max 270 React Running Shoes</p>
            <p class="text-sm text-secondary truncate">Sport > Shoes</p>
          </div>
          <span class="font-semibold text-foreground">$278.00</span>
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 bg-text-main/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4 opacity-0 transition-opacity duration-300">
            <div class="bg-surface rounded-2xl w-full max-w-sm shadow-2xl transform scale-95 transition-transform duration-300" id="delete-modal-content">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-danger/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="alert-triangle" class="w-8 h-8 text-danger"></i>
                </div>
                <h3 class="text-lg font-bold text-text-main mb-2">Remove Item?</h3>
                <p class="text-sm text-text-muted mb-6">Are you sure you want to remove this item from your cart? This action cannot be undone.</p>
                
                <div class="flex gap-3">
                <button class="flex-1 py-2.5 px-4 bg-background border border-border-color text-text-main rounded-xl font-medium hover:bg-border-color transition-colors cursor-pointer" onclick="closeDeleteModal()">
                    Cancel
                </button>
                <form action="" method="post">
                  <button type="submit" name="remove" class="flex-1 py-2.5 px-4 bg-danger text-white rounded-xl font-medium hover:bg-red-600 transition-colors cursor-pointer shadow-md shadow-danger/20">
                      Yes, Remove
                  </button>
                </form>
                </div>
            </div>
            </div>
    </div>

<script>

  // Initialize Icons
    lucide.createIcons();

    // --- State & Logic ---
    let itemToDelete = null;

    // Format currency
    const formatMoney = (amount) => {
      return '$' + amount.toFixed(2);
    };

    // Calculate Totals
    const calculateTotals = () => {
      let subtotal = 0;
      let selectedCount = 0;
      let totalItemsInCart = 0;

      // Iterate over all item checkboxes
      document.querySelectorAll('[data-item-checkbox]').forEach(checkbox => {
        const itemId = checkbox.dataset.itemCheckbox;
        const price = parseFloat(checkbox.dataset.price);
        const qtyInput = document.querySelector(`[data-qty-input="${itemId}"]`);
        
        if (qtyInput) {
          const qty = parseInt(qtyInput.value) || 0;
          totalItemsInCart += qty;
          
          if (checkbox.checked) {
            subtotal += (price * qty);
            selectedCount += qty;
          }
        }
      });

      // Fixed values for demo
      const shipping = 15.00;
      const discount = 19.00;
      const total = subtotal > 0 ? (subtotal + shipping - discount) : 0;

      // Update DOM
      document.querySelector('[data-summary-subtotal]').textContent = formatMoney(subtotal);
      document.querySelector('[data-summary-total]').textContent = formatMoney(total);
      document.querySelector('[data-summary-count]').textContent = selectedCount;
      
      const badge = document.querySelector('[data-cart-badge]');
      if(badge) badge.textContent = totalItemsInCart;
      
      const totalItemsCount = document.querySelector('[data-total-items-count]');
      if(totalItemsCount) totalItemsCount.textContent = totalItemsInCart;
    };

    // --- Event Listeners ---

    // Quantity Buttons
    document.querySelectorAll('[data-qty-btn]').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const action = btn.dataset.qtyBtn;
        const targetId = btn.dataset.target;
        const input = document.querySelector(`[data-qty-input="${targetId}"]`);
        
        if (input) {
          let val = parseInt(input.value) || 1;
          const max = parseInt(input.getAttribute('max')) || 99;
          const min = parseInt(input.getAttribute('min')) || 1;

          if (action === 'plus' && val < max) val++;
          if (action === 'minus' && val > min) val--;
          
          input.value = val;
          calculateTotals();
        }
      });
    });

    // Quantity Input Change
    document.querySelectorAll('[data-qty-input]').forEach(input => {
      input.addEventListener('change', () => {
        let val = parseInt(input.value);
        const max = parseInt(input.getAttribute('max')) || 99;
        const min = parseInt(input.getAttribute('min')) || 1;
        
        if (isNaN(val) || val < min) val = min;
        if (val > max) val = max;
        
        input.value = val;
        calculateTotals();
      });
    });

    // Checkbox Logic (Hierarchical)
    const globalCheckbox = document.querySelector('[data-global-checkbox]');
    const shopCheckboxes = document.querySelectorAll('[data-shop-checkbox]');
    const itemCheckboxes = document.querySelectorAll('[data-item-checkbox]');

    // 1. Global Checkbox Click
    if (globalCheckbox) {
      globalCheckbox.addEventListener('change', (e) => {
        const isChecked = e.target.checked;
        shopCheckboxes.forEach(cb => cb.checked = isChecked);
        itemCheckboxes.forEach(cb => cb.checked = isChecked);
        calculateTotals();
      });
    }

    // 2. Shop Checkbox Click
    shopCheckboxes.forEach(shopCb => {
      shopCb.addEventListener('change', (e) => {
        const shopId = e.target.dataset.shopCheckbox;
        const isChecked = e.target.checked;
        
        // Find items in this shop
        const shopContainer = document.querySelector(`[data-shop-group="${shopId}"]`);
        if (shopContainer) {
          const itemsInShop = shopContainer.querySelectorAll('[data-item-checkbox]');
          itemsInShop.forEach(cb => cb.checked = isChecked);
        }
        
        updateGlobalCheckboxState();
        calculateTotals();
      });
    });

    // 3. Item Checkbox Click
    itemCheckboxes.forEach(itemCb => {
      itemCb.addEventListener('change', (e) => {
        updateShopCheckboxState(e.target);
        updateGlobalCheckboxState();
        calculateTotals();
      });
    });

    // Helper: Update Shop Checkbox based on its items
    function updateShopCheckboxState(itemCheckboxElement) {
      const shopContainer = itemCheckboxElement.closest('[data-shop-group]');
      if (shopContainer) {
        const shopId = shopContainer.dataset.shopGroup;
        const shopCb = document.querySelector(`[data-shop-checkbox="${shopId}"]`);
        const itemsInShop = Array.from(shopContainer.querySelectorAll('[data-item-checkbox]'));
        
        if (shopCb) {
          const allChecked = itemsInShop.every(cb => cb.checked);
          const someChecked = itemsInShop.some(cb => cb.checked);
          
          shopCb.checked = allChecked;
          shopCb.indeterminate = someChecked && !allChecked;
        }
      }
    }

    // Helper: Update Global Checkbox based on all items
    function updateGlobalCheckboxState() {
      if (!globalCheckbox) return;
      const allItems = Array.from(itemCheckboxes);
      const allChecked = allItems.every(cb => cb.checked);
      const someChecked = allItems.some(cb => cb.checked);
      
      globalCheckbox.checked = allChecked;
      globalCheckbox.indeterminate = someChecked && !allChecked;
    }


    // --- Modal Logic ---
    const modal = document.getElementById('delete-modal');
    const modalContent = document.getElementById('delete-modal-content');

    window.showDeleteModal = (id) => {
      itemToDelete = id;
      modal.classList.remove('hidden');
      modal.classList.add('flex');
      // Trigger reflow for animation
      void modal.offsetWidth;
      modal.classList.remove('opacity-0');
      modalContent.classList.remove('scale-95');
    };

    window.closeDeleteModal = () => {
      itemToDelete = null;
      modal.classList.add('opacity-0');
      modalContent.classList.add('scale-95');
      setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }, 300); // match duration
    };

    
    calculateTotals();

  // Toast Notification System
  function showToast(msg, type='success') {
    document.getElementById('toast')?.remove();
    const t = document.createElement('div');
    t.id = 'toast';
    
    // Styling based on type
    const bgClass = type === 'success' ? 'bg-foreground' : 'bg-error';
    const icon = type === 'success' ? 'check-circle' : 'alert-circle';
    const iconColor = type === 'success' ? 'text-success' : 'text-white';

    t.className = `fixed bottom-4 left-1/2 -translate-x-1/2 md:left-auto md:right-8 md:translate-x-0 ${bgClass} text-white px-5 py-4 rounded-2xl z-50 transition-all duration-300 opacity-0 translate-y-[20px] flex items-center gap-3 shadow-2xl min-w-[300px]`;
    
    t.innerHTML = `
      <i data-lucide="${icon}" class="size-5 ${iconColor}"></i>
      <span class="font-medium text-sm">${msg}</span>
    `;
    
    document.body.appendChild(t);
    if(window.lucide) lucide.createIcons();

    requestAnimationFrame(() => {
      t.classList.remove('opacity-0', 'translate-y-[20px]');
      t.classList.add('opacity-100', 'translate-y-0');
    });
    
    setTimeout(() => {
      t.classList.add('opacity-0', 'translate-y-[20px]');
      t.classList.remove('opacity-100', 'translate-y-0');
      setTimeout(() => t.remove(), 300);
    }, 3000);
  }

  // Search Modal Logic
  function openSearchModal() { 
    const modal = document.getElementById('search-modal');
    modal.classList.remove('hidden'); 
    modal.classList.add('flex'); 
    setTimeout(() => document.getElementById('search-input').focus(), 100);
  }
  
  function closeSearchModal() { 
    const modal = document.getElementById('search-modal');
    modal.classList.add('hidden'); 
    modal.classList.remove('flex'); 
  }
  
  document.getElementById('search-modal').addEventListener('click', function(e) { 
    if (e.target === this) closeSearchModal(); 
  });
  
  document.addEventListener('keydown', function(e) { 
    if (e.key === 'Escape') closeSearchModal(); 
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') { 
      e.preventDefault(); 
      openSearchModal(); 
    } 
  });
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    
    <?php if (!empty($_SESSION['error'])): ?>
        
        
        showToast(<?php echo json_encode($_SESSION['error']); ?>, 'error');
        
        <?php 
            
            unset($_SESSION['error']); 
        ?>
        
    <?php endif; ?>
});
</script>


</body>
</html>