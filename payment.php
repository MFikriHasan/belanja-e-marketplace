<?php
    require 'koneksi.php';
    include 'login_check.php';
    check_access_control('buyer');

    if (empty($_SESSION['cart'])) {
        header('Location: home.php');
        exit;
    }

    if (isset($_POST['checkout'])) {
        $items = $_SESSION['cart'];
        $buyer = $_SESSION['buyer_id'];
        $payment_method = $_POST['payment_method'];
        
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += (int)$item['price'] * (int)$item['qty'];
        }

        $shipping = 15;
        $discount = 19;
        $grand_total = ($subtotal + $shipping) - $discount;

        
        $koneksi->begin_transaction();

        try {
            
            $pre1 = $koneksi->prepare("INSERT INTO transaction (buyer_id, grandtotal, payment_method) VALUES (?, ?, ?)");
            $pre1->bind_param("iis", $buyer, $grand_total, $payment_method);
            $pre1->execute();
            $transaction_id = $koneksi->insert_id;

           
            $pre2 = $koneksi->prepare("INSERT INTO transaction_det (transaction_id, product_id, color_varian_id, seller_id, qty, subtotal, discount, shipping_cost) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            
            
            $pre_update_stock = $koneksi->prepare("UPDATE color_varian SET color_stok = color_stok - ? WHERE id = ?");

            foreach ($items as $item) {
                $product_id   = (int)$item['product_id'];
                $seller_id    = (int)$item['seller_id'];
                $variant_id   = (int)$item['variant_id']; 
                $discount_item = 19; 
                $shipping_cost = 15;
                $qty          = (int)$item['qty'];
                $subtotal_item = (int)$item['price'] * $qty;


                
                $pre2->bind_param("iiiiiiii", $transaction_id, $product_id, $variant_id, $seller_id, $qty, $subtotal_item, $discount_item, $shipping_cost);
                $pre2->execute();

                
                $pre_update_stock->bind_param("ii", $qty, $variant_id);
                $pre_update_stock->execute();
            }

            
            $koneksi->commit();

            $_SESSION['success'] = 'Payment Successfully!';
            $_SESSION['cart'] = []; 
            
            header('Location: history_transaction.php');
            exit;

        } catch (Exception $e) {
            
            $koneksi->rollback();
            $_SESSION['error'] = 'Transaction failed: ' . $e->getMessage();
            header('Location: cart.php');
            exit;
        }
    }

    
    $items = $_SESSION['cart'];
    $subtotal = 0;
    foreach ($items as $item) {
        $subtotal += (int)$item['price'] * (int)$item['qty'];
    }
    $shipping    = 15;
    $discount    = 19;
    $grandtotal  = ($subtotal + $shipping) - $discount;
    
    
    $shipping_address = isset($_SESSION['buyer_address']) ? $_SESSION['buyer_address'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Secure Checkout</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js" onload="window.lucideLoaded=true; if(window.initLucide) window.initLucide();"></script>
<script>
  window.initLucide = function() { if(window.lucide) lucide.createIcons(); };
  document.addEventListener('DOMContentLoaded', function() { if(window.lucideLoaded) window.initLucide(); });
</script>
<style type="text/tailwindcss">
  :root {
    --primary: #165DFF;
    --primary-hover: #0E4BD9;
    --foreground: #080C1A;
    --secondary: #6A7686;
    --muted: #F4F6F8;
    --border: #E5E8EB;
    --card-grey: #F9FAFB;
    --success: #10B981;
    --error: #F53F3F;
    --warning: #FF7D00;
    --font-sans: 'Poppins', sans-serif;
  }
  @theme inline {
    --color-primary: var(--primary);
    --color-primary-hover: var(--primary-hover);
    --color-foreground: var(--foreground);
    --color-secondary: var(--secondary);
    --color-muted: var(--muted);
    --color-border: var(--border);
    --color-card-grey: var(--card-grey);
    --color-success: var(--success);
    --color-error: var(--error);
    --color-warning: var(--warning);
    --font-sans: var(--font-sans);
  }
  .scrollbar-hide::-webkit-scrollbar { display: none; }
  .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
  
  /* Custom Radio Button Styles */
  .payment-radio {
    appearance: none;
    width: 20px;
    height: 20px;
    border: 2px solid var(--border);
    border-radius: 50%;
    outline: none;
    cursor: pointer;
    position: relative;
    transition: all 0.2s ease;
  }
  .payment-radio:checked {
    border-color: var(--primary);
  }
  .payment-radio:checked::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 10px;
    height: 10px;
    background-color: var(--primary);
    border-radius: 50%;
  }
</style>
</head>
<body class="font-sans bg-muted min-h-screen text-foreground">

<!-- Navbar -->
<?php
    include 'navbar_buyer.php';
?>

<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  
  <!-- Header -->
  <div class="mb-8">
    <h1 class="text-2xl md:text-3xl font-bold mb-2">Checkout</h1>
    <p class="text-secondary">Choose your preferred payment method to complete the order.</p>
  </div>

  <div class="flex flex-col lg:flex-row gap-8 items-start">
    
    <!-- Left Column: Payment Methods -->
    <div class="w-full lg:flex-1 space-y-6">
      
      <!-- Shipping Address -->
      <div class="bg-white rounded-2xl border border-border overflow-hidden">
        <div class="p-5 border-b border-border bg-card-grey/50">
          <h2 class="font-semibold text-lg flex items-center gap-2">
            <i data-lucide="map-pinned" class="w-5 h-5 text-primary"></i>
            Your Shipping address
          </h2>
        </div>
        <div class="p-2">
          
          <label class="flex items-center justify-between p-4 rounded-xl hover:bg-card-grey cursor-pointer transition-all group">
            <div class="flex items-center gap-4">
              <div class="w-12 h-8 bg-muted rounded flex items-center justify-center border border-border">
                <i data-lucide="map-pin-house" class="w-4 h-4 text-secondary"></i>
              </div>
              <div>
                <p class="font-medium transition-colors capitalize"><?= !empty($shipping_address) ? $shipping_address : 'You have not set an address yet' ?></p>
              </div>
            </div>
          </label>
        </div>
      </div>

      <form action="" method="post">
      <!-- Virtual Accounts -->
      <div class="bg-white rounded-2xl border border-border overflow-hidden">
        <div class="p-5 border-b border-border bg-card-grey/50">
          <h2 class="font-semibold text-lg flex items-center gap-2">
            <i data-lucide="building-2" class="w-5 h-5 text-primary"></i>
            Virtual Account
          </h2>
        </div>
        <div class="p-2">
          <!-- Method 1 -->
          <label class="flex items-center justify-between p-4 rounded-xl hover:bg-card-grey cursor-pointer transition-all group" data-payment-card="va-bca">
            <div class="flex items-center gap-4">
              <div class="w-12 h-8 bg-[#0066AE]/10 rounded flex items-center justify-center border border-[#0066AE]/20">
                <span class="font-bold text-[#0066AE] text-xs">BCA</span>
              </div>
              <div>
                <p class="font-medium group-hover:text-primary transition-colors">BCA Virtual Account</p>
                <p class="text-xs text-secondary">Pay from BCA Mobile or ATM</p>
              </div>
            </div>
            <input type="radio" name="payment_method" value="va-bca" class="payment-radio" onchange="handlePaymentSelection(this)">
          </label>
          
          <!-- Method 2 -->
          <label class="flex items-center justify-between p-4 rounded-xl hover:bg-card-grey cursor-pointer transition-all group" data-payment-card="va-mandiri">
            <div class="flex items-center gap-4">
              <div class="w-12 h-8 bg-[#003D79]/10 rounded flex items-center justify-center border border-[#003D79]/20">
                <span class="font-bold text-[#003D79] text-[10px]">MANDIRI</span>
              </div>
              <div>
                <p class="font-medium group-hover:text-primary transition-colors">Mandiri Virtual Account</p>
                <p class="text-xs text-secondary">Pay from Livin' by Mandiri</p>
              </div>
            </div>
            <input type="radio" name="payment_method" value="va-mandiri" class="payment-radio" onchange="handlePaymentSelection(this)">
          </label>
        </div>
      </div>

      <!-- E-Wallets -->
      <div class="bg-white rounded-2xl border border-border overflow-hidden">
        <div class="p-5 border-b border-border bg-card-grey/50">
          <h2 class="font-semibold text-lg flex items-center gap-2">
            <i data-lucide="smartphone" class="w-5 h-5 text-primary"></i>
            E-Wallet
          </h2>
        </div>
        <div class="p-2">
          <!-- Method 3 -->
          <label class="flex items-center justify-between p-4 rounded-xl hover:bg-card-grey cursor-pointer transition-all group" data-payment-card="ew-gopay">
            <div class="flex items-center gap-4">
              <div class="w-12 h-8 bg-[#00AED6]/10 rounded flex items-center justify-center border border-[#00AED6]/20">
                <span class="font-bold text-[#00AED6] text-xs">GoPay</span>
              </div>
              <div>
                <p class="font-medium group-hover:text-primary transition-colors">GoPay</p>
                <p class="text-xs text-secondary">Instant payment via app</p>
              </div>
            </div>
            <input type="radio" name="payment_method" value="ew-gopay" class="payment-radio" onchange="handlePaymentSelection(this)">
          </label>
          
          <!-- Method 4 -->
          <label class="flex items-center justify-between p-4 rounded-xl hover:bg-card-grey cursor-pointer transition-all group" data-payment-card="ew-ovo">
            <div class="flex items-center gap-4">
              <div class="w-12 h-8 bg-[#4C3494]/10 rounded flex items-center justify-center border border-[#4C3494]/20">
                <span class="font-bold text-[#4C3494] text-xs">OVO</span>
              </div>
              <div>
                <p class="font-medium group-hover:text-primary transition-colors">OVO</p>
                <p class="text-xs text-secondary">Pay with OVO Cash or Points</p>
              </div>
            </div>
            <input type="radio" name="payment_method" value="ew-ovo" class="payment-radio" onchange="handlePaymentSelection(this)">
          </label>
        </div>
      </div>

      

    </div>

    <!-- Right Column: Order Summary (Sticky) -->
    <div class="w-full lg:w-[400px] shrink-0">
      <div class="sticky top-28 bg-white rounded-2xl border border-border p-6 shadow-sm">
        
        <!-- Countdown Timer -->
        <div class="bg-warning/10 border border-warning/20 rounded-xl p-4 flex items-center justify-between mb-6">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-warning/20 rounded-full flex items-center justify-center">
              <i data-lucide="clock" class="w-4 h-4 text-warning"></i>
            </div>
            <div>
              <p class="text-xs text-warning font-medium">Complete payment in</p>
              <p class="text-lg font-bold text-warning" id="countdownTimer">14:59</p>
            </div>
          </div>
        </div>

        <h3 class="font-bold text-lg mb-4">Order Summary</h3>
        
        <!-- Items Preview -->
         <?php foreach ($items as $item): ?>
          <div class="flex items-center gap-3 mb-4 pb-4 border-b border-border">
            <img src="<?= 'storage/image/'.$item['image'] ?>" class="w-12 h-12 rounded-lg object-cover border border-border">
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium truncate"><?= $item['name'] ?></p>
              <p class="text-xs text-secondary">Qty: <?= $item['qty'] ?> | Variant: <?= $item['color'] ?></p>
            </div>
            <p class="text-sm font-semibold">$<?= number_format($item['price'], 0, ",", ".") ?></p>
          </div>
         <?php endforeach; ?>

        <!-- Calculations -->
        <div class="space-y-3 text-sm mb-6">
          <div class="flex justify-between text-secondary">
            <span>Subtotal</span>
            <span class="text-foreground font-medium">$<?= number_format($subtotal, 0, ",", ".") ?></span>
          </div>
          <div class="flex justify-between text-secondary">
            <span>Shipping Estimate</span>
            <span class="text-foreground font-medium">$15.00</span>
          </div>
          <div class="flex justify-between text-success">
            <span>Discount</span>
            <span class="text-success font-medium">$-19.00</span>
          </div>
        </div>

        <!-- Total -->
        <div class="flex justify-between items-center pt-4 border-t border-border mb-6">
          <span class="font-semibold">Total Amount</span>
          <span class="text-2xl font-bold text-primary">$<?= number_format($grandtotal, 0, ",", ".") ?></span>
        </div>

        <!-- Action Button -->
         
           <button type="submit" name="checkout" id="btnPayNow" disabled class="w-full bg-primary text-white rounded-full py-4 font-bold text-lg hover:bg-primary-hover transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
             <i data-lucide="lock" class="w-5 h-5"></i>
             Pay $<?= number_format($grandtotal, 0, ",", ".") ?>
           </button>
         </form>
        
        <p class="text-center text-xs text-secondary mt-4 flex items-center justify-center gap-1">
          <i data-lucide="shield-check" class="w-3 h-3"></i>
          Secure encrypted payment
        </p>

      </div>
    </div>

  </div>
</main>

<!-- Search Modal -->
<div id="search-modal" class="fixed inset-0 bg-black/50 z-[100] hidden items-center justify-center p-4 backdrop-blur-sm transition-opacity">
  <div class="bg-white rounded-3xl w-full max-w-2xl max-h-[80vh] overflow-hidden shadow-2xl flex flex-col">
    <div class="p-4 border-b border-border">
      <div class="flex items-center gap-3 bg-muted rounded-xl px-4 focus-within:ring-2 focus-within:ring-primary/20 transition-all">
        <i data-lucide="search" class="size-5 text-secondary"></i>
        <input type="text" id="search-input" placeholder="Search products, orders, or help..." class="flex-1 py-4 bg-transparent outline-none text-foreground" oninput="handleSearch(this.value)">
        <kbd class="hidden sm:inline-flex px-2 py-1 bg-white rounded-lg text-xs text-secondary border border-border shadow-sm">ESC</kbd>
      </div>
    </div>
    <div class="p-4 overflow-y-auto flex-1">
      <p class="text-xs font-semibold text-secondary uppercase tracking-wider mb-3 px-2">Quick Links</p>
      <div id="search-results" class="flex flex-col gap-1">
        <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-muted transition-all group">
          <div class="size-10 bg-primary/10 rounded-xl flex items-center justify-center group-hover:bg-primary/20 transition-colors"><i data-lucide="package" class="size-5 text-primary"></i></div>
          <div class="flex-1 min-w-0"><p class="font-medium text-foreground truncate">Track Order #ORD-8829</p><p class="text-xs text-secondary truncate">In transit</p></div>
          <i data-lucide="chevron-right" class="size-4 text-secondary opacity-0 group-hover:opacity-100 transition-opacity"></i>
        </a>
        <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-muted transition-all group">
          <div class="size-10 bg-secondary/10 rounded-xl flex items-center justify-center group-hover:bg-secondary/20 transition-colors"><i data-lucide="help-circle" class="size-5 text-secondary"></i></div>
          <div class="flex-1 min-w-0"><p class="font-medium text-foreground truncate">Payment Help Center</p><p class="text-xs text-secondary truncate">FAQs and support</p></div>
          <i data-lucide="chevron-right" class="size-4 text-secondary opacity-0 group-hover:opacity-100 transition-opacity"></i>
        </a>
      </div>
    </div>
  </div>
</div>



<script>
  

  // --- Payment Selection Logic ---
  function handlePaymentSelection(radioInput) {
    // Reset all cards styling
    document.querySelectorAll('[data-payment-card]').forEach(card => {
      card.classList.remove('ring-2', 'ring-primary', 'bg-primary/5');
      card.classList.add('border-transparent'); // Remove border if it had one
    });

    // Apply styling to selected card
    const selectedCard = document.querySelector(`[data-payment-card="${radioInput.value}"]`);
    if (selectedCard) {
      selectedCard.classList.add('ring-2', 'ring-primary', 'bg-primary/5');
    }

    // Enable Pay Button
    const payBtn = document.getElementById('btnPayNow');
    payBtn.disabled = false;
  }

  // --- Countdown Timer Logic ---
  let timeLeft = 14 * 60 + 59; // 14:59 in seconds
  const timerElement = document.getElementById('countdownTimer');

  function updateTimer() {
    const minutes = Math.floor(timeLeft / 60);
    let seconds = timeLeft % 60;
    
    seconds = seconds < 10 ? '0' + seconds : seconds;
    timerElement.textContent = `${minutes}:${seconds}`;
    
    if (timeLeft > 0) {
      timeLeft--;
    } else {
      timerElement.textContent = "0:00";
      timerElement.classList.replace('text-warning', 'text-error');
      document.getElementById('btnPayNow').disabled = true;
      document.getElementById('btnPayNow').textContent = "Session Expired";
      showToast("Payment session expired. Please refresh.", "error");
    }
  }
  
  setInterval(updateTimer, 1000);

  

  // --- Toast Notification ---
  function showToast(msg, type='success') {
    document.getElementById('toast')?.remove();
    const t = document.createElement('div');
    t.id = 'toast';
    const bgClass = type === 'success' ? 'bg-foreground' : 'bg-error';
    t.className = `fixed bottom-4 left-1/2 -translate-x-1/2 ${bgClass} text-white px-6 py-3 rounded-full shadow-lg z-[200] transition-all duration-300 opacity-0 translate-y-[20px] flex items-center gap-2 text-sm font-medium`;
    
    const icon = type === 'success' ? 'check-circle' : 'alert-circle';
    t.innerHTML = `<i data-lucide="${icon}" class="w-4 h-4"></i> ${msg}`;
    
    document.body.appendChild(t);
    lucide.createIcons();
    
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

  // --- Search Modal Logic ---
  function openSearchModal() { 
    const modal = document.getElementById('search-modal');
    modal.classList.remove('hidden'); 
    modal.classList.add('flex'); 
    setTimeout(() => document.getElementById('search-input').focus(), 10);
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

  function handleSearch(query) {
    // Dummy search handler
    console.log("Searching for:", query);
  }
</script>


</body>
</html>