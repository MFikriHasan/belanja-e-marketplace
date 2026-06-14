<?php
    require 'koneksi.php';
    include 'login_check.php';
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
      
      <a href="/dashboard_page.html" class="group cursor-pointer">
        <div class="flex items-center rounded-xl p-3.5 gap-3 hover:bg-muted transition-all">
          <i data-lucide="layout-dashboard" class="size-5 text-secondary group-hover:text-foreground transition-colors"></i>
          <span class="font-medium text-secondary group-hover:text-foreground transition-colors">Dashboard</span>
        </div>
      </a>
      
      <a href="/management_product.html" class="group cursor-pointer">
        <div class="flex items-center rounded-xl p-3.5 gap-3 hover:bg-muted transition-all">
          <i data-lucide="package" class="size-5 text-secondary group-hover:text-foreground transition-colors"></i>
          <span class="font-medium text-secondary group-hover:text-foreground transition-colors">Products</span>
        </div>
      </a>
      
      <a href="/management_transactions.html" class="group  cursor-pointer">
        <div class="flex items-center rounded-xl p-3.5 gap-3 hover:bg-muted transition-all">
          <i data-lucide="arrow-left-right" class="size-5 text-secondary group-hover:text-foreground transition-colors"></i>
          <span class="font-medium text-secondary group-hover:text-foreground transition-colors">Orders</span>
        </div>
      </a>
      
      <a href="/sales_report.html" class="group active cursor-pointer">
        <div class="flex items-center rounded-xl p-3.5 gap-3 group-[.active]:bg-primary/10 group-[.active]:text-primary hover:bg-muted transition-all">
          <i data-lucide="bar-chart-3" class="size-5 text-secondary group-[.active]:text-primary group-hover:text-foreground transition-colors"></i>
          <span class="font-medium text-secondary group-[.active]:font-semibold group-[.active]:text-primary group-hover:text-foreground transition-colors">Sales Report</span>
        </div>
      </a>

      <p class="px-4 text-xs font-semibold text-secondary uppercase tracking-wider mb-2 mt-4">Settings</p>

      <a href="/seller_profile_update.html" class="group cursor-pointer">
        <div class="flex items-center rounded-xl p-3.5 gap-3 hover:bg-muted transition-all">
          <i data-lucide="store" class="size-5 text-secondary group-hover:text-foreground transition-colors"></i>
          <span class="font-medium text-secondary group-hover:text-foreground transition-colors">Shop Profile</span>
        </div>
      </a>
    </nav>

    <!-- Logout Area -->
    <div class="p-4 border-t border-border">
      <a href="#" class="group cursor-pointer">
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
        <div class="flex items-center gap-3 cursor-pointer hover:opacity-80 transition-opacity">
          <div class="text-right hidden sm:block">
            <p class="font-semibold text-sm leading-tight">Blyad Store</p>
            <p class="text-secondary text-xs">blyad.store@example.com</p>
          </div>
          <div class="relative">
            <img src="https://images.unsplash.com/photo-1560179707-f14e90ef3623?w=100&h=100&fit=crop" alt="User Avatar" class="size-11 rounded-xl object-cover ring-2 ring-border">
            <!-- Online Status Indicator -->
            <span class="absolute bottom-0 right-0 size-3.5 bg-success border-2 border-white rounded-full" title="Online"></span>
          </div>
        </div>
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
                <button class="w-full text-left px-4 py-2.5 hover:bg-muted rounded-lg text-sm font-medium flex items-center gap-3 cursor-pointer transition-colors">
                  <div class="p-1.5 bg-error/10 rounded-md"><i data-lucide="file-text" class="size-4 text-error"></i></div>
                  Export as PDF
                </button>
                <button class="w-full text-left px-4 py-2.5 hover:bg-muted rounded-lg text-sm font-medium flex items-center gap-3 cursor-pointer transition-colors mt-1">
                  <div class="p-1.5 bg-success/10 rounded-md"><i data-lucide="table" class="size-4 text-success"></i></div>
                  Export as Excel
                </button>
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
            <span class="flex items-center gap-1 text-success text-xs font-semibold bg-success/10 px-2 py-1 rounded-full">
              <i data-lucide="trending-up" class="size-3"></i> +12.5%
            </span>
          </div>
          <p class="text-secondary text-sm font-medium mb-1">Total Revenue</p>
          <h4 class="text-2xl font-bold">$24.5K</h4>
        </div>
        
        <!-- Stat 2 -->
        <div class="bg-white rounded-2xl border border-border p-5 shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between mb-4">
            <div class="size-10 bg-warning/10 rounded-xl flex items-center justify-center">
              <i data-lucide="shopping-cart" class="size-5 text-warning"></i>
            </div>
            <span class="flex items-center gap-1 text-success text-xs font-semibold bg-success/10 px-2 py-1 rounded-full">
              <i data-lucide="trending-up" class="size-3"></i> +8.2%
            </span>
          </div>
          <p class="text-secondary text-sm font-medium mb-1">Total Orders</p>
          <h4 class="text-2xl font-bold">1,245</h4>
        </div>

        <!-- Stat 3 -->
        <div class="bg-white rounded-2xl border border-border p-5 shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between mb-4">
            <div class="size-10 bg-success/10 rounded-xl flex items-center justify-center">
              <i data-lucide="credit-card" class="size-5 text-success"></i>
            </div>
            <span class="flex items-center gap-1 text-error text-xs font-semibold bg-error/10 px-2 py-1 rounded-full">
              <i data-lucide="trending-down" class="size-3"></i> -2.4%
            </span>
          </div>
          <p class="text-secondary text-sm font-medium mb-1">Avg. Order Value</p>
          <h4 class="text-2xl font-bold">$85.20</h4>
        </div>

        <!-- Stat 4 -->
        <div class="bg-white rounded-2xl border border-border p-5 shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between mb-4">
            <div class="size-10 bg-purple-500/10 rounded-xl flex items-center justify-center">
              <i data-lucide="mouse-pointer-click" class="size-5 text-purple-500"></i>
            </div>
            <span class="flex items-center gap-1 text-success text-xs font-semibold bg-success/10 px-2 py-1 rounded-full">
              <i data-lucide="trending-up" class="size-3"></i> +4.1%
            </span>
          </div>
          <p class="text-secondary text-sm font-medium mb-1">Conversion Rate</p>
          <h4 class="text-2xl font-bold">3.8%</h4>
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
              <span class="text-2xl font-bold text-foreground">1.2K</span>
              <span class="text-xs text-secondary font-medium">Total Items</span>
            </div>
          </div>
          <div class="mt-4 grid grid-cols-2 gap-2">
            <div class="flex items-center gap-2 text-sm"><span class="size-3 rounded-full bg-primary"></span> Electronics</div>
            <div class="flex items-center gap-2 text-sm"><span class="size-3 rounded-full bg-success"></span> Clothing</div>
            <div class="flex items-center gap-2 text-sm"><span class="size-3 rounded-full bg-warning"></span> Home</div>
            <div class="flex items-center gap-2 text-sm"><span class="size-3 rounded-full bg-border"></span> Other</div>
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
                <th class="text-right p-4 font-semibold text-sm text-secondary">Units Sold</th>
                <th class="text-right p-4 font-semibold text-sm text-secondary">Revenue</th>
                <th class="text-center p-4 font-semibold text-sm text-secondary">Trend</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border">
              <tr class="hover:bg-muted/50 transition-colors">
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=100&h=100&fit=crop" class="size-10 rounded-lg object-cover border border-border">
                    <div>
                      <p class="font-semibold text-sm text-foreground">Wireless Noise-Cancelling Headphones</p>
                      <p class="text-xs text-secondary">Variant : Black</p>
                    </div>
                  </div>
                </td>
                <td class="p-4 text-sm text-secondary">Electronics</td>
                <td class="p-4 text-sm font-medium text-right">342</td>
                <td class="p-4 text-sm font-semibold text-right">$8,550.00</td>
                <td class="p-4 text-center">
                  <span class="inline-flex items-center justify-center bg-success/10 text-success rounded-full p-1">
                    <i data-lucide="arrow-up-right" class="size-4"></i>
                  </span>
                </td>
              </tr>
              <tr class="hover:bg-muted/50 transition-colors">
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=100&h=100&fit=crop" class="size-10 rounded-lg object-cover border border-border">
                    <div>
                      <p class="font-semibold text-sm text-foreground">Smart Watch Series 7</p>
                      <p class="text-xs text-secondary">Variant : White</p>
                    </div>
                  </div>
                </td>
                <td class="p-4 text-sm text-secondary">Electronics</td>
                <td class="p-4 text-sm font-medium text-right">215</td>
                <td class="p-4 text-sm font-semibold text-right">$6,450.00</td>
                <td class="p-4 text-center">
                  <span class="inline-flex items-center justify-center bg-success/10 text-success rounded-full p-1">
                    <i data-lucide="arrow-up-right" class="size-4"></i>
                  </span>
                </td>
              </tr>
              <tr class="hover:bg-muted/50 transition-colors">
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=100&h=100&fit=crop" class="size-10 rounded-lg object-cover border border-border">
                    <div>
                      <p class="font-semibold text-sm text-foreground">Running Shoes Pro Max</p>
                      <p class="text-xs text-secondary">Variant : Red</p>
                    </div>
                  </div>
                </td>
                <td class="p-4 text-sm text-secondary">Clothing</td>
                <td class="p-4 text-sm font-medium text-right">189</td>
                <td class="p-4 text-sm font-semibold text-right">$2,835.00</td>
                <td class="p-4 text-center">
                  <span class="inline-flex items-center justify-center bg-error/10 text-error rounded-full p-1">
                    <i data-lucide="arrow-down-right" class="size-4"></i>
                  </span>
                </td>
              </tr>
              <tr class="hover:bg-muted/50 transition-colors">
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <img src="https://images.unsplash.com/photo-1583394838336-acd977736f90?w=100&h=100&fit=crop" class="size-10 rounded-lg object-cover border border-border">
                    <div>
                      <p class="font-semibold text-sm text-foreground">Bluetooth Portable Speaker</p>
                      <p class="text-xs text-secondary">Variant : Grey</p>
                    </div>
                  </div>
                </td>
                <td class="p-4 text-sm text-secondary">Electronics</td>
                <td class="p-4 text-sm font-medium text-right">145</td>
                <td class="p-4 text-sm font-semibold text-right">$1,160.00</td>
                <td class="p-4 text-center">
                  <span class="inline-flex items-center justify-center bg-success/10 text-success rounded-full p-1">
                    <i data-lucide="arrow-up-right" class="size-4"></i>
                  </span>
                </td>
              </tr>
              <tr class="hover:bg-muted/50 transition-colors">
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <img src="https://images.unsplash.com/photo-1584916201218-f4242ceb4809?w=100&h=100&fit=crop" class="size-10 rounded-lg object-cover border border-border">
                    <div>
                      <p class="font-semibold text-sm text-foreground">Minimalist Desk Lamp</p>
                      <p class="text-xs text-secondary">Variant : Black</p>
                    </div>
                  </div>
                </td>
                <td class="p-4 text-sm text-secondary">Home</td>
                <td class="p-4 text-sm font-medium text-right">98</td>
                <td class="p-4 text-sm font-semibold text-right">$490.00</td>
                <td class="p-4 text-center">
                  <span class="inline-flex items-center justify-center bg-success/10 text-success rounded-full p-1">
                    <i data-lucide="arrow-up-right" class="size-4"></i>
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
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
    
    // 1. Revenue Line Chart
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    
    // Create gradient for line chart
    const gradient = ctxRevenue.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(22, 93, 255, 0.2)');
    gradient.addColorStop(1, 'rgba(22, 93, 255, 0)');

    new Chart(ctxRevenue, {
      type: 'line',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
          label: 'Revenue ($)',
          data: [2100, 3400, 2800, 4200, 3900, 5100, 4800],
          borderColor: '#165DFF',
          backgroundColor: gradient,
          borderWidth: 3,
          pointBackgroundColor: '#ffffff',
          pointBorderColor: '#165DFF',
          pointBorderWidth: 2,
          pointRadius: 4,
          pointHoverRadius: 6,
          fill: true,
          tension: 0.4 // Smooth curves
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
              callback: function(value) { return '$' + value / 1000 + 'k'; },
              padding: 10
            }
          },
          x: {
            grid: { display: false, drawBorder: false },
            border: { display: false },
            ticks: { padding: 10 }
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
      }
    });

    // 2. Category Doughnut Chart
    const ctxCategory = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctxCategory, {
      type: 'doughnut',
      data: {
        labels: ['Electronics', 'Clothing', 'Home', 'Other'],
        datasets: [{
          data: [45, 25, 20, 10],
          backgroundColor: [
            '#165DFF', // Primary
            '#00B42A', // Success
            '#FF7D00', // Warning
            '#E5E8EB'  // Border/Gray
          ],
          borderWidth: 0,
          hoverOffset: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '75%', // Make it thin
        plugins: {
          legend: { display: false }, // Custom legend built in HTML
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