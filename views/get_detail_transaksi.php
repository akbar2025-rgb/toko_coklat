<?php
require_once '../config/Database.php';
require_once '../models/Transaksi.php';

if(!isset($_GET['id'])) {
    echo '<div class="alert alert-danger">ID transaksi tidak ditemukan</div>';
    exit;
}

$database = new Database();
$db = $database->getConnection();
$transaksi = new Transaksi($db);

$transaksi->id_transaksi = $_GET['id'];

if(!$transaksi->readOne()) {
    echo '<div class="alert alert-danger">Data transaksi tidak ditemukan</div>';
    exit;
}

// Get detail items
$details = $transaksi->readDetail($_GET['id']);
?>

<div class="row">
    <div class="col-md-6">
        <table class="table table-borderless">
            <tr>
                <td><strong>No. Transaksi:</strong></td>
                <td><?php echo $transaksi->no_transaksi; ?></td>
            </tr>
            <tr>
                <td><strong>Tanggal:</strong></td>
                <td><?php echo date('d/m/Y H:i', strtotime($transaksi->tanggal_transaksi)); ?></td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td>
                    <?php if($transaksi->status == 'selesai'): ?>
                    <span class="badge bg-success">Selesai</span>
                    <?php else: ?>
                    <span class="badge bg-warning">Pending</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <table class="table table-borderless">
            <tr>
                <td><strong>Total Item:</strong></td>
                <td><?php echo $transaksi->total_item; ?> item</td>
            </tr>
            <tr>
                <td><strong>Diskon:</strong></td>
                <td>Rp <?php echo number_format($transaksi->diskon, 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <td><strong>Total Bayar:</strong></td>
                <td><strong>Rp <?php echo number_format($transaksi->total_bayar, 0, ',', '.'); ?></strong></td>
            </tr>
        </table>
    </div>
</div>

<hr>

<h6>Item Produk:</h6>
<table class="table table-sm table-bordered">
    <thead class="table-secondary">
        <tr>
            <th>No</th>
            <th>Produk</th>
            <th>Harga</th>
            <th>Jumlah</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        while($row = $details->fetch_assoc()): 
        ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $row['nama_produk']; ?></td>
            <td>Rp <?php echo number_format($row['harga_jual'], 0, ',', '.'); ?></td>
            <td><?php echo $row['jumlah']; ?> <?php echo $row['satuan']; ?></td>
            <td>Rp <?php echo number_format($row['subtotal'], 0, ',', '.'); ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<div class="row mt-3">
    <div class="col-md-6 offset-md-6">
        <table class="table table-borderless">
            <tr>
                <td><strong>Subtotal:</strong></td>
                <td class="text-end">Rp <?php echo number_format($transaksi->subtotal, 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <td><strong>Diskon:</strong></td>
                <td class="text-end">Rp <?php echo number_format($transaksi->diskon, 0, ',', '.'); ?></td>
            </tr>
            <tr class="table-secondary">
                <td><strong>Total:</strong></td>
                <td class="text-end"><strong>Rp <?php echo number_format($transaksi->total_bayar, 0, ',', '.'); ?></strong></td>
            </tr>
            <tr>
                <td><strong>Jumlah Bayar:</strong></td>
                <td class="text-end">Rp <?php echo number_format($transaksi->jumlah_bayar, 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <td><strong>Kembalian:</strong></td>
                <td class="text-end">Rp <?php echo number_format($transaksi->kembalian, 0, ',', '.'); ?></td>
            </tr>
        </table>
    </div>
</div>

<?php
$database->closeConnection();
?>