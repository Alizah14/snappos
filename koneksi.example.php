<?php
$host = "localhost";
$user = "root";
$pass = ""; // Biarkan kosong sesuai default XAMPP
$db   = "snappos_db"; // Nama database di DBeaver

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>