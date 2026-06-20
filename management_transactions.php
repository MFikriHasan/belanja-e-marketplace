<?php
    require 'koneksi.php';
    include 'login_check.php';
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
    <div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 ms-5">
      
      <!-- Page Header & Actions -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
          <h3 class="font-bold text-2xl">Order Management</h3>
          <p class="text-secondary text-sm mt-1">Track and update customer orders.</p>
        </div>
        <div class="flex items-center gap-3">
          <button class="flex items-center gap-2 px-4 py-2.5 bg-white border border-border rounded-xl font-medium text-sm hover:bg-muted transition-colors shadow-sm">
            <i data-lucide="download" class="size-4"></i>
            Export CSV
          </button>
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
            <p class="font-bold text-2xl mt-1">24</p>
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
            <p class="font-bold text-2xl mt-1">45</p>
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
            <p class="font-bold text-2xl mt-1">18</p>
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
            <p class="font-bold text-2xl mt-1">1,284</p>
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
          
          <div class="flex items-center gap-2">
            <span class="text-sm text-secondary hidden sm:inline-block mr-2"><span id="selectedCount">0</span> selected</span>
            <button class="p-2 text-secondary hover:bg-muted rounded-lg border border-transparent hover:border-border transition-all" title="Print Selected">
              <i data-lucide="printer" class="size-4"></i>
            </button>
            <button class="p-2 text-error hover:bg-error/10 rounded-lg border border-transparent hover:border-error/20 transition-all" title="Delete Selected">
              <i data-lucide="trash-2" class="size-4"></i>
            </button>
          </div>
        </div>

        <!-- Responsive Table -->
        <div class="overflow-x-auto">
          <table class="w-full min-w-[800px] text-left border-collapse">
            <thead>
              <tr class="bg-muted/50 border-b border-border text-secondary text-sm">
                <th class="p-4 w-12 text-center">
                  <input type="checkbox" id="selectAll" class="rounded border-border text-primary focus:ring-primary/20 cursor-pointer size-4" onchange="toggleSelectAll(this)">
                </th>
                <th class="p-4 font-medium">Order & Customer</th>
                <th class="p-4 font-medium hidden md:table-cell">Date</th>
                <th class="p-4 font-medium hidden sm:table-cell">Total</th>
                <th class="p-4 font-medium">Status</th>
                <th class="p-4 font-medium">Actions</th>
              </tr>
            </thead>
            <tbody id="tableBody">
              <!-- Row 1 -->
              <tr class="border-b border-border hover:bg-muted/30 transition-colors group" data-item-id="ORD-1001" data-status="pending" data-searchable="ORD-1001 Sarah Jenkins sarah@example.com">
                <td class="p-4 text-center">
                  <input type="checkbox" class="row-checkbox rounded border-border text-primary focus:ring-primary/20 cursor-pointer size-4" onchange="updateSelectedCount()">
                </td>
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <div class="size-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm shrink-0">SJ</div>
                    <div>
                      <p class="font-semibold text-sm text-foreground">Sarah Jenkins</p>
                      <p class="text-xs text-secondary font-mono mt-0.5">#ORD-1001</p>
                    </div>
                  </div>
                </td>
                <td class="p-4 text-sm text-secondary hidden md:table-cell">Oct 24, 2023</td>
                <td class="p-4 font-medium hidden sm:table-cell">$124.50</td>
                <td class="p-4">
                  <select class="status-select text-xs font-medium px-3 py-1.5 rounded-full border-none focus:ring-2 focus:ring-offset-1 focus:ring-primary/30 bg-warning/10 text-warning" onchange="updateRowStatus(this)">
                    <option value="pending" selected>Pending</option>
                    <option value="shipped">Shipped</option>
                    <option value="success">Success</option>
                    <option value="failed">Failed</option>
                  </select>
                </td>
                <td class="p-4 text-right">
                  <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="viewTransaction('ORD-1001')" class="p-2 text-secondary hover:text-primary hover:bg-primary/10 rounded-lg transition-colors" title="View Details">
                      <i data-lucide="eye" class="size-4"></i>
                    </button>
                    <button onclick="showDeleteModal('ORD-1001')" class="p-2 text-secondary hover:text-error hover:bg-error/10 rounded-lg transition-colors" title="Delete">
                      <i data-lucide="trash-2" class="size-4"></i>
                    </button>
                  </div>
                </td>
              </tr>
              
              <!-- Row 2 -->
              <tr class="border-b border-border hover:bg-muted/30 transition-colors group" data-item-id="ORD-1002" data-status="processing" data-searchable="ORD-1002 Michael Chen michael@example.com">
                <td class="p-4 text-center">
                  <input type="checkbox" class="row-checkbox rounded border-border text-primary focus:ring-primary/20 cursor-pointer size-4" onchange="updateSelectedCount()">
                </td>
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop" class="size-10 rounded-full object-cover shrink-0">
                    <div>
                      <p class="font-semibold text-sm text-foreground">Michael Chen</p>
                      <p class="text-xs text-secondary font-mono mt-0.5">#ORD-1002</p>
                    </div>
                  </div>
                </td>
                <td class="p-4 text-sm text-secondary hidden md:table-cell">Oct 24, 2023</td>
                <td class="p-4 font-medium hidden sm:table-cell">$89.00</td>
                <td class="p-4">
                  <select class="status-select text-xs font-medium px-3 py-1.5 rounded-full border-none focus:ring-2 focus:ring-offset-1 focus:ring-primary/30 bg-primary/10 text-primary" onchange="updateRowStatus(this)">
                    <option value="pending">Pending</option>
                    <option value="shipped" selected>Shipped</option>
                    <option value="success">Success</option>
                    <option value="failed">Failed</option>
                  </select>
                </td>
                <td class="p-4 text-right">
                  <div class="flex items-center  gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="viewTransaction('ORD-1002')" class="p-2 text-secondary hover:text-primary hover:bg-primary/10 rounded-lg transition-colors" title="View Details">
                      <i data-lucide="eye" class="size-4"></i>
                    </button>
                    <button onclick="showDeleteModal('ORD-1002')" class="p-2 text-secondary hover:text-error hover:bg-error/10 rounded-lg transition-colors" title="Delete">
                      <i data-lucide="trash-2" class="size-4"></i>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- Row 3 -->
              <tr class="border-b border-border hover:bg-muted/30 transition-colors group" data-item-id="ORD-1003" data-status="shipped" data-searchable="ORD-1003 Emma Wilson emma@example.com">
                <td class="p-4 text-center">
                  <input type="checkbox" class="row-checkbox rounded border-border text-primary focus:ring-primary/20 cursor-pointer size-4" onchange="updateSelectedCount()">
                </td>
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&h=100&fit=crop" class="size-10 rounded-full object-cover shrink-0">
                    <div>
                      <p class="font-semibold text-sm text-foreground">Emma Wilson</p>
                      <p class="text-xs text-secondary font-mono mt-0.5">#ORD-1003</p>
                    </div>
                  </div>
                </td>
                <td class="p-4 text-sm text-secondary hidden md:table-cell">Oct 23, 2023</td>
                <td class="p-4 font-medium hidden sm:table-cell">$342.20</td>
                <td class="p-4">
                  <select class="status-select text-xs font-medium px-3 py-1.5 rounded-full border-none focus:ring-2 focus:ring-offset-1 focus:ring-primary/30 bg-success/10 text-success" onchange="updateRowStatus(this)">
                    <option value="pending">Pending</option>
                    <option value="shipped">Shipped</option>
                    <option value="success" selected>Success</option>
                    <option value="failed">Failed</option>
                  </select>
                </td>
                <td class="p-4 text-right">
                  <div class="flex items-center  gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="viewTransaction('ORD-1003')" class="p-2 text-secondary hover:text-primary hover:bg-primary/10 rounded-lg transition-colors" title="View Details">
                      <i data-lucide="eye" class="size-4"></i>
                    </button>
                    <button onclick="showDeleteModal('ORD-1003')" class="p-2 text-secondary hover:text-error hover:bg-error/10 rounded-lg transition-colors" title="Delete">
                      <i data-lucide="trash-2" class="size-4"></i>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- Row 4 -->
              <tr class="border-b border-border hover:bg-muted/30 transition-colors group" data-item-id="ORD-1004" data-status="completed" data-searchable="ORD-1004 David Rodriguez david@example.com">
                <td class="p-4 text-center">
                  <input type="checkbox" class="row-checkbox rounded border-border text-primary focus:ring-primary/20 cursor-pointer size-4" onchange="updateSelectedCount()">
                </td>
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <div class="size-10 rounded-full bg-success/10 text-success flex items-center justify-center font-bold text-sm shrink-0">DR</div>
                    <div>
                      <p class="font-semibold text-sm text-foreground">David Rodriguez</p>
                      <p class="text-xs text-secondary font-mono mt-0.5">#ORD-1004</p>
                    </div>
                  </div>
                </td>
                <td class="p-4 text-sm text-secondary hidden md:table-cell">Oct 22, 2023</td>
                <td class="p-4 font-medium hidden sm:table-cell">$56.00</td>
                <td class="p-4">
                  <select class="status-select text-xs font-medium px-3 py-1.5 rounded-full border-none focus:ring-2 focus:ring-offset-1 focus:ring-primary/30 bg-warning/10 text-warning" onchange="updateRowStatus(this)">
                    <option value="pending" selected>Pending</option>
                    <option value="shipped">shipped</option>
                    <option value="success">Success</option>
                    <option value="failed">Failed</option>
                  </select>
                </td>
                <td class="p-4 text-right">
                  <div class="flex items-center  gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="viewTransaction('ORD-1004')" class="p-2 text-secondary hover:text-primary hover:bg-primary/10 rounded-lg transition-colors" title="View Details">
                      <i data-lucide="eye" class="size-4"></i>
                    </button>
                    <button onclick="showDeleteModal('ORD-1004')" class="p-2 text-secondary hover:text-error hover:bg-error/10 rounded-lg transition-colors" title="Delete">
                      <i data-lucide="trash-2" class="size-4"></i>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- Row 5 -->
              <tr class="hover:bg-muted/30 transition-colors group" data-item-id="ORD-1005" data-status="pending" data-searchable="ORD-1005 Lisa Wang lisa@example.com">
                <td class="p-4 text-center">
                  <input type="checkbox" class="row-checkbox rounded border-border text-primary focus:ring-primary/20 cursor-pointer size-4" onchange="updateSelectedCount()">
                </td>
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&h=100&fit=crop" class="size-10 rounded-full object-cover shrink-0">
                    <div>
                      <p class="font-semibold text-sm text-foreground">Lisa Wang</p>
                      <p class="text-xs text-secondary font-mono mt-0.5">#ORD-1005</p>
                    </div>
                  </div>
                </td>
                <td class="p-4 text-sm text-secondary hidden md:table-cell">Oct 24, 2023</td>
                <td class="p-4 font-medium hidden sm:table-cell">$890.00</td>
                <td class="p-4">
                  <select class="status-select text-xs font-medium px-3 py-1.5 rounded-full border-none focus:ring-2 focus:ring-offset-1 focus:ring-primary/30 bg-error/10 text-error" onchange="updateRowStatus(this)">
                    <option value="pending">Pending</option>
                    <option value="shipped">Shipped</option>
                    <option value="success">Success</option>
                    <option value="failed" selected>Failed</option>
                  </select>
                </td>
                <td class="p-4 text-right">
                  <div class="flex items-center  gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="viewTransaction('ORD-1005')" class="p-2 text-secondary hover:text-primary hover:bg-primary/10 rounded-lg transition-colors" title="View Details">
                      <i data-lucide="eye" class="size-4"></i>
                    </button>
                    <button onclick="showDeleteModal('ORD-1005')" class="p-2 text-secondary hover:text-error hover:bg-error/10 rounded-lg transition-colors" title="Delete">
                      <i data-lucide="trash-2" class="size-4"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
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

<!-- Modals -->



<!-- Detail Modal -->
<div id="detail-modal" class="fixed inset-0 bg-black/50 z-[100] hidden items-center justify-center p-4 backdrop-blur-sm">
  <div class="bg-white rounded-2xl w-full max-w-lg max-h-[90vh] flex flex-col shadow-2xl overflow-hidden">
    <div class="flex items-center justify-between p-5 border-b border-border bg-card-grey/30">
      <div>
        <h3 class="font-bold text-lg flex items-center gap-2">
          Order Details
          <span id="detailStatusBadge" class="text-[10px] uppercase tracking-wider font-bold px-2 py-0.5 rounded-full bg-warning/10 text-warning">Pending</span>
        </h3>
        <p id="detailOrderId" class="text-sm text-secondary font-mono mt-0.5">#ORD-XXXX</p>
      </div>
      <button onclick="closeDetailModal()" class="p-2 text-secondary hover:bg-muted rounded-full transition-colors">
        <i data-lucide="x" class="size-5"></i>
      </button>
    </div>
    
    <div class="flex-1 overflow-y-auto p-5 space-y-6">
      <!-- Customer Info -->
      <div>
        <h4 class="text-xs font-semibold text-secondary uppercase tracking-wider mb-3">Customer Information</h4>
        <div class="flex items-center gap-4 p-4 rounded-xl border border-border bg-card-grey/30">
          <img id="detailAvatar" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&h=100&fit=crop" class="size-12 rounded-full object-cover">
          <div>
            <p id="detailName" class="font-semibold text-foreground">Customer Name</p>
            <p id="detailEmail" class="text-sm text-secondary">customer@example.com</p>
          </div>
        </div>
      </div>

      <!-- Order Items -->
      <div>
        <h4 class="text-xs font-semibold text-secondary uppercase tracking-wider mb-3">Order Items</h4>
        <div class="border border-border rounded-xl overflow-hidden">
          <div class="flex items-center justify-between p-3 border-b border-border bg-card-grey/30 text-sm">
            <div class="flex items-center gap-3">
              <div class="size-10 bg-muted rounded-lg flex items-center justify-center"><i data-lucide="package" class="size-5 text-secondary"></i></div>
              <div>
                <p class="font-medium">Wireless Headphones Pro</p>
                <p class="text-xs text-secondary">Qty: 1</p>
              </div>
            </div>
            <p class="font-medium">$124.50</p>
          </div>
          <div class="p-3 bg-muted/20 flex justify-between items-center text-sm">
            <span class="text-secondary">Shipping</span>
            <span class="font-medium">$0.00</span>
          </div>
          <div class="p-3 bg-card-grey/50 flex justify-between items-center border-t border-border">
            <span class="font-semibold">Total</span>
            <span id="detailTotal" class="font-bold text-primary text-lg">$124.50</span>
          </div>
        </div>
      </div>
      
      <!-- Shipping Address -->
      <div>
        <h4 class="text-xs font-semibold text-secondary uppercase tracking-wider mb-3">Shipping Address</h4>
        <div class="p-4 rounded-xl border border-border bg-card-grey/30 text-sm text-secondary leading-relaxed">
          <p class="font-medium text-foreground mb-1">Home Address</p>
          <p>123 Commerce Street, Suite 400</p>
          <p>San Francisco, CA 94107</p>
          <p>United States</p>
        </div>
      </div>
    </div>
    
    <div class="p-5 border-t border-border bg-card-grey/30 flex justify-end gap-3">
      <button onclick="closeDetailModal()" class="px-5 py-2.5 rounded-xl font-medium text-secondary hover:bg-muted transition-colors">Close</button>
      <button onclick="showToast('Successfully Printed!', 'success')" class="px-5 py-2.5 rounded-xl font-medium bg-primary text-white hover:bg-primary-hover transition-colors shadow-sm shadow-primary/30">Print Invoice</button>
    </div>
  </div>
</div>

<!-- Delete Modal -->
<div id="delete-modal" class="fixed inset-0 bg-black/50 z-[100] hidden items-center justify-center p-4 backdrop-blur-sm">
  <div class="bg-white rounded-2xl w-full max-w-sm p-6 text-center shadow-2xl">
    <div class="size-14 bg-error/10 rounded-full flex items-center justify-center mx-auto mb-4">
      <i data-lucide="alert-triangle" class="size-7 text-error"></i>
    </div>
    <h3 class="text-xl font-bold mb-2">Delete Transaction?</h3>
    <p class="text-secondary text-sm mb-6">This action cannot be undone. This will permanently remove the order record.</p>
    <div class="flex gap-3">
      <button onclick="closeDeleteModal()" class="flex-1 py-2.5 rounded-xl font-medium border border-border text-secondary hover:bg-muted transition-colors">Cancel</button>
      <button onclick="confirmDelete()" class="flex-1 py-2.5 rounded-xl font-medium bg-error text-white hover:bg-error/90 transition-colors shadow-sm shadow-error/30">Delete</button>
    </div>
  </div>
</div>

<script>
// --- Data & State ---
const transactionsData = {
  'ORD-1001': { name: 'Sarah Jenkins', email: 'sarah@example.com', total: '$124.50', status: 'pending', avatar: '' },
  'ORD-1002': { name: 'Michael Chen', email: 'michael@example.com', total: '$89.00', status: 'success', avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop' },
  'ORD-1003': { name: 'Emma Wilson', email: 'emma@example.com', total: '$342.20', status: 'shipped', avatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&h=100&fit=crop' },
  'ORD-1004': { name: 'David Rodriguez', email: 'david@example.com', total: '$56.00', status: 'pending', avatar: '' },
  'ORD-1005': { name: 'Lisa Wang', email: 'lisa@example.com', total: '$890.00', status: 'failed', avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&h=100&fit=crop' }
};

let itemToDelete = null;

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
  else if (val === 'success') selectElement.classList.add('bg-success/10', 'text-success');
  else if (val === 'failed') selectElement.classList.add('bg-error/10', 'text-error');

  // Update data attribute for filtering
  row.dataset.status = val;
  
  // Update internal data if needed
  if(transactionsData[orderId]) transactionsData[orderId].status = val;

  showToast(`Order ${orderId} marked as ${val}`, 'success');
}

// --- Filtering Logic ---
document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('searchInput')?.addEventListener('input', applyFilters);
  document.getElementById('statusFilter')?.addEventListener('change', applyFilters);
});

function applyFilters() {
  const search = (document.getElementById('searchInput')?.value || '').toLowerCase().trim();
  const status = document.getElementById('statusFilter')?.value || 'all';

  document.querySelectorAll('tr[data-item-id]').forEach(row => {
    const text = (row.dataset.searchable || '').toLowerCase();
    const rowStatus = row.dataset.status;
    
    const matchesSearch = search === '' || text.includes(search);
    const matchesStatus = status === 'all' || rowStatus === status;
    
    row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
  });
}

// --- Detail Modal Logic ---
function viewTransaction(id) {
  const data = transactionsData[id];
  if (!data) return;
  
  document.getElementById('detailOrderId').textContent = `#${id}`;
  document.getElementById('detailName').textContent = data.name;
  document.getElementById('detailEmail').textContent = data.email;
  document.getElementById('detailTotal').textContent = data.total;
  
  const avatarEl = document.getElementById('detailAvatar');
  if (data.avatar) {
    avatarEl.src = data.avatar;
    avatarEl.classList.remove('hidden');
  } else {
    // Fallback to UI avatar if no image (simplified for this demo)
    avatarEl.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(data.name)}&background=165DFF&color=fff`;
  }

  // Update badge in modal
  const badge = document.getElementById('detailStatusBadge');
  badge.textContent = data.status;
  badge.className = 'text-[10px] uppercase tracking-wider font-bold px-2 py-0.5 rounded-full';
  if (data.status === 'pending') badge.classList.add('bg-warning/10', 'text-warning');
  else if (data.status === 'processing') badge.classList.add('bg-primary/10', 'text-primary');
  else if (data.status === 'shipped') badge.classList.add('bg-indigo-100', 'text-indigo-700');
  else if (data.status === 'completed') badge.classList.add('bg-success/10', 'text-success');

  document.getElementById('detail-modal').classList.remove('hidden');
  document.getElementById('detail-modal').classList.add('flex');
}

function closeDetailModal() {
  document.getElementById('detail-modal').classList.add('hidden');
  document.getElementById('detail-modal').classList.remove('flex');
}

// --- Delete Logic ---
function showDeleteModal(id) {
  itemToDelete = id;
  document.getElementById('delete-modal').classList.remove('hidden');
  document.getElementById('delete-modal').classList.add('flex');
}

function closeDeleteModal() {
  itemToDelete = null;
  document.getElementById('delete-modal').classList.add('hidden');
  document.getElementById('delete-modal').classList.remove('flex');
}

function confirmDelete() {
  if (itemToDelete) {
    const row = document.querySelector(`tr[data-item-id="${itemToDelete}"]`);
    if (row) row.remove();
    showToast(`Order ${itemToDelete} deleted`, 'success');
  }
  closeDeleteModal();
}

// --- Bulk Actions Logic ---
function toggleSelectAll(source) {
  const checkboxes = document.querySelectorAll('.row-checkbox');
  checkboxes.forEach(cb => {
    // Only check visible rows
    if(cb.closest('tr').style.display !== 'none') {
      cb.checked = source.checked;
    }
  });
  updateSelectedCount();
}

function updateSelectedCount() {
  const count = document.querySelectorAll('.row-checkbox:checked').length;
  document.getElementById('selectedCount').textContent = count;
  
  // Update select all checkbox state
  const totalVisible = Array.from(document.querySelectorAll('.row-checkbox')).filter(cb => cb.closest('tr').style.display !== 'none').length;
  const selectAllCb = document.getElementById('selectAll');
  if(totalVisible > 0) {
    selectAllCb.checked = count === totalVisible;
    selectAllCb.indeterminate = count > 0 && count < totalVisible;
  } else {
    selectAllCb.checked = false;
    selectAllCb.indeterminate = false;
  }
}



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