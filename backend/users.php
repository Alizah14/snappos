<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

include 'koneksi.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Mengambil daftar user
    $result = $conn->query("SELECT id, store_id, nama_lengkap, username, no_hp, role FROM users");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);

} elseif ($method === 'POST') {
    // Registrasi User Baru (Admin / Kasir)
    $data = json_decode(file_get_contents("php://input"), true);
    
    $nama_lengkap = trim($data['nama_lengkap'] ?? '');
    $username = trim($data['username'] ?? '');
    $no_hp = trim($data['no_hp'] ?? '');
    $password = trim($data['password'] ?? '');
    $role = trim($data['role'] ?? 'Admin');

    if (empty($username) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Username dan Password wajib diisi."]);
        exit();
    }

    // Cek apakah username sudah ada
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Username sudah terdaftar!"]);
        exit();
    }

    // Hash Password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (nama_lengkap, username, no_hp, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nama_lengkap, $username, $no_hp, $hashedPassword, $role);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Akun {$role} berhasil didaftarkan.",
            "username" => $username,
            "role" => $role
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal mendaftarkan akun."]);
    }
}
?>