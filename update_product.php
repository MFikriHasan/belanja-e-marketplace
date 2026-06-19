<?php
require 'koneksi.php';
include 'login_check.php';


$seller_id = $_SESSION['seller_id'];
$product_id = (int)$_POST['product_id'];
$product_name = trim($_POST['product_name']);
$category_id = (int)$_POST['category'];
$price = (int)$_POST['price'];
$description = trim($_POST['description']);

if (empty($product_id) || empty($product_name)) {
    header("Location: management_product.php");
    exit;
}


$koneksi->begin_transaction();

try {
    
    $query_update_prod = "UPDATE product SET name = ?, category_id = ?, price = ?, description = ? WHERE id = ? AND seller_id = ?";
    $stmt = $koneksi->prepare($query_update_prod);
    $stmt->bind_param("sidsii", $product_name, $category_id, $price, $description, $product_id, $seller_id);
    $stmt->execute();

    
    $query_del_spec = "DELETE FROM spesification WHERE product_id = ?";
    $stmt_del_s = $koneksi->prepare($query_del_spec);
    $stmt_del_s->bind_param("i", $product_id);
    $stmt_del_s->execute();

    if (isset($_POST['spec_names']) && is_array($_POST['spec_names'])) {
        $query_ins_spec = "INSERT INTO spesification (product_id, key_name, value_name) VALUES (?, ?, ?)";
        $stmt_ins_s = $koneksi->prepare($query_ins_spec);

        foreach ($_POST['spec_names'] as $i => $s_name) {
            $s_value = $_POST['spec_values'][$i] ?? '';
            if (!empty(trim($s_name))) {
                $stmt_ins_s->bind_param("iss", $product_id, $s_name, $s_value);
                $stmt_ins_s->execute();
            }
        }
    }

   
    $query_old_v = "SELECT product_image FROM color_varian WHERE product_id = ?";
    $stmt_old_v = $koneksi->prepare($query_old_v);
    $stmt_old_v->bind_param("i", $product_id);
    $stmt_old_v->execute();
    $db_images = $stmt_old_v->get_result()->fetch_all(MYSQLI_ASSOC);
    $current_db_images = array_column($db_images, 'product_image');

    
    $query_del_var = "DELETE FROM color_varian WHERE product_id = ?";
    $stmt_del_v = $koneksi->prepare($query_del_var);
    $stmt_del_v->bind_param("i", $product_id);
    $stmt_del_v->execute();

    $kept_images = [];

    if (isset($_POST['variant_colors']) && is_array($_POST['variant_colors'])) {
        $query_ins_var = "INSERT INTO color_varian (product_id, color_name, color_stok, product_image) VALUES (?, ?, ?, ?)";
        $stmt_ins_v = $koneksi->prepare($query_ins_var);

        foreach ($_POST['variant_colors'] as $index => $color_name) {
            $color_stock = (int)($_POST['variant_stocks'][$index] ?? 0);
            $image_name = $_POST['old_variant_images'][$index] ?? ''; // Default pakai foto lama

            
            if (isset($_FILES['variant_images']['name'][$index]) && $_FILES['variant_images']['error'][$index] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['variant_images']['tmp_name'][$index];
                $file_orig_name = $_FILES['variant_images']['name'][$index];
                $file_ext = pathinfo($file_orig_name, PATHINFO_EXTENSION);
                
                // Buat nama file unik baru
                $image_name = 'variant_' . uniqid() . '.' . $file_ext;
                $target_path = 'storage/image/' . $image_name;

                
                if (!is_dir('storage/image/')) {
                    mkdir('storage/image/', 0755, true);
                }

                move_uploaded_file($file_tmp, $target_path);
            }

            if (!empty(trim($color_name))) {
                $stmt_ins_v->bind_param("isis", $product_id, $color_name, $color_stock, $image_name);
                $stmt_ins_v->execute();
                
                if (!empty($image_name)) {
                    $kept_images[] = $image_name;
                }
            }
        }
    }

    
    foreach ($current_db_images as $old_img) {
        if (!empty($old_img) && !in_array($old_img, $kept_images)) {
            $file_to_delete = 'storage/image/' . $old_img;
            if (file_exists($file_to_delete)) {
                unlink($file_to_delete);
            }
        }
    }

    
    $koneksi->commit();
    
    $_SESSION['success'] = 'Product updated successfully!';
    header("Location: management_product.php");
    exit;

} catch (Exception $e) {
    
    $koneksi->rollback();
    echo "Failed updated product" . $e->getMessage();
}
?>