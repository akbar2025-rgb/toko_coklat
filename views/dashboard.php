<?php
$page_title = "Dashboard";
require_once 'includes/header.php';
require_once '../config/Database.php';
require_once '../models/Produk.php';
require_once '../models/Kategori.php';
require_once '../models/Pelanggan.php';
require_once '../models/Transaksi.php';

$database = new Database();
$db = $database->getConnection();

$produk = new Produk($db);
$kategori = new Kategori($db);
$pelanggan = new Pelanggan($db);
$transaksi = new Transaksi($db);

$total_produk = $produk->count();
$total_kategori = $kategori->count();
$total_pelanggan = $pelanggan->count();
$transaksi_hari_ini = $transaksi->countToday();
$pendapatan_hari_ini = $transaksi->sumToday();
?>

<div class="row">
    <div class="col-12">
        <h2><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
        <hr>
    </div>
</div>

<div class="row">
    <!-- Card Total Produk -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Produk
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo $total_produk; ?> Produk
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-box fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Card Total Kategori -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Kategori
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo $total_kategori; ?> Kategori
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Card Total Pelanggan -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Pelanggan
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo $total_pelanggan; ?> Pelanggan
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Card Transaksi Hari Ini -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Transaksi Hari Ini
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo $transaksi_hari_ini; ?> Transaksi
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Card Pendapatan Hari Ini -->
    <div class="col-xl-12 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Pendapatan Hari Ini
                        </div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">
                            Rp <?php echo number_format($pendapatan_hari_ini, 0, ',', '.'); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-3x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6 mb-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Sistem</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Nama Sistem:</strong></td>
                        <td>Sistem Informasi Toko Coklat</td>
                    </tr>
                    <tr>
                        <td><strong>Versi:</strong></td>
                        <td>1.0.0</td>
                    </tr>
                    <tr>
                        <td><strong>Teknologi:</strong></td>
                        <td>PHP OOP + MySQL + Bootstrap</td>
                    </tr>
                    <tr>
                        <td><strong>Login Sebagai:</strong></td>
                        <td><?php echo $_SESSION['nama_lengkap']; ?> (<?php echo ucfirst($_SESSION['level']); ?>)</td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal:</strong></td>
                        <td><?php echo date('d F Y'); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-xl-6 mb-4">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Statistik Cepat</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <p class="mb-1"><strong>Produk Tersedia</strong></p>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                            <?php echo $total_produk; ?> Produk
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <p class="mb-1"><strong>Kategori Terdaftar</strong></p>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100">
                            <?php echo $total_kategori; ?> Kategori
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <p class="mb-1"><strong>Pelanggan Terdaftar</strong></p>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">
                            <?php echo $total_pelanggan; ?> Pelanggan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-left-primary {
        border-left: 4px solid #4e73df;
    }
    .border-left-success {
        border-left: 4px solid #1cc88a;
    }
    .border-left-info {
        border-left: 4px solid #36b9cc;
    }
    .border-left-warning {
        border-left: 4px solid #f6c23e;
    }
    .border-left-danger {
        border-left: 4px solid #e74a3b;
    }
</style>

<?php
$database->closeConnection();
require_once 'includes/footer.php';
?>