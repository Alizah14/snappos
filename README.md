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
