<?php
    require 'koneksi.php';
    include 'login_check.php';
    check_access_control('buyer');
    $buyer_id = (int)$_SESSION['buyer_id'];

    $shipping_status = isset($_GET['shipping_status']) ? $_GET['shipping_status'] : [];
    

    $sql = "SELECT 
            td.id AS detail_id,
            t.id AS transaction_id,
            t.date,
            t.payment_method,
            p.name AS product_name,
            cv.product_image,
            s.name AS store_name,
            td.qty,
            td.subtotal,
            b.name AS buyer_name,
            b.address AS buyer_address,
            td.shipping_status
            FROM transaction_det td
            JOIN `transaction` t ON td.transaction_id = t.id
            JOIN product p ON td.product_id = p.id
            JOIN buyer b ON t.buyer_id = b.id
            JOIN color_varian cv ON td.color_varian_id = cv.id
            JOIN seller s ON td.seller_id = s.id
            WHERE t.buyer_id = ?";
    if ($shipping_status) {
      $sql .= " AND td.shipping_status = '". $shipping_status ."'";
    }

    $sql .= " ORDER BY t.date DESC, td.id DESC";

    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $buyer_id);
    $stmt->execute();
    $transactions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Buyer Transaction History</title>
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
    --success: #00B42A;
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
</style>
</head>
<body class="font-sans bg-muted min-h-screen flex flex-col">

<!-- Navbar -->
<?php
  include 'navbar_buyer.php';
?>

<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden backdrop-blur-sm" onclick="toggleSidebar()"></div>

<div class="flex flex-1 pt-6 overflow-hidden">
  <main class="flex-1 flex flex-col h-[calc(100vh-88px)] overflow-y-auto bg-muted">
    <div class="p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
      
      <div class="mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-foreground mb-2">Transaction History</h1>
        <p class="text-secondary">View and manage your recent orders and purchases.</p>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-2xl border border-border p-2 mb-6 flex flex-col sm:flex-row justify-between items-center gap-4 shadow-sm">
        <div class="overflow-x-auto scrollbar-hide w-full sm:w-auto">
          <nav class="flex min-w-max px-2">
            <a href="/history_transaction.php" class="tab-btn py-3 px-4 border-b-2 <?= empty($shipping_status) ? 'border-primary text-primary font-semibold' : 'border-transparent text-secondary hover:text-foreground' ?> whitespace-nowrap transition-colors">All Orders</a>
            <a href="?shipping_status=<?= 'pending' ?>" class="tab-btn py-3 px-4 border-b-2 <?= ($shipping_status) == 'pending' ? 'border-primary text-primary font-semibold' : 'border-transparent text-secondary hover:text-foreground' ?> whitespace-nowrap transition-colors">Pending</a>
            <a href="?shipping_status=<?= 'shipped' ?>" class="tab-btn py-3 px-4 border-b-2 <?= ($shipping_status) == 'shipped' ? 'border-primary text-primary font-semibold' : 'border-transparent text-secondary hover:text-foreground' ?> whitespace-nowrap transition-colors">Shipped</a>
            <a href="?shipping_status=<?= 'completed' ?>" class="tab-btn py-3 px-4 border-b-2 <?= ($shipping_status) == 'completed' ? 'border-primary text-primary font-semibold' : 'border-transparent text-secondary hover:text-foreground' ?> whitespace-nowrap transition-colors">Completed</a>
            <a href="?shipping_status=<?= 'failed' ?>" class="tab-btn py-3 px-4 border-b-2 <?= ($shipping_status) == 'failed' ? 'border-primary text-primary font-semibold' : 'border-transparent text-secondary hover:text-foreground' ?> whitespace-nowrap transition-colors">Failed</a>
          </nav>
        </div>
        <input type="hidden" id="statusFilter" value="all">
        
        <div class="w-full sm:w-auto px-2 pb-2 sm:pb-0">
          <div class="relative">
            <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-secondary"></i>
            <select id="selectDateRange" class="w-full sm:w-auto pl-9 pr-8 py-2.5 bg-muted border border-transparent rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary appearance-none cursor-pointer">
              <option value="all">All Time</option>
              <option value="30">Last 30 Days</option>
              <option value="90">Last 3 Months</option>
              <option value="2023">Year 2023</option>
            </select>
            <i data-lucide="chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 size-4 text-secondary pointer-events-none"></i>
          </div>
        </div>
      </div>

      <!-- Transactions List -->
      <div id="transactionsContainer" class="flex flex-col gap-4">
        
        <?php foreach ($transactions as $row): ?>
          <div class="bg-white border border-border rounded-2xl p-4 md:p-5 flex flex-col md:flex-row gap-4 md:gap-6 items-start md:items-center hover:shadow-md transition-shadow">
            <div class="flex items-start gap-4 w-full md:w-auto md:flex-1">
              <img src="<?= !empty($row['product_image']) ? 'storage/image/' . $row['product_image'] : 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=200&h=200&fit=crop' ?>" class="size-20 md:size-24 rounded-xl object-cover border border-border shrink-0">
              <div class="flex-1 min-w-0 pt-1">
                <div class="flex items-center gap-2 mb-1.5">
                  <span class="text-xs font-semibold text-secondary bg-muted px-2 py-0.5 rounded-md">ORD-<?= $row['detail_id'] ?></span>
                  <span class="text-xs text-secondary"><?= date("d M Y", strtotime($row['date'])) ?></span>
                </div>
                <h4 class="font-semibold text-foreground text-base md:text-lg truncate"><?= $row['product_name'] ?></h4>
                <div class="flex items-center gap-1 mt-1">
                  <i data-lucide="store" class="size-3.5 text-secondary"></i>
                  <p class="text-sm text-secondary truncate"><?= $row['store_name'] ?></p>
                </div>
              </div>
            </div>
            
            <div class="flex flex-row md:flex-col items-center md:items-end justify-between w-full md:w-auto gap-2 md:gap-1 border-t border-border md:border-t-0 pt-4 md:pt-0">
              <div class="flex flex-col md:items-end">
                <span class="text-xs text-secondary mb-0.5 md:hidden">Total</span>
                <span class="font-bold text-lg md:text-xl text-foreground">$<?= number_format($row['subtotal'], 0, ",", ".") ?></span>
              </div>
              <span class="capitalize text-xs font-semibold px-2.5 py-1 rounded-full flex items-center gap-1
                <?= ($row['shipping_status'] == 'pending') ? 'bg-warning/10 text-warning' : '' ?>
                <?= ($row['shipping_status'] == 'shipped') ? 'bg-primary/10 text-primary' : '' ?>
                <?= ($row['shipping_status'] == 'completed') ? 'bg-success/10 text-success' : '' ?>
                <?= ($row['shipping_status'] == 'failed') ? 'bg-error/10 text-error' : '' ?>">
                
                <i data-lucide="<?= ($row['shipping_status'] == 'pending') ? 'clock' : '' ?><?= ($row['shipping_status'] == 'shipped') ? 'truck' : '' ?><?= ($row['shipping_status'] == 'completed') ? 'check-circle' : '' ?><?= ($row['shipping_status'] == 'failed') ? 'x-circle' : '' ?>" class="size-3"></i>
                
                <?= $row['shipping_status'] ?>
              </span>
            </div>
            
            <div class="flex items-center gap-2 w-full md:w-auto mt-2 md:mt-0">
              <button onclick="viewItem('detailModal<?= $row['detail_id'] ?>')" class="flex-1 md:flex-none px-4 py-2.5 bg-white border border-border text-foreground text-sm font-semibold rounded-xl hover:bg-muted hover:border-secondary transition-all text-center cursor-pointer">Details</button>
            </div>
          </div>



          <!-- Transaction Detail Modal -->
          <div id="detailModal<?= $row['detail_id'] ?>" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-300">
            <div class="bg-white rounded-3xl w-full max-w-lg max-h-[90vh] overflow-hidden flex flex-col shadow-2xl transform scale-95 transition-transform duration-300" id="detailModalContent">
              <div class="p-5 md:p-6 border-b border-border flex justify-between items-center bg-white sticky top-0 z-10">
                <h3 class="font-bold text-xl text-foreground">Order Details</h3>
                <button onclick="closeDetail('detailModal<?= $row['detail_id'] ?>')" class="size-10 flex items-center justify-center rounded-full hover:bg-muted transition-colors cursor-pointer">
                  <i data-lucide="x" class="size-5 text-secondary"></i>
                </button>
              </div>
              
              <div class="p-5 md:p-6 overflow-y-auto flex-1 bg-card-grey">
                <!-- Product Info -->
                <div class="bg-white rounded-2xl p-4 border border-border mb-4 flex items-center gap-4">
                  <img  src="<?= !empty($row['product_image']) ? 'storage/image/' . $row['product_image'] : 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=200&h=200&fit=crop' ?>" class="size-20 rounded-xl object-cover border border-border">
                  <div class="flex-1 min-w-0">
                    <h4 class="font-bold text-foreground text-lg leading-tight mb-1"><?= $row['product_name'] ?></h4>
                    <div class="flex items-center gap-1 text-secondary">
                      <i data-lucide="store" class="size-4"></i>
                      <p class="text-sm"><?= $row['store_name'] ?></p>
                    </div>
                  </div>
                </div>

                <!-- Order Info -->
                <div class="bg-white rounded-2xl border border-border overflow-hidden">
                  <div class="p-4 border-b border-border bg-muted/30">
                    <h5 class="font-semibold text-sm text-secondary uppercase tracking-wider">Summary</h5>
                  </div>
                  <div class="p-4 space-y-4">
                    <div class="flex justify-between items-center">
                      <span class="text-secondary text-sm">Order ID</span>
                      <span  class="font-semibold text-foreground">ORD-<?= $row['detail_id'] ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                      <span class="text-secondary text-sm">Date Placed</span>
                      <span class="font-medium text-foreground"><?= date("d M, Y, H:i A", strtotime($row['date'])) ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                      <span class="text-secondary text-sm">Status</span>
                      <div>
                        <span class="capitalize text-xs font-semibold px-2.5 py-1 rounded-full flex items-center gap-1
                          <?= ($row['shipping_status'] == 'pending') ? 'bg-warning/10 text-warning' : '' ?>
                          <?= ($row['shipping_status'] == 'shipped') ? 'bg-primary/10 text-primary' : '' ?>
                          <?= ($row['shipping_status'] == 'completed') ? 'bg-success/10 text-success' : '' ?>
                          <?= ($row['shipping_status'] == 'failed') ? 'bg-error/10 text-error' : '' ?>">
                          
                          <i data-lucide="<?= ($row['shipping_status'] == 'pending') ? 'clock' : '' ?><?= ($row['shipping_status'] == 'shipped') ? 'truck' : '' ?><?= ($row['shipping_status'] == 'completed') ? 'check-circle' : '' ?><?= ($row['shipping_status'] == 'failed') ? 'x-circle' : '' ?>" class="size-3"></i>
                          
                          <?= $row['shipping_status'] ?>
                        </span>
                      </div>
                    </div>
                    <div class="flex justify-between items-center">
                      <span class="text-secondary text-sm">Payment Method</span>
                      <span class="font-medium text-foreground flex items-center gap-2 capitalize">
                        <i data-lucide="credit-card" class="size-4 text-secondary"></i> <?= $row['payment_method'] ?>
                      </span>
                    </div>
                    <div class="flex justify-between items-center">
                      <span class="text-secondary text-sm">Total Quantity</span>
                      <span  class="font-semibold text-foreground"><?= $row['qty'] ?></span>
                    </div>
                    <div class="pt-4 border-t border-border flex justify-between items-center">
                      <span class="font-semibold text-foreground">Total Amount</span>
                      <span  class="font-bold text-xl text-primary">$<?= number_format($row['subtotal'], 0, ",", ".") ?></span>
                    </div>
                  </div>
                </div>
                
                <!-- Shipping Info -->
                <div class="bg-white rounded-2xl border border-border overflow-hidden mt-4">
                  <div class="p-4 border-b border-border bg-muted/30">
                    <h5 class="font-semibold text-sm text-secondary uppercase tracking-wider">Shipping Address</h5>
                  </div>
                  <div class="p-4">
                    <p class="font-medium text-foreground"><?= $row['buyer_name'] ?></p>
                    <p class="text-secondary text-sm mt-1"><?= !empty($row['buyer_address']) ? $row['buyer_address'] : 'You have not set an address yet' ?></p>
                  </div>
                </div>
              </div>
              
              <div class="p-5 md:p-6 border-t border-border bg-white flex gap-3">
                <button onclick="closeDetail('detailModal<?= $row['detail_id'] ?>')" class="flex-1 py-3.5 bg-white border border-border text-foreground rounded-xl font-semibold hover:bg-muted transition-colors cursor-pointer">Close</button>
                <button onclick="showToast('Invoice downloaded', 'success')" class="flex-1 py-3.5 bg-primary text-white rounded-xl font-semibold hover:bg-primary-hover shadow-sm shadow-primary/20 transition-colors cursor-pointer flex items-center justify-center gap-2">
                  <i data-lucide="download" class="size-4"></i> Invoice
                </button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Empty State  -->
       <?php if (empty($transactions)): ?>
        <div id="noResults" class="flex flex-col items-center justify-center py-16 text-center">
        <div class="size-20 bg-muted rounded-full flex items-center justify-center mb-4">
          <i data-lucide="search-x" class="size-10 text-secondary"></i>
        </div>
        <h3 class="text-lg font-bold text-foreground mb-1">No transactions found</h3>
        <p class="text-secondary max-w-sm">We couldn't find any orders matching your current filters. Try adjusting your search or status.</p>
      </div>
      <?php endif; ?>
      

    </div>
  </main>
</div>



<!-- Search Modal -->
<div id="search-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] hidden items-start justify-center pt-20 p-4 opacity-0 transition-opacity duration-300">
  <div class="bg-white rounded-3xl w-full max-w-2xl max-h-[80vh] overflow-hidden shadow-2xl transform scale-95 transition-transform duration-300" id="searchModalContent">
    <div class="p-4 border-b border-border">
      <div class="flex items-center gap-3 bg-muted rounded-2xl px-4 border border-transparent focus-within:border-primary focus-within:bg-white transition-colors">
        <i data-lucide="search" class="size-5 text-primary"></i>
        <input type="text" id="search-input-modal" placeholder="Search orders, shops, or items..." class="flex-1 py-3.5 bg-transparent outline-none text-foreground placeholder:text-secondary" oninput="handleSearch(this.value)">
        <kbd class="hidden sm:inline-flex px-2 py-1 bg-white rounded-lg text-xs font-medium text-secondary border border-border shadow-sm">ESC</kbd>
      </div>
    </div>
    <div class="p-4 overflow-y-auto max-h-[60vh]">
      <p class="text-xs font-semibold text-secondary uppercase tracking-wider mb-3 px-2">Recent Searches</p>
      <div id="search-results" class="flex flex-col gap-1">
        <a href="#" class="flex items-center gap-4 p-3 rounded-xl hover:bg-muted transition-all group">
          <div class="size-10 bg-primary/10 rounded-xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors"><i data-lucide="headphones" class="size-5 text-primary group-hover:text-white"></i></div>
          <div class="flex-1 min-w-0"><p class="font-medium text-foreground truncate">Sony Headphones</p><p class="text-xs text-secondary truncate">In Electronics</p></div>
          <i data-lucide="arrow-up-left" class="size-4 text-secondary opacity-0 group-hover:opacity-100 transition-opacity"></i>
        </a>
        <a href="#" class="flex items-center gap-4 p-3 rounded-xl hover:bg-muted transition-all group">
          <div class="size-10 bg-muted rounded-xl flex items-center justify-center"><i data-lucide="store" class="size-5 text-secondary"></i></div>
          <div class="flex-1 min-w-0"><p class="font-medium text-foreground truncate">Tech Haven Official</p><p class="text-xs text-secondary truncate">Shop</p></div>
          <i data-lucide="arrow-up-left" class="size-4 text-secondary opacity-0 group-hover:opacity-100 transition-opacity"></i>
        </a>
        <a href="#" class="flex items-center gap-4 p-3 rounded-xl hover:bg-muted transition-all group">
          <div class="size-10 bg-muted rounded-xl flex items-center justify-center"><i data-lucide="receipt" class="size-5 text-secondary"></i></div>
          <div class="flex-1 min-w-0"><p class="font-medium text-foreground truncate">ORD-8471</p><p class="text-xs text-secondary truncate">Order ID</p></div>
          <i data-lucide="arrow-up-left" class="size-4 text-secondary opacity-0 group-hover:opacity-100 transition-opacity"></i>
        </a>
      </div>
    </div>
  </div>
</div>

<script>
  // Modal Logic
  function viewItem(detailModalId) {

    lucide.createIcons();
    
    const modal = document.getElementById(detailModalId);
    const content = document.getElementById('detailModalContent');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Animation
    requestAnimationFrame(() => {
      modal.classList.remove('opacity-0');
      content.classList.remove('scale-95');
      content.classList.add('scale-100');
    });
  }

  function closeDetail(detailModalId) {
    const modal = document.getElementById(detailModalId);
    const content = document.getElementById('detailModalContent');
    
    modal.classList.add('opacity-0');
    content.classList.remove('scale-100');
    content.classList.add('scale-95');
    
    setTimeout(() => {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }, 300);
  }

  // Search Modal Logic
  function openSearchModal() { 
    const modal = document.getElementById('search-modal');
    const content = document.getElementById('searchModalContent');
    modal.classList.remove('hidden'); 
    modal.classList.add('flex'); 
    
    requestAnimationFrame(() => {
      modal.classList.remove('opacity-0');
      content.classList.remove('scale-95');
      content.classList.add('scale-100');
      document.getElementById('search-input-modal').focus(); 
    });
  }

  document.addEventListener('click', function(e) {
    if (e.target.id.startsWith('detailModal')) {
      closeDetail(e.target.id);
    }
  });

  function closeSearchModal() { 
    const modal = document.getElementById('search-modal');
    const content = document.getElementById('searchModalContent');
    
    modal.classList.add('opacity-0');
    content.classList.remove('scale-100');
    content.classList.add('scale-95');
    
    setTimeout(() => {
      modal.classList.add('hidden'); 
      modal.classList.remove('flex'); 
    }, 300);
  }

  function handleSearch(val) {
    // Sync with main search input if needed, or just handle local modal search
    const mainInput = document.getElementById('searchInput');
  }

  document.getElementById('search-modal').addEventListener('click', function(e) { if (e.target === this) closeSearchModal(); });

  document.addEventListener('keydown', function(e) { 
    if (e.key === 'Escape') {
      closeSearchModal(); 
      closeDetail();
    }
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') { 
      e.preventDefault(); 
      openSearchModal(); 
    } 
  });

  // Sidebar Toggle
  function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('-translate-x-full');
    document.getElementById('sidebar-overlay').classList.toggle('hidden');
  }

  // Toast Notification
  function showToast(msg, type='success') {
    document.getElementById('toast')?.remove();
    const t = document.createElement('div');
    t.id = 'toast';
    
    let bgClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-error' : 'bg-warning';
    let icon = type === 'success' ? 'check-circle' : type === 'error' ? 'x-circle' : 'alert-circle';
    
    t.className = `fixed bottom-4 left-1/2 -translate-x-1/2 md:left-auto md:right-4 md:translate-x-0 ${bgClass} text-white px-5 py-3.5 rounded-2xl z-[200] transition-all duration-300 opacity-0 translate-y-[20px] shadow-lg flex items-center gap-3 font-medium`;
    t.innerHTML = `<i data-lucide="${icon}" class="size-5"></i> ${msg}`;
    
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
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    
    <?php if (!empty($_SESSION['success'])): ?>
        
        
        showToast(<?php echo json_encode($_SESSION['success']); ?>, 'success');
        
        <?php 
            
            unset($_SESSION['success']); 
        ?>
        
    <?php endif; ?>
});
</script>

</body>
</html>