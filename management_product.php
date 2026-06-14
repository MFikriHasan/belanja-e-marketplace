<?php
    require 'koneksi.php';
    include 'login_check.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Seller Product Management</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<style type="text/tailwindcss">
  :root {
    --primary: #2563EB;
    --primary-hover: #1D4ED8;
    --foreground: #0F172A;
    --secondary: #64748B;
    --muted: #F8FAFC;
    --border: #E2E8F0;
    --card-grey: #F1F5F9;
    --success: #10B981;
    --error: #EF4444;
    --warning: #F59E0B;
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
  body { font-family: var(--font-sans); }
  .scrollbar-hide::-webkit-scrollbar { display: none; }
  .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
  
  /* Custom scrollbar for modal */
  .custom-scrollbar::-webkit-scrollbar { width: 6px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background-color: var(--border); border-radius: 10px; }
</style>
</head>
<body class="bg-muted text-foreground min-h-screen flex overflow-hidden">

<!-- Sidebar Overlay -->
<div id="sidebarOverlay" class="fixed inset-0 bg-foreground/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

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
      
      <a href="/management_product.html" class="group active cursor-pointer">
        <div class="flex items-center rounded-xl p-3.5 gap-3 group-[.active]:bg-primary/10 group-[.active]:text-primary hover:bg-muted transition-all">
          <i data-lucide="package" class="size-5 text-secondary group-[.active]:text-primary group-hover:text-foreground transition-colors"></i>
          <span class="font-medium text-secondary group-[.active]:font-semibold group-[.active]:text-primary group-hover:text-foreground transition-colors">Products</span>
        </div>
      </a>
      
      <a href="/management_transactions.html" class="group cursor-pointer">
        <div class="flex items-center rounded-xl p-3.5 gap-3 hover:bg-muted transition-all">
          <i data-lucide="arrow-left-right" class="size-5 text-secondary group-hover:text-foreground transition-colors"></i>
          <span class="font-medium text-secondary group-hover:text-foreground transition-colors">Orders</span>
        </div>
      </a>
      
      <a href="/sales_report.html" class="group cursor-pointer">
        <div class="flex items-center rounded-xl p-3.5 gap-3 hover:bg-muted transition-all">
          <i data-lucide="bar-chart-3" class="size-5 text-secondary group-hover:text-foreground transition-colors"></i>
          <span class="font-medium text-secondary group-hover:text-foreground transition-colors">Sales Report</span>
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
<main class="flex-1 flex flex-col min-w-0 lg:ml-[260px] h-screen">
  <!-- Top Navbar -->
    <header class="flex items-center justify-between h-[90px] border-b border-border bg-white px-5 md:px-8 sticky top-0 z-30">
      <div class="flex items-center gap-4 ms-5">
        <button onclick="toggleSidebar()" class="lg:hidden size-11 flex items-center justify-center rounded-xl bg-muted hover:bg-border transition-colors cursor-pointer">
          <i data-lucide="menu" class="size-6 text-foreground"></i>
        </button>
        <h2 class="hidden sm:block font-bold text-2xl ">Product Management</h2>
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

  <!-- Content Scrollable Area -->
  <div class="flex-1 overflow-y-auto p-4 sm:p-8 ms-5">
    
    <!-- Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
      <div class="bg-white p-5 rounded-2xl border border-border flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
          <i data-lucide="package" class="w-6 h-6 text-primary"></i>
        </div>
        <div>
          <p class="text-sm text-secondary font-medium">Total Products</p>
          <p class="text-2xl font-bold" id="statTotal">0</p>
        </div>
      </div>
      <div class="bg-white p-5 rounded-2xl border border-border flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-success/10 flex items-center justify-center shrink-0">
          <i data-lucide="check-circle" class="w-6 h-6 text-success"></i>
        </div>
        <div>
          <p class="text-sm text-secondary font-medium">Active Listings</p>
          <p class="text-2xl font-bold" id="statActive">0</p>
        </div>
      </div>
      <div class="bg-white p-5 rounded-2xl border border-border flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-warning/10 flex items-center justify-center shrink-0">
          <i data-lucide="alert-triangle" class="w-6 h-6 text-warning"></i>
        </div>
        <div>
          <p class="text-sm text-secondary font-medium">Low Stock</p>
          <p class="text-2xl font-bold" id="statLowStock">0</p>
        </div>
      </div>
    </div>

    <!-- Controls Row -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
      <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
        <div class="relative w-full sm:w-64">
          <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-secondary"></i>
          <input type="text" id="searchInput" placeholder="Search products..." class="w-full pl-9 pr-4 py-2.5 bg-white border border-border rounded-xl text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all">
        </div>
        <select id="categoryFilter" class="w-full sm:w-40 px-4 py-2.5 bg-white border border-border rounded-xl text-sm focus:outline-none focus:border-primary appearance-none cursor-pointer">
          <option value="all">All Categories</option>
          <option value="Electronics">Electronics</option>
          <option value="Accessories">Accessories</option>
          <option value="Audio">Audio</option>
        </select>
      </div>
      <button onclick="openProductModal()" class="w-full sm:w-auto bg-primary hover:bg-primary-hover text-white px-6 py-2.5 rounded-xl font-semibold flex items-center justify-center gap-2 transition-colors">
        <i data-lucide="plus" class="w-5 h-5"></i>
        Add Product
      </button>
    </div>

    <!-- Empty State (Hidden by default) -->
    <div id="noResults" class="hidden flex flex-col items-center justify-center py-16 bg-white rounded-2xl border border-border text-center px-4">
      <div class="w-16 h-16 bg-muted rounded-full flex items-center justify-center mb-4">
        <i data-lucide="package-x" class="w-8 h-8 text-secondary"></i>
      </div>
      <h3 class="text-lg font-bold mb-1">No products found</h3>
      <p class="text-secondary text-sm mb-4">Try adjusting your search or filters.</p>
      <button onclick="document.getElementById('searchInput').value=''; document.getElementById('categoryFilter').value='all'; applyFilters();" class="text-primary font-medium hover:underline">Clear all filters</button>
    </div>

    <!-- Product Grid -->
    <div id="productsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <!-- Cards injected by JS -->
    </div>

  </div>
</main>

<!-- Product Form Modal (Add/Edit) -->
<div id="productModal" class="fixed inset-0 bg-foreground/60 z-[100] hidden flex items-center justify-center p-4 sm:p-6 backdrop-blur-sm opacity-0 transition-opacity duration-300">
  <div class="bg-white rounded-2xl w-full max-w-4xl max-h-[90vh] flex flex-col shadow-2xl transform scale-95 transition-transform duration-300" id="productModalContent">
    
    <!-- Modal Header -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-border shrink-0">
      <h2 class="text-xl font-bold" id="modalTitle">Add New Product</h2>
      <button onclick="closeProductModal()" class="p-2 text-secondary hover:bg-muted rounded-full transition-colors">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <!-- Modal Body (Scrollable) -->
    <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
      <form id="productForm" class="space-y-8">
        <input type="hidden" id="inputId">
        
        <!-- Basic Info Section -->
        <div>
          <h3 class="text-sm font-bold text-foreground uppercase tracking-wider mb-4 flex items-center gap-2">
            <i data-lucide="info" class="w-4 h-4 text-primary"></i> Basic Information
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-secondary mb-1.5">Product Name *</label>
              <input type="text" id="inputName" required class="w-full px-4 py-2.5 bg-muted border border-border rounded-xl text-sm focus:bg-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all" placeholder="e.g. Wireless Noise-Cancelling Headphones">
            </div>
            <div>
              <label class="block text-sm font-medium text-secondary mb-1.5">Category *</label>
              <select id="inputCategory" required class="w-full px-4 py-2.5 bg-muted border border-border rounded-xl text-sm focus:bg-white focus:outline-none focus:border-primary appearance-none">
                <option value="">Select Category</option>
                <option value="Electronics">Electronics</option>
                <option value="Accessories">Accessories</option>
                <option value="Audio">Audio</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-secondary mb-1.5">Base Price ($) *</label>
              <input type="number" id="inputPrice" step="0.01" required class="w-full px-4 py-2.5 bg-muted border border-border rounded-xl text-sm focus:bg-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all" placeholder="0.00">
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-secondary mb-1.5">Description</label>
              <textarea id="inputDescription" rows="3" class="w-full px-4 py-2.5 bg-muted border border-border rounded-xl text-sm focus:bg-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all resize-none" placeholder="Brief product description..."></textarea>
            </div>
          </div>
        </div>

        <hr class="border-border">

        <!-- Specifications Section -->
        <div>
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-foreground uppercase tracking-wider flex items-center gap-2">
              <i data-lucide="list" class="w-4 h-4 text-primary"></i> Specifications
            </h3>
            <button type="button" onclick="addSpecRow()" class="text-sm text-primary font-medium hover:underline flex items-center gap-1">
              <i data-lucide="plus" class="w-4 h-4"></i> Add Spec
            </button>
          </div>
          <div id="specsContainer" class="space-y-3">
            <!-- Spec rows injected here -->
          </div>
        </div>

        <hr class="border-border">

        <!-- Variants Section (Colors, Stock, Images) -->
        <div>
          <div class="flex items-center justify-between mb-4">
            <div>
              <h3 class="text-sm font-bold text-foreground uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="layers" class="w-4 h-4 text-primary"></i> Product Variants
              </h3>
              <p class="text-xs text-secondary mt-1">Add colors, manage stock, and upload images for each variant.</p>
            </div>
            <button type="button" onclick="addVariantRow()" class="bg-primary/10 text-primary px-3 py-1.5 rounded-lg text-sm font-semibold hover:bg-primary/20 transition-colors flex items-center gap-1">
              <i data-lucide="plus" class="w-4 h-4"></i> Add Variant
            </button>
          </div>
          
          <div id="variantsContainer" class="space-y-4">
            <!-- Variant rows injected here -->
          </div>
        </div>

      </form>
    </div>

    <!-- Modal Footer -->
    <div class="px-6 py-4 border-t border-border bg-card-grey rounded-b-2xl flex justify-end gap-3 shrink-0">
      <button type="button" onclick="closeProductModal()" class="px-6 py-2.5 rounded-xl font-medium text-secondary hover:bg-border transition-colors">Cancel</button>
      <button type="button" onclick="saveProduct()" class="px-6 py-2.5 rounded-xl font-semibold bg-primary text-white hover:bg-primary-hover transition-colors shadow-sm">Save Product</button>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-foreground/60 z-[110] hidden flex items-center justify-center p-4 backdrop-blur-sm opacity-0 transition-opacity duration-300">
  <div class="bg-white rounded-2xl w-full max-w-sm p-6 text-center shadow-2xl transform scale-95 transition-transform duration-300" id="deleteModalContent">
    <div class="w-16 h-16 bg-error/10 rounded-full flex items-center justify-center mx-auto mb-4">
      <i data-lucide="alert-triangle" class="w-8 h-8 text-error"></i>
    </div>
    <h3 class="text-xl font-bold mb-2">Delete Product?</h3>
    <p class="text-secondary text-sm mb-6">This action cannot be undone. This will permanently remove the product and all its variants.</p>
    <div class="flex gap-3">
      <button onclick="closeDeleteModal()" class="flex-1 py-2.5 rounded-xl font-medium border border-border hover:bg-muted transition-colors">Cancel</button>
      <button onclick="confirmDelete()" class="flex-1 py-2.5 rounded-xl font-semibold bg-error text-white hover:bg-red-600 transition-colors">Delete</button>
    </div>
  </div>
</div>



<script>
// --- Data State ---
let productsData = {
  'PROD-001': {
    id: 'PROD-001',
    name: 'Sony WH-1000XM5 Wireless Headphones',
    category: 'Audio',
    price: 348.00,
    description: 'Industry leading noise canceling with two processors and 8 microphones.',
    status: 'active',
    specs: [{ key: 'Battery Life', value: '30 Hours' }, { key: 'Bluetooth', value: 'v5.2' }],
    variants: [
      { color: 'Black', stock: 45, image: 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=400&h=400&fit=crop' },
      { color: 'Silver', stock: 12, image: 'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=400&h=400&fit=crop' }
    ]
  },
  'PROD-002': {
    id: 'PROD-002',
    name: 'Logitech MX Master 3S Mouse',
    category: 'Accessories',
    price: 99.99,
    description: 'Advanced wireless mouse with 8K DPI tracking and quiet clicks.',
    status: 'active',
    specs: [{ key: 'Sensor', value: 'Darkfield' }, { key: 'Buttons', value: '7' }],
    variants: [
      { color: 'Graphite', stock: 8, image: 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&h=400&fit=crop' },
      { color: 'Pale Grey', stock: 0, image: 'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?w=400&h=400&fit=crop' }
    ]
  },
  'PROD-003': {
    id: 'PROD-003',
    name: 'Keychron Q1 Pro Mechanical Keyboard',
    category: 'Accessories',
    price: 199.00,
    description: 'QMK/VIA wireless custom mechanical keyboard with full aluminum body.',
    status: 'active',
    specs: [{ key: 'Layout', value: '75%' }, { key: 'Switches', value: 'Hot-swappable' }],
    variants: [
      { color: 'Carbon Black', stock: 25, image: 'https://images.unsplash.com/photo-1595225476474-87563907a212?w=400&h=400&fit=crop' }
    ]
  },
  'PROD-004': {
    id: 'PROD-004',
    name: 'Samsung 34" Odyssey G8 OLED Monitor',
    category: 'Electronics',
    price: 1199.99,
    description: 'Ultra-wide gaming monitor with 0.03ms response time and 175Hz refresh rate.',
    status: 'active',
    specs: [{ key: 'Resolution', value: '3440 x 1440' }, { key: 'Panel', value: 'OLED' }],
    variants: [
      { color: 'Silver', stock: 5, image: 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400&h=400&fit=crop' }
    ]
  },
  'PROD-005': {
    id: 'PROD-005',
    name: 'Anker 737 Power Bank (PowerCore 24K)',
    category: 'Accessories',
    price: 149.99,
    description: 'Ultra-powerful two-way fast charging portable charger.',
    status: 'inactive',
    specs: [{ key: 'Capacity', value: '24,000mAh' }, { key: 'Output', value: '140W' }],
    variants: [
      { color: 'Black', stock: 0, image: 'https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?w=400&h=400&fit=crop' }
    ]
  }
};

let itemToDelete = null;

// --- Initialization ---
document.addEventListener('DOMContentLoaded', () => {
  lucide.createIcons();
  renderProducts();
  updateStats();
  
  // Event Listeners for Filters
  document.getElementById('searchInput').addEventListener('input', applyFilters);
  document.getElementById('categoryFilter').addEventListener('change', applyFilters);
});

// --- Rendering ---
function renderProducts() {
  const container = document.getElementById('productsGrid');
  container.innerHTML = '';
  
  Object.values(productsData).forEach(product => {
    // Calculate total stock and get main image
    const totalStock = product.variants.reduce((sum, v) => sum + (parseInt(v.stock) || 0), 0);
    const mainImage = product.variants.length > 0 && product.variants[0].image ? product.variants[0].image : 'https://placehold.co/400x400/e2e8f0/64748b?text=No+Image';
    
    // Determine status badge
    let statusHtml = '';
    if (totalStock === 0) {
      statusHtml = `<span class="bg-error/10 text-error px-2.5 py-1 rounded-full text-xs font-semibold border border-error/20">Out of Stock</span>`;
    } else if (totalStock < 10) {
      statusHtml = `<span class="bg-warning/10 text-warning px-2.5 py-1 rounded-full text-xs font-semibold border border-warning/20">Low Stock (${totalStock})</span>`;
    } else {
      statusHtml = `<span class="bg-success/10 text-success px-2.5 py-1 rounded-full text-xs font-semibold border border-success/20">In Stock (${totalStock})</span>`;
    }

    const card = document.createElement('div');
    card.className = 'bg-white rounded-2xl border border-border overflow-hidden hover:shadow-lg transition-shadow duration-300 flex flex-col group';
    card.setAttribute('data-item-id', product.id);
    card.setAttribute('data-category', product.category);
    card.setAttribute('data-searchable', `${product.name} ${product.category}`.toLowerCase());
    
    card.innerHTML = `
      <div class="relative aspect-square bg-muted overflow-hidden">
        <img src="${mainImage}" alt="${product.name}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        <div class="absolute top-3 left-3">
          ${statusHtml}
        </div>
        <div class="absolute top-3 right-3 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
          <button onclick="openEditModal('${product.id}')" class="w-8 h-8 bg-white rounded-full shadow-md flex items-center justify-center text-secondary hover:text-primary hover:bg-muted transition-colors" title="Edit">
            <i data-lucide="edit-2" class="w-4 h-4"></i>
          </button>
          <button onclick="promptDelete('${product.id}')" class="w-8 h-8 bg-white rounded-full shadow-md flex items-center justify-center text-secondary hover:text-error hover:bg-muted transition-colors" title="Delete">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
          </button>
        </div>
      </div>
      <div class="p-4 flex flex-col flex-1">
        <p class="text-xs text-secondary font-medium mb-1 uppercase tracking-wider">${product.category}</p>
        <h3 class="font-semibold text-foreground leading-tight mb-2 line-clamp-2 flex-1">${product.name}</h3>
        <div class="flex items-end justify-between mt-auto pt-3 border-t border-border/50">
          <p class="font-bold text-lg">$${parseFloat(product.price).toFixed(2)}</p>
          <p class="text-xs text-secondary">${product.variants.length} Variant${product.variants.length !== 1 ? 's' : ''}</p>
        </div>
      </div>
    `;
    container.appendChild(card);
  });
  lucide.createIcons();
}

function updateStats() {
  const products = Object.values(productsData);
  const total = products.length;
  let active = 0;
  let lowStock = 0;

  products.forEach(p => {
    const stock = p.variants.reduce((sum, v) => sum + (parseInt(v.stock) || 0), 0);
    if (stock > 0) active++;
    if (stock > 0 && stock < 10) lowStock++;
  });

  document.getElementById('statTotal').textContent = total;
  document.getElementById('statActive').textContent = active;
  document.getElementById('statLowStock').textContent = lowStock;
}

function applyFilters() {
  const search = document.getElementById('searchInput').value.toLowerCase().trim();
  const category = document.getElementById('categoryFilter').value;
  let visibleCount = 0;

  document.querySelectorAll('[data-item-id]').forEach(card => {
    const searchable = card.getAttribute('data-searchable');
    const itemCategory = card.getAttribute('data-category');
    
    const matchesSearch = search === '' || searchable.includes(search);
    const matchesCategory = category === 'all' || itemCategory === category;
    
    if (matchesSearch && matchesCategory) {
      card.style.display = '';
      visibleCount++;
    } else {
      card.style.display = 'none';
    }
  });

  const noResults = document.getElementById('noResults');
  if (visibleCount === 0) {
    noResults.classList.remove('hidden');
    document.getElementById('productsGrid').classList.add('hidden');
  } else {
    noResults.classList.add('hidden');
    document.getElementById('productsGrid').classList.remove('hidden');
  }
}

// --- Form & Dynamic Rows Logic ---

function addSpecRow(key = '', value = '') {
  const container = document.getElementById('specsContainer');
  const row = document.createElement('div');
  row.className = 'spec-row flex items-center gap-3';
  row.innerHTML = `
    <input type="text" placeholder="e.g. Material" value="${key}" class="spec-key flex-1 px-3 py-2 bg-white border border-border rounded-lg text-sm focus:outline-none focus:border-primary">
    <input type="text" placeholder="e.g. Aluminum" value="${value}" class="spec-value flex-1 px-3 py-2 bg-white border border-border rounded-lg text-sm focus:outline-none focus:border-primary">
    <button type="button" onclick="this.closest('.spec-row').remove()" class="p-2 text-secondary hover:text-error hover:bg-error/10 rounded-lg transition-colors">
      <i data-lucide="minus" class="w-4 h-4"></i>
    </button>
  `;
  container.appendChild(row);
  lucide.createIcons();
}

function addVariantRow(variant = { color: '', stock: 0, image: '' }) {
  const container = document.getElementById('variantsContainer');
  const rowId = 'var_' + Math.random().toString(36).substr(2, 9);
  const row = document.createElement('div');
  row.className = 'variant-row flex flex-col sm:flex-row items-start gap-4 p-4 border border-border rounded-xl bg-card-grey relative group';
  
  const hasImage = variant.image && variant.image !== '';
  
  row.innerHTML = `
    <!-- Image Upload Box -->
    <div class="shrink-0 w-full sm:w-auto flex justify-center sm:block">
      <label class="cursor-pointer block relative group/img">
        <input type="file" class="hidden variant-file" accept="image/*" onchange="handleImagePreview(this)">
        <input type="hidden" class="variant-image-url" value="${variant.image}">
        <div class="w-24 h-24 rounded-xl border-2 border-dashed border-border flex flex-col items-center justify-center bg-white overflow-hidden relative hover:border-primary transition-colors">
          <img src="${variant.image}" class="w-full h-full object-cover data-preview-img ${hasImage ? '' : 'hidden'}">
          <div class="data-placeholder flex flex-col items-center ${hasImage ? 'hidden' : ''}">
            <i data-lucide="image-plus" class="w-6 h-6 text-secondary mb-1 group-hover/img:text-primary transition-colors"></i>
            <span class="text-[10px] text-secondary font-medium">Upload</span>
          </div>
          <!-- Overlay on hover if image exists -->
          <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 ${hasImage ? 'group-hover/img:opacity-100' : ''} transition-opacity">
             <i data-lucide="edit-2" class="w-5 h-5 text-white"></i>
          </div>
        </div>
      </label>
    </div>
    
    <!-- Inputs -->
    <div class="flex-1 w-full grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-medium text-secondary mb-1">Color / Name</label>
        <input type="text" value="${variant.color}" placeholder="e.g. Matte Black" class="variant-color w-full px-3 py-2 bg-white border border-border rounded-lg text-sm focus:outline-none focus:border-primary">
      </div>
      <div>
        <label class="block text-xs font-medium text-secondary mb-1">Stock Quantity</label>
        <input type="number" value="${variant.stock}" min="0" class="variant-stock w-full px-3 py-2 bg-white border border-border rounded-lg text-sm focus:outline-none focus:border-primary">
      </div>
    </div>
    
    <!-- Remove Button -->
    <button type="button" onclick="this.closest('.variant-row').remove()" class="absolute top-2 right-2 p-1.5 text-secondary hover:text-error hover:bg-error/10 rounded-lg transition-colors opacity-100 sm:opacity-0 sm:group-hover:opacity-100">
      <i data-lucide="trash-2" class="w-4 h-4"></i>
    </button>
  `;
  container.appendChild(row);
  lucide.createIcons();
}

function handleImagePreview(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    const row = input.closest('.variant-row');
    const imgPreview = row.querySelector('.data-preview-img');
    const placeholder = row.querySelector('.data-placeholder');
    const urlInput = row.querySelector('.variant-image-url');
    
    reader.onload = function(e) {
      imgPreview.src = e.target.result;
      imgPreview.classList.remove('hidden');
      placeholder.classList.add('hidden');
      urlInput.value = e.target.result; // Store base64 for saving
      
      // Add hover overlay logic
      const label = row.querySelector('label');
      const overlay = label.querySelector('.absolute.inset-0');
      if(overlay) {
         overlay.classList.add('group-hover/img:opacity-100');
      }
    }
    reader.readAsDataURL(input.files[0]);
  }
}

// --- Modal Logic ---

function openProductModal() {
  document.getElementById('modalTitle').textContent = 'Add New Product';
  document.getElementById('productForm').reset();
  document.getElementById('inputId').value = '';
  
  // Clear dynamic rows
  document.getElementById('specsContainer').innerHTML = '';
  document.getElementById('variantsContainer').innerHTML = '';
  
  // Add initial empty rows
  addSpecRow();
  addVariantRow();
  
  showModal('productModal', 'productModalContent');
}

function openEditModal(id) {
  const product = productsData[id];
  if (!product) return;
  
  document.getElementById('modalTitle').textContent = 'Edit Product';
  document.getElementById('inputId').value = product.id;
  document.getElementById('inputName').value = product.name;
  document.getElementById('inputCategory').value = product.category;
  document.getElementById('inputPrice').value = product.price;
  document.getElementById('inputDescription').value = product.description;
  
  // Populate Specs
  const specsContainer = document.getElementById('specsContainer');
  specsContainer.innerHTML = '';
  if (product.specs && product.specs.length > 0) {
    product.specs.forEach(spec => addSpecRow(spec.key, spec.value));
  } else {
    addSpecRow();
  }
  
  // Populate Variants
  const variantsContainer = document.getElementById('variantsContainer');
  variantsContainer.innerHTML = '';
  if (product.variants && product.variants.length > 0) {
    product.variants.forEach(variant => addVariantRow(variant));
  } else {
    addVariantRow();
  }
  
  showModal('productModal', 'productModalContent');
}

function closeProductModal() {
  hideModal('productModal', 'productModalContent');
}

function saveProduct() {
  const id = document.getElementById('inputId').value || 'PROD-' + Math.floor(Math.random() * 10000).toString().padStart(4, '0');
  const name = document.getElementById('inputName').value.trim();
  const category = document.getElementById('inputCategory').value;
  const price = document.getElementById('inputPrice').value;
  
  if (!name || !category || !price) {
    showToast('Please fill in all required basic information.', 'error');
    return;
  }

  // Gather Specs
  const specs = [];
  document.querySelectorAll('.spec-row').forEach(row => {
    const key = row.querySelector('.spec-key').value.trim();
    const value = row.querySelector('.spec-value').value.trim();
    if (key || value) specs.push({ key, value });
  });

  // Gather Variants
  const variants = [];
  let hasVariantError = false;
  document.querySelectorAll('.variant-row').forEach(row => {
    const color = row.querySelector('.variant-color').value.trim();
    const stock = row.querySelector('.variant-stock').value;
    const image = row.querySelector('.variant-image-url').value;
    
    if (color) {
      variants.push({ color, stock: parseInt(stock) || 0, image });
    } else if (stock > 0 || image) {
      hasVariantError = true; // Has data but no color name
    }
  });

  if (hasVariantError) {
    showToast('Please provide a color/name for all variants with data.', 'error');
    return;
  }
  
  if (variants.length === 0) {
    // Force at least one default variant if empty
    variants.push({ color: 'Default', stock: 0, image: '' });
  }

  // Save to data object
  productsData[id] = {
    id,
    name,
    category,
    price: parseFloat(price),
    description: document.getElementById('inputDescription').value,
    status: 'active',
    specs,
    variants
  };

  renderProducts();
  updateStats();
  applyFilters(); // Re-apply current filters
  closeProductModal();
  showToast('Product saved successfully!', 'success');
}

// --- Delete Logic ---
function promptDelete(id) {
  itemToDelete = id;
  showModal('deleteModal', 'deleteModalContent');
}

function closeDeleteModal() {
  itemToDelete = null;
  hideModal('deleteModal', 'deleteModalContent');
}

function confirmDelete() {
  if (itemToDelete && productsData[itemToDelete]) {
    delete productsData[itemToDelete];
    renderProducts();
    updateStats();
    applyFilters();
    closeDeleteModal();
    showToast('Product deleted.', 'success');
  }
}

// --- Utility Functions ---
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');
  sidebar.classList.toggle('-translate-x-full');
  overlay.classList.toggle('hidden');
}

function showModal(overlayId, contentId) {
  const overlay = document.getElementById(overlayId);
  const content = document.getElementById(contentId);
  overlay.classList.remove('hidden');
  // Small delay to allow display:block to apply before animating opacity
  requestAnimationFrame(() => {
    overlay.classList.remove('opacity-0');
    content.classList.remove('scale-95');
    content.classList.add('scale-100');
  });
}

function hideModal(overlayId, contentId) {
  const overlay = document.getElementById(overlayId);
  const content = document.getElementById(contentId);
  overlay.classList.add('opacity-0');
  content.classList.remove('scale-100');
  content.classList.add('scale-95');
  setTimeout(() => {
    overlay.classList.add('hidden');
  }, 300); // Match transition duration
}

function showToast(msg, type = 'success') {
  document.getElementById('toast')?.remove();
  const t = document.createElement('div');
  t.id = 'toast';
  const bgClass = type === 'success' ? 'bg-foreground' : 'bg-error';
  const icon = type === 'success' ? 'check-circle' : 'alert-circle';
  
  t.className = `fixed bottom-4 right-4 ${bgClass} text-white px-5 py-3 rounded-xl z-[200] flex items-center gap-3 shadow-xl transition-all duration-300 opacity-0 translate-y-4`;
  t.innerHTML = `<i data-lucide="${icon}" class="w-5 h-5"></i><span class="font-medium text-sm">${msg}</span>`;
  
  document.body.appendChild(t);
  lucide.createIcons();
  
  requestAnimationFrame(() => {
    t.classList.remove('opacity-0', 'translate-y-4');
  });
  
  setTimeout(() => {
    t.classList.add('opacity-0', 'translate-y-4');
    setTimeout(() => t.remove(), 300);
  }, 3000);
}


</script>


</body>
</html>