<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $nama_toko = trim($data['nama_toko'] ?? '');
    $kategori = trim($data['kategori_toko'] ?? '');
    $alamat = trim($data['alamat_toko'] ?? '');

    if (empty($nama_toko)) {
        echo json_encode(["status" => "error", "message" => "Nama toko wajib diisi."]);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO stores (nama_toko, kategori_toko, alamat_toko) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nama_toko, $kategori, $alamat);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Toko berhasil didaftarkan.",
            "store_id" => $conn->insert_id,
            "nama_toko" => $nama_toko
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menyimpan data toko."]);
    }
}
?>