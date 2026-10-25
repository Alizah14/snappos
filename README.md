# SnapPOS — Sistem Kasir & Manajemen Inventaris Berbasis Web

## Latar Belakang

Banyak toko kelontong dan UMKM masih mengandalkan pencatatan penjualan serta stok barang secara manual berbasis kertas. Hal ini berisiko menimbulkan ketidakcocokan stok, lambatnya proses transaksi kasir, dan sulitnya memantau omset harian secara *real-time*. Di sisi lain, aplikasi kasir modern yang ada seringkali terlalu rumit atau memerlukan perangkat keras mahal.

**SnapPOS** adalah aplikasi kasir (Point of Sale / POS) berbasis web yang dirancang untuk merapikan dan mempermudah operasional toko. Melalui SnapPOS, pemilik toko dapat memisahkan hak akses pengguna secara aman antara **Admin** (untuk memantau statistik dashboard, kelola harga, dan stok barang) dan **Kasir** (untuk melayani transaksi dengan cepat).

Selain fitur kasir dasar, SnapPOS juga dirancang dari awal agar siap diintegrasikan dengan **Model AI Pengenal Produk**, sehingga ke depannya kasir cukup mengunggah foto kemasan barang tanpa perlu input barcode secara manual.

---

## Batasan Sistem (Progress 30%)

* **Autentikasi & Sesi**: Pengguna melakukan pendaftaran toko dan akun. Data sesi login (`username`, `nama_lengkap`, `role`, dan `user_id`) disimpan secara lokal pada `localStorage` browser.
* **Fitur AI**: Indikator kualitas data AI dan persentase kesiapan model pada halaman katalog saat ini masih berupa data statis/mockup antarmuka untuk pengembangan tahap awal.
* **Metode Pembayaran**: Transaksi pada milestone saat ini difokuskan pada pemrosesan katalog produk dan dashboard analitis. Endpoint `transaksi.php` sudah tersedia di backend dan masih dalam tahap pengembangan.

---

## Tech Stack

| Lapisan | Teknologi |
| :--- | :--- |
| **Frontend** | Vue.js 3 (Composition API), Vite, Tailwind CSS |
| **Routing & State** | Vue Router 4, Browser LocalStorage |
| **Backend API** | PHP 8.2 Native (RESTful API) di atas Apache |
| **Database** | MySQL 8 |
| **Containerization** | Docker, Docker Compose |

---

## Struktur Folder

```text
snappos/
├── backend/
│   ├── database/
│   │   └── snappos.sql
│   ├── koneksi.example.php
│   ├── login.php
│   ├── produk.php
│   ├── register_store.php
│   ├── transaksi.php
│   ├── users.php
│   ├── Dockerfile
│   └── .gitignore
├── frontend/
│   ├── public/
│   ├── src/
│   │   ├── assets/
│   │   ├── components/
│   │   │   ├── Footer.vue
│   │   │   ├── NavbarAdmin.vue
│   │   │   └── NavbarKasir.vue
│   │   ├── router/
│   │   ├── views/
│   │   │   ├── DashboardView.vue
│   │   │   ├── LoginView.vue
│   │   │   ├── ProductsView.vue
│   │   │   ├── RegisterStoreView.vue
│   │   │   └── RegisterUserView.vue
│   │   ├── App.vue
│   │   └── main.js
│   ├── .dockerignore
│   ├── .gitignore
│   ├── Dockerfile
│   ├── index.html
│   ├── jsconfig.json
│   ├── package.json
│   ├── package-lock.json
│   └── vite.config.js
├── docker-compose.yml
└── README.md
```

> File `backend/koneksi.php` (konfigurasi koneksi database yang aktif) tidak disimpan di repositori. File ini dibuat dari `koneksi.example.php`, otomatis saat memakai Docker atau manual saat memakai XAMPP.

---

## Endpoint API

### Konvensi Umum

| Aspek | Ketentuan |
| :--- | :--- |
| **Base URL** | `http://localhost/snappos-api` (sama untuk XAMPP maupun Docker) |
| **Format Bodi** | JSON (`application/json`) |
| **Format Response Sukses** | `[ { "id": 1, "name": "..." } ]` atau `{ "status": "success", "message": "..." }` |
| **Format Response Gagal** | `{ "status": "error", "message": "..." }` |
| **Bahasa Pesan** | Bahasa Indonesia |

### 1. Autentikasi & Pengguna

| Method | Path | Auth | Keterangan |
| :--- | :--- | :--- | :--- |
| `POST` | `/register_store.php` | — | Mendaftarkan toko baru ke database MySQL |
| `POST` | `/users.php` | — | Mendaftarkan akun pengguna baru (Admin / Kasir) |
| `POST` | `/login.php` | — | Verifikasi akun masuk berdasarkan username, password, dan role |

Contoh body untuk `POST /login.php`:

```json
{
  "username": "contoh_user",
  "password": "contoh_password",
  "role": "Admin"
}
```

Respons sukses berisi objek `user` dengan field `id`, `username`, `nama_lengkap`, dan `role`.

### 2. Katalog Produk (`/produk.php`)

| Method | Path | Auth | Keterangan |
| :--- | :--- | :--- | :--- |
| `GET` | `/produk.php` | — | Mengambil seluruh daftar item produk dari tabel `products` |
| `POST` | `/produk.php` | — | Menambahkan data produk baru ke database |
| `DELETE` | `/produk.php?id=:id` | Admin | Belum tersedia pada milestone ini (direncanakan) |

Respons `GET /produk.php` berupa array berisi seluruh produk, diurutkan dari ID terbaru. Contoh satu item:

```json
{
  "id": "5",
  "store_id": null,
  "name": "Snack Keripik Kentang BBQ",
  "sku": "SKU-89933452",
  "category": "Makanan",
  "price": "11000.00",
  "stock": "55",
  "unit": "bungkus",
  "model_photos_count": "4",
  "is_trained": "1",
  "image": "https://images.unsplash.com/...",
  "created_at": "2026-10-25 02:38:32"
}
```

Contoh body `POST /produk.php`. Hanya `name` dan `price` yang wajib, sisanya memakai nilai bawaan jika tidak diisi:

```json
{
  "name": "Teh Botol 450ml",
  "price": 5000,
  "sku": "SKU-0001",
  "category": "Minuman",
  "stock": 24,
  "unit": "botol",
  "image": "https://contoh.com/gambar.jpg"
}
```

| Field | Wajib | Nilai bawaan |
| :--- | :--- | :--- |
| `name` | Ya | — |
| `price` | Ya (harus lebih dari 0) | — |
| `sku` | Tidak | `SKU-` diikuti angka acak 6 digit |
| `category` | Tidak | `Umum` |
| `stock` | Tidak | `0` |
| `unit` | Tidak | `pcs` |
| `image` | Tidak | Gambar bawaan |

Respons sukses: `{ "status": "success", "message": "Produk baru berhasil ditambahkan." }`

### 3. Transaksi (`/transaksi.php`)

| Method | Path | Auth | Keterangan |
| :--- | :--- | :--- | :--- |
| `POST` | `/transaksi.php` | — | Mencatat transaksi penjualan (dalam tahap pengembangan) |

---

## Kode Error API

| Kode HTTP | Arti | Penyebab Umum |
| :--- | :--- | :--- |
| `400` | `BAD_REQUEST` | Parameter input tidak lengkap atau format JSON salah |
| `401` | `INVALID_CREDENTIALS` | Username atau password yang dimasukkan tidak cocok |
| `404` | `NOT_FOUND` | Endpoint API tidak ditemukan atau ID produk tidak ada |
| `409` | `USERNAME_EXISTS` | Username sudah terdaftar di database |
| `500` | `INTERNAL_ERROR` | Koneksi ke database MySQL gagal |

> **Catatan:** Pada tahap ini sebagian endpoint (misalnya `produk.php`) mengembalikan kesalahan validasi di dalam body JSON (`"status": "error"`) dengan kode HTTP `200`. Tabel di atas adalah standar kode kesalahan yang menjadi target pengembangan berikutnya.

---

## Cara Menjalankan SnapPOS

### Opsi 1 — Dengan Docker (Disarankan)

Cara paling praktis. Tidak perlu memasang PHP, XAMPP, MySQL, atau Node.js secara manual.

**Yang dibutuhkan:** [Git](https://git-scm.com/) dan [Docker Desktop](https://www.docker.com/products/docker-desktop/) (pastikan statusnya *Engine running*).

> **Penting:** Port `80` dan `5173` harus kosong. Matikan Apache XAMPP (dan program lain yang memakai port 80) sebelum menjalankan Docker.

1. **Unduh repositori:**

   ```bash
   git clone https://github.com/Alizah14/snappos.git
   cd snappos
   ```

2. **Jalankan container:**

   ```bash
   docker compose up --build -d
   ```

   Build pertama membutuhkan beberapa menit karena mengunduh image. Tunggu sekitar 30 detik setelah selesai agar MySQL selesai mengimpor database.

3. **Cek status container:**

   ```bash
   docker compose ps
   ```

   Ketiga container (`snappos_db`, `snappos_backend`, `snappos_frontend`) harus berstatus `Up`.

4. **Buka aplikasi:**

   | Layanan | Alamat |
   | :--- | :--- |
   | Frontend SnapPOS | http://localhost:5173 |
   | Backend API | http://localhost/snappos-api/produk.php |
   | MySQL (untuk DBeaver) | `localhost`, port `3308`, user `root`, password `rootpassword`, database `snappos_db` |

   Alamat backend harus menampilkan data produk dalam bentuk JSON.

#### Perintah Harian Docker

| Tujuan | Perintah |
| :--- | :--- |
| Nyalakan aplikasi (background) | `docker compose up --build -d` |
| Lihat status container | `docker compose ps` |
| Lihat log backend PHP | `docker compose logs -f backend` |
| Lihat log database | `docker compose logs -f db` |
| Bangun ulang setelah mengubah kode backend | `docker compose up --build -d backend` |
| Bangun ulang setelah mengubah kode frontend | `docker compose up --build -d frontend` |
| Hentikan container (data MySQL tetap aman) | `docker compose down` |
| Hentikan + hapus database | `docker compose down -v` |
| Masuk ke container backend | `docker compose exec backend sh` |

> File `backend/database/snappos.sql` hanya diimpor saat database pertama kali dibuat. Jika isinya diubah, jalankan `docker compose down -v` lalu `docker compose up --build -d`.

---

### Opsi 2 — Tanpa Docker (PHP Native + DBeaver / XAMPP)

**Yang dibutuhkan:** Git, XAMPP (Apache + MySQL), DBeaver, dan [Node.js](https://nodejs.org/) versi 20 ke atas.

1. **Unduh repositori:**

   ```bash
   git clone https://github.com/Alizah14/snappos.git
   cd snappos
   ```

2. **Siapkan database di DBeaver**
   - Nyalakan Apache dan MySQL di XAMPP, lalu hubungkan DBeaver ke server MySQL lokal kamu.
   - Buat database baru dengan nama `snappos_db`.
   - Buka file `backend/database/snappos.sql`, lalu jalankan script (`Ctrl + Alt + X` / Execute Script) di DBeaver untuk membuat tabel `stores`, `users`, `products`, beserta data dummy produknya.

3. **Jalankan backend (PHP)**
   - Salin isi folder `backend/` ke folder web server kamu, misalnya `C:/xampp/htdocs/snappos-api`.
   - Di folder tersebut, salin `koneksi.example.php` menjadi `koneksi.php`.
   - Buka `koneksi.php` dan sesuaikan password MySQL jika berbeda dari bawaan XAMPP (kosong).
   - Cek lewat browser: buka `http://localhost/snappos-api/produk.php`, harus muncul data JSON produk.

4. **Jalankan frontend (Vue.js 3)**

   Buka terminal baru di folder proyek:

   ```bash
   cd frontend
   npm install
   npm run dev
   ```

   Frontend berjalan di `http://localhost:5173`.

---

## Alur Penggunaan Pertama Kali

1. Buka `http://localhost:5173`, lalu klik **Daftarkan Toko Baru**.
2. Daftarkan akun pengguna (Admin atau Kasir) untuk toko tersebut.
3. Masuk lewat halaman login dengan memilih peran yang sesuai.
4. Admin diarahkan ke **Dashboard**, Kasir diarahkan ke halaman **Produk**.

---

## Pengujian API dengan Postman

Semua contoh memakai Base URL `http://localhost/snappos-api`, berlaku untuk XAMPP maupun Docker.

### 1. Cek Katalog Produk (`GET`)

- **Method:** `GET`
- **URL:** `http://localhost/snappos-api/produk.php`
- **Hasil diharapkan:** daftar produk dalam bentuk array JSON.

### 2. Tes Login Multi-Role (`POST`)

Daftarkan toko dan akun terlebih dahulu lewat halaman aplikasi (lihat *Alur Penggunaan Pertama Kali*), lalu:

- **Method:** `POST`
- **URL:** `http://localhost/snappos-api/login.php`
- **Body** (`raw` → `JSON`):

```json
{
  "username": "isi_username_akunmu",
  "password": "isi_password_akunmu",
  "role": "Admin"
}
```

- **Hasil diharapkan:** respons `"status": "success"` beserta objek `user`.
- **Jika gagal:** respons `"status": "error"` dengan pesan kesalahan.

### 3. Verifikasi di DBeaver

Buka DBeaver, klik kanan pada tabel `users` atau `stores`, lalu pilih **Refresh** / **View Data**. Data yang dikirim dari aplikasi atau Postman akan tersimpan di MySQL.

- Jika memakai XAMPP, hubungkan ke `localhost` port `3306`.
- Jika memakai Docker, hubungkan ke `localhost` port `3308` (user `root`, password `rootpassword`).

---

## Pemecahan Masalah

| Masalah | Penyebab & Solusi |
| :--- | :--- |
| `ports are not available ... port 80` | Apache XAMPP atau program lain memakai port 80. Matikan dulu, lalu jalankan ulang Docker. |
| `ports are not available ... port 3308` | Ubah angka kiri pada `ports` service `db` di `docker-compose.yml`, misalnya `"3309:3306"`. |
| `produk.php` menampilkan `Fatal error ... mysqli` | Backend belum terhubung ke database. Pastikan container `snappos_db` berstatus `Up`, tunggu 30 detik, lalu muat ulang. |
| `produk.php` menampilkan `[]` atau tabel tidak ditemukan | Database belum terisi. Jalankan `docker compose down -v` lalu `docker compose up --build -d`. |
| Login menampilkan "Gagal terhubung ke server backend API" | Backend belum berjalan atau Base URL salah. Cek `http://localhost/snappos-api/produk.php` di browser. |
| `http://localhost:5173` tidak terbuka | Pastikan tidak ada `npm run dev` lain yang berjalan, dan container `snappos_frontend` berstatus `Up`. |

---

## Catatan Mode Pengembangan & Batasan (Progress 30%)

- **Pilihan Peran Login:** Di halaman login tersedia tombol pilihan peran (**Admin** / **Kasir**). Pilih peran yang diinginkan sebelum memasukkan username dan kata sandi. Untuk berpindah peran, lakukan Logout lalu pilih peran lain di halaman login.
- **Sesi Login:** Sesi disimpan di `localStorage` browser. Jika sesi sebelumnya masih aktif dan ingin memulai dari awal, lakukan Logout atau Clear LocalStorage.
- **Status AI Kualitas Data:** Widget persentase Kualitas Data AI di katalog produk saat ini masih berupa tampilan mockup UI untuk persiapan tahap integrasi model AI selanjutnya.
- **Import CSV & Export Data:** Tombol operasi massal di sidebar katalog produk masih berupa desain antarmuka dan akan difungsikan di milestone berikutnya.
- **Mode Docker Frontend:** Frontend di Docker berjalan sebagai server pengembangan Vite. Perubahan kode frontend memerlukan build ulang container (`docker compose up --build -d frontend`).
