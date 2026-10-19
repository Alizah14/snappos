<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $username = trim($data['username'] ?? '');
    $password = trim($data['password'] ?? '');
    $selectedRole = trim($data['role'] ?? 'Admin');

    if (empty($username) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Username dan password tidak boleh kosong."]);
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Cek apakah role sesuai dengan pilihan di form login
        if ($user['role'] !== $selectedRole) {
            echo json_encode([
                "status" => "error",
                "message" => "Akun terdaftar sebagai {$user['role']}, tidak bisa masuk sebagai {$selectedRole}."
            ]);
            exit();
        }

        echo json_encode([
            "status" => "success",
            "message" => "Login berhasil.",
            "user" => [
                "id" => $user['id'],
                "nama_lengkap" => $user['nama_lengkap'],
                "username" => $user['username'],
                "role" => $user['role']
            ]
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Username atau password salah!"]);
    }
}
?>