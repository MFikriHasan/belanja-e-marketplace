<?php
    
    $seller_name = $_SESSION['seller_name'];
    $selller_email = $_SESSION['seller_email'];
    $seller_logo = $_SESSION['seller_logo'];
?>
<div class="flex items-center gap-3 cursor-pointer hover:opacity-80 transition-opacity">
          <div class="text-right hidden sm:block">
            <p class="font-semibold text-sm leading-tight capitalize"><?= $seller_name ?></p>
            <p class="text-secondary text-xs"><?= $selller_email ?></p>
          </div>
          <div class="relative">
            <img src="<?= !empty($seller_logo) ? 'storage/image/' . $seller_logo : 'https://images.unsplash.com/photo-1560179707-f14e90ef3623?w=100&h=100&fit=crop' ?>" alt="User Avatar" class="size-11 rounded-xl object-cover ring-2 ring-border">
            <!-- Online Status Indicator -->
            <span class="absolute bottom-0 right-0 size-3.5 bg-success border-2 border-white rounded-full" title="Online"></span>
          </div>
</div>