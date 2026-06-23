<?php
    require 'koneksi.php';
    include 'login_check.php';
    check_access_control('seller');

    $seller_id = $_SESSION['seller_id'];

    // total pending shipping status
    $query_pending = "SELECT COUNT(id) AS total_pending 
                      FROM transaction_det 
                      WHERE shipping_status = ? 
                      AND seller_id = ?";
    $pre = $koneksi->prepare($query_pending);
    $pending = 'pending';
    $pre->bind_param("si", $pending, $seller_id);
    $pre->execute();
    $result_pending = $pre->get_result()->fetch_assoc();
    
    // total shipped shipping status
    $query_shipped = "SELECT COUNT(id) AS total_shipped 
                      FROM transaction_det 
                      WHERE shipping_status = ?
                      AND seller_id = ?";
    $pre = $koneksi->prepare($query_shipped);
    $shipped = 'shipped';
    $pre->bind_param("si", $shipped, $seller_id);
    $pre->execute();
    $result_shipped = $pre->get_result()->fetch_assoc();


    // total failed shipping status
    $query_failed = "SELECT COUNT(id) AS total_failed 
                    FROM transaction_det 
                    WHERE shipping_status = ?
                    AND seller_id = ?";
    $pre = $koneksi->prepare($query_failed);
    $failed = 'failed';
    $pre->bind_param("si", $failed, $seller_id);
    $pre->execute();
    $result_failed = $pre->get_result()->fetch_assoc();


    // total completed shipping status
    $query_completed = "SELECT COUNT(id) AS total_completed 
                        FROM transaction_det 
                        WHERE shipping_status = ?
                        AND seller_id = ?";
    $pre = $koneksi->prepare($query_completed);
    $completed = 'completed';
    $pre->bind_param("si", $completed, $seller_id);
    $pre->execute();
    $result_completed = $pre->get_result()->fetch_assoc();

    // order list query
    $query_order = "SELECT 
                    td.id,
                    td.shipping_status,
                    td.subtotal,
                    b.avatar AS buyer_avatar,
                    b.name AS buyer_name,
                    t.date AS transaction_date
                    FROM transaction_det td
                    JOIN transaction t ON t.id = td.transaction_id
                    JOIN buyer b ON b.id = t.buyer_id
                    WHERE td.seller_id = ?
                    ORDER BY (td.shipping_status = 'pending') DESC, t.date DESC";

    $pre = $koneksi->prepare($query_order);
    $pre->bind_param("i", $seller_id);
    $pre->execute();
    $orders = $pre->get_result()->fetch_all(MYSQLI_ASSOC);
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Seller Transactions Dashboard</title>
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
    --muted: #EFF2F7;
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
  
  /* Custom Select Arrow hiding for status badge look */
  .status-select {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.5rem center;
    background-size: 1em;
    padding-right: 2rem !important;
  }
</style>
</head>
<body class="font-sans bg-muted min-h-screen text-foreground">

<!-- Sidebar Overlay -->
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
      
      <a href="/dashboard.php" class="group cursor-pointer">
        <div class="flex items-center rounded-xl p-3.5 gap-3 hover:bg-muted transition-all">
          <i data-lucide="layout-dashboard" class="size-5 text-secondary group-hover:text-foreground transition-colors"></i>
          <span class="font-medium text-secondary group-hover:text-foreground transition-colors">Dashboard</span>
        </div>
      </a>
      
      <a href="/management_product.php" class="group cursor-pointer">
        <div class="flex items-center rounded-xl p-3.5 gap-3 hover:bg-muted transition-all">
          <i data-lucide="package" class="size-5 text-secondary group-hover:text-foreground transition-colors"></i>
          <span class="font-medium text-secondary group-hover:text-foreground transition-colors">Products</span>
        </div>
      </a>
      
      <a href="/management_transactions.php" class="group active cursor-pointer">
        <div class="flex items-center rounded-xl p-3.5 gap-3 group-[.active]:bg-primary/10 group-[.active]:text-primary hover:bg-muted transition-all">
          <i data-lucide="arrow-left-right" class="size-5 text-secondary group-[.active]:text-primary group-hover:text-foreground transition-colors"></i>
          <span class="font-medium text-secondary group-[.active]:font-semibold group-[.active]:text-primary group-hover:text-foreground transition-colors">Orders</span>
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
      <a href="logout.php" class="group cursor-pointer">
        <div class="flex items-center rounded-xl p-3.5 gap-3 hover:bg-error/10 transition-all">
          <i data-lucide="log-out" class="size-5 text-secondary group-hover:text-error transition-colors"></i>
          <span class="font-medium text-secondary group-hover:text-error transition-colors">Logout</span>
        </div>
      </a>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 lg:ml-[260px] flex flex-col min-h-screen bg-muted/30">
    
    <!-- Top Navbar -->
    <header class="flex items-center justify-between h-[90px] border-b border-border bg-white px-5 md:px-8 sticky top-0 z-30">
      <div class="flex items-center gap-4 ms-5">
        <button onclick="toggleSidebar()" class="lg:hidden size-11 flex items-center justify-center rounded-xl bg-muted hover:bg-border transition-colors cursor-pointer">
          <i data-lucide="menu" class="size-6 text-foreground"></i>
        </button>
        <h2 class="hidden sm:block font-bold text-2xl ">Transactions</h2>
      </div>

      <div class="flex items-center gap-4">
        

        

        <!-- Seller Profile -->
        <?php
          include 'seller_header_profile.php';
        ?>
      </div>
    </header>

    <!-- Page Content -->
    <div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 ms-5">
      
      <!-- Page Header & Actions -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
          <h3 class="font-bold text-2xl">Order Management</h3>
          <p class="text-secondary text-sm mt-1">Track and update customer orders.</p>
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Pending -->
        <div class="bg-white p-5 rounded-2xl border border-border shadow-sm flex flex-col gap-3">
          <div class="flex items-center justify-between">
            <div class="size-10 bg-warning/10 rounded-xl flex items-center justify-center">
              <i data-lucide="clock" class="size-5 text-warning"></i>
            </div>
            <span class="text-xs font-medium text-secondary bg-muted px-2 py-1 rounded-md">Needs Action</span>
          </div>
          <div>
            <p class="text-secondary text-sm font-medium">Pending Orders</p>
            <p class="font-bold text-2xl mt-1"><?= $result_pending['total_pending'] ?></p>
          </div>
        </div>

        

        <!-- Shipped -->
        <div class="bg-white p-5 rounded-2xl border border-border shadow-sm flex flex-col gap-3">
          <div class="flex items-center justify-between">
            <div class="size-10 bg-indigo-100 rounded-xl flex items-center justify-center">
              <i data-lucide="truck" class="size-5 text-indigo-600"></i>
            </div>
          </div>
          <div>
            <p class="text-secondary text-sm font-medium">Shipped</p>
            <p class="font-bold text-2xl mt-1"><?= $result_shipped['total_shipped'] ?></p>
          </div>
        </div>


        <!-- Failed -->
        <div class="bg-white p-5 rounded-2xl border border-border shadow-sm flex flex-col gap-3">
          <div class="flex items-center justify-between">
            <div class="size-10 bg-error/10 rounded-xl flex items-center justify-center">
              <i data-lucide="circle-x" class="size-5 text-error"></i>
            </div>
          </div>
          <div>
            <p class="text-secondary text-sm font-medium">Failed</p>
            <p class="font-bold text-2xl mt-1"><?= $result_failed['total_failed'] ?></p>
          </div>
        </div>


        <!-- Completed -->
        <div class="bg-white p-5 rounded-2xl border border-border shadow-sm flex flex-col gap-3">
          <div class="flex items-center justify-between">
            <div class="size-10 bg-success/10 rounded-xl flex items-center justify-center">
              <i data-lucide="check-circle-2" class="size-5 text-success"></i>
            </div>
          </div>
          <div>
            <p class="text-secondary text-sm font-medium">Completed</p>
            <p class="font-bold text-2xl mt-1"><?= $result_completed['total_completed'] ?></p>
          </div>
        </div>
      </div>

      <!-- Table Section -->
      <div class="bg-white rounded-2xl border border-border shadow-sm flex flex-col overflow-hidden">
        
        <!-- Table Controls -->
        <div class="p-4 border-b border-border flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-card-grey/50">
          <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-64">
              <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-secondary"></i>
              <input type="text" id="searchInput" placeholder="Search orders..." class="w-full pl-9 pr-4 py-2 bg-white border border-border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
            </div>
            <select id="statusFilter" class="bg-white border border-border rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 cursor-pointer">
              <option value="all">All Status</option>
              <option value="pending">Pending</option>
              <option value="shipped">Shipped</option>
              <option value="success">Success</option>
              <option value="failed">Failed</option>
            </select>
          </div>
        </div>

        <!-- Responsive Table -->
        <div class="overflow-x-auto">
          <table class="w-full min-w-[800px] text-left border-collapse">
            <thead>
              <tr class="bg-muted/50 border-b border-border text-secondary text-sm">
              <th class="p-4 font-medium">Order & Customer</th>
                <th class="p-4 font-medium hidden md:table-cell">Date</th>
                <th class="p-4 font-medium hidden sm:table-cell">SubTotal</th>
                <th class="p-4 font-medium ">Shipping Status</th>
              </tr>
            </thead>
            <tbody id="tableBody">
              <?php foreach ($orders as $row): ?>
                <tr class="border-b border-border hover:bg-muted/30 transition-colors group">
                  <td class="p-4">
                    <div class="flex items-center gap-3">
                      <img src="<?= !empty($product['buyer_avatar']) ? 'storage/image/' . $product['seller_logo'] : 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop'; ?>" class="size-10 rounded-full object-cover shrink-0">
                      <div>
                        <p class="font-semibold text-sm text-foreground"><?= $row['buyer_name'] ?></p>
                        <p class="text-xs text-secondary font-mono mt-0.5">#ORD-<?= $row['id'] ?></p>
                      </div>
                    </div>
                  </td>
                  <td class="p-4 text-sm text-secondary hidden md:table-cell"><?= date('d M Y', strtotime($row['transaction_date'])) ?></td>
                  <td class="p-4 font-medium hidden sm:table-cell">$<?= number_format($row['subtotal'], 0, ",", ".") ?></td>
                  <td class="py-4 px-6">
                    <form action="update_shipping_status.php" method="post" class="flex items-center gap-2">
                      <input type="hidden" name="transaction_det_id" value="<?= $row['id'] ?>">
                      <select name="shipping_status" class="status-select text-xs font-medium px-3 py-1.5       rounded-full border-none focus:ring-2 focus:ring-offset-1 focus:ring-primary/30 
                        <?= ($row['shipping_status'] == 'pending') ? 'bg-warning/10 text-warning' : '' ?>
                        <?= ($row['shipping_status'] == 'shipped') ? 'bg-primary/10 text-primary' : '' ?>
                        <?= ($row['shipping_status'] == 'completed') ? 'bg-success/10 text-success' : '' ?>
                        <?= ($row['shipping_status'] == 'failed') ? 'bg-error/10 text-error' : '' ?>" 
                        onchange="updateRowStatus(this)">
                        
                        <option value="pending" <?= ($row['shipping_status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                        <option value="shipped" <?= ($row['shipping_status'] == 'shipped') ? 'selected' : '' ?>>Shipped</option>
                        <option value="completed" <?= ($row['shipping_status'] == 'completed') ? 'selected' : '' ?>>Completed</option>
                        <option value="failed" <?= ($row['shipping_status'] == 'failed') ? 'selected' : '' ?>>Failed</option>
                      </select>
                      <button name="submit" type="submit" class="px-2 py-1 bg-primary text-white text-xs rounded-lg hover:bg-primary/90">
                        Save
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php if (empty($orders)): ?>
            <div  class="flex flex-col items-center justify-center py-16 bg-white rounded-2xl border border-border text-center px-4">
              <div class="w-16 h-16 bg-muted rounded-full flex items-center justify-center mb-4">
                <i data-lucide="package-x" class="w-8 h-8 text-secondary"></i>
              </div>
              <h3 class="text-lg font-bold mb-1">No orders waiting in line yet.</h3>
              <p class="text-secondary text-sm mb-4">Boost your visibility! Running a limited-time discount or updating your product keywords can help catch a customer's eye.</p>
            </div>
          <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <div class="p-4 border-t border-border flex flex-col sm:flex-row items-center justify-between gap-4 bg-white">
          <p class="text-sm text-secondary">Showing <span class="font-medium text-foreground">1</span> to <span class="font-medium text-foreground">5</span> of <span class="font-medium text-foreground">1,371</span> results</p>
          <div class="flex items-center gap-1">
            <button class="p-2 rounded-lg border border-border text-secondary hover:bg-muted disabled:opacity-50 disabled:cursor-not-allowed" disabled>
              <i data-lucide="chevron-left" class="size-4"></i>
            </button>
            <button class="w-8 h-8 rounded-lg bg-primary text-white font-medium text-sm flex items-center justify-center">1</button>
            <button class="w-8 h-8 rounded-lg border border-transparent text-secondary hover:bg-muted font-medium text-sm flex items-center justify-center">2</button>
            <button class="w-8 h-8 rounded-lg border border-transparent text-secondary hover:bg-muted font-medium text-sm flex items-center justify-center">3</button>
            <span class="text-secondary px-1 hidden sm:inline">...</span>
            <button class="w-8 h-8 rounded-lg border border-transparent text-secondary hover:bg-muted font-medium text-sm hidden sm:flex items-center justify-center">28</button>
            <button class="p-2 rounded-lg border border-border text-secondary hover:bg-muted">
              <i data-lucide="chevron-right" class="size-4"></i>
            </button>
          </div>
        </div>
      </div>

    </div>
  </main>
</div>
<script>

// --- UI Interactions ---
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('-translate-x-full');
  document.getElementById('sidebar-overlay').classList.toggle('hidden');
}

// --- Status Update Logic ---
function updateRowStatus(selectElement) {
  const val = selectElement.value;
  const row = selectElement.closest('tr');
  const orderId = row.dataset.itemId;
  
  // Reset classes
  selectElement.className = 'status-select text-xs font-medium px-3 py-1.5 rounded-full border-none focus:ring-2 focus:ring-offset-1 focus:ring-primary/30 transition-colors';
  
  // Apply new color classes based on value
  if (val === 'pending') selectElement.classList.add('bg-warning/10', 'text-warning');
  if (val === 'shipped') selectElement.classList.add('bg-primary/10', 'text-primary');
  else if (val === 'completed') selectElement.classList.add('bg-success/10', 'text-success');
  else if (val === 'failed') selectElement.classList.add('bg-error/10', 'text-error');

  // Update data attribute for filtering
  row.dataset.status = val;
  
  
}


document.addEventListener('DOMContentLoaded', () => {
  <?php if (!empty($_SESSION['success'])): ?>
          
          
          showToast(<?php echo json_encode($_SESSION['success']); ?>, 'success');
          
          <?php 
              
              unset($_SESSION['success']); 
          ?>
          
    <?php endif; ?>
});
// --- Toast Notification ---
function showToast(msg, type='success') {
  document.getElementById('toast')?.remove();
  const t = document.createElement('div');
  t.id = 'toast';
  
  let icon = type === 'success' ? 'check-circle' : 'alert-circle';
  let colorClass = type === 'success' ? 'bg-success' : 'bg-error';
  
  t.className = `fixed bottom-4 right-4 ${colorClass} text-white px-4 py-3 rounded-xl shadow-lg z-[200] flex items-center gap-3 transition-all duration-300 opacity-0 translate-y-4`;
  t.innerHTML = `<i data-lucide="${icon}" class="size-5"></i><span class="font-medium text-sm">${msg}</span>`;
  
  document.body.appendChild(t);
  lucide.createIcons({ root: t });
  
  requestAnimationFrame(() => {
    t.classList.remove('opacity-0', 'translate-y-4');
    t.classList.add('opacity-100', 'translate-y-0');
  });
  
  setTimeout(() => {
    t.classList.add('opacity-0', 'translate-y-4');
    t.classList.remove('opacity-100', 'translate-y-0');
    setTimeout(() => t.remove(), 300);
  }, 3000);
}
</script>
</body>
</html>