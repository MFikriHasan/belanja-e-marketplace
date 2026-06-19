<?php
require 'koneksi.php';
include 'login_check.php';

$seller_id = $_SESSION['seller_id'];


if (isset($_POST['id']) && !empty($_POST['id'])) {
    $product_id = (int)$_POST['id'];

    
    $query_img = "SELECT product_image FROM color_varian WHERE product_id = ?";
    $stmt_img = $koneksi->prepare($query_img);
    $stmt_img->bind_param("i", $product_id);
    $stmt_img->execute();
    $images = $stmt_img->get_result()->fetch_all(MYSQLI_ASSOC);

    
    $query_del = "DELETE FROM product WHERE id = ? AND seller_id = ?";
    $stmt_del = $koneksi->prepare($query_del);
    $stmt_del->bind_param("ii", $product_id, $seller_id);

    if ($stmt_del->execute()) {
        if ($stmt_del->affected_rows > 0) {
            
            foreach ($images as $img) {
                if (!empty($img['product_image'])) {
                    $file_path = 'storage/image/' . $img['product_image'];
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
            }
            $_SESSION['success'] = "Product permanently deleted.";
        } else {
            $_SESSION['error'] = "Product not found or unauthorized.";
        }
    } else {
        $_SESSION['error'] = "Failed to delete product.";
    }

    header("Location: management_product.php");
    exit;

} else {
    header("Location: management_product.php");
    exit;
}
?>