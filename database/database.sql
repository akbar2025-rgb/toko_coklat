-- Database Toko Coklat
-- Sistem Informasi Penjualan Coklat

CREATE DATABASE IF NOT EXISTS toko_coklat;
USE toko_coklat;

-- Tabel User/Admin
CREATE TABLE IF NOT EXISTS users (
    id_user INT(11) PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    level ENUM('admin', 'kasir') DEFAULT 'kasir',
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Kategori Coklat
CREATE TABLE IF NOT EXISTS kategori (
    id_kategori INT(11) PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Produk Coklat
CREATE TABLE IF NOT EXISTS produk (
    id_produk INT(11) PRIMARY KEY AUTO_INCREMENT,
    id_kategori INT(11),
    kode_produk VARCHAR(50) NOT NULL UNIQUE,
    nama_produk VARCHAR(150) NOT NULL,
    deskripsi TEXT,
    harga_beli DECIMAL(15,2) NOT NULL DEFAULT 0,
    harga_jual DECIMAL(15,2) NOT NULL DEFAULT 0,
    stok INT(11) NOT NULL DEFAULT 0,
    satuan VARCHAR(20) DEFAULT 'pcs',
    gambar VARCHAR(255),
    status ENUM('tersedia', 'habis') DEFAULT 'tersedia',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori) ON DELETE SET NULL
);

-- Tabel Pelanggan
CREATE TABLE IF NOT EXISTS pelanggan (
    id_pelanggan INT(11) PRIMARY KEY AUTO_INCREMENT,
    kode_pelanggan VARCHAR(50) NOT NULL UNIQUE,
    nama_pelanggan VARCHAR(100) NOT NULL,
    alamat TEXT,
    telepon VARCHAR(20),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Transaksi/Penjualan
CREATE TABLE IF NOT EXISTS transaksi (
    id_transaksi INT(11) PRIMARY KEY AUTO_INCREMENT,
    no_transaksi VARCHAR(50) NOT NULL UNIQUE,
    tanggal_transaksi DATETIME NOT NULL,
    id_pelanggan INT(11),
    id_user INT(11),
    total_item INT(11) NOT NULL DEFAULT 0,
    subtotal DECIMAL(15,2) NOT NULL DEFAULT 0,
    diskon DECIMAL(15,2) NOT NULL DEFAULT 0,
    total_bayar DECIMAL(15,2) NOT NULL DEFAULT 0,
    jumlah_bayar DECIMAL(15,2) NOT NULL DEFAULT 0,
    kembalian DECIMAL(15,2) NOT NULL DEFAULT 0,
    status ENUM('selesai', 'pending', 'batal') DEFAULT 'selesai',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan) ON DELETE SET NULL,
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE SET NULL
);

-- Tabel Detail Transaksi
CREATE TABLE IF NOT EXISTS detail_transaksi (
    id_detail INT(11) PRIMARY KEY AUTO_INCREMENT,
    id_transaksi INT(11),
    id_produk INT(11),
    harga_jual DECIMAL(15,2) NOT NULL DEFAULT 0,
    jumlah INT(11) NOT NULL DEFAULT 0,
    subtotal DECIMAL(15,2) NOT NULL DEFAULT 0,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi) ON DELETE CASCADE,
    FOREIGN KEY (id_produk) REFERENCES produk(id_produk) ON DELETE SET NULL
);

-- Tabel Supplier
CREATE TABLE IF NOT EXISTS supplier (
    id_supplier INT(11) PRIMARY KEY AUTO_INCREMENT,
    kode_supplier VARCHAR(50) NOT NULL UNIQUE,
    nama_supplier VARCHAR(100) NOT NULL,
    alamat TEXT,
    telepon VARCHAR(20),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert data default
INSERT INTO users (username, password, nama_lengkap, email, level) VALUES
('admin', MD5('admin123'), 'Administrator', 'admin@tokocklat.com', 'admin'),
('kasir', MD5('kasir123'), 'Kasir 1', 'kasir@tokocklat.com', 'kasir');

INSERT INTO kategori (nama_kategori, deskripsi) VALUES
('Coklat Batangan', 'Coklat dalam bentuk batangan atau bar'),
('Coklat Praline', 'Coklat dengan isian berbagai rasa'),
('Coklat Truffle', 'Coklat premium dengan tekstur lembut'),
('Coklat Box', 'Coklat dalam kemasan box cantik'),
('Coklat Minuman', 'Coklat bubuk dan minuman coklat');

INSERT INTO pelanggan (kode_pelanggan, nama_pelanggan, alamat, telepon) VALUES
('PLG001', 'Umum', 'Toko', '-');