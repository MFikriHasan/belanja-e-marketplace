<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Buyer Profile Update - Belanja</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
  body {
    font-family: var(--font-sans);
    background-color: #F8FAFC;
  }
  .scrollbar-hide::-webkit-scrollbar { display: none; }
  .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
</head>
<body class="text-foreground min-h-screen flex flex-col">

  <!-- Navbar -->
<?php
    include 'navbar_buyer.php';
?>

  <!-- Main Content -->
  <main class="flex-1 pt-[50px] pb-12 px-4 md:px-8 max-w-7xl mx-auto w-full">
    
    <!-- Page Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold text-foreground">Profile Settings</h1>
        <p class="text-secondary mt-1 text-sm md:text-base">Take control of your personal details, delivery addresses, and account security.</p>
      </div>
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8">
      
      <!-- Left Column: Avatar & Status (lg:col-span-4) -->
      <div class="lg:col-span-4 space-y-6">
        
        <!-- Profile Card -->
        <div class="bg-white rounded-3xl border border-border p-8 flex flex-col items-center text-center shadow-sm">
          
          <!-- Avatar Upload -->
           
          <div id="avatarContainer" class="relative group cursor-pointer mb-5" title="Click to change avatar">
            <img id="profileAvatar" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=300&h=300&fit=crop" alt="Profile Avatar" class="size-32 md:size-40 rounded-full object-cover ring-4 ring-muted shadow-inner transition-all group-hover:ring-primary/20">
            
            <!-- Hover Overlay -->
            <div class="absolute inset-0 bg-foreground/50 rounded-full flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
              <i data-lucide="camera" class="text-white size-8 mb-1"></i>
              <span class="text-white text-xs font-medium">Upload Photo</span>
            </div>
            
            <!-- Status Badge (Absolute) -->
            <div class="absolute bottom-1 right-1 bg-success text-white p-1.5 rounded-full border-4 border-white shadow-sm" title="Verified Account">
              <i data-lucide="check" class="size-4"></i>
            </div>
            
            <input type="file" id="inputAvatar" class="hidden" accept="image/*">
          </div>

          <h2 class="font-bold text-xl text-foreground mb-1">Blyad Bomboclad</h2>
          <p class="text-secondary text-sm mb-5">blyad.bomboclad@example.com</p>
          
          <div class="w-full flex flex-col gap-3">
            <div class="flex items-center justify-between p-3 rounded-2xl bg-card-grey border border-border">
              <div class="flex items-center gap-3">
                <div class="p-2 bg-primary/10 text-primary rounded-xl">
                  <i data-lucide="shield-check" class="size-5"></i>
                </div>
                <div class="text-left">
                  <p class="text-sm font-semibold text-foreground">Account Status</p>
                  <p class="text-xs text-success font-medium">Verified Buyer</p>
                </div>
              </div>
            </div>
            
            <div class="flex items-center justify-between p-3 rounded-2xl bg-card-grey border border-border">
              <div class="flex items-center gap-3">
                <div class="p-2 bg-warning/10 text-warning rounded-xl">
                  <i data-lucide="star" class="size-5"></i>
                </div>
                <div class="text-left">
                  <p class="text-sm font-semibold text-foreground">Buyer Rating</p>
                  <p class="text-xs text-secondary">4.9 / 5.0 (12 reviews)</p>
                </div>
              </div>
            </div>


           <div class="flex items-center justify-between p-3 rounded-2xl bg-card-grey border border-border">
                <div class="flex items-center gap-3 w-full">
                    <a href="logout.php" class="group flex items-center gap-3 w-full p-3.5 rounded-xl hover:bg-rose-500/10 transition-all cursor-pointer">
                    <i data-lucide="log-out" class="w-5 h-5 text-secondary group-hover:text-rose-600 transition-colors"></i>
                    <span class="font-medium text-secondary group-hover:text-rose-600 transition-colors">Logout</span>
                    </a>
                </div>
            </div>

          </div>
        </div>

        

      </div>

      <!-- Right Column: Form (lg:col-span-8) -->
      <div class="lg:col-span-8">
        <div class="bg-white rounded-3xl border border-border overflow-hidden shadow-sm">
          
          <form id="profileForm" onsubmit="handleProfileUpdate(event)">
            
            <!-- Personal Info Section -->
            <div class="p-6 md:p-8 border-b border-border">
              <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-primary/10 text-primary rounded-xl">
                  <i data-lucide="user" class="size-5"></i>
                </div>
                <h2 class="font-bold text-lg text-foreground">Personal Information</h2>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label for="inputFullName" class="text-sm font-medium text-foreground ml-1">Full Name <span class="text-error">*</span></label>
                  <div class="relative">
                    <i data-lucide="user" class="absolute left-4 top-1/2 -translate-y-1/2 size-5 text-secondary pointer-events-none"></i>
                    <input type="text" id="inputFullName" value="Blyad Bomboclad" class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-border bg-card-grey focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all text-sm font-medium" required>
                  </div>
                </div>
                
                <div class="space-y-2">
                  <label for="inputEmail" class="text-sm font-medium text-foreground ml-1">Email Address <span class="text-error">*</span></label>
                  <div class="relative">
                    <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 size-5 text-secondary pointer-events-none"></i>
                    <input type="email" id="inputEmail" value="blyad.bomboclad@example.com" class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-border bg-card-grey focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all text-sm font-medium" required>
                  </div>
                </div>
                
                <div class="space-y-2">
                  <label for="inputPhone" class="text-sm font-medium text-foreground ml-1">Phone Number</label>
                  <div class="relative">
                    <i data-lucide="phone" class="absolute left-4 top-1/2 -translate-y-1/2 size-5 text-secondary pointer-events-none"></i>
                    <input type="tel" id="inputPhone" class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-border bg-card-grey focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all text-sm font-medium" placeholder="+62 813 1234 5678">
                  </div>
                </div>

                <div class="space-y-2">
                  <label for="inputDob" class="text-sm font-medium text-foreground ml-1">Date of Birth</label>
                  <div class="relative">
                    <i data-lucide="calendar" class="absolute left-4 top-1/2 -translate-y-1/2 size-5 text-secondary pointer-events-none"></i>
                    <input type="date" id="inputDob" class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-border bg-card-grey focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all text-sm font-medium text-foreground">
                  </div>
                </div>


                <div class="md:col-span-2 space-y-2">
                  <label for="inputStreet" class="text-sm font-medium text-foreground ml-1">Street Address</label>
                  <div class="relative">
                    <textarea name="address" id="inputStreet"  rows="4" class="form-input resize-none w-full px-4 py-3.5 rounded-2xl border border-border bg-card-grey focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all text-sm font-medium" placeholder="Enter your shipping address"></textarea>
                    <p class="text-xs text-secondary mt-2 text-right">Maksimum 500 characters</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Security Information Section -->
            <div class="p-6 md:p-8">
              <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-primary/10 text-primary rounded-xl">
                  <i data-lucide="shield" class="size-5"></i>
                </div>
                <h2 class="font-bold text-lg text-foreground">Password & Security</h2>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="md:col-span-2 space-y-2">
                  <label for="inputCurrentPassword" class="text-sm font-medium text-foreground ml-1">Current Password</label>
                  <div class="relative">
                    <i data-lucide="key-round" class="absolute left-4 top-1/2 -translate-y-1/2 size-5 text-secondary pointer-events-none"></i>
                    <input type="password" id="inputCurrentPassword" class="w-full pl-11 pr-12 py-3.5 rounded-2xl border border-border bg-card-grey focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all text-sm font-medium" placeholder="••••••••">
                    <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-secondary hover:text-foreground cursor-pointer" onclick="togglePassword('inputCurrentPassword', this)">
                      <i data-lucide="eye" class="size-5"></i>
                    </button>
                  </div>
                </div>

                <div class="space-y-2">
                  <label for="inputNewPassword" class="text-sm font-medium text-foreground ml-1">New Password</label>
                  <div class="relative">
                    <i data-lucide="key-round" class="absolute left-4 top-1/2 -translate-y-1/2 size-5 text-secondary pointer-events-none"></i>
                    <input type="password" id="inputNewPassword" class="w-full pl-11 pr-12 py-3.5 rounded-2xl border border-border bg-card-grey focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all text-sm font-medium" placeholder="Create a new password" oninput="checkPasswordStrength(this.value)">
                    <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-secondary hover:text-foreground cursor-pointer" onclick="togglePassword('inputNewPassword', this)">
                      <i data-lucide="eye" class="size-5"></i>
                    </button>
                  </div>
                  
                  <div class="pt-1">
                    <div class="flex items-center gap-2 mb-1.5">
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
                    <p id="strengthText" class="text-xs text-secondary font-medium ml-1">Password must be at least 8 characters</p>
                  </div>
                </div>

                <div class="space-y-2">
                  <label for="inputConfirmPassword" class="text-sm font-medium text-foreground ml-1">Confirm New Password</label>
                  <div class="relative">
                    <i data-lucide="key-round" class="absolute left-4 top-1/2 -translate-y-1/2 size-5 text-secondary pointer-events-none"></i>
                    <input type="password" id="inputConfirmPassword" class="w-full pl-11 pr-12 py-3.5 rounded-2xl border border-border bg-card-grey focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all text-sm font-medium" placeholder="Confirm your new password">
                    <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-secondary hover:text-foreground cursor-pointer" onclick="togglePassword('inputConfirmPassword', this)">
                      <i data-lucide="eye" class="size-5"></i>
                    </button>
                  </div>
                </div>

              </div>
            </div>

            <!-- Form Actions -->
            <div class="p-6 md:p-8 bg-card-grey border-t border-border flex flex-col sm:flex-row items-center justify-end gap-3">
              <button type="button" class="w-full sm:w-auto px-6 py-3.5 rounded-full font-semibold text-secondary hover:bg-border transition-colors cursor-pointer order-2 sm:order-1">
                Cancel
              </button>
              <button type="submit" id="btnSubmit" class="w-full sm:w-auto px-8 py-3.5 rounded-full font-bold bg-primary text-white hover:bg-primary-hover transition-all shadow-lg shadow-primary/25 cursor-pointer flex items-center justify-center gap-2 order-1 sm:order-2">
                <span>Save Changes</span>
                <i data-lucide="save" class="size-4"></i>
              </button>
            </div>

            

          </form>
        </div>
      </div>

    </div>
  </main>

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
  // Toast Notification System
  function showToast(msg, type='success') {
    document.getElementById('toast')?.remove();
    const t = document.createElement('div');
    t.id = 'toast';
    
    // Colors based on type
    const bgClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-error' : 'bg-foreground';
    const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'alert-circle' : 'info';
    
    t.className = `fixed bottom-6 right-6 ${bgClass} text-white px-5 py-3.5 rounded-2xl shadow-xl z-50 flex items-center gap-3 transition-all duration-300 opacity-0 translate-y-4 font-medium text-sm`;
    
    t.innerHTML = `
      <i data-lucide="${icon}" class="size-5"></i>
      <span>${msg}</span>
    `;
    
    document.body.appendChild(t);
    lucide.createIcons(); // Re-init icons for the toast
    
    // Animate in
    requestAnimationFrame(() => {
      t.classList.remove('opacity-0', 'translate-y-4');
      t.classList.add('opacity-100', 'translate-y-0');
    });
    
    // Animate out and remove
    setTimeout(() => {
      t.classList.add('opacity-0', 'translate-y-4');
      t.classList.remove('opacity-100', 'translate-y-0');
      setTimeout(() => t.remove(), 300);
    }, 3000);
  }

  // Avatar Upload Logic
  const avatarContainer = document.getElementById('avatarContainer');
  const inputAvatar = document.getElementById('inputAvatar');
  const profileAvatar = document.getElementById('profileAvatar');
  const navAvatar = document.getElementById('navAvatar');

  avatarContainer.addEventListener('click', () => {
    inputAvatar.click();
  });

  inputAvatar.addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
      const file = e.target.files[0];
      
      // Basic validation
      if (!file.type.startsWith('image/')) {
        showToast('Please select an image file', 'error');
        return;
      }
      
      if (file.size > 5 * 1024 * 1024) { // 5MB limit
        showToast('Image size should be less than 5MB', 'error');
        return;
      }

      const reader = new FileReader();
      reader.onload = function(e) {
        // Update both avatars
        profileAvatar.src = e.target.result;
        navAvatar.src = e.target.result;
        showToast('Profile photo updated successfully', 'success');
      }
      reader.readAsDataURL(file);
    }
  });

  // Form Submission Logic
  function handleProfileUpdate(e) {
    e.preventDefault();
    
    const btn = document.getElementById('btnSubmit');
    const originalText = btn.innerHTML;
    
    // Loading state
    btn.disabled = true;
    btn.innerHTML = `<i data-lucide="loader-2" class="size-5 animate-spin"></i><span>Saving...</span>`;
    lucide.createIcons();
    
    // Simulate API call
    setTimeout(() => {
      // Reset button
      btn.disabled = false;
      btn.innerHTML = originalText;
      lucide.createIcons();
      
      // Show success message
      showToast('Profile updated successfully!', 'success');
      
      // Update name in UI if changed
      const newName = document.getElementById('inputFullName').value;
      document.querySelector('h2.font-bold.text-xl').textContent = newName;
      document.querySelector('.group-hover\\:text-primary').textContent = newName;
      
    }, 1000);
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

document.getElementById('search-modal').addEventListener('click', function(e) { if (e.target === this) closeSearchModal(); });
</script>


</body>
</html>