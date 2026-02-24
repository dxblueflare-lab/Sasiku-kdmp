-- Database Schema untuk Sistem E-Commerce Dapur MBG

-- Membuat database
CREATE DATABASE IF NOT EXISTS ecommerce_dapur_mbg;
USE ecommerce_dapur_mbg;

-- Tabel pengguna (users)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'supplier', 'customer') NOT NULL,
    nama_lengkap VARCHAR(100),
    alamat TEXT,
    nomor_telepon VARCHAR(15),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel kategori produk
CREATE TABLE kategori_produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(50) UNIQUE NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel produk
CREATE TABLE produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(10, 2) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    id_kategori INT,
    id_supplier INT,
    gambar_produk VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kategori) REFERENCES kategori_produk(id),
    FOREIGN KEY (id_supplier) REFERENCES users(id) ON DELETE SET NULL
);

-- Tabel keranjang belanja
CREATE TABLE keranjang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    id_produk INT,
    jumlah INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_produk) REFERENCES produk(id) ON DELETE CASCADE
);

-- Tabel pesanan
CREATE TABLE pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    total_harga DECIMAL(10, 2) NOT NULL,
    status_pesanan ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    metode_pembayaran VARCHAR(50),
    bukti_pembayaran VARCHAR(255),
    tanggal_pesan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tanggal_pengiriman DATE,
    alamat_pengiriman TEXT,
    catatan TEXT,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel detail pesanan
CREATE TABLE detail_pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pesanan INT,
    id_produk INT,
    jumlah INT NOT NULL,
    harga_satuan DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_pesanan) REFERENCES pesanan(id) ON DELETE CASCADE,
    FOREIGN KEY (id_produk) REFERENCES produk(id) ON DELETE CASCADE
);

-- Tabel pembayaran
CREATE TABLE pembayaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pesanan INT,
    jumlah_pembayaran DECIMAL(10, 2) NOT NULL,
    metode_pembayaran VARCHAR(50),
    status_pembayaran ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    tanggal_pembayaran TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    bukti_pembayaran VARCHAR(255),
    FOREIGN KEY (id_pesanan) REFERENCES pesanan(id) ON DELETE CASCADE
);

-- Tabel pelacakan pesanan
CREATE TABLE pelacakan_pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pesanan INT,
    status_pesanan ENUM('order_placed', 'confirmed', 'processing', 'shipped', 'delivered') NOT NULL,
    catatan TEXT,
    tanggal_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pesanan) REFERENCES pesanan(id) ON DELETE CASCADE
);

-- Data awal untuk kategori produk
INSERT INTO kategori_produk (nama_kategori, deskripsi) VALUES
('Sayur Segar', 'Berbagai jenis sayuran segar untuk kebutuhan dapur'),
('Protein Hewani', 'Daging, ayam, ikan, dan telur segar'),
('Sembako', 'Sembilan bahan pokok seperti beras, gula, minyak'),
('Buah-buahan', 'Buah-buahan segar dan sehat');

-- Data awal untuk pengguna (admin)
INSERT INTO users (username, email, password, role, nama_lengkap) VALUES
('admin', 'admin@dapursppgmbg.co.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrator Dapur SPPG');