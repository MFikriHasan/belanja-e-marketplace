<?php
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $db_name = 'belanja';

    $koneksi = new mysqli($host, $username, $password, $db_name);

    if ($koneksi->connect_error) {
        echo "Koneksi gagal!";
    }
