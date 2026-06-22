<?php
    session_start();
    
/**
 * @param string $required_role 'buyer' atau 'seller'
 */

function check_access_control($required_role) {
    
    if ($required_role === 'seller') {
        if (!isset($_SESSION['seller_logged_in']) || $_SESSION['seller_logged_in'] !== true) {
            
            header('Location: /seller_login.php?error=unauthorized');
            exit;
        }
    }
    
    
    if ($required_role === 'buyer') {
        if (!isset($_SESSION['buyer_logged_in']) || $_SESSION['buyer_logged_in'] !== true) {
            
            header('Location: /index.php?error=unauthorized');
            exit;
        }
    }
}
?>