<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include 'koneksi.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Ambil histori transaksi
    $result = $conn->query("SELECT * FROM transactions ORDER BY id DESC");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
} elseif ($method === 'POST') {
    // Simpan Transaksi Baru & Potong Stok Produk
    $input = json_decode(file_get_contents('php://input'), true);
    
    $no_transaksi = 'TRX-' . time();
    $user_id = $input['user_id'] ?? NULL;
    $total_tagihan = $input['total_tagihan'] ?? 0;
    $metode_pembayaran = $input['metode_pembayaran'] ?? 'Tunai';
    $uang_diterima = $input['uang_diterima'] ?? 0;
    $kembalian = $uang_diterima - $total_tagihan;
    $items = $input['items'] ?? [];

    if (empty($items)) {
        echo json_encode(['status' => 'error', 'message' => 'Keranjang belanja kosong']);
        exit;
    }

    $conn->begin_transaction();

    try {
        // 1. Simpan Transaksi Utama
        $stmt = $conn->prepare("INSERT INTO transactions (no_transaksi, user_id, total_tagihan, metode_pembayaran, uang_diterima, kembalian) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siiiii", $no_transaksi, $user_id, $total_tagihan, $metode_pembayaran, $uang_diterima, $kembalian);
        $stmt->execute();
        $transaction_id = $conn->insert_id;

        // 2. Simpan Rincian Transaksi (transaction_items) & Update Stok
        $stmt_item = $conn->prepare("INSERT INTO transaction_items (transaction_id, product_id, jumlah_barang, harga_satuan, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmt_stok = $conn->prepare("UPDATE products SET stok = stok - ? WHERE id = ?");

        foreach ($items as $item) {
            $p_id = $item['product_id'];
            $jumlah = $item['jumlah_barang'];
            $harga = $item['harga_satuan'];
            $subtotal = $jumlah * $harga;

            $stmt_item->bind_param("iiiii", $transaction_id, $p_id, $jumlah, $harga, $subtotal);
            $stmt_item->execute();

            $stmt_stok->bind_param("ii", $jumlah, $p_id);
            $stmt_stok->execute();
        }

        $conn->commit();
        echo json_encode([
            'status' => 'success', 
            'message' => 'Transaksi berhasil disimpan', 
            'no_transaksi' => $no_transaksi,
            'kembalian' => $kembalian
        ]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Transaksi gagal: ' . $e->getMessage()]);
    }
}
?>