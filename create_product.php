<?php
    require 'koneksi.php';
    include 'login_check.php'; 

    
    $seller_id = $_SESSION['seller_id']; 
    
    if (isset($_POST['save_product'])) {
        $product_name = $_POST['product_name'];
        $category_id  = (int)$_POST['category'];
        $price        = (int)$_POST['price'];
        $description  = trim($_POST['description']);
        
        $color_names  = $_POST['color_name'] ?? [];
        $color_stoks  = $_POST['color_stock'] ?? [];
        $spec_keys    = $_POST['spec_key'] ?? [];
        $spec_values  = $_POST['spec_value'] ?? [];

        
        $target_dir = "storage/image/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        
        $koneksi->begin_transaction();

        try {
            
            $stmt_save = $koneksi->prepare("INSERT INTO product (category_id, seller_id, name, price, description) VALUES (?, ?, ?, ?, ?)");
            $stmt_save->bind_param("iisds", $category_id, $seller_id, $product_name, $price, $description);
            $stmt_save->execute();
            $product_id = $koneksi->insert_id;

            
            if (!empty($color_names)) {
                
                $stmt_color = $koneksi->prepare("INSERT INTO color_varian (product_id, color_name, color_stok, product_image) VALUES (?, ?, ?, ?)");
                
                foreach ($color_names as $index => $c_name) {
                    $c_name = trim($c_name);
                    
                    if (!empty($c_name)) {
                        $c_stok = isset($color_stoks[$index]) ? (int)$color_stoks[$index] : 0;
                        $saved_file_name = null; 

                        
                        if (isset($_FILES['product_image']['name'][$index]) && $_FILES['product_image']['error'][$index] === UPLOAD_ERR_OK) {
                            
                            $file_name     = $_FILES['product_image']['name'][$index];
                            $file_tmp      = $_FILES['product_image']['tmp_name'][$index];
                            $file_ext      = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                            
                            
                            $allowed_extensions = ['jpg', 'jpeg', 'png'];
                            
                            if (in_array($file_ext, $allowed_extensions)) {
                                
                                $saved_file_name = "prod_" . uniqid() . "_" . $index . "." . $file_ext;
                                $target_file     = $target_dir . $saved_file_name;
                                
                                
                                if (!move_uploaded_file($file_tmp, $target_file)) {
                                    throw new Exception("Gagal mengunggah file gambar untuk varian: " . $c_name);
                                }
                            } else {
                                throw new Exception("Format gambar untuk varian " . $c_name . " tidak valid. Hanya JPG, JPEG, PNG");
                            }
                        }

                        
                        $stmt_color->bind_param("isis", $product_id, $c_name, $c_stok, $saved_file_name);
                        $stmt_color->execute();
                    }
                }
            }

            
            if (!empty($spec_keys)) {
                $stmt_spec = $koneksi->prepare("INSERT INTO spesification (product_id, key_name, value_name) VALUES (?, ?, ?)");
                
                foreach ($spec_keys as $index => $s_key) {
                    $s_key   = trim($s_key);
                    $s_value = isset($spec_values[$index]) ? trim($spec_values[$index]) : '';
                    
                    if (!empty($s_key) && !empty($s_value)) {
                        $stmt_spec->bind_param("iss", $product_id, $s_key, $s_value);
                        $stmt_spec->execute();
                    }
                }
            }

            
            $koneksi->commit();
            
            $_SESSION['success'] = 'Product created successfully!';
            header('Location: management_product.php');
            exit;

        } catch (Exception $e) {
            
            $koneksi->rollback();
            echo "Error: " . $e->getMessage();
        }
    }
?>