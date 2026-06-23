<!-- Navbar -->
<nav class="sticky top-0 z-40 bg-white border-b border-border shadow-sm">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16 items-center gap-4">
      
      <!-- Logo -->
      <a href="/home.php" class="flex-shrink-0 flex items-center gap-2 text-primary">
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
        <a href="/cart.php" class="p-2 text-secondary hover:text-primary hover:bg-muted rounded-full transition-colors relative cursor-pointer">
          <i data-lucide="shopping-cart" class="h-6 w-6"></i>
        </a>
        
        <!-- Notifications -->
        <a href="/history_transaction.php" class=" p-2 text-secondary hover:text-primary hover:bg-muted rounded-full transition-colors relative cursor-pointer">
          <i data-lucide="bell" class="h-6 w-6"></i>
          <span class="absolute top-1 right-1 bg-red-500 text-white text-[10px] font-bold h-2 w-2 rounded-full border-2 border-white"></span>
        </a>
        
        <!-- User Profile -->
         <a href="/buyer_profile.php">
           <div class="h-9 w-9 rounded-full bg-muted overflow-hidden border border-border cursor-pointer ml-2 hover:ring-2 hover:ring-primary hover:ring-offset-2 transition-all">
             <img src="<?= !empty($_SESSION['buyer_avatar']) ? 'storage/image/' . $_SESSION['buyer_avatar'] : 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop' ?>" alt="User" class="w-full h-full object-cover">
           </div>
         </a>
      </div>

    </div>
  </div>
</nav>