<?php
    require 'koneksi.php';
    include 'login_check.php';
    check_access_control('seller');

    $seller_id = $_SESSION['seller_id'];


    // top selling products
    $q_products = $koneksi->prepare(
              "SELECT p.name, 
                      p.price, 
                      cv.color_name, 
                      cv.product_image, 
                      c.name AS category_name,
                      SUM(td.qty) AS total_qty, 
                      SUM(td.subtotal) AS total_revenue
              FROM transaction_det td
              JOIN color_varian cv ON cv.id = td.color_varian_id 
              JOIN product p ON p.id = cv.product_id             
              JOIN category c ON c.id = p.category_id
              WHERE td.seller_id = ?
              GROUP BY cv.id                                    
              ORDER BY total_qty DESC
              LIMIT 10"
          );
    $q_products->bind_param("i", $seller_id);
    $q_products->execute();
    $top_products = $q_products->get_result()->fetch_all(MYSQLI_ASSOC);


    // total revenue stats dll
    $q_revenue = $koneksi->prepare(
        "SELECT COALESCE(SUM(td.subtotal), 0) AS total_revenue,
                COUNT(DISTINCT td.transaction_id) AS total_orders,
                COALESCE(SUM(td.qty), 0) AS total_units
         FROM transaction_det td
         WHERE td.seller_id = ?"
    );
    $q_revenue->bind_param("i", $seller_id);
    $q_revenue->execute();
    $stats = $q_revenue->get_result()->fetch_assoc();

    $total_revenue = (int)$stats['total_revenue'];
    $total_orders  = (int)$stats['total_orders'];
    $total_units   = (int)$stats['total_units'];
    $avg_order     = $total_orders > 0 ? $total_revenue / $total_orders : 0;

    // daily revenue chart.js
    $q_daily = $koneksi->prepare(
        "SELECT DATE(t.date) AS day, COALESCE(SUM(td.subtotal), 0) AS revenue
         FROM transaction t
         JOIN transaction_det td ON td.transaction_id = t.id
         WHERE td.seller_id = ? AND t.date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
         GROUP BY DATE(t.date)
         ORDER BY DATE(t.date) ASC"
    );
    $q_daily->bind_param("i", $seller_id);
    $q_daily->execute();
    $daily_result = $q_daily->get_result();

    
    $daily_labels  = [];
    $daily_revenue = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-{$i} days"));
        $daily_labels[$date]  = date('D', strtotime($date));
        $daily_revenue[$date] = 0;
    }
    while ($row = $daily_result->fetch_assoc()) {
        if (isset($daily_revenue[$row['day']])) {
            $daily_revenue[$row['day']] = (int)$row['revenue'];
        }
    }

    // sales by category chart
    $q_category = $koneksi->prepare(
        "SELECT c.name AS category_name, SUM(td.qty) AS total_qty
         FROM transaction_det td
         JOIN product p ON p.id = td.product_id
         JOIN category c ON c.id = p.category_id
         WHERE td.seller_id = ?
         GROUP BY c.id
         ORDER BY total_qty DESC"
    );
    $q_category->bind_param("i", $seller_id);
    $q_category->execute();
    $category_result = $q_category->get_result()->fetch_all(MYSQLI_ASSOC);

    $cat_labels  = array_column($category_result, 'category_name');
    $cat_values  = array_column($category_result, 'total_qty');
    $cat_total   = array_sum($cat_values) ?: 1;
    $cat_pcts    = array_map(fn($v) => round($v / $cat_total * 100), $cat_values);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Seller Sales Report</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js" onload="window.lucideLoaded=true; if(window.initLucide) window.initLucide();"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
</style>
</head>
<body class="font-sans bg-muted min-h-screen text-foreground">

<!-- Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden transition-opacity" onclick="toggleSidebar()"></div>

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
      
      <a href="/management_transactions.php" class="group  cursor-pointer">
        <div class="flex items-center rounded-xl p-3.5 gap-3 hover:bg-muted transition-all">
          <i data-lucide="arrow-left-right" class="size-5 text-secondary group-hover:text-foreground transition-colors"></i>
          <span class="font-medium text-secondary group-hover:text-foreground transition-colors">Orders</span>
        </div>
      </a>
      
      <a href="/sales_report.php" class="group active cursor-pointer">
        <div class="flex items-center rounded-xl p-3.5 gap-3 group-[.active]:bg-primary/10 group-[.active]:text-primary hover:bg-muted transition-all">
          <i data-lucide="bar-chart-3" class="size-5 text-secondary group-[.active]:text-primary group-hover:text-foreground transition-colors"></i>
          <span class="font-medium text-secondary group-[.active]:font-semibold group-[.active]:text-primary group-hover:text-foreground transition-colors">Sales Report</span>
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
  <main class="flex-1 lg:ml-[280px] flex flex-col min-h-screen">
    <!-- Top Navbar -->
    <header class="flex items-center justify-between h-[90px] border-b border-border bg-white px-5 md:px-8 sticky top-0 z-30">
      <div class="flex items-center gap-4">
        <button onclick="toggleSidebar()" class="lg:hidden size-11 flex items-center justify-center rounded-xl bg-muted hover:bg-border transition-colors cursor-pointer">
          <i data-lucide="menu" class="size-6 text-foreground"></i>
        </button>
        <h2 class="hidden sm:block font-bold text-2xl ">Sales Report</h2>
      </div>

      <div class="flex items-center gap-4">
        

        

        <!-- User Profile -->
        <?php
          include 'seller_header_profile.php';
        ?>
      </div>
    </header>

    <!-- Page Content -->
    <div class="flex-1 overflow-y-auto p-5 md:p-8">
      
      <!-- Top Filter Bar -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
          <h3 class="text-2xl font-bold">Overview</h3>
          <p class="text-secondary text-sm mt-1">Track your store's performance and revenue.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
          <!-- Date Range Filter -->
          <div class="relative w-full sm:w-auto">
            <select class="w-full sm:w-auto appearance-none bg-white border border-border rounded-xl pl-4 pr-10 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary cursor-pointer shadow-sm">
              <option value="weekly">This Week</option>
              <option value="monthly" selected>This Month</option>
              <option value="yearly">This Year</option>
            </select>
            <i data-lucide="calendar" class="absolute right-3 top-1/2 -translate-y-1/2 size-4 text-secondary pointer-events-none"></i>
          </div>

          <!-- Export Dropdown -->
          <div class="relative w-full sm:w-auto" id="exportDropdownContainer">
            <button onclick="toggleExportMenu()" class="w-full sm:w-auto flex items-center justify-center gap-2 bg-primary text-white px-5 py-2.5 rounded-xl font-medium hover:bg-primary-hover transition-colors shadow-sm shadow-primary/20 cursor-pointer">
              <i data-lucide="download" class="size-4"></i>
              <span>Export</span>
              <i data-lucide="chevron-down" class="size-4 ml-1"></i>
            </button>
            
            <!-- Dropdown Menu -->
            <div id="exportMenu" class="hidden absolute right-0 top-full mt-2 w-48 bg-white border border-border rounded-xl shadow-lg z-20 overflow-hidden transform origin-top-right transition-all">
              <div class="p-1">
                <a href="export_pdf.php" target="_blank" class="w-full text-left px-4 py-2.5 hover:bg-muted rounded-lg text-sm font-medium flex items-center gap-3 cursor-pointer transition-colors">
                  <div class="p-1.5 bg-error/10 rounded-md"><i data-lucide="file-text" class="size-4 text-error"></i></div>
                  Export as PDF
                </a>
                <a href="export_excel.php" class="w-full text-left px-4 py-2.5 hover:bg-muted rounded-lg text-sm font-medium flex items-center gap-3 cursor-pointer transition-colors mt-1">
                  <div class="p-1.5 bg-success/10 rounded-md"><i data-lucide="table" class="size-4 text-success"></i></div>
                  Export as Excel
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Stats Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        <!-- Stat 1 -->
        <div class="bg-white rounded-2xl border border-border p-5 shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between mb-4">
            <div class="size-10 bg-primary/10 rounded-xl flex items-center justify-center">
              <i data-lucide="dollar-sign" class="size-5 text-primary"></i>
            </div>
          </div>
          <p class="text-secondary text-sm font-medium mb-1">Total Revenue</p>
          <h4 class="text-2xl font-bold">$<?= number_format($total_revenue, 0, ",", ".") ?></h4>
        </div>
        
        <!-- Stat 2 -->
        <div class="bg-white rounded-2xl border border-border p-5 shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between mb-4">
            <div class="size-10 bg-warning/10 rounded-xl flex items-center justify-center">
              <i data-lucide="shopping-cart" class="size-5 text-warning"></i>
            </div>
          </div>
          <p class="text-secondary text-sm font-medium mb-1">Total Orders</p>
          <h4 class="text-2xl font-bold"><?= number_format($total_orders, 0, ",", ".") ?></h4>
        </div>


        <!-- Stat 3 -->
        <div class="bg-white rounded-2xl border border-border p-5 shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between mb-4">
            <div class="size-10 bg-purple-500/10 rounded-xl flex items-center justify-center">
              <i data-lucide="package" class="size-5 text-purple-500"></i>
            </div>
          </div>
          <p class="text-secondary text-sm font-medium mb-1">Total Units</p>
          <h4 class="text-2xl font-bold"><?= number_format($total_units, 0, ",", ".") ?></h4>
        </div>


        <!-- Stat 4 -->
        <div class="bg-white rounded-2xl border border-border p-5 shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between mb-4">
            <div class="size-10 bg-success/10 rounded-xl flex items-center justify-center">
              <i data-lucide="credit-card" class="size-5 text-success"></i>
            </div>
          </div>
          <p class="text-secondary text-sm font-medium mb-1">Avg. Order Value</p>
          <h4 class="text-2xl font-bold">$<?= number_format($avg_order, 0, ",", ".") ?></h4>
        </div>

        
      </div>

      <!-- Charts Section -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Main Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-border p-6 shadow-sm">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h3 class="font-bold text-lg">Revenue Trend</h3>
              <p class="text-sm text-secondary">Daily revenue for the current period</p>
            </div>
          </div>
          <div class="h-[300px] w-full relative">
            <canvas id="revenueChart"></canvas>
          </div>
        </div>

        <!-- Secondary Chart -->
        <div class="bg-white rounded-2xl border border-border p-6 shadow-sm flex flex-col">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h3 class="font-bold text-lg">Sales by Category</h3>
              <p class="text-sm text-secondary">Top performing categories</p>
            </div>
          </div>
          <div class="flex-1 flex items-center justify-center relative min-h-[250px]">
            <canvas id="categoryChart"></canvas>
            <!-- Center Text for Doughnut -->
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-4">
              <span class="text-2xl font-bold text-foreground"><?= number_format($total_units) ?></span>
              <span class="text-xs text-secondary font-medium">Total Units</span>
            </div>
          </div>
          <div class="mt-4 grid grid-cols-2 gap-2">
            <?php
            $cat_colors = ['bg-primary','bg-success','bg-warning','bg-error','bg-purple-500','bg-border'];
            foreach ($cat_labels as $ci => $cat_label):
            ?>
            <div class="flex items-center gap-2 text-sm">
              <span class="size-3 rounded-full <?= $cat_colors[$ci % count($cat_colors)] ?>"></span>
              <?= htmlspecialchars($cat_label) ?> (<?= $cat_pcts[$ci] ?? 0 ?>%)
            </div>
            <?php endforeach; ?>
            <?php if (empty($cat_labels)): ?>
              <div class="col-span-2 text-center text-secondary text-sm mt-2">No category sales yet.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Summary Table -->
      <div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <h3 class="font-bold text-lg">Top Selling Products</h3>
            <p class="text-sm text-secondary">Products generating the most revenue</p>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full min-w-[700px]">
            <thead class="bg-card-grey border-b border-border">
              <tr>
                <th class="text-left p-4 font-semibold text-sm text-secondary">Product Name</th>
                <th class="text-left p-4 font-semibold text-sm text-secondary">Category</th>
                <th class="text-center p-4 font-semibold text-sm text-secondary">Price</th>
                <th class="text-center p-4 font-semibold text-sm text-secondary">Units Sold</th>
                <th class="text-center p-4 font-semibold text-sm text-secondary">Revenue</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border">
              <?php foreach ($top_products as $row): ?>
                <tr class="hover:bg-muted/50 transition-colors">
                  <td class="p-4">
                    <div class="flex items-center gap-3">
                      <img src="<?= 'storage/image/'.$row['product_image'] ?>" class="size-10 rounded-lg object-cover border border-border">
                      <div>
                        <p class="font-semibold text-sm text-foreground"><?= $row['name'] ?></p>
                        <p class="text-xs text-secondary">Variant : <?= $row['color_name'] ?></p>
                      </div>
                    </div>
                  </td>
                  <td class="p-4 text-sm text-secondary text-left"><?= $row['category_name'] ?></td>
                  <td class="p-4 text-center text-sm font-semibold">$<?= number_format($row['price'], 0, ",", ".") ?></td>
                  <td class="p-4 text-sm font-medium text-center"><?= number_format($row['total_qty'], 0, ",", ".") ?></td>
                  <td class="p-4 text-sm font-semibold text-center">$<?= number_format($row['total_revenue'], 0, ",", ".") ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php if (empty($top_products)): ?>
            <div  class="flex flex-col items-center justify-center py-16 bg-white rounded-2xl border border-border text-center px-4">
              <div class="w-16 h-16 bg-muted rounded-full flex items-center justify-center mb-4">
                <i data-lucide="package-x" class="w-8 h-8 text-secondary"></i>
              </div>
              <h3 class="text-lg font-bold mb-1">No top products yet.</h3>
              <p class="text-secondary text-sm mb-4">Try promoting products or running a discount to boost sales and see them on this list.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </main>
</div>





<script>
  // Sidebar Toggle
  function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('-translate-x-full');
    document.getElementById('sidebar-overlay').classList.toggle('hidden');
  }

  // Export Menu Toggle
  function toggleExportMenu() {
    const menu = document.getElementById('exportMenu');
    menu.classList.toggle('hidden');
  }

  // Close export menu when clicking outside
  document.addEventListener('click', function(event) {
    const container = document.getElementById('exportDropdownContainer');
    const menu = document.getElementById('exportMenu');
    if (container && !container.contains(event.target) && !menu.classList.contains('hidden')) {
      menu.classList.add('hidden');
    }
  });

  

  // Initialize Charts
    initCharts();
  

  

  // Chart.js Initialization
  function initCharts() {
    // Common Chart Options
    Chart.defaults.font.family = "'Poppins', sans-serif";
    Chart.defaults.color = '#6A7686';
    
    // Data dari PHP
    const revenueLabels  = <?= json_encode(array_values($daily_labels)) ?>;
    const revenueData    = <?= json_encode(array_values($daily_revenue)) ?>;
    const categoryLabels = <?= json_encode($cat_labels) ?>;
    const categoryData   = <?= json_encode($cat_pcts) ?>;

    // 1. Revenue Line Chart
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    
    // Create gradient for line chart
    const gradient = ctxRevenue.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(22, 93, 255, 0.2)');
    gradient.addColorStop(1, 'rgba(22, 93, 255, 0)');

    new Chart(ctxRevenue, {
      type: 'line',
      data: {
        labels: revenueLabels,
        datasets: [{
          label: 'Revenue ($)',
          data: revenueData,
          borderColor: '#165DFF',
          backgroundColor: gradient,
          borderWidth: 3,
          pointBackgroundColor: '#ffffff',
          pointBorderColor: '#165DFF',
          pointBorderWidth: 2,
          pointRadius: 4,
          pointHoverRadius: 6,
          fill: true,
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#080C1A',
            padding: 12,
            titleFont: { size: 13, weight: '600' },
            bodyFont: { size: 14, weight: '500' },
            displayColors: false,
            callbacks: {
              label: function(context) {
                return '$' + context.parsed.y.toLocaleString();
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: '#E5E8EB', drawBorder: false },
            border: { display: false },
            ticks: {
              callback: function(value) { return '$' + value.toLocaleString(); },
              padding: 10
            }
          },
          x: {
            grid: { display: false, drawBorder: false },
            border: { display: false },
            ticks: { padding: 10 }
          }
        },
        interaction: { intersect: false, mode: 'index' },
      }
    });

    // 2. Category Doughnut Chart
    const ctxCategory = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctxCategory, {
      type: 'doughnut',
      data: {
        labels: categoryLabels.length ? categoryLabels : ['No Data'],
        datasets: [{
          data: categoryData.length ? categoryData : [100],
          backgroundColor: ['#165DFF','#00B42A','#FF7D00','#F53F3F','#8B5CF6','#E5E8EB'],
          borderWidth: 0,
          hoverOffset: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '75%',
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#080C1A',
            padding: 12,
            callbacks: {
              label: function(context) {
                return ' ' + context.label + ': ' + context.parsed + '%';
              }
            }
          }
        }
      }
    });
  }
</script>


</body>
</html>