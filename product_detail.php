<?php
  
  require 'koneksi.php';
  include 'login_check.php';

  
  if (isset($_POST['add_to_cart'])) {

    $post_product_id = (int)($_POST['product_id'] ?? 0);
    $post_variant_id = (int)($_POST['variant_id'] ?? 0);
    $post_qty        = max(1, min(10, (int)($_POST['qty'] ?? 1)));

    if ($post_product_id > 0 && $post_variant_id > 0) {

      if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

      $key = $post_product_id . '_' . $post_variant_id;

      if (isset($_SESSION['cart'][$key])) {
        
        $_SESSION['cart'][$key]['qty'] = min(10, $_SESSION['cart'][$key]['qty'] + $post_qty);
        $cart_name  = $_SESSION['cart'][$key]['name'];
        $cart_color = $_SESSION['cart'][$key]['color'];
        $cart_qty   = $_SESSION['cart'][$key]['qty'];
      } else {
        
        $stmt = $koneksi->prepare(
          "SELECT p.name, p.price, p.seller_id, cv.color_name, cv.product_image
           FROM product p
           JOIN color_varian cv ON cv.product_id = p.id
           WHERE p.id = ? AND cv.id = ?"
        );
        $stmt->bind_param("ii", $post_product_id, $post_variant_id);
        $stmt->execute();
        $cart_item = $stmt->get_result()->fetch_assoc();

        if ($cart_item) {
          $_SESSION['cart'][$key] = [
            'product_id' => $post_product_id,
            'variant_id' => $post_variant_id,
            'seller_id' => $cart_item['seller_id'],
            'name'       => $cart_item['name'],
            'color'      => $cart_item['color_name'],
            'image'      => $cart_item['product_image'],
            'price'      => $cart_item['price'],
            'qty'        => $post_qty,
          ];
          $cart_name  = $cart_item['name'];
          $cart_color = $cart_item['color_name'];
          $cart_qty   = $post_qty;
        }
      }

      if (isset($cart_name)) {
        $_SESSION['toast'] = [
          'message' => "Added {$cart_qty}x {$cart_name} ({$cart_color}) to cart!",
          'type'    => 'success',
        ];
      } else {
        $_SESSION['toast'] = ['message' => 'Produk tidak ditemukan.', 'type' => 'error'];
      }
    } else {
      $_SESSION['toast'] = ['message' => 'Pilih warna terlebih dahulu.', 'type' => 'error'];
    }

    
    header("Location: product_detail.php?id={$post_product_id}");
    exit;
  }
  

  $str_id = $_GET['id'];
  $product_id = (int)$str_id;

  if ($product_id === 0) {
    header('Location: /home.php');
  }

  // product detail
  $query_main = "SELECT p.*, s.name AS seller_name, s.logo AS seller_logo, c.name AS category_name 
               FROM product p
               INNER JOIN seller s ON p.seller_id = s.id
               INNER JOIN category c ON p.category_id = c.id
               WHERE p.id = ?";
  $pre = $koneksi->prepare($query_main);
  $pre->bind_param("i", $product_id);
  $pre->execute();
  $result_main = $pre->get_result();
  $product = $result_main->fetch_assoc();

  // variant color

  $query_variant = "SELECT id, color_name, color_stok, product_image FROM color_varian WHERE product_id = ?";
  $pre2 = $koneksi->prepare($query_variant);
  $pre2->bind_param("i", $product_id);
  $pre2->execute();
  $variants = $pre2->get_result();

  // detail spec
  $query_spec = "SELECT key_name, value_name FROM spesification WHERE product_id = ?";
  $pre3 = $koneksi->prepare($query_spec);
  $pre3->bind_param("i", $product_id);
  $pre3->execute();
  $specifications = $pre3->get_result();


?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Product Detail - <?= $product['name'] ?></title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js" onload="window.lucideLoaded=true; if(window.initLucide) window.initLucide();"></script>
<script>
  window.initLucide = function() { if(window.lucide) lucide.createIcons(); };
  document.addEventListener('DOMContentLoaded', function() { if(window.lucideLoaded) window.initLucide(); });
</script>
<style type="text/tailwindcss">
  :root {
    --primary: #2563EB; /* Blue-600 */
    --primary-hover: #1D4ED8; /* Blue-700 */
    --foreground: #0F172A; /* Slate-900 */
    --secondary: #64748B; /* Slate-500 */
    --muted: #F1F5F9; /* Slate-100 */
    --border: #E2E8F0; /* Slate-200 */
    --card-grey: #F8FAFC; /* Slate-50 */
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
  .scrollbar-hide::-webkit-scrollbar { display: none; }
  .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
  
  /* Custom Radio Button Styling for Variants */
  .variant-radio:checked + label {
    border-color: var(--primary);
    background-color: color-mix(in srgb, var(--primary) 10%, transparent);
    color: var(--primary);
  }
</style>
</head>
<body class="font-sans bg-white text-foreground min-h-screen flex flex-col">

<!-- Navbar -->
<?php
  include 'navbar_buyer.php';
?>

<!-- Main Content -->
<main class="flex-1 max-w-7xl mx-auto w-full px-4 md:px-8 py-6 md:py-8">
  
  <!-- Breadcrumbs -->
  <nav class="flex items-center gap-2 text-sm text-secondary mb-6 md:mb-8 overflow-x-auto scrollbar-hide whitespace-nowrap">
    <a href="home.php" class="hover:text-primary transition-colors">Home</a>
    <i data-lucide="chevron-right" class="size-4"></i>
    <a href="#" class="hover:text-primary transition-colors">Product Detail</a>
    <i data-lucide="chevron-right" class="size-4"></i>
    <a href="#" class="hover:text-primary transition-colors"><?= $product['category_name'] ?></a>
    <i data-lucide="chevron-right" class="size-4"></i>
    <span class="text-foreground font-medium truncate"><?= $product['name'] ?></span>
  </nav>

  <!-- Product Layout -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
    
    <!-- Left Column: Images (5 cols on desktop) -->
    <div class="lg:col-span-5 flex flex-col gap-4">
      <!-- Main Image -->
      <?php
        
        $variants_arr = [];
        while ($v = $variants->fetch_assoc()) {
          $variants_arr[] = $v;
        }
        $first_image = !empty($variants_arr) ? 'storage/image/' . $variants_arr[0]['product_image'] : '';
      ?>
      <div class="aspect-square rounded-3xl overflow-hidden bg-card-grey border border-border relative group flex items-center justify-center p-8">
        <img id="main-product-image" src="<?= $first_image ?>" alt="Product" class="w-full h-full object-contain transition-transform duration-500 group-hover:scale-105">
        <button class="absolute top-4 right-4 size-10 bg-white rounded-full shadow-sm flex items-center justify-center text-secondary hover:text-error hover:bg-error/10 transition-colors cursor-pointer">
          <i data-lucide="heart" class="size-5"></i>
        </button>
      </div>
      
      <!-- Thumbnails -->
      <div class="flex gap-3 overflow-x-auto scrollbar-hide pb-2">
        <?php foreach ($variants_arr as $idx => $variant): ?>
          <button onclick="changeImage(this, '<?= htmlspecialchars('storage/image/' . $variant['product_image']) ?>')" class="thumbnail-btn flex-shrink-0 size-20 rounded-xl border-2 <?= $idx === 0 ? 'border-primary' : 'border-transparent' ?> overflow-hidden bg-card-grey p-2 cursor-pointer">
            <img src="<?= !empty($variant['product_image']) ? 'storage/image/' . $variant['product_image'] : 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=400&h=400&fit=crop'; ?>" class="w-full h-full object-contain">
          </button>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Right Column: Details (7 cols on desktop) -->
    <div class="lg:col-span-7 flex flex-col">
      
      <!-- Title & Rating -->
      <div class="mb-6">
        <div class="flex items-center gap-2 mb-3">
          <span class="bg-primary/10 text-primary text-xs font-semibold px-2.5 py-1 rounded-md uppercase tracking-wider">Best Seller</span>
          <span class="text-sm text-secondary flex items-center gap-1"><i data-lucide="check-circle-2" class="size-4 text-success"></i> In Stock</span>
        </div>
        <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-foreground leading-tight mb-3"><?= $product['name'] ?></h1>
        
        <div class="flex items-center gap-4 text-sm">
          <div class="flex items-center gap-1 text-warning">
            <i data-lucide="star" class="size-4 fill-current"></i>
            <i data-lucide="star" class="size-4 fill-current"></i>
            <i data-lucide="star" class="size-4 fill-current"></i>
            <i data-lucide="star" class="size-4 fill-current"></i>
            <i data-lucide="star-half" class="size-4 fill-current"></i>
            <span class="text-foreground font-medium ml-1">4.8</span>
          </div>
          <div class="w-1 h-1 rounded-full bg-border"></div>
          <a href="#reviews" class="text-secondary hover:text-primary transition-colors underline underline-offset-4">1.2k Reviews</a>
          <div class="w-1 h-1 rounded-full bg-border"></div>
          <span class="text-secondary">8.5k Sold</span>
        </div>
      </div>

      <!-- Price -->
      <div class="mb-8 p-5 rounded-2xl bg-muted/50 border border-border flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-end gap-3 mb-1">
            <span class="text-4xl font-bold text-primary">$<?= number_format($product['price'], 0, ",", ".") ?></span>
            
          </div>
          <p class="text-sm text-success font-medium">Save $51.00 (13%)</p>
        </div>
        <div class="flex items-center gap-2 text-sm text-secondary bg-white px-3 py-2 rounded-xl border border-border shadow-sm">
          <i data-lucide="truck" class="size-4 text-primary"></i>
          <span>Free shipping over $50</span>
        </div>
      </div>

      <!-- Variants (Color) -->
      <div class="mb-8">
        <h3 class="text-sm font-semibold text-foreground mb-3">
          Color: <span id="selected-color-label" class="text-secondary font-normal">
            <?= htmlspecialchars($variants_arr[0]['color_name'] ?? '-') ?>
          </span>
        </h3>
        <div class="flex flex-wrap gap-3">
          <?php foreach ($variants_arr as $idx => $variant): ?>
            <div class="relative">
              <input type="radio" name="color" id="color-<?= $variant['id'] ?>" class="peer sr-only variant-radio" <?= $idx === 0 ? 'checked' : '' ?> onchange="document.getElementById('selected-color-label').textContent='<?= htmlspecialchars($variant['color_name']) ?>'; document.getElementById('input-variant-id').value='<?= $variant['id'] ?>';">
              <label for="color-<?= $variant['id'] ?>" class="flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 border-border cursor-pointer transition-all hover:border-primary/50">
                <span class="text-sm font-medium"><?= htmlspecialchars($variant['color_name']) ?></span>
              </label>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Actions (Qty & Buttons) -->
      <form method="POST" action="product_detail.php?id=<?= $product_id ?>">
        <input type="hidden" name="add_to_cart" value="1">
        <input type="hidden" name="product_id" value="<?= $product_id ?>">
        <input type="hidden" name="variant_id" id="input-variant-id" value="<?= $variants_arr[0]['id'] ?? '' ?>">
        <input type="hidden" name="qty" id="input-qty" value="1">

        <div class="flex flex-col sm:flex-row gap-4 mb-8">
          <!-- Quantity -->
          <div class="flex items-center justify-between border-2 border-border rounded-xl h-14 px-2 sm:w-32 bg-white">
            <button type="button" onclick="updateQty(-1)" class="size-10 flex items-center justify-center rounded-lg hover:bg-muted text-secondary transition-colors cursor-pointer">
              <i data-lucide="minus" class="size-4"></i>
            </button>
            <span id="qty-display" class="font-semibold text-foreground w-8 text-center">1</span>
            <button type="button" onclick="updateQty(1)" class="size-10 flex items-center justify-center rounded-lg hover:bg-muted text-secondary transition-colors cursor-pointer">
              <i data-lucide="plus" class="size-4"></i>
            </button>
          </div>
          
          <!-- Add to Cart -->
          <button type="submit" class="flex-1 h-14 bg-primary hover:bg-primary-hover text-white py-3 rounded-xl font-semibold flex items-center justify-center gap-2 transition-colors shadow-lg shadow-primary/25 cursor-pointer">
            <i data-lucide="shopping-cart" class="size-5"></i>
            Add to Cart
          </button>
        </div>
      </form>

      <!-- Merchant Info Card -->
      <div class="flex items-center justify-between p-4 rounded-2xl border border-border bg-card-grey mb-8">
        <div class="flex items-center gap-4">
          <div class="size-12 rounded-full bg-white border border-border flex items-center justify-center overflow-hidden shadow-sm">
            <img src="<?= !empty($product['seller_logo']) ? 'storage/image/' . $product['seller_logo'] : 'https://images.unsplash.com/photo-1560179707-f14e90ef3623?w=100&h=100&fit=crop'; ?>" alt="Earbuds" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500">
          </div>
          <div>
            <h4 class="font-semibold text-foreground flex items-center gap-1">
              <?= $product['seller_name'] ?>
              <i data-lucide="badge-check" class="size-4 text-primary"></i>
            </h4>
            <p class="text-xs text-secondary mt-0.5">99% Positive Feedback • 12k Followers</p>
          </div>
        </div>
        <a href="seller_detail.php?id=<?= $product['seller_id'] ?>" class="px-4 py-2 rounded-full border border-border bg-white text-sm font-medium hover:border-primary hover:text-primary transition-colors cursor-pointer hidden sm:block">
          Visit Store
        </a>
      </div>

      <!-- Specifications Table -->
      <div>
        <h3 class="text-lg font-semibold text-foreground mb-4">Key Specifications</h3>
        <div class="border border-border rounded-2xl overflow-hidden bg-white">
          <table class="w-full text-sm text-left">
            <tbody>
              <?php
                $spec_rows = [];
                while ($spec = $specifications->fetch_assoc()) {
                  $spec_rows[] = $spec;
                }
                foreach ($spec_rows as $i => $spec):
                  $is_last = ($i === count($spec_rows) - 1);
              ?>
              <tr class="<?= !$is_last ? 'border-b border-border' : '' ?>">
                <th class="py-3 px-4 bg-muted/50 font-medium text-secondary w-1/3"><?= htmlspecialchars($spec['key_name']) ?></th>
                <td class="py-3 px-4 text-foreground"><?= htmlspecialchars($spec['value_name']) ?></td>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($spec_rows)): ?>
              <tr>
                <td colspan="2" class="py-4 px-4 text-secondary text-center">No specifications available.</td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>

  <!-- Tabs Section -->
  <div class="mt-16 border border-border rounded-3xl bg-white overflow-hidden shadow-sm">
    <!-- Tab Headers -->
    <div class="overflow-x-auto scrollbar-hide border-b border-border bg-card-grey px-4 md:px-8 pt-4">
      <nav class="flex min-w-max gap-8">
        <button onclick="switchTab('description')" data-tab-btn="description" class="py-4 border-b-2 border-primary text-primary font-semibold whitespace-nowrap transition-colors">Product Description</button>
        <button onclick="switchTab('reviews')" data-tab-btn="reviews" class="py-4 border-b-2 border-transparent text-secondary hover:text-foreground font-medium whitespace-nowrap transition-colors">Reviews (1.2k)</button>
        <button onclick="switchTab('shipping')" data-tab-btn="shipping" class="py-4 border-b-2 border-transparent text-secondary hover:text-foreground font-medium whitespace-nowrap transition-colors">Shipping & Returns</button>
      </nav>
    </div>
    
    <!-- Tab Content -->
    <div class="p-6 md:p-8">
      <!-- Description Tab -->
      <div id="tab-description" data-tab-content class="block space-y-6 text-secondary leading-relaxed">
        <p><?= $product['description'] ?></p>
      </div>

      <!-- Reviews Tab (Hidden by default) -->
      <div id="tab-reviews" data-tab-content class="hidden">
        <div class="flex flex-col md:flex-row gap-8 mb-8 pb-8 border-b border-border">
          <div class="flex flex-col items-center justify-center p-6 bg-muted rounded-2xl min-w-[200px]">
            <span class="text-5xl font-bold text-foreground mb-2">4.8</span>
            <div class="flex text-warning mb-2">
              <i data-lucide="star" class="size-5 fill-current"></i>
              <i data-lucide="star" class="size-5 fill-current"></i>
              <i data-lucide="star" class="size-5 fill-current"></i>
              <i data-lucide="star" class="size-5 fill-current"></i>
              <i data-lucide="star-half" class="size-5 fill-current"></i>
            </div>
            <span class="text-sm text-secondary">Based on 1,245 reviews</span>
          </div>
          <div class="flex-1 flex flex-col justify-center gap-2">
            <!-- Progress bars for ratings -->
            <div class="flex items-center gap-3 text-sm"><span class="w-3 text-secondary">5</span><i data-lucide="star" class="size-3 text-warning fill-current"></i><div class="flex-1 h-2 bg-border rounded-full overflow-hidden"><div class="h-full bg-warning w-[85%]"></div></div><span class="w-8 text-right text-secondary">85%</span></div>
            <div class="flex items-center gap-3 text-sm"><span class="w-3 text-secondary">4</span><i data-lucide="star" class="size-3 text-warning fill-current"></i><div class="flex-1 h-2 bg-border rounded-full overflow-hidden"><div class="h-full bg-warning w-[10%]"></div></div><span class="w-8 text-right text-secondary">10%</span></div>
            <div class="flex items-center gap-3 text-sm"><span class="w-3 text-secondary">3</span><i data-lucide="star" class="size-3 text-warning fill-current"></i><div class="flex-1 h-2 bg-border rounded-full overflow-hidden"><div class="h-full bg-warning w-[3%]"></div></div><span class="w-8 text-right text-secondary">3%</span></div>
            <div class="flex items-center gap-3 text-sm"><span class="w-3 text-secondary">2</span><i data-lucide="star" class="size-3 text-warning fill-current"></i><div class="flex-1 h-2 bg-border rounded-full overflow-hidden"><div class="h-full bg-warning w-[1%]"></div></div><span class="w-8 text-right text-secondary">1%</span></div>
            <div class="flex items-center gap-3 text-sm"><span class="w-3 text-secondary">1</span><i data-lucide="star" class="size-3 text-warning fill-current"></i><div class="flex-1 h-2 bg-border rounded-full overflow-hidden"><div class="h-full bg-warning w-[1%]"></div></div><span class="w-8 text-right text-secondary">1%</span></div>
          </div>
        </div>
        
        <!-- Review List (Max 5) -->
        <div class="space-y-6">
          <div class="pb-6 border-b border-border">
            <div class="flex justify-between items-start mb-2">
              <div class="flex items-center gap-3">
                <div class="size-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold">JD</div>
                <div>
                  <p class="font-medium text-foreground text-sm">John Doe</p>
                  <div class="flex text-warning"><i data-lucide="star" class="size-3 fill-current"></i><i data-lucide="star" class="size-3 fill-current"></i><i data-lucide="star" class="size-3 fill-current"></i><i data-lucide="star" class="size-3 fill-current"></i><i data-lucide="star" class="size-3 fill-current"></i></div>
                </div>
              </div>
              <span class="text-xs text-secondary">2 days ago</span>
            </div>
            <p class="text-secondary text-sm mt-2">Absolutely incredible noise cancellation. I use these in a noisy open office and it's like I'm in my own world. Sound quality is top-notch as expected from Sony.</p>
          </div>
          <div class="pb-6 border-b border-border">
            <div class="flex justify-between items-start mb-2">
              <div class="flex items-center gap-3">
                <div class="size-10 rounded-full bg-success/10 text-success flex items-center justify-center font-bold">SM</div>
                <div>
                  <p class="font-medium text-foreground text-sm">Sarah M.</p>
                  <div class="flex text-warning"><i data-lucide="star" class="size-3 fill-current"></i><i data-lucide="star" class="size-3 fill-current"></i><i data-lucide="star" class="size-3 fill-current"></i><i data-lucide="star" class="size-3 fill-current"></i><i data-lucide="star-half" class="size-3 fill-current"></i></div>
                </div>
              </div>
              <span class="text-xs text-secondary">1 week ago</span>
            </div>
            <p class="text-secondary text-sm mt-2">Very comfortable for long listening sessions. The multipoint connection works flawlessly between my phone and laptop.</p>
          </div>
        </div>
        <button class="mt-6 w-full py-3 rounded-xl border border-border text-foreground font-medium hover:bg-muted transition-colors cursor-pointer">View All Reviews</button>
      </div>

      <!-- Shipping Tab (Hidden by default) -->
      <div id="tab-shipping" data-tab-content class="hidden text-secondary space-y-4">
        <h4 class="text-foreground font-semibold">Delivery Options</h4>
        <ul class="space-y-3">
          <li class="flex items-start gap-3">
            <i data-lucide="truck" class="size-5 text-primary shrink-0 mt-0.5"></i>
            <div>
              <p class="font-medium text-foreground">Standard Delivery (Free)</p>
              <p class="text-sm">Estimated delivery: 3-5 business days.</p>
            </div>
          </li>
          <li class="flex items-start gap-3">
            <i data-lucide="zap" class="size-5 text-primary shrink-0 mt-0.5"></i>
            <div>
              <p class="font-medium text-foreground">Express Delivery ($15.00)</p>
              <p class="text-sm">Estimated delivery: 1-2 business days. Order before 2 PM.</p>
            </div>
          </li>
        </ul>
        <h4 class="text-foreground font-semibold mt-6">Return Policy</h4>
        <p class="text-sm">We accept returns within 30 days of purchase. Items must be in original condition with all packaging intact. A restocking fee may apply for opened items.</p>
      </div>
    </div>
  </div>

  <!-- Related Products -->
  <div class="mt-16 mb-8">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-2xl font-bold text-foreground">You Might Also Like</h2>
      <div class="flex gap-2">
        <button class="size-10 rounded-full border border-border flex items-center justify-center hover:bg-muted transition-colors cursor-pointer"><i data-lucide="chevron-left" class="size-5"></i></button>
        <button class="size-10 rounded-full border border-border flex items-center justify-center hover:bg-muted transition-colors cursor-pointer"><i data-lucide="chevron-right" class="size-5"></i></button>
      </div>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
      <!-- Product Card 1 -->
      <div class="group flex flex-col bg-white rounded-2xl border border-border overflow-hidden hover:shadow-lg hover:border-primary/30 transition-all duration-300">
        <div class="relative aspect-square bg-card-grey p-4 flex items-center justify-center overflow-hidden">
          <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop" alt="Earbuds" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500">
          <button class="absolute top-3 right-3 size-8 bg-white rounded-full shadow-sm flex items-center justify-center text-secondary hover:text-error transition-colors opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 cursor-pointer">
            <i data-lucide="heart" class="size-4"></i>
          </button>
        </div>
        <div class="p-4 flex flex-col flex-1">
          <h3 class="font-medium text-foreground text-sm line-clamp-2 mb-1 group-hover:text-primary transition-colors">Nike Air Max 270 React Running Shoes</h3>
          <div class="flex items-center gap-1 text-warning mb-2">
            <i data-lucide="star" class="size-3 fill-current"></i><span class="text-xs text-secondary ml-1">4.6</span>
          </div>
          <div class="mt-auto flex items-center justify-between">
            <span class="font-bold text-lg text-foreground">$278.00</span>
            <button onclick="addToCart()" class="size-8 bg-primary/10 text-primary rounded-full flex items-center justify-center hover:bg-primary hover:text-white transition-colors cursor-pointer">
              <i data-lucide="plus" class="size-4"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Product Card 2 -->
      <div class="group flex flex-col bg-white rounded-2xl border border-border overflow-hidden hover:shadow-lg hover:border-primary/30 transition-all duration-300">
        <div class="relative aspect-square bg-card-grey p-4 flex items-center justify-center overflow-hidden">
          <span class="absolute top-3 left-3 bg-error text-white text-[10px] font-bold px-2 py-1 rounded-md uppercase z-10">-20%</span>
          <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&q=80&fit=crop" alt="Headphones" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500">
          <button  class="absolute top-3 right-3 size-8 bg-white rounded-full shadow-sm flex items-center justify-center text-secondary hover:text-error transition-colors opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 cursor-pointer">
            <i data-lucide="heart" class="size-4"></i>
          </button>
        </div>
        <div class="p-4 flex flex-col flex-1">
          <h3 class="font-medium text-foreground text-sm line-clamp-2 mb-1 group-hover:text-primary transition-colors">Bose QuietComfort 45 Wireless Headphones</h3>
          <div class="flex items-center gap-1 text-warning mb-2">
            <i data-lucide="star" class="size-3 fill-current"></i><span class="text-xs text-secondary ml-1">4.7</span>
          </div>
          <div class="mt-auto flex items-center justify-between">
            <div class="flex flex-col">
              <span class="font-bold text-lg text-foreground">$329.00</span>
              <span class="text-xs text-secondary line-through">$399.00</span>
            </div>
            <button onclick="addToCart()" class="size-8 bg-primary/10 text-primary rounded-full flex items-center justify-center hover:bg-primary hover:text-white transition-colors cursor-pointer">
              <i data-lucide="plus" class="size-4"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Product Card 3 -->
      <div class="group flex flex-col bg-white rounded-2xl border border-border overflow-hidden hover:shadow-lg hover:border-primary/30 transition-all duration-300">
        <div class="relative aspect-square bg-card-grey p-4 flex items-center justify-center overflow-hidden">
          <img src="https://images.unsplash.com/photo-1583394838336-acd977736f90?w=400&q=80&fit=crop" alt="Speaker" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500">
          <button class="absolute top-3 right-3 size-8 bg-white rounded-full shadow-sm flex items-center justify-center text-secondary hover:text-error transition-colors opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 cursor-pointer">
            <i data-lucide="heart" class="size-4"></i>
          </button>
        </div>
        <div class="p-4 flex flex-col flex-1">
          <h3 class="font-medium text-foreground text-sm line-clamp-2 mb-1 group-hover:text-primary transition-colors">Sony SRS-XB43 Portable Bluetooth Speaker</h3>
          <div class="flex items-center gap-1 text-warning mb-2">
            <i data-lucide="star" class="size-3 fill-current"></i><span class="text-xs text-secondary ml-1">4.5</span>
          </div>
          <div class="mt-auto flex items-center justify-between">
            <span class="font-bold text-lg text-foreground">$198.00</span>
            <button onclick="addToCart()" class="size-8 bg-primary/10 text-primary rounded-full flex items-center justify-center hover:bg-primary hover:text-white transition-colors cursor-pointer">
              <i data-lucide="plus" class="size-4"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Product Card 4 -->
      <div class="group flex flex-col bg-white rounded-2xl border border-border overflow-hidden hover:shadow-lg hover:border-primary/30 transition-all duration-300 hidden md:flex">
        <div class="relative aspect-square bg-card-grey p-4 flex items-center justify-center overflow-hidden">
          <img src="https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=400&q=80&fit=crop" alt="Headphones" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500">
          <button class="absolute top-3 right-3 size-8 bg-white rounded-full shadow-sm flex items-center justify-center text-secondary hover:text-error transition-colors opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 cursor-pointer">
            <i data-lucide="heart" class="size-4"></i>
          </button>
        </div>
        <div class="p-4 flex flex-col flex-1">
          <h3 class="font-medium text-foreground text-sm line-clamp-2 mb-1 group-hover:text-primary transition-colors">Sennheiser Momentum 4 Wireless</h3>
          <div class="flex items-center gap-1 text-warning mb-2">
            <i data-lucide="star" class="size-3 fill-current"></i><span class="text-xs text-secondary ml-1">4.8</span>
          </div>
          <div class="mt-auto flex items-center justify-between">
            <span class="font-bold text-lg text-foreground">$349.00</span>
            <button onclick="addToCart()" class="size-8 bg-primary/10 text-primary rounded-full flex items-center justify-center hover:bg-primary hover:text-white transition-colors cursor-pointer">
              <i data-lucide="plus" class="size-4"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

</main>

<!-- Search Modal -->
<div id="search-modal" class="fixed inset-0 bg-foreground/50 backdrop-blur-sm z-[100] hidden items-start justify-center pt-20 px-4">
  <div class="bg-white rounded-3xl w-full max-w-2xl max-h-[80vh] overflow-hidden shadow-2xl transform transition-all">
    <div class="p-4 border-b border-border">
      <div class="flex items-center gap-3 bg-muted rounded-2xl px-4 border border-transparent focus-within:border-primary focus-within:bg-white transition-colors">
        <i data-lucide="search" class="size-5 text-secondary"></i>
        <input type="text" id="search-input" placeholder="Search for products, brands, categories..." class="flex-1 py-4 bg-transparent outline-none text-foreground placeholder:text-secondary">
        <kbd class="hidden sm:inline-flex px-2 py-1 bg-card-grey rounded-lg text-[10px] font-semibold text-secondary border border-border">ESC</kbd>
      </div>
    </div>
    <div class="p-4 overflow-y-auto max-h-[60vh]">
      <p class="text-xs font-semibold text-secondary uppercase tracking-wider mb-3 px-2">Trending Searches</p>
      <div class="flex flex-wrap gap-2 mb-6 px-2">
        <button class="px-4 py-2 rounded-full bg-muted text-sm font-medium hover:bg-primary/10 hover:text-primary transition-colors cursor-pointer">Wireless Earbuds</button>
        <button class="px-4 py-2 rounded-full bg-muted text-sm font-medium hover:bg-primary/10 hover:text-primary transition-colors cursor-pointer">Sony WH-1000XM5</button>
        <button class="px-4 py-2 rounded-full bg-muted text-sm font-medium hover:bg-primary/10 hover:text-primary transition-colors cursor-pointer">Mechanical Keyboard</button>
        <button class="px-4 py-2 rounded-full bg-muted text-sm font-medium hover:bg-primary/10 hover:text-primary transition-colors cursor-pointer">4K Monitor</button>
      </div>
      
      <p class="text-xs font-semibold text-secondary uppercase tracking-wider mb-3 px-2">Suggested Products</p>
      <div id="search-results" class="flex flex-col gap-1">
        <a href="#" class="flex items-center gap-4 p-3 rounded-2xl hover:bg-muted transition-all group">
          <div class="size-12 bg-card-grey rounded-xl flex items-center justify-center overflow-hidden border border-border">
            <img src="https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=100&q=80&fit=crop" class="w-full h-full object-contain">
          </div>
          <div class="flex-1 min-w-0">
            <p class="font-medium text-foreground truncate group-hover:text-primary transition-colors">Sony WH-1000XM5 Wireless Headphones</p>
            <p class="text-sm text-secondary truncate">Audio > Headphones</p>
          </div>
          <span class="font-semibold text-foreground">$348.00</span>
        </a>
        <a href="#" class="flex items-center gap-4 p-3 rounded-2xl hover:bg-muted transition-all group">
          <div class="size-12 bg-card-grey rounded-xl flex items-center justify-center overflow-hidden border border-border">
            <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop" class="w-full h-full object-contain">
          </div>
          <div class="flex-1 min-w-0">
            <p class="font-medium text-foreground truncate group-hover:text-primary transition-colors">Nike Air Max 270 React Running Shoes</p>
            <p class="text-sm text-secondary truncate">Sport > Shoes</p>
          </div>
          <span class="font-semibold text-foreground">$278.00</span>
        </a>
      </div>
    </div>
  </div>
</div>

<script>
  // Image Gallery Logic
  function changeImage(btn, src) {
    // Update main image
    document.getElementById('main-product-image').src = src;
    
    // Update active state of thumbnails
    document.querySelectorAll('.thumbnail-btn').forEach(el => {
      el.classList.remove('border-primary');
      el.classList.add('border-transparent');
    });
    btn.classList.remove('border-transparent');
    btn.classList.add('border-primary');
  }

  // Quantity Logic
  let qty = 1;
  function updateQty(change) {
    qty += change;
    if (qty < 1) qty = 1;
    if (qty > 10) qty = 10; // Max limit example
    document.getElementById('qty-display').textContent = qty;
    document.getElementById('input-qty').value = qty; // sync ke form
  }

  // Tab Logic
  function switchTab(tabId) {
    // Hide all content
    document.querySelectorAll('[data-tab-content]').forEach(el => {
      el.classList.add('hidden');
      el.classList.remove('block');
    });
    
    // Reset all buttons
    document.querySelectorAll('[data-tab-btn]').forEach(el => {
      el.classList.remove('border-primary', 'text-primary');
      el.classList.add('border-transparent', 'text-secondary');
    });
    
    // Show selected content
    document.getElementById(`tab-${tabId}`).classList.remove('hidden');
    document.getElementById(`tab-${tabId}`).classList.add('block');
    
    // Highlight selected button
    const activeBtn = document.querySelector(`[data-tab-btn="${tabId}"]`);
    activeBtn.classList.remove('border-transparent', 'text-secondary');
    activeBtn.classList.add('border-primary', 'text-primary');
  }

  // Add to Cart Toast
  function addToCart() {
    const color = document.getElementById('selected-color-label').textContent;
    showToast(`Added ${qty}x Sony WH-1000XM5 (${color}) to cart!`, 'success');
  }

  // Toast Notification System
  function showToast(msg, type='success') {
    document.getElementById('toast')?.remove();
    const t = document.createElement('div');
    t.id = 'toast';
    
    // Styling based on type
    const bgClass = type === 'success' ? 'bg-foreground' : 'bg-error';
    const icon = type === 'success' ? 'check-circle' : 'alert-circle';
    const iconColor = type === 'success' ? 'text-success' : 'text-white';

    t.className = `fixed bottom-4 left-1/2 -translate-x-1/2 md:left-auto md:right-8 md:translate-x-0 ${bgClass} text-white px-5 py-4 rounded-2xl z-50 transition-all duration-300 opacity-0 translate-y-[20px] flex items-center gap-3 shadow-2xl min-w-[300px]`;
    
    t.innerHTML = `
      <i data-lucide="${icon}" class="size-5 ${iconColor}"></i>
      <span class="font-medium text-sm">${msg}</span>
    `;
    
    document.body.appendChild(t);
    if(window.lucide) lucide.createIcons();

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

  // Search Modal Logic
  function openSearchModal() { 
    const modal = document.getElementById('search-modal');
    modal.classList.remove('hidden'); 
    modal.classList.add('flex'); 
    setTimeout(() => document.getElementById('search-input').focus(), 100);
  }
  
  function closeSearchModal() { 
    const modal = document.getElementById('search-modal');
    modal.classList.add('hidden'); 
    modal.classList.remove('flex'); 
  }
  
  document.getElementById('search-modal').addEventListener('click', function(e) { 
    if (e.target === this) closeSearchModal(); 
  });
  
  document.addEventListener('keydown', function(e) { 
    if (e.key === 'Escape') closeSearchModal(); 
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') { 
      e.preventDefault(); 
      openSearchModal(); 
    } 
  });

  
  <?php if (!empty($_SESSION['toast'])): ?>
  document.addEventListener('DOMContentLoaded', function() {
    showToast(<?= json_encode($_SESSION['toast']['message']) ?>, <?= json_encode($_SESSION['toast']['type']) ?>);
  });
  <?php unset($_SESSION['toast']); ?>
  <?php endif; ?>
</script>


</body>
</html>