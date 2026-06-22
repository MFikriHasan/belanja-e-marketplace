<?php
    require 'koneksi.php';
    include 'login_check.php';
    check_access_control('seller');

    $seller_id = $_SESSION['seller_id'];

    // stats total revenue, orders  dan total customers
    $q_stats = "SELECT COALESCE(SUM(td.subtotal), 0) AS total_revenue,
                COUNT(DISTINCT td.qty) AS total_orders,
                COUNT(DISTINCT t.buyer_id) AS total_customers
                FROM transaction_det td
                JOIN transaction t ON t.id = td.transaction_id
                WHERE td.seller_id = ?";
    $pre = $koneksi->prepare($q_stats);
    $pre->bind_param("i", $seller_id);
    $pre->execute();
    $result_stats = $pre->get_result()->fetch_assoc();
    
    // stats active listings

    $q_active = "SELECT 
                    COUNT(CASE WHEN p_total.total_all_stock >= 30 THEN 1 END) AS active_listings
                    FROM (
                    SELECT p.id AS product_id, COALESCE(SUM(cv.color_stok), 0) AS total_all_stock
                    FROM product p
                    LEFT JOIN color_varian cv ON p.id = cv.product_id
                    WHERE p.seller_id = ?
                    GROUP BY p.id
                ) AS p_total";

    $pre = $koneksi->prepare($q_active);
    $pre->bind_param("i", $seller_id);
    $pre->execute();
    $result_active_listings = $pre->get_result()->fetch_assoc();

    $active_listings = $result_active_listings['active_listings'];
    $total_revenue = $result_stats['total_revenue'];
    $total_orders = $result_stats['total_orders'];
    $total_customers = $result_stats['total_customers'];

    // latest transaction
  $q_transaction = $koneksi->prepare(
      "SELECT 
          t.id AS order_id,
          p.name AS product_name, 
          cv.color_name AS variant, 
          cv.product_image, 
          b.name AS buyer_name,
          b.email AS buyer_email,
          t.date AS transaction_date,
          td.subtotal AS amount,
          td.shipping_status AS status
      FROM transaction_det td
      JOIN transaction t ON t.id = td.transaction_id
      JOIN color_varian cv ON cv.id = td.color_varian_id 
      JOIN product p ON p.id = cv.product_id            
      JOIN buyer b ON b.id = t.buyer_id
      WHERE td.seller_id = ?
      ORDER BY t.date DESC, td.id DESC
      LIMIT 5"                                
  );

  $q_transaction->bind_param("i", $seller_id);
  $q_transaction->execute();
  $recent_sales = $q_transaction->get_result()->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Seller Dashboard</title>
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
<body class="font-sans bg-muted min-h-screen text-foreground">

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden backdrop-blur-sm transition-opacity" onclick="toggleSidebar()"></div>

<div class="flex h-screen overflow-hidden">
  
  <!-- Left Sidebar -->
  <aside id="sidebar" class="flex flex-col w-[280px] h-screen fixed left-0 z-50 bg-white border-r border-border transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
    <!-- Logo Area -->
    <div class="flex items-center justify-between border-b border-border h-[90px] px-6">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-sm shadow-primary/20">
          <i data-lucide="shopping-bag" class="w-5 h-5 text-white"></i>
        </div>
        <h1 class="font-bold text-xl tracking-tight">Belanja</h1>
      </div>
      <button onclick="toggleSidebar()" class="lg:hidden size-10 flex bg-muted rounded-xl items-center justify-center cursor-pointer hover:bg-border transition-colors">
        <i data-lucide="x" class="size-5 text-secondary"></i>
      </button>
    </div>

    <!-- Navigation Links -->
    <nav class="flex flex-col p-4 gap-2 overflow-y-auto flex-1">
      <p class="px-4 text-xs font-semibold text-secondary uppercase tracking-wider mb-2 mt-4">Menu</p>
      
      <a href="/dashboard.php" class="group active cursor-pointer">
        <div class="flex items-center rounded-xl p-3.5 gap-3 group-[.active]:bg-primary/10 group-[.active]:text-primary hover:bg-muted transition-all">
          <i data-lucide="layout-dashboard" class="size-5 text-secondary group-[.active]:text-primary group-hover:text-foreground transition-colors"></i>
          <span class="font-medium text-secondary group-[.active]:font-semibold group-[.active]:text-primary group-hover:text-foreground transition-colors">Dashboard</span>
        </div>
      </a>
      
      <a href="/management_product.php" class="group cursor-pointer">
        <div class="flex items-center rounded-xl p-3.5 gap-3 hover:bg-muted transition-all">
          <i data-lucide="package" class="size-5 text-secondary group-hover:text-foreground transition-colors"></i>
          <span class="font-medium text-secondary group-hover:text-foreground transition-colors">Products</span>
        </div>
      </a>
      
      <a href="/management_transactions.php" class="group cursor-pointer">
        <div class="flex items-center rounded-xl p-3.5 gap-3 hover:bg-muted transition-all">
          <i data-lucide="arrow-left-right" class="size-5 text-secondary group-hover:text-foreground transition-colors"></i>
          <span class="font-medium text-secondary group-hover:text-foreground transition-colors">Orders</span>
        </div>
      </a>
      
      <a href="/sales_report.php" class="group cursor-pointer">
        <div class="flex items-center rounded-xl p-3.5 gap-3 hover:bg-muted transition-all">
          <i data-lucide="bar-chart-3" class="size-5 text-secondary group-hover:text-foreground transition-colors"></i>
          <span class="font-medium text-secondary group-hover:text-foreground transition-colors">Sales Report</span>
        </div>
      </a>

      <p class="px-4 text-xs font-semibold text-secondary uppercase tracking-wider mb-2 mt-4">Settings</p>

      <a href="/seller_profile.php" class="group cursor-pointer">
        <div class="flex items-center rounded-xl p-3.5 gap-3 hover:bg-muted transition-all">
          <i data-lucide="store" class="size-5 text-secondary group-hover:text-foreground transition-colors"></i>
          <span class="font-medium text-secondary group-hover:text-foreground transition-colors">Shop Profile</span>
        </div>
      </a>
    </nav>

    <!-- Logout Area -->
    <div class="p-4 border-t border-border">
      <a href="/logout.php" class="group cursor-pointer">
        <div class="flex items-center rounded-xl p-3.5 gap-3 hover:bg-error/10 transition-all">
          <i data-lucide="log-out" class="size-5 text-secondary group-hover:text-error transition-colors"></i>
          <span class="font-medium text-secondary group-hover:text-error transition-colors">Logout</span>
        </div>
      </a>
    </div>
  </aside>

  <!-- Main Content Area -->
  <main class="flex-1 lg:ml-[280px] flex flex-col min-h-screen w-full">
    
    <!-- Top Navbar -->
    <header class="flex items-center justify-between h-[90px] border-b border-border bg-white px-5 md:px-8 sticky top-0 z-30">
      <div class="flex items-center gap-4">
        <button onclick="toggleSidebar()" class="lg:hidden size-11 flex items-center justify-center rounded-xl bg-muted hover:bg-border transition-colors cursor-pointer">
          <i data-lucide="menu" class="size-6 text-foreground"></i>
        </button>
        <h2 class="hidden sm:block font-bold text-2xl">Overview</h2>
      </div>

      <div class="flex items-center gap-4">
        

        

        <!-- Seller Profile -->
        <?php
          include 'seller_header_profile.php';
        ?>
      </div>
    </header>

    <!-- Dashboard Content -->
    <div class="flex-1 overflow-y-auto p-5 md:p-8">
      
      <!-- Analytics Summary Cards Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        
        <!-- Card 1: Total Revenue -->
        <div class="flex flex-col rounded-2xl border border-border p-6 gap-4 bg-white shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="size-12 bg-primary/10 rounded-xl flex items-center justify-center">
                <i data-lucide="dollar-sign" class="size-6 text-primary"></i>
              </div>
              <p class="font-medium text-secondary">Total Revenue</p>
            </div>
          </div>
          <div>
            <p class="font-bold text-3xl mb-1">$<?= number_format($total_revenue, 0, ",", ".") ?></p>
            <div class="flex items-center gap-1 text-sm">
              <span class="text-success flex items-center font-medium"><i data-lucide="trending-up" class="size-4 mr-1"></i> +12.5%</span>
              <span class="text-secondary">vs last month</span>
            </div>
          </div>
        </div>

        <!-- Card 2: Total Orders -->
        <div class="flex flex-col rounded-2xl border border-border p-6 gap-4 bg-white shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="size-12 bg-warning/10 rounded-xl flex items-center justify-center">
                <i data-lucide="shopping-bag" class="size-6 text-warning"></i>
              </div>
              <p class="font-medium text-secondary">Total Orders</p>
            </div>
          </div>
          <div>
            <p class="font-bold text-3xl mb-1"><?= number_format($total_orders, 0, ",", ".") ?></p>
            <div class="flex items-center gap-1 text-sm">
              <span class="text-success flex items-center font-medium"><i data-lucide="trending-up" class="size-4 mr-1"></i> +8.2%</span>
              <span class="text-secondary">vs last month</span>
            </div>
          </div>
        </div>

        <!-- Card 3: Active Products -->
        <div class="flex flex-col rounded-2xl border border-border p-6 gap-4 bg-white shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="size-12 bg-success/10 rounded-xl flex items-center justify-center">
                <i data-lucide="package" class="size-6 text-success"></i>
              </div>
              <p class="font-medium text-secondary">Active Listings</p>
            </div>
          </div>
          <div>
            <p class="font-bold text-3xl mb-1"><?= number_format($active_listings, 0, ",", ".") ?></p>
            <div class="flex items-center gap-1 text-sm">
              <span class="text-secondary flex items-center font-medium"><i data-lucide="minus" class="size-4 mr-1"></i> 0.0%</span>
              <span class="text-secondary">vs last month</span>
            </div>
          </div>
        </div>

        <!-- Card 4: Total Customers -->
        <div class="flex flex-col rounded-2xl border border-border p-6 gap-4 bg-white shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="size-12 bg-error/10 rounded-xl flex items-center justify-center">
                <i data-lucide="users" class="size-6 text-error"></i>
              </div>
              <p class="font-medium text-secondary">Total Customers</p>
            </div>
          </div>
          <div>
            <p class="font-bold text-3xl mb-1"><?= number_format($total_customers, 0, ",", ".") ?></p>
            <div class="flex items-center gap-1 text-sm">
              <span class="text-error flex items-center font-medium"><i data-lucide="trending-down" class="size-4 mr-1"></i> -2.4%</span>
              <span class="text-secondary">vs last month</span>
            </div>
          </div>
        </div>

      </div>

      <!-- Recent Sales Performance Table -->
      <div class="flex flex-col rounded-2xl border border-border bg-white shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <h3 class="font-bold text-lg">Recent Sales Performance</h3>
            <p class="text-sm text-secondary">Latest transactions from your store.</p>
          </div>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full min-w-[800px]">
            <thead class="bg-card-grey border-b border-border">
              <tr>
                <th class="text-left p-4 font-semibold text-sm text-secondary w-24">Order ID</th>
                <th class="text-left p-4 font-semibold text-sm text-secondary">Product Details</th>
                <th class="text-left p-4 font-semibold text-sm text-secondary hidden md:table-cell">Customer</th>
                <th class="text-left p-4 font-semibold text-sm text-secondary hidden lg:table-cell">Date</th>
                <th class="text-left p-4 font-semibold text-sm text-secondary">Amount</th>
                <th class="text-left p-4 font-semibold text-sm text-secondary">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border">
              
              <?php foreach ($recent_sales as $row): ?>
                <tr class="hover:bg-muted/50 transition-colors group" >
                  <td class="p-4 font-medium text-sm">#ORD-<?= $row['order_id'] ?></td>
                  <td class="p-4">
                    <div class="flex items-center gap-3">
                      <img src="<?= !empty($row['product_image']) ? 'storage/image/' . $row['product_image'] : 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=400&h=400&fit=crop' ?>" class="size-10 rounded-lg object-cover border border-border">
                      <div>
                        <p class="font-semibold text-sm"><?= $row['product_name'] ?></p>
                        <p class="text-xs text-secondary">Variant : <?= $row['variant'] ?></p>
                      </div>
                    </div>
                  </td>
                  <td class="p-4 hidden md:table-cell">
                    <p class="font-medium text-sm"><?= $row['buyer_name'] ?></p>
                    <p class="text-xs text-secondary"><?= $row['buyer_email'] ?></p>
                  </td>
                  <td class="p-4 hidden lg:table-cell text-sm text-secondary"><?= date('M d, Y', strtotime($row['transaction_date'])) ?></td>
                  <td class="p-4 font-semibold text-sm">$<?= number_format($row['amount'], 0, ",", ".") ?></td>
                  <td class="p-4">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full capitalize 
                                <?= ($row['status'] == 'pending') ? 'bg-warning/10 text-warning border border-warning/20' : '' ?>
                                <?= ($row['status'] == 'shipped') ? 'bg-primary/10 text-primary border border-primary/20' : '' ?>
                                <?= ($row['status'] == 'completed') ? 'bg-success/10 text-success border border-success/20' : '' ?>
                                <?= ($row['status'] == 'failed') ? 'bg-error/10 text-error border border-error/20' : '' ?>"><?= $row['status'] ?></span>
                  </td>
                  
                </tr>
              <?php endforeach; ?>

            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div class="p-4 border-t border-border flex flex-col sm:flex-row items-center justify-between gap-4 bg-card-grey">
          <p class="text-sm text-secondary">Showing <span class="font-medium text-foreground">1</span> to <span class="font-medium text-foreground">5</span> of <span class="font-medium text-foreground">24</span> results</p>
          <div class="flex items-center gap-1">
            <button class="size-8 flex items-center justify-center rounded-lg border border-border bg-white text-secondary hover:bg-muted disabled:opacity-50 cursor-pointer" disabled>
              <i data-lucide="chevron-left" class="size-4"></i>
            </button>
            <button class="size-8 flex items-center justify-center rounded-lg bg-primary text-white font-medium text-sm cursor-pointer">1</button>
            <button class="size-8 flex items-center justify-center rounded-lg border border-border bg-white text-secondary hover:bg-muted font-medium text-sm cursor-pointer">2</button>
            <button class="size-8 flex items-center justify-center rounded-lg border border-border bg-white text-secondary hover:bg-muted font-medium text-sm cursor-pointer hidden sm:flex">3</button>
            <span class="px-1 text-secondary hidden sm:block">...</span>
            <button class="size-8 flex items-center justify-center rounded-lg border border-border bg-white text-secondary hover:bg-muted cursor-pointer">
              <i data-lucide="chevron-right" class="size-4"></i>
            </button>
          </div>
        </div>
      </div>

    </div>
  </main>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
    <?php if (!empty($_SESSION['success'])): ?>
          
          
          showToast(<?php echo json_encode($_SESSION['success']); ?>, 'success');
          
          <?php 
              
              unset($_SESSION['success']); 
          ?>
          
      <?php endif; ?>
  });

  function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    sidebar.classList.toggle('-translate-x-full');
    
    if (sidebar.classList.contains('-translate-x-full')) {
      overlay.classList.add('hidden');
      setTimeout(() => overlay.classList.remove('opacity-100'), 10);
    } else {
      overlay.classList.remove('hidden');
      setTimeout(() => overlay.classList.add('opacity-100'), 10);
    }
  }

  // Toast Notification System
  function showToast(msg, type = 'success') {
    document.getElementById('toast')?.remove();
    
    const toast = document.createElement('div');
    toast.id = 'toast';
    
    const bgClass = type === 'success' ? 'bg-success' : 'bg-error';
    const icon = type === 'success' ? 'check-circle' : 'alert-circle';
    
    toast.className = `fixed bottom-6 right-6 ${bgClass} text-white px-5 py-4 rounded-xl shadow-2xl z-50 flex items-center gap-3 transition-all duration-300 opacity-0 translate-y-4`;
    
    toast.innerHTML = `
      <i data-lucide="${icon}" class="size-5"></i>
      <span class="font-medium text-sm">${msg}</span>
    `;
    
    document.body.appendChild(toast);
    lucide.createIcons();
    
    // Animate in
    requestAnimationFrame(() => {
      toast.classList.remove('opacity-0', 'translate-y-4');
    });
    
    // Auto remove
    setTimeout(() => {
      toast.classList.add('opacity-0', 'translate-y-4');
      setTimeout(() => toast.remove(), 300);
    }, 4000);
  }
</script>



</body>
</html>