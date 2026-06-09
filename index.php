<?php
session_start();
require 'koneksi.php';

$error = "";

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    
    $query = "SELECT id, name, email, password FROM buyer WHERE email = ?";
    $pre = $koneksi->prepare($query);
    
    
    $pre->bind_param("s", $email);
    $pre->execute();
    
    
    $result = $pre->get_result();

    
    if ($result->num_rows > 0) {
        $buyer = $result->fetch_assoc();

        
        if (password_verify($password, $buyer['password'])) {
            
            
            $_SESSION['buyer_id']   = $buyer['id'];
            $_SESSION['buyer_name'] = $buyer['name'];
            $_SESSION['buyer_email']= $buyer['email'];
            $_SESSION['is_login']   = true;

           header("Location: /home.php"); 
            exit;
        } else {
            $error = "Wrong Password";
        }
    } else {
        
        $error = "Unknown email";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Belanja - Login</title>
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
        Over 1M+ Active Users
      </div>
      <h1 class="text-4xl xl:text-5xl font-bold text-white leading-[1.15] mb-6">
        Your Ultimate<br>Shopping Destination.
      </h1>
      <p class="text-white/80 text-lg leading-relaxed">
        Discover millions of products at unbeatable prices. Join our community today and experience seamless shopping with exclusive daily deals.
      </p>
    </div>

    <!-- Bottom: Social Proof / Stats -->
    <div class="relative z-10 flex items-center gap-8 pt-12 border-t border-white/20 mt-12">
      <div>
        <p class="text-white text-2xl font-bold">11/5</p>
        <p class="text-white/70 text-sm mt-1">BGN Store Rating</p>
      </div>
      <div class="w-px h-10 bg-white/20"></div>
      <div>
        <p class="text-white text-2xl font-bold">24/7</p>
        <p class="text-white/70 text-sm mt-1">Customer Support</p>
      </div>
      <div class="w-px h-10 bg-white/20"></div>
      <div>
        <p class="text-white text-2xl font-bold">11/100</p>
        <p class="text-white/70 text-sm mt-1">Anies Gambit</p>
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
        <h2 class="text-3xl font-bold text-foreground mb-2">Welcome Back Blyad</h2>
        <p class="text-secondary">Please enter your details to sign in to your account.</p>
      </div>

      <!-- Form -->
       <form method="post" autocomplete="off">
           <div class="flex flex-col gap-5">
             
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
                   name="email"
                   class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-border bg-muted/50 focus:bg-white focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all text-foreground placeholder:text-secondary/70" 
                   placeholder="Enter your email"
                   required
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
                   name="password"
                   class="w-full pl-11 pr-12 py-3.5 rounded-2xl border border-border bg-muted/50 focus:bg-white focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all text-foreground placeholder:text-secondary/70" 
                   placeholder="••••••••"
                   autocomplete="current-password"
                   required
                 >
                 <button 
                   type="button" 
                   onclick="togglePassword()" 
                   class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer text-secondary hover:text-foreground transition-colors outline-none focus-visible:text-primary"
                   aria-label="Toggle password visibility"
                 >
                   <i data-lucide="eye" id="iconEye" class="w-5 h-5"></i>
                   <i data-lucide="eye-off" id="iconEyeOff" class="w-5 h-5 hidden"></i>
                 </button>
               </div>
             </div>
     
             <!-- Options Row -->
             <div class="flex items-center justify-between mt-1">
               <label class="custom-checkbox flex items-center gap-2.5 cursor-pointer group">
                 <div class="relative flex items-center justify-center">
                   <input type="checkbox" id="inputRemember" class="peer sr-only">
                   <div class="w-5 h-5 rounded-md border-2 border-border bg-white transition-all duration-200 flex items-center justify-center group-hover:border-primary/50">
                     <i data-lucide="check" class="w-3.5 h-3.5 text-white opacity-0 scale-50 transition-all duration-200"></i>
                   </div>
                 </div>
                 <span class="text-sm font-medium text-secondary group-hover:text-foreground transition-colors select-none">Remember me</span>
               </label>
             </div>
     
             <!-- Submit Button -->
             <button 
               type="submit"
               name="submit"
               class="w-full py-3.5 px-4 bg-primary hover:bg-primary-hover text-white rounded-2xl font-semibold shadow-lg shadow-primary/25 transition-all active:scale-[0.98] mt-4 cursor-pointer flex items-center justify-center gap-2"
             >
               <span>Sign In</span>
               <i data-lucide="arrow-right" class="w-4 h-4"></i>
             </button>
     
             
     
           </div>
       </form>
      
      <!-- Footer Link -->
      <p class="text-center text-secondary text-sm mt-8">
        Don't have an account? <a href="#" class="font-semibold text-primary hover:text-primary-hover transition-colors">Sign up for free</a>
      </p>

    </div>
  </div>
</main>

<script>
  
  document.addEventListener("DOMContentLoaded", function() {
    <?php if (!empty($error)): ?>
        showToast(<?php echo json_encode($error); ?>, 'error');
    <?php endif; ?>
  });

  // Toggle Password Visibility
  function togglePassword() {
    const input = document.getElementById('inputPassword');
    const iconEye = document.getElementById('iconEye');
    const iconEyeOff = document.getElementById('iconEyeOff');
    
    if (input.type === 'password') {
      input.type = 'text';
      iconEye.classList.add('hidden');
      iconEyeOff.classList.remove('hidden');
    } else {
      input.type = 'password';
      iconEye.classList.remove('hidden');
      iconEyeOff.classList.add('hidden');
    }
  }

  // Dynamic Toast Notification System
  function showToast(msg, type = 'success') {
    document.getElementById('toast')?.remove();
    
    const toast = document.createElement('div');
    toast.id = 'toast';
    
    // Gunakan warna standar Tailwind jika kamu belum mengonfigurasi 'bg-success'/'bg-error' di tailwind.config
    const bgColor = type === 'success' ? 'bg-emerald-600' : 'bg-rose-600';
    const icon = type === 'success' ? 'check-circle' : 'alert-circle';
    
    toast.className = `fixed top-6 right-6 flex items-center gap-3 ${bgColor} text-white px-5 py-3.5 rounded-2xl shadow-xl z-50 transition-all duration-300 opacity-0 translate-y-[-20px] font-medium`;
    
    toast.innerHTML = `
      <i data-lucide="${icon}" class="w-5 h-5"></i>
      <span>${msg}</span>
    `;
    
    document.body.appendChild(toast);
    
    // Cek memastikan library Lucide sudah di-load oleh browser sebelum di-render
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
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
  window.tailwind = window.tailwind || {};
  tailwind.config = {
    theme: {
      extend: {
        keyframes: {
          shake: {
            '0%, 100%': { transform: 'translateX(0)' },
            '25%': { transform: 'translateX(-5px)' },
            '75%': { transform: 'translateX(5px)' }
          }
        },
        animation: {
          shake: 'shake 0.2s ease-in-out 0s 2'
        }
      }
    }
  }
</script>


</body>
</html>