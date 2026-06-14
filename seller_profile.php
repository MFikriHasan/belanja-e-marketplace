<?php
    require 'koneksi.php';
    include 'login_check.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Seller Profile Update</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<style type="text/tailwindcss">
  :root {
    --primary: #2563EB; /* Blue 600 */
    --primary-hover: #1D4ED8; /* Blue 700 */
    --foreground: #0F172A; /* Slate 900 */
    --secondary: #64748B; /* Slate 500 */
    --muted: #F8FAFC; /* Slate 50 */
    --border: #E2E8F0; /* Slate 200 */
    --card-grey: #F1F5F9; /* Slate 100 */
    --success: #10B981; /* Emerald 500 */
    --error: #EF4444; /* Red 500 */
    --warning: #F59E0B; /* Amber 500 */
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
  
  /* Custom Input Styles */
  .form-input {
    @apply w-full px-4 py-3 rounded-xl border border-border bg-muted/50 focus:bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-foreground placeholder:text-secondary/70;
  }
  .form-label {
    @apply block text-sm font-medium text-foreground mb-2;
  }
</style>
</head>
<body class="bg-muted min-h-screen text-foreground flex overflow-hidden">

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-foreground/80 z-40 lg:hidden hidden backdrop-blur-sm transition-opacity" onclick="toggleSidebar()"></div>

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
      
      <a href="/sales_report.html" class="group  cursor-pointer">
        <div class="flex items-center rounded-xl p-3.5 gap-3 hover:bg-muted transition-all">
          <i data-lucide="bar-chart-3" class="size-5 text-secondary group-hover:text-foreground transition-colors"></i>
          <span class="font-medium text-secondary group-hover:text-foreground transition-colors">Sales Report</span>
        </div>
      </a>

      <p class="px-4 text-xs font-semibold text-secondary uppercase tracking-wider mb-2 mt-4">Settings</p>

      <a href="/seller_profile_update.html" class="group active cursor-pointer">
        <div class="flex items-center rounded-xl p-3.5 gap-3 group-[.active]:bg-primary/10 group-[.active]:text-primary hover:bg-muted transition-all">
          <i data-lucide="store" class="size-5 text-secondary group-[.active]:text-primary group-hover:text-foreground transition-colors"></i>
          <span class="font-medium text-secondary group-[.active]:font-semibold group-[.active]:text-primary group-hover:text-foreground transition-colors">Shop Profile</span>
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
<main class="flex-1 lg:ml-[280px] flex flex-col h-screen relative">
  
  <!-- Top Navbar -->
    <header class="flex items-center justify-between h-[90px] border-b border-border bg-white px-5 md:px-8 sticky top-0 z-30">
      <div class="flex items-center gap-4">
        <button onclick="toggleSidebar()" class="lg:hidden size-11 flex items-center justify-center rounded-xl bg-muted hover:bg-border transition-colors cursor-pointer">
          <i data-lucide="menu" class="size-6 text-foreground"></i>
        </button>
        <h2 class="hidden sm:block font-bold text-2xl ">Shop Profile</h2>
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

  <!-- Scrollable Content -->
  <div class="flex-1 overflow-y-auto p-5 md:p-8">
    <div class="max-w-6xl mx-auto">
      
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8">
        
        <!-- Left Column: Logo & Status -->
        <div class="lg:col-span-4 space-y-6">
          <!-- Logo Card -->
          <div class="bg-white rounded-2xl border border-border p-6 shadow-sm flex flex-col items-center text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-b from-primary/10 to-transparent"></div>
            
            <h3 class="font-semibold text-lg w-full text-left mb-6 relative z-10">Shop Logo</h3>
            
            <div class="relative group cursor-pointer mb-6" onclick="document.getElementById('logoUpload').click()">
              <div class="size-32 rounded-2xl border-2 border-dashed border-border p-1 transition-all group-hover:border-primary">
                <img id="logoPreview" src="https://images.unsplash.com/photo-1560179707-f14e90ef3623?w=300&h=300&fit=crop" class="w-full h-full rounded-xl object-cover" alt="Current Logo">
              </div>
              
              <!-- Hover Overlay -->
              <div class="absolute inset-0 bg-foreground/50 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-2 m-1">
                <i data-lucide="camera" class="size-6 text-white"></i>
                <span class="text-white text-xs font-medium">Change Logo</span>
              </div>
              
              <!-- Hidden File Input -->
              <input type="file" id="logoUpload" class="hidden" accept="image/*" onchange="handleImageUpload(event)">
            </div>
            
            <p class="text-sm text-secondary mb-6 px-4">Recommended size: 500x500px. Max file size: 2MB. Format: JPG, PNG.</p>
            
            <div class="w-full border-t border-border pt-6">
              <div class="flex items-center justify-center gap-2 bg-success/10 text-success py-2.5 px-4 rounded-xl border border-success/20">
                <i data-lucide="badge-check" class="size-5"></i>
                <span class="font-semibold text-sm">Verified Seller</span>
              </div>
            </div>
          </div>

          <!-- Quick Stats Card -->
          <div class="bg-white rounded-2xl border border-border p-6 shadow-sm">
            <h3 class="font-semibold text-lg mb-4">Shop Info</h3>
            <div class="space-y-4">
              <div class="flex justify-between items-center">
                <span class="text-secondary text-sm">Member Since</span>
                <span class="font-medium text-sm">Oct 2022</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-secondary text-sm">Total Sales</span>
                <span class="font-medium text-sm">1,248</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-secondary text-sm">Rating</span>
                <div class="flex items-center gap-1">
                  <i data-lucide="star" class="size-4 text-warning fill-warning"></i>
                  <span class="font-medium text-sm">4.9</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column: Form -->
        <div class="lg:col-span-8">
          <div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
            
            <div class="p-6 md:p-8 border-b border-border">
              <h3 class="font-semibold text-xl mb-1">General Information</h3>
              <p class="text-sm text-secondary">Update your shop details and public description.</p>
            </div>

            <div class="p-6 md:p-8 space-y-8">
              <!-- Shop Name -->
              <div>
                <label for="inputShopName" class="form-label">Shop Name <span class="text-error">*</span></label>
                <input type="text" id="inputShopName" class="form-input" value="Blyad Store" placeholder="Enter your shop name">
              </div>


              <!-- Email -->
              <div>
                <label for="inputEmail" class="form-label">Email <span class="text-error">*</span></label>
                <input type="email" id="inputEmail" class="form-input" value="blyad.store@example.com" placeholder="Enter your email">
              </div>

              <!-- Operational Hour -->

              
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label for="inputOpeningHour" class="form-label">Opening Hour <span class="text-error">*</span></label>
                  <div class="relative">
                    <input type="time" id="inputOpeningHour" class="form-input pr-10" value="08:00">
                  </div>
                </div>
                <div>
                  <label for="inputClosingHour" class="form-label">Closing Hour <span class="text-error">*</span></label>
                  <div class="relative">
                    <input type="time" id="inputClosingHour" class="form-input pr-10" value="21:00">
                  </div>
                </div>
              </div>

              <!-- Description -->
              <div>
                <label for="inputDescription" class="form-label">Public Description</label>
                <textarea id="inputDescription" rows="4" class="form-input resize-none" placeholder="Tell customers about your shop, what you sell, and your mission..."></textarea>
                <p class="text-xs text-secondary mt-2 text-right">Maksimum 500 characters</p>


                <!-- Address -->

                <label for="inputAddress" class="form-label">Address</label>
                <textarea id="inputAddress" rows="4" class="form-input resize-none" placeholder="Tell your address"></textarea>
                <p class="text-xs text-secondary mt-2 text-right">Maksimum 500 characters</p>


              </div>
            </div>

            

            <div class="p-6 md:p-8 border-t border-border bg-muted/30">
              <h3 class="font-semibold text-xl mb-1">Security</h3>
              <p class="text-sm text-secondary mb-6">Ensure your account is using a long, random password to stay secure.</p>
              
              <div class="space-y-6 max-w-xl">
                <!-- Current Password -->
                <div>
                  <label for="inputCurrentPassword" class="form-label">Current Password</label>
                  <div class="relative">
                    <input type="password" id="inputCurrentPassword" class="form-input pr-10" placeholder="••••••••">
                    <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary hover:text-foreground cursor-pointer" onclick="togglePassword('inputCurrentPassword', this)">
                      <i data-lucide="eye" class="size-5"></i>
                    </button>
                  </div>
                </div>

                <!-- New Password -->
                <div>
                  <label for="inputNewPassword" class="form-label">New Password</label>
                  <div class="relative mb-2">
                    <input type="password" id="inputNewPassword" class="form-input pr-10" placeholder="Create a new password" oninput="checkPasswordStrength(this.value)">
                    <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary hover:text-foreground cursor-pointer" onclick="togglePassword('inputNewPassword', this)">
                      <i data-lucide="eye" class="size-5"></i>
                    </button>
                  </div>
                  
                  <!-- Strength Meter -->
                  <div class="flex items-center gap-2 mb-1">
                    <div class="h-1.5 flex-1 rounded-full bg-border overflow-hidden">
                      <div id="strengthBar1" class="h-full w-full bg-transparent transition-colors duration-300"></div>
                    </div>
                    <div class="h-1.5 flex-1 rounded-full bg-border overflow-hidden">
                      <div id="strengthBar2" class="h-full w-full bg-transparent transition-colors duration-300"></div>
                    </div>
                    <div class="h-1.5 flex-1 rounded-full bg-border overflow-hidden">
                      <div id="strengthBar3" class="h-full w-full bg-transparent transition-colors duration-300"></div>
                    </div>
                    <div class="h-1.5 flex-1 rounded-full bg-border overflow-hidden">
                      <div id="strengthBar4" class="h-full w-full bg-transparent transition-colors duration-300"></div>
                    </div>
                  </div>
                  <p id="strengthText" class="text-xs text-secondary font-medium">Password must be atleast 8 characters</p>
                </div>

                <!-- Confirm Password -->
                <div>
                  <label for="inputConfirmPassword" class="form-label">Confirm New Password</label>
                  <div class="relative">
                    <input type="password" id="inputConfirmPassword" class="form-input pr-10" placeholder="Confirm your new password">
                    <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary hover:text-foreground cursor-pointer" onclick="togglePassword('inputConfirmPassword', this)">
                      <i data-lucide="eye" class="size-5"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Action Footer -->
            <div class="p-6 md:p-8 border-t border-border flex items-center justify-end gap-4 bg-white">
              <button type="button" class="px-6 py-3 rounded-xl font-semibold text-secondary hover:bg-muted transition-colors cursor-pointer">
                Cancel
              </button>
              <button type="button" onclick="saveProfile()" class="px-8 py-3 rounded-xl font-semibold bg-primary text-white hover:bg-primary-hover shadow-lg shadow-primary/20 transition-all cursor-pointer flex items-center gap-2">
                <i data-lucide="save" class="size-5"></i>
                Save Changes
              </button>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>
</main>



<script>
  // Initialize Lucide Icons
  document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
  });

  // Sidebar Toggle
  function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
  }

  // Image Upload Preview
  function handleImageUpload(event) {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('logoPreview').src = e.target.result;
        showToast('Logo updated successfully. Remember to save changes.', 'success');
      }
      reader.readAsDataURL(file);
    }
  }

  // Password Visibility Toggle
  function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    
    const icon = btn.querySelector('i') || btn.querySelector('svg');
    
    if (!input || !icon) return;

    if (input.type === 'password') {
      input.type = 'text';
      icon.setAttribute('data-lucide', 'eye-off');
    } else {
      input.type = 'password';
      icon.setAttribute('data-lucide', 'eye');
    }
    
    
    lucide.createIcons();
  }

  // Password Strength Meter
  function checkPasswordStrength(password) {
    let strength = 0;
    const bars = [
      document.getElementById('strengthBar1'),
      document.getElementById('strengthBar2'),
      document.getElementById('strengthBar3'),
      document.getElementById('strengthBar4')
    ];
    const text = document.getElementById('strengthText');

    // Reset bars
    bars.forEach(bar => {
      bar.className = 'h-full w-full bg-transparent transition-colors duration-300';
    });

    if (password.length === 0) {
      text.textContent = 'Password strength';
      text.className = 'text-xs text-secondary font-medium';
      return;
    }

    if (password.length >= 8) strength += 1;
    if (password.match(/(?=.*[A-Z])/)) strength += 1;
    if (password.match(/(?=.*[0-9])/)) strength += 1;
    if (password.match(/(?=.*[!@#\$%\^&\*])/)) strength += 1;

    // Update UI based on strength
    if (strength === 1 || (password.length > 0 && strength === 0)) {
      bars[0].classList.replace('bg-transparent', 'bg-error');
      text.textContent = 'Weak';
      text.className = 'text-xs text-error font-medium';
    } else if (strength === 2) {
      bars[0].classList.replace('bg-transparent', 'bg-warning');
      bars[1].classList.replace('bg-transparent', 'bg-warning');
      text.textContent = 'Fair';
      text.className = 'text-xs text-warning font-medium';
    } else if (strength === 3) {
      bars[0].classList.replace('bg-transparent', 'bg-primary');
      bars[1].classList.replace('bg-transparent', 'bg-primary');
      bars[2].classList.replace('bg-transparent', 'bg-primary');
      text.textContent = 'Good';
      text.className = 'text-xs text-primary font-medium';
    } else if (strength === 4) {
      bars[0].classList.replace('bg-transparent', 'bg-success');
      bars[1].classList.replace('bg-transparent', 'bg-success');
      bars[2].classList.replace('bg-transparent', 'bg-success');
      bars[3].classList.replace('bg-transparent', 'bg-success');
      text.textContent = 'Strong';
      text.className = 'text-xs text-success font-medium';
    }
  }

  // Save Profile Action
  function saveProfile() {
    const shopName = document.getElementById('inputShopName').value;
    if (!shopName.trim()) {
      showToast('Shop Name is required', 'error');
      document.getElementById('inputShopName').focus();
      return;
    }
    
    // Simulate API call
    const btn = document.querySelector('button[onclick="saveProfile()"]');
    const originalContent = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="loader-2" class="size-5 animate-spin"></i> Saving...';
    btn.disabled = true;
    lucide.createIcons();

    setTimeout(() => {
      btn.innerHTML = originalContent;
      btn.disabled = false;
      lucide.createIcons();
      showToast('Profile updated successfully!', 'success');
      
      // Clear password fields after save
      document.getElementById('inputCurrentPassword').value = '';
      document.getElementById('inputNewPassword').value = '';
      document.getElementById('inputConfirmPassword').value = '';
      checkPasswordStrength('');
    }, 1000);
  }

  // Toast Notification System
  function showToast(msg, type = 'success') {
    document.getElementById('toast')?.remove();
    
    const toast = document.createElement('div');
    toast.id = 'toast';
    
    const bgClass = type === 'success' ? 'bg-foreground' : 'bg-error';
    const icon = type === 'success' ? 'check-circle' : 'alert-circle';
    
    toast.className = `fixed bottom-6 right-6 ${bgClass} text-white px-5 py-4 rounded-xl shadow-2xl z-50 flex items-center gap-3 transition-all duration-300 opacity-0 translate-y-4`;
    
    toast.innerHTML = `
      <i data-lucide="${icon}" class="size-5"></i>
      <span class="font-medium text-sm">${msg}</span>
    `;
    
    document.body.appendChild(toast);
    lucide.createIcons();
    
    // Animate in
    requestAnimationFrame(() => {
      toast.classList.remove('opacity-0', 'translate-y-4');
    });
    
    // Auto remove
    setTimeout(() => {
      toast.classList.add('opacity-0', 'translate-y-4');
      setTimeout(() => toast.remove(), 300);
    }, 4000);
  }

 
</script>


</body>
</html>