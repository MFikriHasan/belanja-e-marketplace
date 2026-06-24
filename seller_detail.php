<?php
  require 'koneksi.php';
  include 'login_check.php';
  check_access_control('buyer');

  $seller_id = (int)$_GET['id'];

  // seller profile
  $q_profile = "SELECT * FROM seller WHERE id = ?";
  $pre = $koneksi->prepare($q_profile);

  $pre->bind_param("i", $seller_id);
  $pre->execute();
  $seller = $pre->get_result()->fetch_assoc();
  
  // seller category list
  $q_category = "SELECT c.id, 
                c.name AS category_name, 
                c.icon AS category_icon,
                COUNT(p.id) AS total_products
                FROM category c
                JOIN product p ON  p.category_id = c.id
                WHERE p.seller_id = ?
                GROUP BY c.id
                ORDER BY total_products DESC";
    $pre = $koneksi->prepare($q_category);
    $pre->bind_param("i", $seller_id);
    $pre->execute();
    $categories = $pre->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // sidebar category filter
    $category_filter = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
    
    $q_products = "SELECT p.id,
                  p.name AS product_name,
                  p.price,
                  (SELECT cv.product_image FROM color_varian cv WHERE cv.product_id = p.id LIMIT 1) AS product_image,
                  COALESCE((SELECT SUM(td.qty) FROM transaction_det td WHERE td.color_varian_id IN (SELECT id FROM color_varian WHERE product_id = p.id)), 0) AS sold_total
                  FROM product p
                  WHERE p.seller_id = ?";
    
    if ($category_filter > 0) {
    $q_products .= " AND p.category_id = " . $category_filter;
    }

    $q_products .= " ORDER BY p.id DESC";

    $q_products = $koneksi->prepare($q_products);
    $q_products->bind_param("i", $seller_id);
    $q_products->execute();
    $products = $q_products->get_result()->fetch_all(MYSQLI_ASSOC);
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Belanja - Official Store</title>
<link href="https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@100..900&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
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
        },
        fontFamily: {
          sans: ['Lexend Deca', 'sans-serif'],
        }
      }
    }
  }
</script>
<style type="text/tailwindcss">
  @layer utilities {
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
  }
</style>
</head>
<body class="font-sans bg-muted min-h-screen text-foreground">

<!-- Navbar -->
<?php
  include 'navbar_buyer.php';
?>

<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 md:px-8 py-6 md:py-8">
  
  <!-- Store Header -->
  <div onclick="openStoreModal()" class="bg-white rounded-3xl border border-border p-4 md:p-6 flex items-center justify-between cursor-pointer hover:border-primary/50 hover:shadow-md transition-all group relative overflow-hidden">
    <!-- Decorative background element -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none"></div>
    
    <div class="flex items-center gap-4 md:gap-6 relative z-10">
      <div class="relative">
        <img src="<?= !empty($seller['logo']) ? 'storage/image/' . $seller['logo'] : 'https://images.unsplash.com/photo-1560179707-f14e90ef3623?w=200&h=200&fit=crop' ?>" alt="Store Logo" class="w-16 h-16 md:w-20 md:h-20 rounded-full object-cover border-2 border-white shadow-sm">
        <div class="absolute bottom-0 right-0 w-5 h-5 bg-success border-2 border-white rounded-full flex items-center justify-center" title="Verified Store">
          <i data-lucide="check" class="w-3 h-3 text-white"></i>
        </div>
      </div>
      <div>
        <div class="flex items-center gap-2 mb-1">
          <h1 class="font-bold text-xl md:text-2xl text-foreground"><?= $seller['name'] ?></h1>
          <span class="bg-primary/10 text-primary text-[10px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider hidden sm:inline-block">Mall</span>
        </div>
        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-secondary">
          <span class="flex items-center gap-1"><i data-lucide="users" class="w-4 h-4"></i> 1.2M Followers</span>
          <span class="hidden sm:flex items-center gap-1"><i data-lucide="star" class="w-4 h-4 text-warning fill-warning"></i> 4.9 Rating</span>
          <span class="flex items-center gap-1 text-success"><i data-lucide="clock" class="w-4 h-4"></i> Active Now</span>
        </div>
      </div>
    </div>
    <button class="w-10 h-10 md:w-12 md:h-12 shrink-0 flex items-center justify-center rounded-full bg-card-grey text-secondary group-hover:bg-primary group-hover:text-white transition-colors relative z-10">
      <i data-lucide="chevron-right" class="w-5 h-5 md:w-6 md:h-6"></i>
    </button>
  </div>

  <!-- Mobile Categories (Horizontal Scroll) -->
  <div class="lg:hidden overflow-x-auto scrollbar-hide -mx-4 px-4 flex gap-3 mt-6 pb-2">
    <button class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-full text-sm font-medium whitespace-nowrap shrink-0">
      <i data-lucide="layout-grid" class="w-4 h-4"></i> All Products
    </button>
    <button class="flex items-center gap-2 px-4 py-2 bg-white border border-border text-foreground hover:bg-card-grey rounded-full text-sm font-medium whitespace-nowrap shrink-0 transition-colors">
      <i data-lucide="headphones" class="w-4 h-4 text-secondary"></i> Audio
    </button>
    <button class="flex items-center gap-2 px-4 py-2 bg-white border border-border text-foreground hover:bg-card-grey rounded-full text-sm font-medium whitespace-nowrap shrink-0 transition-colors">
      <i data-lucide="watch" class="w-4 h-4 text-secondary"></i> Wearables
    </button>
    <button class="flex items-center gap-2 px-4 py-2 bg-white border border-border text-foreground hover:bg-card-grey rounded-full text-sm font-medium whitespace-nowrap shrink-0 transition-colors">
      <i data-lucide="laptop" class="w-4 h-4 text-secondary"></i> Computing
    </button>
    <button class="flex items-center gap-2 px-4 py-2 bg-white border border-border text-foreground hover:bg-card-grey rounded-full text-sm font-medium whitespace-nowrap shrink-0 transition-colors">
      <i data-lucide="cable" class="w-4 h-4 text-secondary"></i> Accessories
    </button>
  </div>

  <div class="flex flex-col lg:flex-row gap-8 mt-6 lg:mt-8">
    
   <!-- Sidebar Catalog (Desktop) -->
  <aside class="hidden lg:block w-64 shrink-0">
    <div class="sticky top-24 bg-white rounded-2xl border border-border p-4">
      <h3 class="font-bold text-sm text-secondary uppercase tracking-wider mb-4 px-2">Categories</h3>
      <nav class="flex flex-col gap-1">
        
        
        <?php 
        
        $is_all_active = ($category_filter == 0); 
        ?>
        <a href="?id=<?= $seller_id ?>" 
          class="flex items-center justify-between px-3 py-2.5 rounded-xl font-medium transition-colors <?= $is_all_active ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-card-grey hover:text-foreground' ?>">
          <div class="flex items-center gap-3">
            <i data-lucide="layout-grid" class="w-5 h-5"></i>
            <span>All Products</span>
          </div>
        </a>

        
        <?php foreach ($categories as $row): ?>
          <?php 
          
          $is_category_active = ($category_filter == $row['id']); 
          ?>
          <a href="?id=<?= $seller_id ?>&category_id=<?= $row['id'] ?>"  
            class="flex items-center justify-between px-3 py-2.5 rounded-xl font-medium transition-colors <?= $is_category_active ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-card-grey hover:text-foreground' ?>">
            <div class="flex items-center gap-3">
              
              <i data-lucide="<?= !empty($row['category_icon']) ? $row['category_icon'] : 'tag' ?>" class="w-5 h-5"></i>
              <span><?= $row['category_name'] ?></span>
            </div>
            <span class="text-xs px-2 py-0.5 rounded-full <?= $is_category_active ? 'bg-primary/20 text-primary' : 'bg-card-grey' ?>">
              <?= $row['total_products'] ?? 0 ?>
            </span>
          </a>
        <?php endforeach; ?>

      </nav>
    </div>
  </aside>

    <!-- Main Content Area -->
    <div class="flex-1 min-w-0 flex flex-col gap-8">
      
      <!-- Vouchers Section -->
      <section>
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-bold text-lg md:text-xl">Store Vouchers</h2>
        </div>
        <div class="flex gap-4 overflow-x-auto scrollbar-hide pb-2 -mx-4 px-4 md:mx-0 md:px-0">
          
          <!-- Voucher 1 -->
          <div class="min-w-[280px] md:min-w-[300px] flex rounded-2xl border border-border overflow-hidden bg-white shadow-sm hover:shadow-md transition-shadow">
            <div class="bg-primary/10 p-4 flex flex-col justify-center border-r border-dashed border-primary/30 w-24 shrink-0 items-center relative">
              <div class="absolute -left-2 top-1/2 -translate-y-1/2 w-4 h-4 bg-muted rounded-full"></div>
              <span class="font-bold text-primary text-2xl">$10</span>
              <span class="text-xs text-primary font-bold tracking-wider uppercase">OFF</span>
            </div>
            <div class="p-4 flex-1 flex flex-col justify-between relative">
              <div class="absolute -right-2 top-1/2 -translate-y-1/2 w-4 h-4 bg-muted rounded-full"></div>
              <div>
                <p class="font-bold text-sm text-foreground">Min. Spend $100</p>
                <p class="text-xs text-secondary mt-1">Valid till 31 Oct</p>
              </div>
              <button onclick="showToast('Voucher claimed successfully!')" class="text-xs font-bold text-white bg-primary rounded-full py-2 px-5 w-max mt-3 hover:bg-primary-hover transition-colors">Claim</button>
            </div>
          </div>

          <!-- Voucher 2 -->
          <div class="min-w-[280px] md:min-w-[300px] flex rounded-2xl border border-border overflow-hidden bg-white shadow-sm hover:shadow-md transition-shadow">
            <div class="bg-warning/10 p-4 flex flex-col justify-center border-r border-dashed border-warning/30 w-24 shrink-0 items-center relative">
              <div class="absolute -left-2 top-1/2 -translate-y-1/2 w-4 h-4 bg-muted rounded-full"></div>
              <span class="font-bold text-warning text-2xl">15%</span>
              <span class="text-xs text-warning font-bold tracking-wider uppercase">OFF</span>
            </div>
            <div class="p-4 flex-1 flex flex-col justify-between relative">
              <div class="absolute -right-2 top-1/2 -translate-y-1/2 w-4 h-4 bg-muted rounded-full"></div>
              <div>
                <p class="font-bold text-sm text-foreground">Min. Spend $250</p>
                <p class="text-xs text-secondary mt-1">Capped at $50</p>
              </div>
              <button onclick="showToast('Voucher claimed successfully!')" class="text-xs font-bold text-white bg-warning rounded-full py-2 px-5 w-max mt-3 hover:bg-yellow-600 transition-colors">Claim</button>
            </div>
          </div>

        </div>
      </section>

      <!-- Flash Sale Banner -->
      <section class="bg-gradient-to-r from-primary to-blue-900 rounded-3xl p-6 md:p-8 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative overflow-hidden shadow-lg">
        <!-- Abstract shapes -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-2xl -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-20 w-32 h-32 bg-white/10 rounded-full blur-xl -mb-10"></div>
        
        <div class="relative z-10">
          <div class="flex items-center gap-2 mb-2">
            <div class="bg-white/20 p-1.5 rounded-lg backdrop-blur-sm">
              <i data-lucide="zap" class="w-5 h-5 text-warning fill-warning"></i>
            </div>
            <h2 class="font-bold text-2xl md:text-3xl tracking-tight">SUPER FLASH SALE</h2>
          </div>
          <div class="flex items-center gap-3 text-white/90">
            <span class="text-sm font-medium">Ends in:</span>
            <div class="flex gap-1 font-mono font-bold text-lg">
              <span class="bg-black/30 backdrop-blur-sm px-2 py-1 rounded-md">02</span>:
              <span class="bg-black/30 backdrop-blur-sm px-2 py-1 rounded-md">45</span>:
              <span class="bg-black/30 backdrop-blur-sm px-2 py-1 rounded-md">10</span>
            </div>
          </div>
        </div>
        <button class="relative z-10 bg-white text-primary font-bold py-3 px-8 rounded-full hover:bg-card-grey transition-colors shadow-md whitespace-nowrap w-full md:w-auto">
          Shop Deals Now
        </button>
      </section>

      <!-- Product Grid -->
      <section>
        <div class="flex items-center justify-between mb-6">
          <h2 class="font-bold text-xl md:text-2xl">Recommended For You</h2>
          <div class="flex items-center gap-2">
            <span class="text-sm text-secondary hidden sm:inline-block">Sort by:</span>
            <select class="bg-transparent border-none text-sm font-medium text-foreground focus:ring-0 cursor-pointer outline-none">
              <option>Popular</option>
              <option>Latest</option>
              <option>Price: Low to High</option>
            </select>
          </div>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
          
          <?php foreach ($products as $row): ?>
            <div class="group bg-white rounded-2xl border border-border overflow-hidden hover:shadow-xl hover:border-primary/30 transition-all duration-300 cursor-pointer flex flex-col">
              <div class="relative aspect-square bg-card-grey overflow-hidden">
                <img src="<?= !empty($row['product_image']) ? 'storage/image/' . $row['product_image'] : 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=500&h=500&fit=crop' ?>" alt="Watch" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                <button class="absolute top-3 right-3 w-8 h-8 bg-white/80 backdrop-blur-sm rounded-full flex items-center justify-center text-secondary hover:text-error hover:bg-white transition-colors shadow-sm opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 duration-300">
                  <i data-lucide="heart" class="w-4 h-4"></i>
                </button>
              </div>
              <div class="p-4 flex flex-col flex-1">
                <a href="product_detail.php?id=<?= $row['id'] ?>">
                  <h3 class="font-medium text-sm md:text-base text-foreground line-clamp-2 mb-2 group-hover:text-primary transition-colors"><?= $row['product_name'] ?></h3>
                </a>
                <div class="mt-auto">
                  <div class="flex items-center gap-1 mb-2">
                    <i data-lucide="star" class="w-3.5 h-3.5 text-warning fill-warning"></i>
                    <span class="text-xs font-medium text-secondary">4.8 <span class="text-border mx-1">|</span> <?= number_format($row['sold_total'], 0, ",", ".") ?> Sold</span>
                  </div>
                  <div class="flex items-end justify-between">
                    <div>
                      <p class="font-bold text-lg md:text-xl text-foreground">$<?= number_format($row['price'], 0, ",", ".") ?></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>

        </div>
        <?php if (empty($products)): ?>
          <p class="text-center">this seller has no products yet.</p>
        <?php endif; ?>
        
      </section>

    </div>
  </div>
</main>

<!-- Store Info Modal -->
<div id="store-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4 opacity-0 transition-opacity duration-300">
  <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl transform scale-95 transition-transform duration-300" id="store-modal-content">
    <div class="relative h-32 bg-gradient-to-r from-primary to-blue-800">
      <button onclick="closeStoreModal()" class="absolute top-4 right-4 w-8 h-8 bg-black/20 hover:bg-black/40 text-white rounded-full flex items-center justify-center transition-colors backdrop-blur-sm">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>
    <div class="px-6 pb-6 relative mt-3">
      <div class="flex justify-between items-end -mt-12 mb-4">
        <img src="<?= !empty($seller['logo']) ? 'storage/image/' . $seller['logo'] : 'https://images.unsplash.com/photo-1560179707-f14e90ef3623?w=100&h=100&fit=crop' ?>" alt="Store Logo" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md bg-white">
        <button class="px-6 py-2 bg-primary text-white font-bold rounded-full hover:bg-primary-hover transition-colors shadow-sm mb-2">
          Follow
        </button>
      </div>
      
      <h2 id="detailStoreName" class="font-bold text-2xl mb-1"><?= $seller['name'] ?></h2>
      <p class="text-secondary text-sm mb-6"><?= $seller['description'] ?? 'This shop has no description yet.' ?></p>
      
      <div class="space-y-4">
        <div class="flex items-center gap-4 p-3 rounded-2xl bg-card-grey">
          <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shrink-0 shadow-sm">
            <i data-lucide="star" class="w-5 h-5 text-warning fill-warning"></i>
          </div>
          <div>
            <p class="text-xs text-secondary font-medium">Store Rating</p>
            <p class="font-bold text-foreground">4.9 / 5.0 <span class="text-xs font-normal text-secondary">(12.5k reviews)</span></p>
          </div>
        </div>
        
        <div class="flex items-center gap-4 p-3 rounded-2xl bg-card-grey">
          <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shrink-0 shadow-sm">
            <i data-lucide="clock" class="w-5 h-5 text-primary"></i>
          </div>
          <div>
            <p class="text-xs text-secondary font-medium">Operational Hours</p>
            <p class="font-bold text-foreground"><?= !empty($seller['opening_hour']) ? date("H:i", strtotime($seller['opening_hour'])) : '09.00' ?> - <?= !empty($seller['opening_hour']) ? date("H:i", strtotime($seller['closing_hour'])) : '17.00' ?></p>
          </div>
        </div>
        
        <div class="flex items-center gap-4 p-3 rounded-2xl bg-card-grey">
          <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shrink-0 shadow-sm">
            <i data-lucide="map-pin" class="w-5 h-5 text-error"></i>
          </div>
          <div>
            <p class="text-xs text-secondary font-medium">Location</p>
            <p class="font-bold text-foreground"><?= $seller['address'] ?? 'This shop has not set up location yet.' ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Search Modal -->
<div id="search-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[100] hidden items-start justify-center pt-20 p-4">
  <div class="bg-white rounded-3xl w-full max-w-2xl max-h-[80vh] overflow-hidden shadow-2xl">
    <div class="p-4 border-b border-border">
      <div class="flex items-center gap-3 bg-card-grey rounded-2xl px-4 border border-transparent focus-within:border-primary focus-within:bg-white transition-all">
        <i data-lucide="search" class="w-5 h-5 text-secondary"></i>
        <input type="text" id="search-input" placeholder="Search products, brands..." class="flex-1 py-3.5 bg-transparent outline-none text-foreground" oninput="handleSearch(this.value)">
        <kbd class="hidden sm:inline-flex px-2 py-1 bg-white rounded-lg text-xs font-medium text-secondary border border-border shadow-sm">ESC</kbd>
      </div>
    </div>
    <div class="p-4 overflow-y-auto max-h-[60vh]">
      <p class="text-xs font-bold text-secondary uppercase tracking-wider mb-3 px-2">Popular Searches</p>
      <div id="search-results" class="flex flex-col gap-1">
        <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-card-grey transition-all group">
          <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center group-hover:bg-white group-hover:shadow-sm transition-all"><i data-lucide="headphones" class="w-5 h-5 text-primary"></i></div>
          <div class="flex-1 min-w-0"><p class="font-medium text-foreground truncate">Wireless Earbuds</p></div>
          <i data-lucide="arrow-up-left" class="w-4 h-4 text-secondary opacity-0 group-hover:opacity-100 transition-opacity"></i>
        </a>
        <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-card-grey transition-all group">
          <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center group-hover:bg-white group-hover:shadow-sm transition-all"><i data-lucide="watch" class="w-5 h-5 text-primary"></i></div>
          <div class="flex-1 min-w-0"><p class="font-medium text-foreground truncate">Smart Watches 2023</p></div>
          <i data-lucide="arrow-up-left" class="w-4 h-4 text-secondary opacity-0 group-hover:opacity-100 transition-opacity"></i>
        </a>
        <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-card-grey transition-all group">
          <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center group-hover:bg-white group-hover:shadow-sm transition-all"><i data-lucide="laptop" class="w-5 h-5 text-primary"></i></div>
          <div class="flex-1 min-w-0"><p class="font-medium text-foreground truncate">Gaming Laptops</p></div>
          <i data-lucide="arrow-up-left" class="w-4 h-4 text-secondary opacity-0 group-hover:opacity-100 transition-opacity"></i>
        </a>
      </div>
    </div>
  </div>
</div>

<script>
  // Initialize Icons
  document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
  });

  // Store Modal Logic
  const storeModal = document.getElementById('store-modal');
  const storeModalContent = document.getElementById('store-modal-content');

  function openStoreModal() {
    storeModal.classList.remove('hidden');
    storeModal.classList.add('flex');
    // Small delay to allow display:flex to apply before animating opacity
    requestAnimationFrame(() => {
      storeModal.classList.remove('opacity-0');
      storeModalContent.classList.remove('scale-95');
    });
  }

  function closeStoreModal() {
    storeModal.classList.add('opacity-0');
    storeModalContent.classList.add('scale-95');
    setTimeout(() => {
      storeModal.classList.add('hidden');
      storeModal.classList.remove('flex');
    }, 300); // Match transition duration
  }

  // Close modal on outside click
  storeModal.addEventListener('click', function(e) {
    if (e.target === this) closeStoreModal();
  });

  // Search Modal Logic
  const searchModal = document.getElementById('search-modal');
  const searchInput = document.getElementById('search-input');

  function openSearchModal() {
    searchModal.classList.remove('hidden');
    searchModal.classList.add('flex');
    setTimeout(() => searchInput.focus(), 10);
  }

  function closeSearchModal() {
    searchModal.classList.add('hidden');
    searchModal.classList.remove('flex');
    searchInput.value = '';
  }

  searchModal.addEventListener('click', function(e) {
    if (e.target === this) closeSearchModal();
  });

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeStoreModal();
      closeSearchModal();
    }
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
      e.preventDefault();
      openSearchModal();
    }
  });

  function handleSearch(val) {
    // Dummy search handler
    console.log('Searching for:', val);
  }

  
</script>


</body>
</html>