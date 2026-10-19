-- Yang Benar:
DROP DATABASE IF EXISTS snappos_db;
CREATE DATABASE IF NOT EXISTS snappos_db DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE snappos_db;

-- 1. TABEL TOKO
CREATE TABLE IF NOT EXISTS stores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_toko VARCHAR(150) NOT NULL,
  kategori_toko VARCHAR(100) NULL,
  alamat_toko TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. TABEL PENGGUNA (ADMIN & KASIR)
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  store_id INT NULL,
  nama_lengkap VARCHAR(150) NOT NULL,
  username VARCHAR(100) NOT NULL UNIQUE,
  no_hp VARCHAR(20) NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('Admin', 'Kasir') NOT NULL DEFAULT 'Admin',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. TABEL KATALOG PRODUK
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  store_id INT NULL,
  name VARCHAR(200) NOT NULL,
  sku VARCHAR(100) NOT NULL,
  category VARCHAR(100) NOT NULL,
  price DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  stock INT NOT NULL DEFAULT 0,
  unit VARCHAR(50) NOT NULL DEFAULT 'pcs',
  model_photos_count INT NOT NULL DEFAULT 0,
  is_trained TINYINT(1) NOT NULL DEFAULT 1,
  image VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- DUMMY DATA PRODUK UNTUK TESTING
INSERT INTO products (name, sku, category, price, stock, unit, model_photos_count, is_trained, image) VALUES
('Kopi Arabika Specialty', 'SKU-89910023', 'Minuman', 65000.00, 24, 'bks', 3, 1, 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?q=80&w=600&auto=format&fit=crop'),
('Minyak Goreng 2L Pouch', 'SKU-89945101', 'Sembako', 34000.00, 8, 'pouch', 4, 0, 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?q=80&w=600&auto=format&fit=crop'),
('Susu UHT Full Cream 1L', 'SKU-89921445', 'Minuman', 18500.00, 42, 'pcs', 5, 1, 'https://images.unsplash.com/photo-1563636619-e9143da7973b?q=80&w=600&auto=format&fit=crop'),
('Sabun Cuci Piring Jeruk Nipis', 'SKU-89987211', 'Kebersihan', 14500.00, 16, 'pouch', 3, 1, 'https://images.unsplash.com/photo-1585842378054-ee2e52f94ba2?q=80&w=600&auto=format&fit=crop'),
('Snack Keripik Kentang BBQ', 'SKU-89933452', 'Makanan', 11000.00, 55, 'bungkus', 4, 1, 'https://images.unsplash.com/photo-1566478989037-eec170784d0b?q=80&w=600&auto=format&fit=crop');