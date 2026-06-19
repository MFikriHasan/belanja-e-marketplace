<?php
  require 'koneksi.php';
  include 'login_check.php';

  $product_id = (int)$_GET['id'];
  $seller_id = $_SESSION['seller_id'];

  $query_prod = "SELECT p.id, p.name, p.category_id, p.seller_id, p.price, p.description, c.name AS category_name
               FROM product p
               LEFT JOIN category c ON p.category_id = c.id
               WHERE p.seller_id = ? AND p.id = ?";

$stmt = $koneksi->prepare($query_prod);
$stmt->bind_param("ii", $seller_id, $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();



$query_varian = "SELECT id, color_name, color_stok, product_image 
                 FROM color_varian 
                 WHERE product_id = ?";

$stmt_v = $koneksi->prepare($query_varian);
$stmt_v->bind_param("i", $product_id);
$stmt_v->execute();
$variants = $stmt_v->get_result()->fetch_all(MYSQLI_ASSOC);



$query_spec = "SELECT id, key_name AS spec_name, value_name AS spec_value 
               FROM spesification 
               WHERE product_id = ?";

$stmt_s = $koneksi->prepare($query_spec);
$stmt_s->bind_param("i", $product_id);
$stmt_s->execute();
$specs = $stmt_s->get_result()->fetch_all(MYSQLI_ASSOC);


$sqlC = "SELECT id, name FROM category";

$exec = $koneksi->execute_query($sqlC);

$categories = $exec->fetch_all(MYSQLI_ASSOC);

?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Product - Seller Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        fontFamily: { sans: ['Poppins', 'sans-serif'] },
        colors: {
          primary: '#2563EB',
          'primary-hover': '#1D4ED8',
          foreground: '#0F172A',
          secondary: '#64748B',
          muted: '#F8FAFC',
          border: '#E2E8F0',
          'card-grey': '#F1F5F9',
          success: '#10B981',
          error: '#EF4444',
          warning: '#F59E0B',
        }
      }
    }
  }
</script>
<style>
  .scrollbar-hide::-webkit-scrollbar { display: none; }
  .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
  
  /* Custom file input styling */
  .file-upload-wrapper input[type="file"] {
    position: absolute;
    left: 0;
    top: 0;
    opacity: 0;
    cursor: pointer;
    width: 100%;
    height: 100%;
  }
</style>
</head>
<body class="font-sans bg-muted min-h-screen text-foreground">

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-foreground/80 z-40 lg:hidden hidden transition-opacity" onclick="toggleSidebar()"></div>

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
  <main class="flex-1 lg:ml-[280px] flex flex-col h-screen overflow-hidden bg-muted">
    <!-- Top Navbar -->
    <header class="flex items-center justify-between h-[90px] border-b border-border bg-white px-5 md:px-8 sticky top-0 z-30">
      <div class="flex items-center gap-4">
        <button onclick="toggleSidebar()" class="lg:hidden size-11 flex items-center justify-center rounded-xl bg-muted hover:bg-border transition-colors cursor-pointer">
          <i data-lucide="menu" class="size-6 text-foreground"></i>
        </button>
        <div class="hidden md:flex items-center gap-2 text-sm font-medium text-secondary">
          <a href="management_product.php" class="hover:text-primary transition-colors">Product Management</a>
          <i data-lucide="chevron-right" class="size-4"></i>
          <span class="text-foreground">Edit Product</span>
        </div>
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

    <!-- Scrollable Content Area -->
    <div class="flex-1 overflow-y-auto p-4 md:p-8">
      
      <!-- Page Header & Actions -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
          <h2 class="text-2xl font-bold text-foreground">Edit Product</h2>
          <p class="text-sm text-secondary mt-1">Update product details, specifications, and variants.</p>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto">
          <button type="button" onclick="window.history.back()" class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl font-semibold text-secondary bg-white border border-border hover:bg-muted transition-colors text-center">
            Cancel
          </button>
          <button type="button" onclick="saveProduct()" class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl font-semibold text-white bg-primary hover:bg-primary-hover shadow-lg shadow-primary/20 transition-all text-center flex items-center justify-center gap-2">
            <i data-lucide="save" class="size-4"></i>
            Save Changes
          </button>
        </div>
      </div>

      <!-- Main Form -->
      <form id="productForm" class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8 pb-10">
        
        <!-- Left Column: Basic Info & Specs -->
        <div class="lg:col-span-2 flex flex-col gap-6 md:gap-8">
          
          <!-- Basic Information Card -->
          <div class="bg-white rounded-2xl border border-border p-6 shadow-sm">
            <h3 class="text-lg font-bold mb-6 flex items-center gap-2">
              <i data-lucide="info" class="size-5 text-primary"></i>
              Basic Information
            </h3>
            
            <div class="space-y-5">
              <div>
                <label class="block text-sm font-medium text-foreground mb-2">Product Name <span class="text-error">*</span></label>
                <input type="text" id="inputProductName" name="product_name" value="<?= $product['name'] ?>" class="w-full bg-muted border border-border rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all text-foreground" placeholder="Enter product name">
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                  <label class="block text-sm font-medium text-foreground mb-2">Category <span class="text-error">*</span></label>
                  <div class="relative">
                    <select id="selectCategory" name="category" class="w-full bg-muted border border-border rounded-xl px-4 py-3 appearance-none outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all text-foreground">
                      <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= ($product['category_id'] == $category['id'] ? 'selected' : '') ?>><?= $category['name'] ?></option>
                      <?php endforeach; ?>
                    </select>
                    <i data-lucide="chevron-down" class="size-5 text-secondary absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-foreground mb-2">Base Price ($) <span class="text-error">*</span></label>
                  <input type="number" id="inputBasePrice" value="<?= $product['price'] ?>" step="0.01" class="w-full bg-muted border border-border rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all text-foreground">
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-foreground mb-2">Description</label>
                <textarea id="inputDescription" rows="4" class="w-full bg-muted border border-border rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all text-foreground resize-none" placeholder="Describe your product..."><?= $product['description'] ?></textarea>
              </div>
            </div>
          </div>

          <!-- Specifications Card (Dynamic CRUD) -->
          <div class="bg-white rounded-2xl border border-border p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-lg font-bold flex items-center gap-2">
                <i data-lucide="list" class="size-5 text-primary"></i>
                Specifications
              </h3>
              <button type="button" onclick="addSpecRow()" class="text-sm font-semibold text-primary hover:text-primary-hover flex items-center gap-1 bg-primary/10 px-3 py-1.5 rounded-lg transition-colors">
                <i data-lucide="plus" class="size-4"></i> Add Spec
              </button>
            </div>
            
            <!-- Headers for desktop -->
            <div class="hidden sm:grid grid-cols-[1fr_1fr_40px] gap-4 mb-2 px-1">
              <span class="text-xs font-semibold text-secondary uppercase tracking-wider">Specification Name</span>
              <span class="text-xs font-semibold text-secondary uppercase tracking-wider">Value</span>
              <span></span>
            </div>

            <div id="specsContainer" class="flex flex-col gap-3">
              
              <?php foreach ($specs as $row): ?>
                <div class="dynamic-row flex flex-col sm:flex-row items-start sm:items-center gap-3 bg-muted/50 p-3 sm:p-0 sm:bg-transparent rounded-xl border border-border sm:border-none">
                  <div class="w-full sm:flex-1">
                    <label class="sm:hidden text-xs font-medium text-secondary mb-1 block">Name</label>
                    <input type="text" value="<?= $row['spec_name'] ?>" placeholder="e.g. Material" class="w-full bg-white sm:bg-muted border border-border rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all text-sm">
                  </div>
                  <div class="w-full sm:flex-1">
                    <label class="sm:hidden text-xs font-medium text-secondary mb-1 block">Value</label>
                    <input type="text" value="<?= $row['spec_value'] ?>" placeholder="e.g. Cotton" class="w-full bg-white sm:bg-muted border border-border rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all text-sm">
                  </div>
                  <button type="button" onclick="removeRow(this)" class="w-full sm:w-10 h-10 flex items-center justify-center rounded-xl text-error hover:bg-error/10 transition-colors shrink-0 border border-error/20 sm:border-none mt-2 sm:mt-0" title="Remove">
                    <i data-lucide="trash-2" class="size-4 sm:size-5"></i>
                    <span class="sm:hidden ml-2 text-sm font-medium">Remove Spec</span>
                  </button>
                </div>
              <?php endforeach; ?>
              
              
            </div>
          </div>

        </div>

        <!-- Right Column: Status & Variants -->
        <div class="flex flex-col gap-6 md:gap-8">
          
          <!-- Status Card -->
          <div class="bg-white rounded-2xl border border-border p-6 shadow-sm">
            <h3 class="text-lg font-bold mb-4">Visibility</h3>
            <div class="flex flex-col gap-3">
              <label class="flex items-center justify-between p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                <div class="flex items-center gap-3">
                  <div class="size-8 rounded-lg bg-success/10 flex items-center justify-center">
                    <i data-lucide="eye" class="size-4 text-success"></i>
                  </div>
                  <div>
                    <p class="font-semibold text-sm">Published</p>
                    <p class="text-xs text-secondary">Visible to customers</p>
                  </div>
                </div>
                <input type="radio" name="status" value="published" checked class="size-4 text-primary focus:ring-primary border-border">
              </label>
              
              <label class="flex items-center justify-between p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                <div class="flex items-center gap-3">
                  <div class="size-8 rounded-lg bg-secondary/10 flex items-center justify-center">
                    <i data-lucide="eye-off" class="size-4 text-secondary"></i>
                  </div>
                  <div>
                    <p class="font-semibold text-sm">Draft</p>
                    <p class="text-xs text-secondary">Hidden from store</p>
                  </div>
                </div>
                <input type="radio" name="status" value="draft" class="size-4 text-primary focus:ring-primary border-border">
              </label>
            </div>
          </div>

          <!-- Color Variants Card (Dynamic CRUD with Image) -->
          <div class="bg-white rounded-2xl border border-border p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-lg font-bold flex items-center gap-2">
                <i data-lucide="palette" class="size-5 text-primary"></i>
                Color Variants
              </h3>
            </div>
            
            <div id="variantsContainer" class="flex flex-col gap-4">
              <?php foreach ($variants as $row): ?>
                <div class="dynamic-row bg-muted/30 border border-border rounded-xl p-4 relative group transition-all duration-200 hover:border-muted-foreground/30 hover:bg-muted/50">
                  
                  <button type="button" onclick="removeRow(this)" class="absolute top-3 right-3 size-7 flex items-center justify-center rounded-xl bg-white border border-border shadow-sm text-secondary hover:text-error hover:bg-error/10 hover:border-error/20 md:opacity-0 md:group-hover:opacity-100 transition-all duration-200 z-30" title="Remove Variant">
                    <i data-lucide="x" class="size-4"></i>
                  </button>
                  
                  <div class="flex gap-4 sm:gap-7 pr-4">
                    <div class="shrink-0">
                      <label class="file-upload-wrapper relative w-20 h-20 border-2 border-dashed border-border rounded-xl flex flex-col items-center justify-center bg-white hover:bg-muted transition-colors overflow-hidden group/img cursor-pointer">
                        <input type="file" accept="image/*" onchange="previewImage(this)" class="hidden">
                        <img src="<?= !empty($row['product_image']) ? 'storage/image/' . $row['product_image'] : 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=200&h=200&fit=crop'; ?>" class="absolute inset-0 w-full h-full object-cover z-10" alt="<?= $row['product_image'] ?>">
                        <div class="absolute inset-0 bg-black/40 z-20 hidden group-hover/img:flex items-center justify-center">
                          <i data-lucide="edit-2" class="size-4 text-white"></i>
                        </div>
                        <i data-lucide="image-plus" class="size-6 text-secondary mb-1 hidden"></i>
                        <span class="text-[10px] font-medium text-secondary hidden">Upload</span>
                      </label>
                    </div>
                    
                    <div class="flex-1 flex flex-col gap-3 justify-center">
                      <div>
                        <input type="text" value="<?= $row['color_name'] ?>" placeholder="Color Name (e.g. Red)" class="w-full bg-white border border-border rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all text-sm font-medium">
                      </div>
                      <div class="flex items-center gap-2">
                        <span class="text-xs text-secondary font-medium w-12">Stock:</span>
                        <input type="number" value="<?= $row['color_stok'] ?>" placeholder="0" class="w-24 bg-white border border-border rounded-lg px-3 py-1.5 outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all text-sm">
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>

              
            </div>

            <button type="button" onclick="addVariantRow()" class="w-full mt-4 py-3 border-2 border-dashed border-primary/30 rounded-xl text-primary font-semibold hover:bg-primary/5 hover:border-primary/50 transition-all flex items-center justify-center gap-2 cursor-pointer">
              <i data-lucide="plus-circle" class="size-5"></i>
              Add Another Variant
            </button>
          </div>

        </div>
      </form>

    </div>
  </main>
</div>

<!-- Search Modal -->
<div id="search-modal" class="fixed inset-0 bg-foreground/50 z-[100] hidden items-start justify-center pt-[10vh] px-4 transition-opacity">
  <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[80vh] overflow-hidden shadow-2xl transform transition-transform scale-95" id="search-modal-content">
    <div class="p-4 border-b border-border">
      <div class="flex items-center gap-3 bg-muted rounded-xl px-4 border border-border focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 transition-all">
        <i data-lucide="search" class="size-5 text-secondary"></i>
        <input type="text" id="search-input" placeholder="Search products, orders, customers..." class="flex-1 py-3 bg-transparent outline-none text-foreground placeholder:text-secondary">
        <kbd class="hidden sm:inline-flex px-2 py-1 bg-white rounded-lg text-[10px] font-semibold text-secondary border border-border shadow-sm">ESC</kbd>
      </div>
    </div>
    <div class="p-4 overflow-y-auto max-h-[60vh]">
      <p class="text-xs font-semibold text-secondary uppercase tracking-wider mb-3 px-2">Quick Links</p>
      <div class="flex flex-col gap-1">
        <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-muted transition-all group">
          <div class="size-10 bg-primary/10 rounded-xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors"><i data-lucide="plus" class="size-5 text-primary group-hover:text-white"></i></div>
          <div class="flex-1 min-w-0"><p class="font-medium text-foreground truncate">Add New Product</p><p class="text-xs text-secondary truncate">Create a new listing</p></div>
        </a>
        <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-muted transition-all group">
          <div class="size-10 bg-success/10 rounded-xl flex items-center justify-center group-hover:bg-success group-hover:text-white transition-colors"><i data-lucide="shopping-cart" class="size-5 text-success group-hover:text-white"></i></div>
          <div class="flex-1 min-w-0"><p class="font-medium text-foreground truncate">Recent Orders</p><p class="text-xs text-secondary truncate">View pending fulfillments</p></div>
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Page Not Found Modal (for dummy links) -->
<div id="page-not-found-modal" class="fixed inset-0 bg-foreground/50 z-[100] hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl p-8 max-w-sm w-full text-center shadow-2xl">
    <div class="size-16 bg-muted rounded-2xl flex items-center justify-center mx-auto mb-4">
      <i data-lucide="compass" class="size-8 text-secondary"></i>
    </div>
    <h3 class="text-xl font-bold text-foreground mb-2">Page Not Available</h3>
    <p class="text-secondary text-sm mb-6">This is a demo interface. Navigation links are placeholders.</p>
    <button onclick="closePageNotFoundModal()" class="w-full py-3 bg-primary hover:bg-primary-hover text-white rounded-xl font-semibold transition-colors">Got it</button>
  </div>
</div>

<script>
  // Initialize Lucide Icons
  document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    
    // Setup dummy links
    document.querySelectorAll('a[href="#"]').forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('page-not-found-modal').classList.remove('hidden');
      });
    });
  });

  // Sidebar Toggle
  function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
  }

  // Modals
  function closePageNotFoundModal() { 
    document.getElementById('page-not-found-modal').classList.add('hidden'); 
  }

  // Search Modal Logic
  const searchModal = document.getElementById('search-modal');
  const searchInput = document.getElementById('search-input');
  const searchContent = document.getElementById('search-modal-content');

  function openSearchModal() { 
    searchModal.classList.remove('hidden'); 
    searchModal.classList.add('flex'); 
    setTimeout(() => {
      searchContent.classList.remove('scale-95');
      searchContent.classList.add('scale-100');
      searchInput.focus(); 
    }, 10);
  }
  
  function closeSearchModal() { 
    searchContent.classList.remove('scale-100');
    searchContent.classList.add('scale-95');
    setTimeout(() => {
      searchModal.classList.add('hidden'); 
      searchModal.classList.remove('flex'); 
    }, 150);
  }

  searchModal.addEventListener('click', function(e) { 
    if (e.target === this) closeSearchModal(); 
  });
  
  document.addEventListener('keydown', function(e) { 
    if (e.key === 'Escape' && !searchModal.classList.contains('hidden')) closeSearchModal(); 
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') { 
      e.preventDefault(); 
      openSearchModal(); 
    } 
  });

  // Dynamic CRUD Logic
  function removeRow(btn) {
    const row = btn.closest('.dynamic-row');
    row.style.opacity = '0';
    row.style.transform = 'scale(0.95)';
    row.style.transition = 'all 0.2s ease-out';
    setTimeout(() => {
      row.remove();
    }, 200);
  }

  function addSpecRow() {
    const container = document.getElementById('specsContainer');
    const row = document.createElement('div');
    row.className = 'dynamic-row flex flex-col sm:flex-row items-start sm:items-center gap-3 bg-muted/50 p-3 sm:p-0 sm:bg-transparent rounded-xl border border-border sm:border-none opacity-0 translate-y-2 transition-all duration-300';
    
    row.innerHTML = `
      <div class="w-full sm:flex-1">
        <label class="sm:hidden text-xs font-medium text-secondary mb-1 block">Name</label>
        <input type="text" placeholder="e.g. Weight" class="w-full bg-white sm:bg-muted border border-border rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all text-sm">
      </div>
      <div class="w-full sm:flex-1">
        <label class="sm:hidden text-xs font-medium text-secondary mb-1 block">Value</label>
        <input type="text" placeholder="e.g. 250g" class="w-full bg-white sm:bg-muted border border-border rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all text-sm">
      </div>
      <button type="button" onclick="removeRow(this)" class="w-full sm:w-10 h-10 flex items-center justify-center rounded-xl text-error hover:bg-error/10 transition-colors shrink-0 border border-error/20 sm:border-none mt-2 sm:mt-0" title="Remove">
        <i data-lucide="trash-2" class="size-4 sm:size-5"></i>
        <span class="sm:hidden ml-2 text-sm font-medium">Remove Spec</span>
      </button>
    `;
    
    container.appendChild(row);
    lucide.createIcons();
    
    // Animate in
    requestAnimationFrame(() => {
      row.classList.remove('opacity-0', 'translate-y-2');
    });
  }

  function addVariantRow() {
    const container = document.getElementById('variantsContainer');
    const row = document.createElement('div');
    row.className = 'dynamic-row bg-muted/30 border border-border rounded-xl p-4 relative group opacity-0 translate-y-2 transition-all duration-300';
    
    row.innerHTML = `
      <button type="button" onclick="removeRow(this)" class="absolute top-2 right-2 size-8 flex items-center justify-center rounded-lg text-secondary hover:text-error hover:bg-error/10 transition-colors" title="Remove Variant">
        <i data-lucide="x" class="size-4"></i>
      </button>
      
      <div class="flex gap-4">
        <div class="shrink-0">
          <label class="file-upload-wrapper relative w-20 h-20 border-2 border-dashed border-border rounded-xl flex flex-col items-center justify-center bg-white hover:bg-muted transition-colors overflow-hidden group/img">
            <input type="file" accept="image/*" onchange="previewImage(this)">
            <img src="" class="absolute inset-0 w-full h-full object-cover z-10 hidden" alt="Variant">
            <div class="absolute inset-0 bg-black/40 z-20 hidden group-hover/img:flex items-center justify-center">
              <i data-lucide="edit-2" class="size-5 text-white"></i>
            </div>
            <i data-lucide="image-plus" class="size-6 text-secondary mb-1"></i>
            <span class="text-[10px] font-medium text-secondary">Upload</span>
          </label>
        </div>
        
        <div class="flex-1 flex flex-col gap-3 justify-center">
          <div>
            <input type="text" placeholder="Color Name (e.g. Blue)" class="w-full bg-white border border-border rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all text-sm font-medium">
          </div>
          <div class="flex items-center gap-2">
            <span class="text-xs text-secondary font-medium w-12">Stock:</span>
            <input type="number" placeholder="0" class="w-24 bg-white border border-border rounded-lg px-3 py-1.5 outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all text-sm">
          </div>
        </div>
      </div>
    `;
    
    container.appendChild(row);
    lucide.createIcons();
    
    // Animate in
    requestAnimationFrame(() => {
      row.classList.remove('opacity-0', 'translate-y-2');
    });
  }

  // Image Preview Logic
  function previewImage(input) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        const label = input.closest('label');
        const img = label.querySelector('img');
        const icon = label.querySelector('i[data-lucide="image-plus"]');
        const text = label.querySelector('span');
        
        img.src = e.target.result;
        img.classList.remove('hidden');
        if(icon) icon.classList.add('hidden');
        if(text) text.classList.add('hidden');
      }
      reader.readAsDataURL(input.files[0]);
    }
  }

  // Save Action & Toast
  function saveProduct() {
    // Basic validation simulation
    const name = document.getElementById('inputProductName').value;
    if(!name) {
      showToast('Product name is required', 'error');
      return;
    }
    
    // Simulate API call
    const btn = document.querySelector('button[onclick="saveProduct()"]');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="loader-2" class="size-4 animate-spin"></i> Saving...';
    btn.disabled = true;
    lucide.createIcons();

    setTimeout(() => {
      btn.innerHTML = originalText;
      btn.disabled = false;
      lucide.createIcons();
      showToast('Product updated successfully!', 'success');
    }, 800);
  }

  function showToast(msg, type='success') {
    document.getElementById('toast')?.remove();
    const t = document.createElement('div');
    t.id = 'toast';
    
    const bgClass = type === 'success' ? 'bg-foreground' : 'bg-error';
    const icon = type === 'success' ? 'check-circle-2' : 'alert-circle';
    
    t.className = `fixed bottom-6 left-1/2 -translate-x-1/2 sm:left-auto sm:right-6 sm:translate-x-0 ${bgClass} text-white px-5 py-3.5 rounded-xl shadow-2xl z-[200] flex items-center gap-3 transition-all duration-300 opacity-0 translate-y-4 font-medium text-sm w-[90%] sm:w-auto max-w-sm`;
    
    t.innerHTML = `
      <i data-lucide="${icon}" class="size-5 shrink-0"></i>
      <span class="flex-1">${msg}</span>
    `;
    
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