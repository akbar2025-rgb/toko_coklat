# Sistem Informasi Toko Coklat

Sistem Informasi Berbasis Web untuk pengelolaan penjualan di toko coklat menggunakan PHP OOP, MySQL, dan Bootstrap (offline).

## Spesifikasi Sistem

### Teknologi yang Digunakan
- **Bahasa Pemrograman**: PHP 7.4+ dengan konsep OOP (Object-Oriented Programming)
- **Database**: MySQL 5.7+
- **Framework CSS**: Bootstrap 5.1.3 (Offline)
- **Web Server**: Debian 6 (atau server lain yang kompatibel)
- **JavaScript**: jQuery 3.6.0 (Offline)
- **Icon**: Font Awesome 5.15.4 (Offline)

### Fitur Sistem

#### 1. Master Data
- **Kategori Produk**: CRUD (Create, Read, Update, Delete) data kategori coklat
- **Produk**: CRUD data produk coklat dengan detail lengkap
- **Pelanggan**: CRUD data pelanggan

#### 2. Transaksi
- Input transaksi penjualan
- Pencatatan detail transaksi
- Generate nomor transaksi otomatis
- Perhitungan otomatis (subtotal, diskon, total)

#### 3. Dashboard
- Statistik penjualan hari ini
- Total produk, kategori, dan pelanggan
- Pendapatan hari ini
- Grafik dan informasi sistem

#### 4. Laporan
- Laporan penjualan berdasarkan periode
- Laporan stok produk
- Laporan pelanggan

#### 5. Autentikasi
- Login multi-level (Admin & Kasir)
- Session management
- Logout

## Struktur Database

### Tabel Users
- id_user (PK)
- username
- password (MD5)
- nama_lengkap
- email
- level (admin/kasir)
- status (aktif/nonaktif)

### Tabel Kategori
- id_kategori (PK)
- nama_kategori
- deskripsi

### Tabel Produk
- id_produk (PK)
- id_kategori (FK)
- kode_produk
- nama_produk
- deskripsi
- harga_beli
- harga_jual
- stok
- satuan
- gambar
- status

### Tabel Pelanggan
- id_pelanggan (PK)
- kode_pelanggan
- nama_pelanggan
- alamat
- telepon
- email

### Tabel Transaksi
- id_transaksi (PK)
- no_transaksi
- tanggal_transaksi
- id_pelanggan (FK)
- id_user (FK)
- total_item
- subtotal
- diskon
- total_bayar
- jumlah_bayar
- kembalian
- status

### Tabel Detail Transaksi
- id_detail (PK)
- id_transaksi (FK)
- id_produk (FK)
- harga_jual
- jumlah
- subtotal

## Struktur Folder

```
toko_coklat/
│
├── config/
│   └── Database.php          # Class koneksi database
│
├── models/
│   ├── Kategori.php          # Model Kategori
│   ├── Produk.php            # Model Produk
│   ├── Pelanggan.php         # Model Pelanggan
│   └── Transaksi.php         # Model Transaksi
│
├── views/
│   ├── includes/
│   │   ├── header.php        # Header & Navbar
│   │   └── footer.php        # Footer
│   ├── login.php             # Halaman Login
│   ├── dashboard.php         # Dashboard
│   ├── kategori.php          # Manajemen Kategori
│   ├── produk.php            # Manajemen Produk
│   ├── pelanggan.php         # Manajemen Pelanggan
│   ├── transaksi.php         # Transaksi Penjualan
│   ├── laporan.php           # Laporan
│   └── logout.php            # Logout
│
├── assets/
│   ├── bootstrap/            # Bootstrap CSS & JS (offline)
│   ├── fontawesome/          # Font Awesome (offline)
│   ├── jquery/               # jQuery (offline)
│   ├── css/
│   │   └── style.css         # Custom CSS
│   └── js/
│       └── script.js         # Custom JavaScript
│
├── database/
│   └── database.sql          # File SQL Database
│
└── README.md                 # Dokumentasi
```

## Login Default

### Admin
- Username: `admin`
- Password: `admin123`

### Kasir
- Username: `kasir`
- Password: `kasir123`

## Penggunaan

### 1. Login
Akses sistem melalui browser: `http://localhost/toko_coklat/views/login.php`

### 2. Dashboard
Setelah login, Anda akan diarahkan ke dashboard yang menampilkan statistik penjualan.

### 3. Master Data
- **Kategori**: Kelola kategori produk coklat
- **Produk**: Kelola data produk coklat dengan harga dan stok
- **Pelanggan**: Kelola data pelanggan

### 4. Transaksi
- Pilih pelanggan
- Tambah produk ke keranjang
- Hitung total dan proses pembayaran
- Cetak nota (opsional)

### 5. Laporan
- Filter laporan berdasarkan tanggal
- Export ke PDF/Excel (opsional)
- Cetak laporan

## Fitur OOP yang Diimplementasikan

### 1. Encapsulation
- Properties private/protected
- Getter dan Setter methods
- Visibility control

### 2. Inheritance
- Class Database sebagai parent
- Model classes sebagai children

### 3. Abstraction
- Method-method CRUD yang abstrak
- Interface database yang jelas

### 4. Method Overloading
- Flexible parameter handling
- Default values

## Keamanan

### 1. SQL Injection Prevention
- Prepared statements
- Parameterized queries
- Input sanitization

### 2. XSS Prevention
- htmlspecialchars()
- strip_tags()
- Input validation

### 3. Session Security
- Session hijacking prevention
- Proper session management
- Logout functionality

### 4. Password Security
- MD5 hashing (bisa di-upgrade ke bcrypt)
- Secure password storage

## Pengembangan Lebih Lanjut

### 1. Fitur Tambahan
- Upload gambar produk
- Barcode scanner
- Laporan grafik lebih detail
- Export ke Excel/PDF
- Email notification
- WhatsApp integration

### 2. Peningkatan Keamanan
- Upgrade password hashing ke bcrypt
- Implementasi CSRF protection
- Rate limiting
- Two-factor authentication

### 3. Optimasi
- Implementasi caching
- Query optimization
- Lazy loading
- Ajax pagination

### 4. UI/UX
- Responsive design improvement
- Dark mode
- Progressive Web App (PWA)
- Real-time notification

## Troubleshooting

### Error: Cannot connect to database
- Cek kredensial database di `config/Database.php`
- Pastikan MySQL service berjalan
- Cek firewall settings

### Error: Bootstrap not loaded
- Pastikan folder `assets/bootstrap` ada dan lengkap
- Cek path di `header.php` dan `footer.php`
- Download Bootstrap offline jika belum ada

### Error: Session not working
- Pastikan `session_start()` dipanggil
- Cek permission folder session di server
- Restart Apache

## Catatan Penting

1. **Backup Data**: Selalu backup database secara berkala
2. **Update Sistem**: Perbarui PHP dan MySQL secara teratur
3. **Keamanan**: Ganti password default setelah instalasi
4. **Testing**: Test sistem di environment development dulu
5. **Dokumentasi**: Catat setiap perubahan yang dilakukan

---

**Dikembangkan dengan ❤️ untuk Toko Coklat Indonesia**
