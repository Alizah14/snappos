# SnapPOS — Sistem Kasir & Manajemen Inventaris Berbasis Web

## Latar Belakang

Banyak toko kelontong dan UMKM masih mengandalkan pencatatan penjualan serta stok barang secara manual berbasis kertas. Hal ini berisiko menimbulkan ketidakcocokan stok, lambatnya proses transaksi kasir, dan sulitnya memantau omset harian secara *real-time*. Di sisi lain, aplikasi kasir modern yang ada seringkali terlalu rumit atau memerlukan perangkat keras mahal.

**SnapPOS** adalah aplikasi kasir (Point of Sale / POS) berbasis web yang dirancang untuk merapikan dan mempermudah operasional toko. Melalui SnapPOS, pemilik toko dapat memisahkan hak akses pengguna secara aman antara **Admin** (untuk memantau statistik dashboard, kelola harga, dan stok barang) dan **Kasir** (untuk melayani transaksi dengan cepat). 

Selain fitur kasir dasar, SnapPOS juga dirancang dari awal agar siap diintegrasikan dengan **Model AI Pengenal Produk**, sehingga ke depannya kasir cukup mengunggah foto kemasan barang tanpa perlu input barcode secara manual.

---

## Batasan Sistem (Progress 30%)

* **Autentikasi & Sesi**: Pengguna melakukan pendaftaran toko dan akun. Sesi login (*role* dan nama toko) disimpan secara lokal pada `localStorage` browser.
* **Fitur AI**: Indikator kualitas data AI dan persentase kesiapan model pada halaman katalog saat ini masih berupa data statis/mockup antarmuka untuk pengembangan tahap awal.
* **Metode Pembayaran**: Transaksi pada milestone saat ini difokuskan pada pemrosesan katalog produk dan dashboard analitis.

---

## Tech Stack

| Lapisan | Teknologi |
| :--- | :--- |
| **Frontend** | Vue.js 3 (Composition API), Vite, Tailwind CSS |
| **Routing & State** | Vue Router 4, Browser LocalStorage |
| **Backend API** | PHP Native (RESTful API) |
| **Database** | MySQL 8 |
| **Containerization** | Docker, Docker Compose, Nginx |

---

## Struktur Folder

```text
SnapPOS/
├── backend/
│   ├── config/
│   │   └── koneksi.php
│   ├── uploads/
│   ├── login.php
│   ├── produk.php
│   ├── register_store.php
│   ├── users.php
│   ├── Dockerfile
│   └── .dockerignore
├── frontend/
│   ├── src/
│   │   ├── components/
│   │   │   ├── Footer.vue
│   │   │   ├── NavbarAdmin.vue
│   │   │   └── NavbarKasir.vue
│   │   ├── views/
│   │   │   ├── DashboardView.vue
│   │   │   ├── LoginView.vue
│   │   │   ├── ProductsView.vue
│   │   │   ├── RegisterStoreView.vue
│   │   │   └── RegisterUserView.vue
│   │   ├── App.vue
│   │   ├── main.js
│   │   └── router.js
│   ├── index.html
│   ├── vite.config.js
│   ├── tailwind.config.js
│   ├── Dockerfile
│   ├── nginx.conf
│   └── .dockerignore
├── snappos_db.sql
├── docker-compose.yml
├── .gitignore
└── README.md

---

## Endpoint API

### Konvensi Umum
| Aspek | Ketentuan |
| :--- | :--- |
| **Base URL (Lokal Manual)** | `http://localhost/snappos-api` |
| **Base URL (Docker)** | `http://localhost:3001` |
| **Format Bodi** | JSON (`application/json`) |
| **Format Response Sukses** | `[ { "id": 1, "name": "..." } ]` atau `{ "status": "success", "message": "..." }` |
| **Format Response Gagal** | `{ "status": "error", "message": "..." }` |
| **Bahasa Pesan** | Bahasa Indonesia |

### 1. Autentikasi & Pengguna
| Method | Path | Auth | Keterangan |
| :--- | :--- | :--- | :--- |
| `POST` | `/register_store.php` | — | Mendaftarkan toko baru ke database MySQL |
| `POST` | `/users.php` | — | Mendaftarkan akun pengguna baru (Admin / Kasir) |
| `POST` | `/login.php` | — | Verifikasi akun masuk & menyimpan sesi toko/role |

### 2. Katalog Produk (`/produk.php`)
| Method | Path | Auth | Keterangan |
| :--- | :--- | :--- | :--- |
| `GET` | `/produk.php` | — | Mengambil seluruh daftar item produk dari tabel `products` |
| `POST` | `/produk.php` | — | Menambahkan data produk baru ke database |
| `DELETE` | `/produk.php?id=:id` | Admin | Menghapus data produk dari MySQL berdasarkan ID |

---

## Kode Error API

| Kode HTTP | Arti | Penyebab Umum |
| :--- | :--- | :--- |
| `400` | `BAD_REQUEST` | Parameter input tidak lengkap atau format JSON salah |
| `401` | `INVALID_CREDENTIALS` | Username atau password yang dimasukkan tidak cocok |
| `404` | `NOT_FOUND` | Endpoint API tidak ditemukan atau ID produk tidak ada |
| `409` | `USERNAME_EXISTS` | Username sudah terdaftar di database |
| `500` | `INTERNAL_ERROR` | Koneksi ke database MySQL gagal |

---

## Cara Menjalankan Proyek

Terdapat dua cara untuk menjalankan SnapPOS: Menggunakan **Docker (Direkomendasikan)** atau **Tanpa Docker (Manual XAMPP/Laragon)**.

### Opsi 1 — Dengan Docker (Direkomendasikan)
Metode paling praktis untuk menjalankan aplikasi tanpa perlu mengonfigurasi Apache atau MySQL secara manual.

1. **Unduh Repositori:**
   ```bash
   git clone [https://github.com/Alizah14/snappos.git](https://github.com/Alizah14/snappos.git)
   cd snappos
