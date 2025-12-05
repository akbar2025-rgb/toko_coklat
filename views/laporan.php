<?php
$page_title = "Laporan";
require_once 'includes/header.php';
require_once '../config/Database.php';
require_once '../models/Transaksi.php';
require_once '../models/Produk.php';

$database = new Database();
$db = $database->getConnection();
$transaksi = new Transaksi($db);
$produk = new Produk($db);

// Default filter: hari ini
$tanggal_mulai = isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : date('Y-m-d');
$tanggal_selesai = isset($_GET['tanggal_selesai']) ? $_GET['tanggal_selesai'] : date('Y-m-d');

// Get data transaksi
$result = $transaksi->readByTanggal($tanggal_mulai, $tanggal_selesai);

// Hitung statistik
$total_transaksi = 0;
$total_pendapatan = 0;
$total_item_terjual = 0;

if($result->num_rows > 0) {
    $result->data_seek(0);
    while($row = $result->fetch_assoc()) {
        if($row['status'] == 'selesai') {
            $total_transaksi++;
            $total_pendapatan += $row['total_bayar'];
            $total_item_terjual += $row['total_item'];
        }
    }
}
?>

<div class="row">
    <div class="col-12">
        <h2><i class="fas fa-file-alt"></i> Laporan Penjualan</h2>
        <hr>
    </div>
</div>

<!-- Filter -->
<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-filter"></i> Filter Laporan</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="">
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Mulai:</label>
                        <input type="date" name="tanggal_mulai" class="form-control" value="<?php echo $tanggal_mulai; ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Selesai:</label>
                        <input type="date" name="tanggal_selesai" class="form-control" value="<?php echo $tanggal_selesai; ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">&nbsp;</label><br>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>
                        <button type="button" class="btn btn-success" onclick="printLaporan()">
                            <i class="fas fa-print"></i> Cetak
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Statistik -->
<div class="row">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Transaksi
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo $total_transaksi; ?> Transaksi
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Pendapatan
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Item Terjual
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo $total_item_terjual; ?> Item
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-box fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Transaksi -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Detail Transaksi</h6>
    </div>
    <div class="card-body">
        <div id="printArea">
            <div class="text-center mb-4" id="printHeader" style="display:none;">
                <h3>LAPORAN PENJUALAN TOKO COKLAT</h3>
                <p>Periode: <?php echo date('d/m/Y', strtotime($tanggal_mulai)); ?> - <?php echo date('d/m/Y', strtotime($tanggal_selesai)); ?></p>
                <hr>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tableLaporan">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>No. Transaksi</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Kasir</th>
                            <th>Total Item</th>
                            <th>Total Bayar</th>
                            <th>Status</th>
                            <th class="no-print">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if($result->num_rows > 0):
                            $result->data_seek(0);
                            $no = 1;
                            while($row = $result->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row['no_transaksi']; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['tanggal_transaksi'])); ?></td>
                            <td><?php echo $row['nama_pelanggan']; ?></td>
                            <td><?php echo $row['nama_lengkap']; ?></td>
                            <td><?php echo $row['total_item']; ?></td>
                            <td>Rp <?php echo number_format($row['total_bayar'], 0, ',', '.'); ?></td>
                            <td>
                                <?php if($row['status'] == 'selesai'): ?>
                                <span class="badge bg-success">Selesai</span>
                                <?php elseif($row['status'] == 'pending'): ?>
                                <span class="badge bg-warning">Pending</span>
                                <?php else: ?>
                                <span class="badge bg-danger">Batal</span>
                                <?php endif; ?>
                            </td>
                            <td class="no-print">
                                <button class="btn btn-sm btn-info" onclick="lihatDetail(<?php echo $row['id_transaksi']; ?>)">
                                    <i class="fas fa-eye"></i> Detail
                                </button>
                            </td>
                        </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data transaksi</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-secondary">
                            <th colspan="5" class="text-end">TOTAL:</th>
                            <th><?php echo $total_item_terjual; ?></th>
                            <th>Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Transaksi -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-file-invoice"></i> Detail Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, .navbar, .btn, footer, .card-header, .alert {
        display: none !important;
    }
    #printHeader {
        display: block !important;
    }
    .card {
        border: none;
        box-shadow: none;
    }
    body {
        background: white;
    }
}
</style>

<script>
function printLaporan() {
    window.print();
}

function lihatDetail(id) {
    const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
    modal.show();
    
    // Load detail via AJAX (simplified - dalam implementasi nyata gunakan AJAX)
    fetch('get_detail_transaksi.php?id=' + id)
        .then(response => response.text())
        .then(data => {
            document.getElementById('detailContent').innerHTML = data;
        })
        .catch(error => {
            document.getElementById('detailContent').innerHTML = 
                '<div class="alert alert-danger">Gagal memuat detail transaksi</div>';
        });
}
</script>

<?php
$database->closeConnection();
require_once 'includes/footer.php';
?>