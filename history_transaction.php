<?php
    require 'koneksi.php';
    include 'login_check.php';
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
<nav class="sticky top-0 z-40 bg-white border-b border-border shadow-sm">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16 items-center gap-4">
      
      <!-- Logo -->
      <a href="index.html" class="flex-shrink-0 flex items-center gap-2 text-primary">
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
        <a href="/cart.html" class="p-2 text-secondary hover:text-primary hover:bg-muted rounded-full transition-colors relative cursor-pointer">
          <i data-lucide="shopping-cart" class="h-6 w-6"></i>
          <span class="absolute top-1 right-1 bg-red-500 text-white text-[10px] font-bold h-4 min-w-[16px] px-1 flex items-center justify-center rounded-full border-2 border-white">3</span>
        </a>
        
        <!-- Notifications -->
        <a href="history_transaction.html" class=" p-2 text-primary bg-muted rounded-full transition-colors relative cursor-pointer">
          <i data-lucide="bell" class="h-6 w-6"></i>
          <span class="absolute top-1 right-1 bg-red-500 text-white text-[10px] font-bold h-2 w-2 rounded-full border-2 border-white"></span>
        </a>
        
        <!-- User Profile -->
         <a href="/buyer_update_profile.html">
           <div class="h-9 w-9 rounded-full bg-muted overflow-hidden border border-border cursor-pointer ml-2 hover:ring-2 hover:ring-primary hover:ring-offset-2 transition-all" onclick="showToast('Profile menu clicked', 'success')">
             <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop" alt="User" class="w-full h-full object-cover">
           </div>
         </a>
      </div>

    </div>
  </div>
</nav>

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
            <button onclick="filterByTab('status', 'all')" data-filter-type="status" data-filter-value="all" class="tab-btn py-3 px-4 border-b-2 border-primary text-primary font-semibold whitespace-nowrap transition-colors">All Orders</button>
            <button onclick="filterByTab('status', 'pending')" data-filter-type="status" data-filter-value="pending" class="tab-btn py-3 px-4 border-b-2 border-transparent text-secondary hover:text-foreground whitespace-nowrap transition-colors">Pending</button>
            <button onclick="filterByTab('status', 'success')" data-filter-type="status" data-filter-value="success" class="tab-btn py-3 px-4 border-b-2 border-transparent text-secondary hover:text-foreground whitespace-nowrap transition-colors">Success</button>
            <button onclick="filterByTab('status', 'failed')" data-filter-type="status" data-filter-value="failed" class="tab-btn py-3 px-4 border-b-2 border-transparent text-secondary hover:text-foreground whitespace-nowrap transition-colors">Failed</button>
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
        
        <!-- Item 1 -->
        <div data-item-id="ORD-8472" data-status="success" data-searchable="ord-8472 sony wh-1000xm5 headphones tech haven" class="bg-white border border-border rounded-2xl p-4 md:p-5 flex flex-col md:flex-row gap-4 md:gap-6 items-start md:items-center hover:shadow-md transition-shadow">
          <div class="flex items-start gap-4 w-full md:w-auto md:flex-1">
            <img src="https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=200&h=200&fit=crop" class="size-20 md:size-24 rounded-xl object-cover border border-border shrink-0">
            <div class="flex-1 min-w-0 pt-1">
              <div class="flex items-center gap-2 mb-1.5">
                <span class="text-xs font-semibold text-secondary bg-muted px-2 py-0.5 rounded-md">ORD-8472</span>
                <span class="text-xs text-secondary">Oct 24, 2023</span>
              </div>
              <h4 class="font-semibold text-foreground text-base md:text-lg truncate">Sony WH-1000XM5 Headphones</h4>
              <div class="flex items-center gap-1 mt-1">
                <i data-lucide="store" class="size-3.5 text-secondary"></i>
                <p class="text-sm text-secondary truncate">Tech Haven Official</p>
              </div>
            </div>
          </div>
          
          <div class="flex flex-row md:flex-col items-center md:items-end justify-between w-full md:w-auto gap-2 md:gap-1 border-t border-border md:border-t-0 pt-4 md:pt-0">
            <div class="flex flex-col md:items-end">
              <span class="text-xs text-secondary mb-0.5 md:hidden">Total</span>
              <span class="font-bold text-lg md:text-xl text-foreground">$348.00</span>
            </div>
            <span class="bg-success/10 text-success text-xs font-semibold px-2.5 py-1 rounded-full flex items-center gap-1">
              <i data-lucide="check-circle-2" class="size-3"></i> Success
            </span>
          </div>
          
          <div class="flex items-center gap-2 w-full md:w-auto mt-2 md:mt-0">
            <button onclick="viewItem('ORD-8472')" class="flex-1 md:flex-none px-4 py-2.5 bg-white border border-border text-foreground text-sm font-semibold rounded-xl hover:bg-muted hover:border-secondary transition-all text-center cursor-pointer">Details</button>
            <button onclick="showToast('Added to cart', 'success')" class="flex-1 md:flex-none px-4 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-primary-hover shadow-sm shadow-primary/20 transition-all text-center cursor-pointer">Buy Again</button>
          </div>
        </div>

        <!-- Item 2 -->
        <div data-item-id="ORD-8471" data-status="pending" data-searchable="ord-8471 minimalist ceramic vase home decor studio" class="bg-white border border-border rounded-2xl p-4 md:p-5 flex flex-col md:flex-row gap-4 md:gap-6 items-start md:items-center hover:shadow-md transition-shadow">
          <div class="flex items-start gap-4 w-full md:w-auto md:flex-1">
            <img src="https://images.unsplash.com/photo-1578500494198-246f612d3b3d?w=200&h=200&fit=crop" class="size-20 md:size-24 rounded-xl object-cover border border-border shrink-0">
            <div class="flex-1 min-w-0 pt-1">
              <div class="flex items-center gap-2 mb-1.5">
                <span class="text-xs font-semibold text-secondary bg-muted px-2 py-0.5 rounded-md">ORD-8471</span>
                <span class="text-xs text-secondary">Oct 22, 2023</span>
              </div>
              <h4 class="font-semibold text-foreground text-base md:text-lg truncate">Minimalist Ceramic Vase</h4>
              <div class="flex items-center gap-1 mt-1">
                <i data-lucide="store" class="size-3.5 text-secondary"></i>
                <p class="text-sm text-secondary truncate">Home Decor Studio</p>
              </div>
            </div>
          </div>
          
          <div class="flex flex-row md:flex-col items-center md:items-end justify-between w-full md:w-auto gap-2 md:gap-1 border-t border-border md:border-t-0 pt-4 md:pt-0">
            <div class="flex flex-col md:items-end">
              <span class="text-xs text-secondary mb-0.5 md:hidden">Total</span>
              <span class="font-bold text-lg md:text-xl text-foreground">$45.00</span>
            </div>
            <span class="bg-warning/10 text-warning text-xs font-semibold px-2.5 py-1 rounded-full flex items-center gap-1">
              <i data-lucide="clock" class="size-3"></i> Pending
            </span>
          </div>
          
          <div class="flex items-center gap-2 w-full md:w-auto mt-2 md:mt-0">
            <button onclick="viewItem('ORD-8471')" class="flex-1 md:flex-none px-4 py-2.5 bg-white border border-border text-foreground text-sm font-semibold rounded-xl hover:bg-muted hover:border-secondary transition-all text-center cursor-pointer">Details</button>
            <button onclick="showToast('Payment reminder sent', 'success')" class="flex-1 md:flex-none px-4 py-2.5 bg-warning text-white text-sm font-semibold rounded-xl hover:bg-warning/90 shadow-sm shadow-warning/20 transition-all text-center cursor-pointer">Pay Now</button>
          </div>
        </div>

        <!-- Item 3 -->
        <div data-item-id="ORD-8465" data-status="success" data-searchable="ord-8465 organic cotton t-shirt essential wear" class="bg-white border border-border rounded-2xl p-4 md:p-5 flex flex-col md:flex-row gap-4 md:gap-6 items-start md:items-center hover:shadow-md transition-shadow">
          <div class="flex items-start gap-4 w-full md:w-auto md:flex-1">
            <img src="https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=200&h=200&fit=crop" class="size-20 md:size-24 rounded-xl object-cover border border-border shrink-0">
            <div class="flex-1 min-w-0 pt-1">
              <div class="flex items-center gap-2 mb-1.5">
                <span class="text-xs font-semibold text-secondary bg-muted px-2 py-0.5 rounded-md">ORD-8465</span>
                <span class="text-xs text-secondary">Oct 15, 2023</span>
              </div>
              <h4 class="font-semibold text-foreground text-base md:text-lg truncate">Organic Cotton T-Shirt</h4>
              <div class="flex items-center gap-1 mt-1">
                <i data-lucide="store" class="size-3.5 text-secondary"></i>
                <p class="text-sm text-secondary truncate">Essential Wear</p>
              </div>
            </div>
          </div>
          
          <div class="flex flex-row md:flex-col items-center md:items-end justify-between w-full md:w-auto gap-2 md:gap-1 border-t border-border md:border-t-0 pt-4 md:pt-0">
            <div class="flex flex-col md:items-end">
              <span class="text-xs text-secondary mb-0.5 md:hidden">Total</span>
              <span class="font-bold text-lg md:text-xl text-foreground">$28.50</span>
            </div>
            <span class="bg-success/10 text-success text-xs font-semibold px-2.5 py-1 rounded-full flex items-center gap-1">
              <i data-lucide="check-circle-2" class="size-3"></i> Success
            </span>
          </div>
          
          <div class="flex items-center gap-2 w-full md:w-auto mt-2 md:mt-0">
            <button onclick="viewItem('ORD-8465')" class="flex-1 md:flex-none px-4 py-2.5 bg-white border border-border text-foreground text-sm font-semibold rounded-xl hover:bg-muted hover:border-secondary transition-all text-center cursor-pointer">Details</button>
            <button onclick="showToast('Added to cart', 'success')" class="flex-1 md:flex-none px-4 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-primary-hover shadow-sm shadow-primary/20 transition-all text-center cursor-pointer">Buy Again</button>
          </div>
        </div>

        <!-- Item 4 -->
        <div data-item-id="ORD-8450" data-status="failed" data-searchable="ord-8450 smart fitness watch fitgear pro" class="bg-white border border-border rounded-2xl p-4 md:p-5 flex flex-col md:flex-row gap-4 md:gap-6 items-start md:items-center hover:shadow-md transition-shadow">
          <div class="flex items-start gap-4 w-full md:w-auto md:flex-1">
            <img src="https://images.unsplash.com/photo-1579586337278-3befd40fd17a?w=200&h=200&fit=crop" class="size-20 md:size-24 rounded-xl object-cover border border-border shrink-0 opacity-70 grayscale">
            <div class="flex-1 min-w-0 pt-1">
              <div class="flex items-center gap-2 mb-1.5">
                <span class="text-xs font-semibold text-secondary bg-muted px-2 py-0.5 rounded-md">ORD-8450</span>
                <span class="text-xs text-secondary">Oct 10, 2023</span>
              </div>
              <h4 class="font-semibold text-foreground text-base md:text-lg truncate">Smart Fitness Watch</h4>
              <div class="flex items-center gap-1 mt-1">
                <i data-lucide="store" class="size-3.5 text-secondary"></i>
                <p class="text-sm text-secondary truncate">FitGear Pro</p>
              </div>
            </div>
          </div>
          
          <div class="flex flex-row md:flex-col items-center md:items-end justify-between w-full md:w-auto gap-2 md:gap-1 border-t border-border md:border-t-0 pt-4 md:pt-0">
            <div class="flex flex-col md:items-end">
              <span class="text-xs text-secondary mb-0.5 md:hidden">Total</span>
              <span class="font-bold text-lg md:text-xl text-secondary line-through">$129.00</span>
            </div>
            <span class="bg-error/10 text-error text-xs font-semibold px-2.5 py-1 rounded-full flex items-center gap-1">
              <i data-lucide="x-circle" class="size-3"></i> Failed
            </span>
          </div>
          
          <div class="flex items-center gap-2 w-full md:w-auto mt-2 md:mt-0">
            <button onclick="viewItem('ORD-8450')" class="flex-1 md:flex-none px-4 py-2.5 bg-white border border-border text-foreground text-sm font-semibold rounded-xl hover:bg-muted hover:border-secondary transition-all text-center cursor-pointer">Details</button>
            <button onclick="showToast('Item no longer available', 'error')" class="flex-1 md:flex-none px-4 py-2.5 bg-muted text-secondary text-sm font-semibold rounded-xl cursor-not-allowed text-center">Unavailable</button>
          </div>
        </div>

        <!-- Item 5 -->
        <div data-item-id="ORD-8422" data-status="success" data-searchable="ord-8422 artisan coffee beans roast masters" class="bg-white border border-border rounded-2xl p-4 md:p-5 flex flex-col md:flex-row gap-4 md:gap-6 items-start md:items-center hover:shadow-md transition-shadow">
          <div class="flex items-start gap-4 w-full md:w-auto md:flex-1">
            <img src="https://images.unsplash.com/photo-1559525839-b184a4d698c7?w=200&h=200&fit=crop" class="size-20 md:size-24 rounded-xl object-cover border border-border shrink-0">
            <div class="flex-1 min-w-0 pt-1">
              <div class="flex items-center gap-2 mb-1.5">
                <span class="text-xs font-semibold text-secondary bg-muted px-2 py-0.5 rounded-md">ORD-8422</span>
                <span class="text-xs text-secondary">Oct 02, 2023</span>
              </div>
              <h4 class="font-semibold text-foreground text-base md:text-lg truncate">Artisan Coffee Beans (1kg)</h4>
              <div class="flex items-center gap-1 mt-1">
                <i data-lucide="store" class="size-3.5 text-secondary"></i>
                <p class="text-sm text-secondary truncate">Roast Masters</p>
              </div>
            </div>
          </div>
          
          <div class="flex flex-row md:flex-col items-center md:items-end justify-between w-full md:w-auto gap-2 md:gap-1 border-t border-border md:border-t-0 pt-4 md:pt-0">
            <div class="flex flex-col md:items-end">
              <span class="text-xs text-secondary mb-0.5 md:hidden">Total</span>
              <span class="font-bold text-lg md:text-xl text-foreground">$32.00</span>
            </div>
            <span class="bg-success/10 text-success text-xs font-semibold px-2.5 py-1 rounded-full flex items-center gap-1">
              <i data-lucide="check-circle-2" class="size-3"></i> Success
            </span>
          </div>
          
          <div class="flex items-center gap-2 w-full md:w-auto mt-2 md:mt-0">
            <button onclick="viewItem('ORD-8422')" class="flex-1 md:flex-none px-4 py-2.5 bg-white border border-border text-foreground text-sm font-semibold rounded-xl hover:bg-muted hover:border-secondary transition-all text-center cursor-pointer">Details</button>
            <button onclick="showToast('Added to cart', 'success')" class="flex-1 md:flex-none px-4 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-primary-hover shadow-sm shadow-primary/20 transition-all text-center cursor-pointer">Buy Again</button>
          </div>
        </div>

      </div>

      <!-- Empty State (Hidden by default) -->
      <div id="noResults" class="hidden flex-col items-center justify-center py-16 text-center">
        <div class="size-20 bg-muted rounded-full flex items-center justify-center mb-4">
          <i data-lucide="search-x" class="size-10 text-secondary"></i>
        </div>
        <h3 class="text-lg font-bold text-foreground mb-1">No transactions found</h3>
        <p class="text-secondary max-w-sm">We couldn't find any orders matching your current filters. Try adjusting your search or status.</p>
        <button onclick="filterByTab('status', 'all'); document.getElementById('searchInput').value='';" class="mt-6 px-6 py-2.5 bg-white border border-border text-foreground font-semibold rounded-xl hover:bg-muted transition-colors cursor-pointer">Clear Filters</button>
      </div>

    </div>
  </main>
</div>

<!-- Transaction Detail Modal -->
<div id="detailModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-300">
  <div class="bg-white rounded-3xl w-full max-w-lg max-h-[90vh] overflow-hidden flex flex-col shadow-2xl transform scale-95 transition-transform duration-300" id="detailModalContent">
    <div class="p-5 md:p-6 border-b border-border flex justify-between items-center bg-white sticky top-0 z-10">
      <h3 class="font-bold text-xl text-foreground">Order Details</h3>
      <button onclick="closeDetail()" class="size-10 flex items-center justify-center rounded-full hover:bg-muted transition-colors cursor-pointer">
        <i data-lucide="x" class="size-5 text-secondary"></i>
      </button>
    </div>
    
    <div class="p-5 md:p-6 overflow-y-auto flex-1 bg-card-grey">
      <!-- Product Info -->
      <div class="bg-white rounded-2xl p-4 border border-border mb-4 flex items-center gap-4">
        <img id="detailImage" src="" class="size-20 rounded-xl object-cover border border-border">
        <div class="flex-1 min-w-0">
          <h4 id="detailName" class="font-bold text-foreground text-lg leading-tight mb-1"></h4>
          <div class="flex items-center gap-1 text-secondary">
            <i data-lucide="store" class="size-4"></i>
            <p id="detailShop" class="text-sm"></p>
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
            <span id="detailId" class="font-semibold text-foreground"></span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-secondary text-sm">Date Placed</span>
            <span id="detailDate" class="font-medium text-foreground"></span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-secondary text-sm">Status</span>
            <div id="detailStatusContainer"></div>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-secondary text-sm">Payment Method</span>
            <span class="font-medium text-foreground flex items-center gap-2">
              <i data-lucide="credit-card" class="size-4 text-secondary"></i> Visa •••• 4242
            </span>
          </div>
          <div class="pt-4 border-t border-border flex justify-between items-center">
            <span class="font-semibold text-foreground">Total Amount</span>
            <span id="detailPrice" class="font-bold text-xl text-primary"></span>
          </div>
        </div>
      </div>
      
      <!-- Shipping Info -->
      <div class="bg-white rounded-2xl border border-border overflow-hidden mt-4">
        <div class="p-4 border-b border-border bg-muted/30">
          <h5 class="font-semibold text-sm text-secondary uppercase tracking-wider">Shipping Address</h5>
        </div>
        <div class="p-4">
          <p class="font-medium text-foreground">Alex Doe</p>
          <p class="text-secondary text-sm mt-1">123 Market Street, Suite 400<br>San Francisco, CA 94103<br>United States</p>
        </div>
      </div>
    </div>
    
    <div class="p-5 md:p-6 border-t border-border bg-white flex gap-3">
      <button onclick="closeDetail()" class="flex-1 py-3.5 bg-white border border-border text-foreground rounded-xl font-semibold hover:bg-muted transition-colors cursor-pointer">Close</button>
      <button onclick="showToast('Invoice downloaded', 'success')" class="flex-1 py-3.5 bg-primary text-white rounded-xl font-semibold hover:bg-primary-hover shadow-sm shadow-primary/20 transition-colors cursor-pointer flex items-center justify-center gap-2">
        <i data-lucide="download" class="size-4"></i> Invoice
      </button>
    </div>
  </div>
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
// Data for Modals
const itemsData = {
  'ORD-8472': { id: 'ORD-8472', date: 'Oct 24, 2023, 14:30 PM', shop: 'Tech Haven Official', name: 'Sony WH-1000XM5 Headphones', price: '$348.00', status: 'success', img: 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=400&h=400&fit=crop' },
  'ORD-8471': { id: 'ORD-8471', date: 'Oct 22, 2023, 09:15 AM', shop: 'Home Decor Studio', name: 'Minimalist Ceramic Vase', price: '$45.00', status: 'pending', img: 'https://images.unsplash.com/photo-1578500494198-246f612d3b3d?w=400&h=400&fit=crop' },
  'ORD-8465': { id: 'ORD-8465', date: 'Oct 15, 2023, 16:45 PM', shop: 'Essential Wear', name: 'Organic Cotton T-Shirt', price: '$28.50', status: 'success', img: 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400&h=400&fit=crop' },
  'ORD-8450': { id: 'ORD-8450', date: 'Oct 10, 2023, 11:20 AM', shop: 'FitGear Pro', name: 'Smart Fitness Watch', price: '$129.00', status: 'failed', img: 'https://images.unsplash.com/photo-1579586337278-3befd40fd17a?w=400&h=400&fit=crop' },
  'ORD-8422': { id: 'ORD-8422', date: 'Oct 02, 2023, 08:00 AM', shop: 'Roast Masters', name: 'Artisan Coffee Beans (1kg)', price: '$32.00', status: 'success', img: 'https://images.unsplash.com/photo-1559525839-b184a4d698c7?w=400&h=400&fit=crop' }
};

// Filtering Logic
document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('searchInput')?.addEventListener('input', applyFilters);
  document.getElementById('selectDateRange')?.addEventListener('change', applyFilters);
});

function applyFilters() {
  const search = (document.getElementById('searchInput')?.value || '').toLowerCase().trim();
  const status = document.getElementById('statusFilter')?.value || 'all';
  let visible = 0;

  document.querySelectorAll('[data-item-id]').forEach(item => {
    const text = (item.dataset.searchable || '').toLowerCase();
    const itemStatus = item.dataset.status;
    
    const matchesSearch = search === '' || text.includes(search);
    const matchesStatus = status === 'all' || itemStatus === status;
    
    const show = matchesSearch && matchesStatus;
    item.style.display = show ? '' : 'none';
    if (show) visible++;
  });

  const noResults = document.getElementById('noResults');
  if (noResults) {
    if (visible === 0) {
      noResults.classList.remove('hidden');
      noResults.classList.add('flex');
    } else {
      noResults.classList.add('hidden');
      noResults.classList.remove('flex');
    }
  }
}

function filterByTab(type, value) {
  document.querySelectorAll(`[data-filter-type="${type}"]`).forEach(btn => {
    const isActive = btn.dataset.filterValue === value;
    if (isActive) {
      btn.classList.remove('border-transparent', 'text-secondary');
      btn.classList.add('border-primary', 'text-primary');
    } else {
      btn.classList.add('border-transparent', 'text-secondary');
      btn.classList.remove('border-primary', 'text-primary');
    }
  });
  document.getElementById(`${type}Filter`).value = value;
  applyFilters();
}

// Modal Logic
function viewItem(id) {
  const data = itemsData[id];
  if (!data) return;
  
  document.getElementById('detailImage').src = data.img;
  document.getElementById('detailName').textContent = data.name;
  document.getElementById('detailShop').textContent = data.shop;
  document.getElementById('detailId').textContent = data.id;
  document.getElementById('detailDate').textContent = data.date;
  document.getElementById('detailPrice').textContent = data.price;
  
  const statusContainer = document.getElementById('detailStatusContainer');
  if (data.status === 'success') {
    statusContainer.innerHTML = `<span class="bg-success/10 text-success text-xs font-semibold px-2.5 py-1 rounded-full flex items-center gap-1"><i data-lucide="check-circle-2" class="size-3"></i> Success</span>`;
  } else if (data.status === 'pending') {
    statusContainer.innerHTML = `<span class="bg-warning/10 text-warning text-xs font-semibold px-2.5 py-1 rounded-full flex items-center gap-1"><i data-lucide="clock" class="size-3"></i> Pending</span>`;
  } else {
    statusContainer.innerHTML = `<span class="bg-error/10 text-error text-xs font-semibold px-2.5 py-1 rounded-full flex items-center gap-1"><i data-lucide="x-circle" class="size-3"></i> Failed</span>`;
  }
  
  lucide.createIcons();
  
  const modal = document.getElementById('detailModal');
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

function closeDetail() {
  const modal = document.getElementById('detailModal');
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
  if(mainInput) {
    mainInput.value = val;
    applyFilters();
  }
}

document.getElementById('search-modal').addEventListener('click', function(e) { if (e.target === this) closeSearchModal(); });
document.getElementById('detailModal').addEventListener('click', function(e) { if (e.target === this) closeDetail(); });

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


</body>
</html>