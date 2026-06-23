<?php

require 'koneksi.php';
include 'login_check.php';

check_access_control('seller');

$seller_id = $_SESSION['seller_id'];

if (isset($_POST['submit']) && isset($_POST['shipping_status'])) {
    $transaction_detail_id = (int)$_POST['transaction_det_id'];
    $shipping_status = $_POST['shipping_status'];
    
    $query = "UPDATE transaction_det SET shipping_status = ? WHERE id = ? AND seller_id = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("sii", $shipping_status, $transaction_detail_id, $seller_id);
    $stmt->execute();

    if ($koneksi->affected_rows) {
        $_SESSION['success'] = "Shipping status updated successfully!";
        header("Location: management_transactions.php");
        exit;
    } else {
        $_SESSION['success'] = "Shipping status updated successfully!";
        header("Location: management_transactions.php");
        exit;
    }
}