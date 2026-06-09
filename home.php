<?php
require 'koneksi.php';


include 'login_check.php';

// categories

$sqlC = "SELECT * FROM category ORDER BY RAND() LIMIT 10";

$exec = $koneksi->execute_query($sqlC);

$categories = $exec->fetch_all(MYSQLI_ASSOC);

// new releases

$sqlN = "SELECT * FROM product LIMIT 5";

$exec = $koneksi->execute_query($sqlN);

$new_releases = $exec->fetch_all(MYSQLI_ASSOC);

// sellers

$sqlS = "SELECT id,  name, email FROM seller LIMIT 6";

$exec = $koneksi->execute_query($sqlS);

$sellers = $exec->fetch_all(MYSQLI_ASSOC);

?>


<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Belanja - E-Commerce Homepage</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<style type="text/tailwindcss">
  :root {
    --primary: #2563EB; /* blue-600 */
    --primary-hover: #1D4ED8; /* blue-700 */
    --foreground: #111827; /* gray-900 */
    --secondary: #6B7280; /* gray-500 */
    --muted: #F3F4F6; /* gray-100 */
    --border: #E5E7EB; /* gray-200 */
    --success: #10B981; /* emerald-500 */
    --warning: #F59E0B; /* amber-500 */
    --font-sans: 'Poppins', sans-serif;
  }
  @theme inline {
    --color-primary: var(--primary);
    --color-primary-hover: var(--primary-hover);
    --color-foreground: var(--foreground);
    --color-secondary: var(--secondary);
    --color-muted: var(--muted);
    --color-border: var(--border);
    --color-success: var(--success);
    --color-warning: var(--warning);
    --font-sans: var(--font-sans);
  }
  body { font-family: var(--font-sans); }
  .scrollbar-hide::-webkit-scrollbar { display: none; }
  .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
  .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>
</head>
<body class="bg-gray-50 text-foreground min-h-screen flex flex-col">

<!-- Navbar -->
<?php
  include 'navbar_buyer.php';
?>

<!-- Main Content -->
<main class="flex-1 pb-12">
  
  <!-- Hero Banner -->
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
    <div class="bg-primary rounded-3xl overflow-hidden relative h-[250px] sm:h-[350px] flex items-center shadow-lg">
      <img src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1200&h=600&fit=crop" alt="Sale Banner" class="absolute inset-0 w-full h-full object-cover opacity-30 mix-blend-overlay">
      <div class="absolute inset-0 bg-gradient-to-r from-primary/90 to-transparent"></div>
      <div class="relative z-10 px-6 sm:px-12 md:px-16 max-w-2xl">
        <span class="inline-block py-1 px-3 rounded-full bg-white/20 text-white text-xs font-semibold tracking-wider mb-4 backdrop-blur-sm border border-white/30">SUPER SALE</span>
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white mb-4 leading-tight">Discover the Best Deals of the Season</h1>
        <p class="text-blue-100 mb-6 text-sm sm:text-base hidden sm:block">Up to 50% off on electronics, fashion, home essentials, and more. Limited time offer.</p>
        <button class="bg-white text-primary px-6 py-3 rounded-full font-semibold hover:bg-muted transition-colors shadow-md cursor-pointer flex items-center gap-2">
          Shop Now <i data-lucide="arrow-right" class="h-4 w-4"></i>
        </button>
      </div>
    </div>
  </section>

  <!-- Categories -->
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">
    <h2 class="text-xl font-bold mb-4">Categories</h2>  
    <div class="flex overflow-x-auto scrollbar-hide gap-4 pb-2 -mx-4 px-4 sm:mx-0 sm:px-0">
        <?php foreach ($categories as $category): ?>
            <a href="#" class="flex flex-col items-center gap-3 min-w-[80px] group">
              <div class="w-16 h-16 rounded-2xl bg-white border border-border flex items-center justify-center group-hover:border-primary group-hover:shadow-md transition-all">
                <i data-lucide="<?= $category['icon'] ?>" class="h-8 w-8 text-secondary group-hover:text-primary transition-colors"></i>
              </div>
              <span class="text-xs font-medium text-center group-hover:text-primary transition-colors"><?= $category['name'] ?></span>
            </a>
        <?php endforeach; ?>
    </div>
  </section>

  <!-- Most Picked Products -->
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
    <div class="flex justify-between items-end mb-6">
      <h2 class="text-xl font-bold">Most Picked Products</h2>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-5">
      
      <!-- Product 1 -->
      <div class="bg-white rounded-2xl border border-border overflow-hidden hover:shadow-lg hover:border-primary/30 transition-all cursor-pointer group flex flex-col" onclick="viewProduct('p1')">
        <div class="aspect-square bg-muted relative overflow-hidden">
          <img src="https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=400&h=400&fit=crop" alt="Headphones" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
          <div class="absolute top-2 left-2 bg-primary text-white text-[10px] font-bold px-2 py-1 rounded flex items-center gap-1 shadow-sm">
            <i data-lucide="check-circle-2" class="h-3 w-3"></i> Official
          </div>
        </div>
        <div class="p-3 sm:p-4 flex flex-col flex-1">
          <h3 class="text-sm font-medium line-clamp-2 mb-1 group-hover:text-primary transition-colors">Sony WH-1000XM5 Wireless Noise Cancelling Headphones</h3>
          <div class="mt-auto">
            <p class="text-lg font-bold text-primary mb-2">$298.00</p>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-1 text-xs text-secondary">
                <i data-lucide="star" class="h-3 w-3 fill-warning text-warning"></i>
                <span class="font-medium text-foreground">4.9</span>
                <span class="mx-0.5">•</span>
                <span>1.2k sold</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Product 2 -->
      <div class="bg-white rounded-2xl border border-border overflow-hidden hover:shadow-lg hover:border-primary/30 transition-all cursor-pointer group flex flex-col" onclick="viewProduct('p2')">
        <div class="aspect-square bg-muted relative overflow-hidden">
          <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop" alt="Sneakers" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
          <div class="absolute top-2 left-2 bg-primary text-white text-[10px] font-bold px-2 py-1 rounded flex items-center gap-1 shadow-sm">
            <i data-lucide="check-circle-2" class="h-3 w-3"></i> Official
          </div>
          <div class="absolute top-2 right-2 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm">-20%</div>
        </div>
        <div class="p-3 sm:p-4 flex flex-col flex-1">
          <h3 class="text-sm font-medium line-clamp-2 mb-1 group-hover:text-primary transition-colors">Nike Air Max 270 React Running Shoes</h3>
          <div class="mt-auto">
            <div class="flex items-baseline gap-2 mb-2">
              <p class="text-lg font-bold text-primary">$120.00</p>
              <p class="text-xs text-secondary line-through">$150.00</p>
            </div>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-1 text-xs text-secondary">
                <i data-lucide="star" class="h-3 w-3 fill-warning text-warning"></i>
                <span class="font-medium text-foreground">4.7</span>
                <span class="mx-0.5">•</span>
                <span>850 sold</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Product 3 -->
      <div class="bg-white rounded-2xl border border-border overflow-hidden hover:shadow-lg hover:border-primary/30 transition-all cursor-pointer group flex flex-col" onclick="viewProduct('p3')">
        <div class="aspect-square bg-muted relative overflow-hidden">
          <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=400&fit=crop" alt="Watch" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        </div>
        <div class="p-3 sm:p-4 flex flex-col flex-1">
          <h3 class="text-sm font-medium line-clamp-2 mb-1 group-hover:text-primary transition-colors">Minimalist Smart Watch with Heart Rate Monitor</h3>
          <div class="mt-auto">
            <p class="text-lg font-bold text-primary mb-2">$89.99</p>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-1 text-xs text-secondary">
                <i data-lucide="star" class="h-3 w-3 fill-warning text-warning"></i>
                <span class="font-medium text-foreground">4.5</span>
                <span class="mx-0.5">•</span>
                <span>420 sold</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Product 4 -->
      <div class="bg-white rounded-2xl border border-border overflow-hidden hover:shadow-lg hover:border-primary/30 transition-all cursor-pointer group flex flex-col" onclick="viewProduct('p4')">
        <div class="aspect-square bg-muted relative overflow-hidden">
          <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop" alt="Headphones" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
          <div class="absolute top-2 left-2 bg-primary text-white text-[10px] font-bold px-2 py-1 rounded flex items-center gap-1 shadow-sm">
            <i data-lucide="check-circle-2" class="h-3 w-3"></i> Official
          </div>
        </div>
        <div class="p-3 sm:p-4 flex flex-col flex-1">
          <h3 class="text-sm font-medium line-clamp-2 mb-1 group-hover:text-primary transition-colors">JBL Tune 710BT Wireless Over-Ear Headphones</h3>
          <div class="mt-auto">
            <p class="text-lg font-bold text-primary mb-2">$65.00</p>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-1 text-xs text-secondary">
                <i data-lucide="star" class="h-3 w-3 fill-warning text-warning"></i>
                <span class="font-medium text-foreground">4.6</span>
                <span class="mx-0.5">•</span>
                <span>2.1k sold</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Product 5 (Hidden on mobile/tablet to maintain grid, visible on lg) -->
      <div class="hidden lg:flex bg-white rounded-2xl border border-border overflow-hidden hover:shadow-lg hover:border-primary/30 transition-all cursor-pointer group flex-col" onclick="viewProduct('p5')">
        <div class="aspect-square bg-muted relative overflow-hidden">
          <img src="https://images.unsplash.com/photo-1585386959984-a4155224a1ad?w=400&h=400&fit=crop" alt="Perfume" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        </div>
        <div class="p-3 sm:p-4 flex flex-col flex-1">
          <h3 class="text-sm font-medium line-clamp-2 mb-1 group-hover:text-primary transition-colors">Luxury Eau De Parfum 100ml Natural Spray</h3>
          <div class="mt-auto">
            <p class="text-lg font-bold text-primary mb-2">$145.00</p>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-1 text-xs text-secondary">
                <i data-lucide="star" class="h-3 w-3 fill-warning text-warning"></i>
                <span class="font-medium text-foreground">4.8</span>
                <span class="mx-0.5">•</span>
                <span>310 sold</span>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
    <button class="w-full mt-4 py-3 rounded-xl border border-border text-sm font-medium sm:hidden hover:bg-muted transition-colors">See All Products</button>
  </section>

  <!-- Explore Official Shops -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
    <h2 class="text-xl font-bold mb-6">Explore Official Shops</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        

    <?php foreach ($sellers as $seller): ?>
        <a href="/seller.php?id=<?= $seller['id'] ?>" class="bg-white rounded-2xl border border-border p-4 flex items-center gap-4 hover:border-primary/50 hover:shadow-md transition-all group cursor-pointer text-left decoration-none">
        <div class="w-16 h-16 rounded-full bg-muted border border-border overflow-hidden flex-shrink-0">
          <img src="https://images.unsplash.com/photo-1560159906-839eb14e15a5?w=100&h=100&fit=crop" alt="Shop Logo" class="w-full h-full object-cover">
        </div>
        <div class="flex-1 min-w-0">
          <h3 class="font-bold text-foreground flex items-center gap-1 truncate group-hover:text-primary transition-colors">
            <?= $seller['name'] ?> <i data-lucide="badge-check" class="h-4 w-4 text-primary flex-shrink-0"></i>
          </h3>
          <p class="text-xs text-secondary truncate"><?= $seller['email'] ?></p>
        </div>
        <span class="px-4 py-2 rounded-full border border-primary text-primary text-xs font-semibold group-hover:bg-primary group-hover:text-white transition-colors whitespace-nowrap">
          Visit
        </span>
      </a>
    <?php endforeach; ?>
      

      

    </div>
  </section>

  <!-- New Releases -->
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 mb-8">
    <div class="flex justify-between items-end mb-6">
      <h2 class="text-xl font-bold">New Releases</h2>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-5">
      
            
    <?php foreach ($new_releases as $row): ?>
        <a href="/product_detail.php?id=<?= $row['id'] ?>">
            <div class="bg-white rounded-2xl border border-border overflow-hidden hover:shadow-lg hover:border-primary/30 transition-all cursor-pointer group flex flex-col">
              <div class="aspect-square bg-muted relative overflow-hidden">
                <img src="https://images.unsplash.com/photo-1583394838336-acd977736f90?w=400&h=400&fit=crop" alt="Earbuds" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute top-2 left-2 bg-success text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm">NEW</div>
              </div>
              <div class="p-3 sm:p-4 flex flex-col flex-1">
                <h3 class="text-sm font-medium line-clamp-2 mb-1 group-hover:text-primary transition-colors"><?= $row['name'] ?></h3>
                <div class="mt-auto">
                  <p class="text-lg font-bold text-primary mb-2">$<?= number_format($row['price'], 0, ',', '.') ?></p>
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1 text-xs text-secondary">
                      <i data-lucide="star" class="h-3 w-3 fill-muted text-muted"></i>
                      <span class="font-medium text-foreground">0.0</span>
                      <span class="mx-0.5">•</span>
                      <span>0 sold</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </a>
    <?php endforeach; ?>
      
      

    </div>
  </section>

</main>

<!-- Footer -->
<footer class="bg-white border-t border-border py-8 mt-auto">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-secondary">
    <p>&copy; 2026 Belanja. All rights reserved.</p>
  </div>
</footer>

<!-- Modals -->

<!-- Search Modal (Mobile) -->
<div id="search-modal" class="fixed inset-0 bg-black/60 z-[100] hidden items-start justify-center pt-16 px-4 backdrop-blur-sm transition-opacity">
  <div class="bg-white rounded-3xl w-full max-w-2xl max-h-[80vh] overflow-hidden shadow-2xl flex flex-col">
    <div class="p-4 border-b border-border flex items-center gap-3">
      <div class="flex-1 flex items-center gap-3 bg-muted rounded-full px-4 border border-transparent focus-within:border-primary focus-within:bg-white transition-all">
        <i data-lucide="search" class="size-5 text-secondary"></i>
        <input type="text" id="inputSearchMobile" placeholder="Search Belanja..." class="flex-1 py-3 bg-transparent outline-none text-sm">
      </div>
      <button onclick="closeSearchModal()" class="p-2 text-secondary hover:text-foreground rounded-full hover:bg-muted transition-colors cursor-pointer">
        <i data-lucide="x" class="size-6"></i>
      </button>
    </div>
    <div class="p-4 overflow-y-auto flex-1">
      <p class="text-xs font-semibold text-secondary mb-3 uppercase tracking-wider">Popular Searches</p>
      <div class="flex flex-wrap gap-2 mb-6">
        <span class="px-3 py-1.5 bg-muted rounded-full text-sm cursor-pointer hover:bg-border transition-colors">Wireless Earbuds</span>
        <span class="px-3 py-1.5 bg-muted rounded-full text-sm cursor-pointer hover:bg-border transition-colors">Mechanical Keyboard</span>
        <span class="px-3 py-1.5 bg-muted rounded-full text-sm cursor-pointer hover:bg-border transition-colors">Running Shoes</span>
        <span class="px-3 py-1.5 bg-muted rounded-full text-sm cursor-pointer hover:bg-border transition-colors">Smart Watch</span>
      </div>
    </div>
  </div>
</div>



<script>
  
  document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
  });

  

  // Search Modal Logic
  function openSearchModal() {
    const modal = document.getElementById('search-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => document.getElementById('inputSearchMobile').focus(), 100);
  }

  function closeSearchModal() {
    const modal = document.getElementById('search-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  

  function addToCart() {
    closeProductModal();
    showToast('Added to cart successfully!', 'success');
  }

  // Toast Notification System
  function showToast(msg, type='success') {
    document.getElementById('toast')?.remove();
    const t = document.createElement('div');
    t.id = 'toast';
    const bgClass = type === 'success' ? 'bg-gray-900' : 'bg-red-500';
    const icon = type === 'success' ? 'check-circle' : 'alert-circle';
    
    t.className = `fixed bottom-4 left-1/2 -translate-x-1/2 ${bgClass} text-white px-6 py-3 rounded-full shadow-xl z-[200] transition-all duration-300 opacity-0 translate-y-[20px] flex items-center gap-2 text-sm font-medium`;
    t.innerHTML = `<i data-lucide="${icon}" class="size-4"></i> ${msg}`;
    
    document.body.appendChild(t);
    lucide.createIcons({ root: t });
    
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

  // Close modals on outside click
  document.getElementById('search-modal').addEventListener('click', function(e) {
    if (e.target === this) closeSearchModal();
  });
  

  // Keyboard shortcuts
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeSearchModal();
      closeProductModal();
    }
  });
</script>


</body>
</html>