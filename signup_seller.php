<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Belanja - Sign Up</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js" onload="window.lucideLoaded=true; if(window.initLucide) window.initLucide();"></script>
<script>
  window.initLucide = function() { if(window.lucide) lucide.createIcons(); };
  document.addEventListener('DOMContentLoaded', function() { if(window.lucideLoaded) window.initLucide(); });
</script>
<style type="text/tailwindcss">
  :root {
    --primary: #2563EB; /* Blue 600 */
    --primary-hover: #1D4ED8; /* Blue 700 */
    --foreground: #0F172A; /* Slate 900 */
    --secondary: #64748B; /* Slate 500 */
    --muted: #F8FAFC; /* Slate 50 */
    --border: #E2E8F0; /* Slate 200 */
    --success: #10B981; /* Emerald 500 */
    --error: #EF4444; /* Red 500 */
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
    --color-success: var(--success);
    --color-error: var(--error);
    --color-warning: var(--warning);
    --font-sans: var(--font-sans);
  }
  
  /* Custom Checkbox Styles */
  .custom-checkbox input:checked + div {
    background-color: var(--primary);
    border-color: var(--primary);
  }
  .custom-checkbox input:checked + div svg {
    opacity: 1;
    transform: scale(1);
  }
</style>
</head>
<body class="font-sans bg-white min-h-screen text-foreground selection:bg-primary/20 selection:text-primary">

<main class="flex min-h-screen flex-col lg:flex-row w-full">
  
  <!-- Left Column: Branding & Copywriting (Hidden on small screens, visible on lg+) -->
  <div class="hidden lg:flex lg:w-1/2 bg-primary relative overflow-hidden flex-col justify-between p-12 xl:p-20">
    <!-- Decorative Background Elements -->
    <div class="absolute top-[-15%] left-[-10%] w-[500px] h-[500px] bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[400px] h-[400px] bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
    
    <!-- Top: Logo -->
    <div class="relative z-10 flex items-center gap-3">
      <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-lg shadow-black/10">
        <i data-lucide="shopping-bag" class="w-6 h-6 text-primary"></i>
      </div>
      <span class="text-white text-3xl font-bold tracking-tight">Belanja</span>
    </div>

    <!-- Middle: Copywriting -->
    <div class="relative z-10 max-w-lg mt-12">
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/20 border border-white/20 backdrop-blur-sm text-white text-sm font-medium mb-6">
        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
        50k+ Active Seller
      </div>
      <h1 class="text-4xl xl:text-5xl font-bold text-white leading-[1.15] mb-6">
        Grow Your Business<br>Faster.
      </h1>
      <p class="text-white/80 text-lg leading-relaxed">
        Sell to millions of active buyers, manage your store effortlessly, and withdraw funds anytime. Start selling today with zero registration fees.
      </p>
    </div>

    <!-- Bottom: Social Proof / Stats -->
    <div class="relative z-10 flex items-center gap-8 pt-12 border-t border-white/20 mt-12">
      <div>
        <p class="text-white text-2xl font-bold">4.5/5</p>
        <p class="text-white/70 text-sm mt-1">App Store Rating</p>
      </div>
      <div class="w-px h-10 bg-white/20"></div>
      <div>
        <p class="text-white text-2xl font-bold">24/7</p>
        <p class="text-white/70 text-sm mt-1">Customer Support</p>
      </div>
    </div>
  </div>

  <!-- Right Column: Login Form -->
  <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 bg-white relative">
    
    <!-- Mobile Logo (Visible only on small screens) -->
    <div class="absolute top-6 left-6 flex lg:hidden items-center gap-2">
      <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-md shadow-primary/20">
        <i data-lucide="shopping-bag" class="w-5 h-5 text-white"></i>
      </div>
      <span class="text-foreground text-2xl font-bold tracking-tight">Belanja</span>
    </div>

    <div class="w-full max-w-[420px] flex flex-col mt-12 lg:mt-0">
      
      <!-- Header -->
      <div class="mb-8 text-center lg:text-left">
        <h2 class="text-3xl font-bold text-foreground mb-2">Create an account</h2>
        <p class="text-secondary">Please enter your details to create your account.</p>
      </div>

      <!-- Form -->
      <div class="flex flex-col gap-5">
        
        <!-- Full Name Input -->
        <div class="flex flex-col gap-2">
          <label for="inputFullName" class="text-sm font-medium text-foreground ml-1">Full Name</label>
          <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <i data-lucide="user" class="w-5 h-5 text-secondary group-focus-within:text-primary transition-colors"></i>
            </div>
            <input 
              type="text" 
              id="inputFullName" 
              class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-border bg-muted/50 focus:bg-white focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all text-foreground placeholder:text-secondary/70" 
              placeholder="Enter your name"
            >
          </div>
        </div>

        <!-- Email Input -->
        <div class="flex flex-col gap-2">
          <label for="inputEmail" class="text-sm font-medium text-foreground ml-1">Email</label>
          <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <i data-lucide="mail" class="w-5 h-5 text-secondary group-focus-within:text-primary transition-colors"></i>
            </div>
            <input 
              type="email" 
              id="inputEmail" 
              class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-border bg-muted/50 focus:bg-white focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all text-foreground placeholder:text-secondary/70" 
              placeholder="Enter your email"
            >
          </div>
        </div>

        <!-- Password Input -->
        <div class="flex flex-col gap-2">
          <label for="inputPassword" class="text-sm font-medium text-foreground ml-1">Password</label>
          <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <i data-lucide="lock" class="w-5 h-5 text-secondary group-focus-within:text-primary transition-colors"></i>
            </div>
            <input 
              type="password" 
              id="inputPassword" 
              class="w-full pl-11 pr-12 py-3.5 rounded-2xl border border-border bg-muted/50 focus:bg-white focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all text-foreground placeholder:text-secondary/70" 
              placeholder="••••••••"
              autocomplete="current-password"
              oninput="checkPasswordStrength(this.value)"
            >
            <button 
              type="button" 
              onclick="togglePassword('inputPassword', this)" 
              class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer text-secondary hover:text-foreground transition-colors outline-none focus-visible:text-primary"
              aria-label="Toggle password visibility"
            >
              <i data-lucide="eye" id="iconEye" class="w-5 h-5"></i>
              <i data-lucide="eye-off" id="iconEyeOff" class="w-5 h-5 hidden"></i>
            </button>
          </div>
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

        <!-- Confirm Password Input -->
        <div class="flex flex-col gap-2">
          <label for="inputConfirmPassword" class="text-sm font-medium text-foreground ml-1">Confirm Password</label>
          <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <i data-lucide="lock" class="w-5 h-5 text-secondary group-focus-within:text-primary transition-colors"></i>
            </div>
            <input 
              type="password" 
              id="inputConfirmPassword" 
              class="w-full pl-11 pr-12 py-3.5 rounded-2xl border border-border bg-muted/50 focus:bg-white focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all text-foreground placeholder:text-secondary/70" 
              placeholder="••••••••"
              autocomplete="current-password"
            >
            <button 
              type="button" 
              onclick="togglePassword('inputConfirmPassword', this)" 
              class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer text-secondary hover:text-foreground transition-colors outline-none focus-visible:text-primary"
              aria-label="Toggle password visibility"
            >
              <i data-lucide="eye" id="iconEye" class="w-5 h-5"></i>
              <i data-lucide="eye-off" id="iconEyeOff" class="w-5 h-5 hidden"></i>
            </button>
          </div>
        </div>

        

        <!-- Submit Button -->
        <button 
          onclick="handleLogin()" 
          class="w-full py-3.5 px-4 bg-primary hover:bg-primary-hover text-white rounded-2xl font-semibold shadow-lg shadow-primary/25 transition-all active:scale-[0.98] mt-4 cursor-pointer flex items-center justify-center gap-2"
        >
          <span>Create account</span>
          <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </button>

        

      </div>

      <!-- Footer Link -->
      <p class="text-center text-secondary text-sm mt-8">
        Already have an account? <a href="seller_login.php" class="font-semibold text-primary hover:text-primary-hover transition-colors">Sign in</a>
      </p>

    </div>
  </div>
</main>

<script>
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
      text.textContent = 'Password must be at least 8 characters';
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


  // Handle Login Submission
  function handleLogin() {
    const username = document.getElementById('inputUsername').value.trim();
    const password = document.getElementById('inputPassword').value.trim();
    
    if (!username || !password) {
      showToast('Please enter both username and password.', 'error');
      
      // Add shake animation to empty fields
      if (!username) document.getElementById('inputUsername').parentElement.classList.add('animate-[shake_0.5s_ease-in-out]');
      if (!password) document.getElementById('inputPassword').parentElement.classList.add('animate-[shake_0.5s_ease-in-out]');
      
      setTimeout(() => {
        document.getElementById('inputUsername').parentElement.classList.remove('animate-[shake_0.5s_ease-in-out]');
        document.getElementById('inputPassword').parentElement.classList.remove('animate-[shake_0.5s_ease-in-out]');
      }, 500);
      
      return;
    }

    // Simulate API call
    const btn = document.querySelector('button[onclick="handleLogin()"]');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i> Signing in...';
    btn.disabled = true;
    lucide.createIcons();

    setTimeout(() => {
      btn.innerHTML = originalText;
      btn.disabled = false;
      lucide.createIcons();
      showToast('Successfully signed in! Redirecting...', 'success');
      
      // Clear fields for demo purposes
      document.getElementById('inputUsername').value = '';
      document.getElementById('inputPassword').value = '';
    }, 1500);
  }

  // Dynamic Toast Notification System
  function showToast(msg, type = 'success') {
    document.getElementById('toast')?.remove();
    
    const toast = document.createElement('div');
    toast.id = 'toast';
    
    // Set colors based on type
    const bgColor = type === 'success' ? 'bg-success' : 'bg-error';
    const icon = type === 'success' ? 'check-circle' : 'alert-circle';
    
    toast.className = `fixed top-6 right-6 flex items-center gap-3 ${bgColor} text-white px-5 py-3.5 rounded-2xl shadow-xl z-50 transition-all duration-300 opacity-0 translate-y-[-20px] font-medium`;
    
    toast.innerHTML = `
      <i data-lucide="${icon}" class="w-5 h-5"></i>
      <span>${msg}</span>
    `;
    
    document.body.appendChild(toast);
    lucide.createIcons();
    
    // Animate in
    requestAnimationFrame(() => {
      toast.classList.remove('opacity-0', 'translate-y-[-20px]');
      toast.classList.add('opacity-100', 'translate-y-0');
    });
    
    // Animate out and remove
    setTimeout(() => {
      toast.classList.add('opacity-0', 'translate-y-[-20px]');
      toast.classList.remove('opacity-100', 'translate-y-0');
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  }

  // Add custom shake animation to Tailwind config dynamically for validation feedback
  tailwind.config = {
    theme: {
      extend: {
        keyframes: {
          shake: {
            '0%, 100%': { transform: 'translateX(0)' },
            '25%': { transform: 'translateX(-5px)' },
            '75%': { transform: 'translateX(5px)' }
          }
        }
      }
    }
  }
</script>


</body>
</html>