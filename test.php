<?php
$password_asli = "admin123";
$password_terhash = password_hash($password_asli, PASSWORD_DEFAULT);

echo "Password Asli: " . $password_asli . "<br>";
echo "Hasil Hash (Simpan ini ke DB): " . $password_terhash;
?>