<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

include 'koneksi.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Ambil daftar seluruh produk dari database
    $result = $conn->query("SELECT * FROM products ORDER BY id DESC");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    // PERBAIKAN DI SINI: Langsung echo $data agar terbaca sebagai Array oleh Vue
    echo json_encode($data);

} elseif ($method === 'POST') {
    // Tambah Produk Baru
    $input = json_decode(file_get_contents('php://input'), true);

    $name = trim($input['name'] ?? '');
    $sku = trim($input['sku'] ?? ('SKU-' . rand(100000, 999999)));
    $category = trim($input['category'] ?? 'Umum');
    $price = floatval($input['price'] ?? 0);
    $stock = intval($input['stock'] ?? 0);
    $unit = trim($input['unit'] ?? 'pcs');
    $image = trim($input['image'] ?? 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?q=80&w=600&auto=format&fit=crop');

    if (empty($name) || $price <= 0) {
        echo json_encode(["status" => "error", "message" => "Nama produk dan harga wajib diisi secara benar."]);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO products (name, sku, category, price, stock, unit, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdiss", $name, $sku, $category, $price, $stock, $unit, $image);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Produk baru berhasil ditambahkan."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menambahkan produk."]);
    }
}
?>