<?php
$page_title = "Transaksi Penjualan";
require_once 'includes/header.php';
require_once '../config/Database.php';
require_once '../models/Produk.php';
require_once '../models/Pelanggan.php';
require_once '../models/Transaksi.php';

$database = new Database();
$db = $database->getConnection();
$produk = new Produk($db);
$pelanggan = new Pelanggan($db);
$transaksi = new Transaksi($db);

// Proses simpan transaksi
$message = '';
$message_type = '';

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan_transaksi'])) {
    // Generate nomor transaksi
    $transaksi->no_transaksi = $transaksi->generateNoTransaksi();
    $transaksi->tanggal_transaksi = date('Y-m-d H:i:s');
    $transaksi->id_pelanggan = $_POST['id_pelanggan'];
    $transaksi->id_user = $_SESSION['id_user'];
    $transaksi->total_item = $_POST['total_item'];
    $transaksi->subtotal = $_POST['subtotal'];
    $transaksi->diskon = $_POST['diskon'];
    $transaksi->total_bayar = $_POST['total_bayar'];
    $transaksi->jumlah_bayar = $_POST['jumlah_bayar'];
    $transaksi->kembalian = $_POST['kembalian'];
    $transaksi->status = 'selesai';
    
    // Begin transaction
    $db->begin_transaction();
    
    try {
        // Simpan transaksi
        if($transaksi->create()) {
            // Simpan detail transaksi
            $cart_items = json_decode($_POST['cart_items'], true);
            
            foreach($cart_items as $item) {
                $transaksi->createDetail(
                    $item['id_produk'],
                    $item['harga_jual'],
                    $item['jumlah'],
                    $item['subtotal']
                );
                
                // Kurangi stok
                $produk->updateStok($item['id_produk'], $item['jumlah'], 'kurang');
            }
            
            $db->commit();
            $message = "Transaksi berhasil disimpan! No. Transaksi: " . $transaksi->no_transaksi;
            $message_type = "success";
            
            // Redirect untuk prevent double submit
            echo "<script>
                alert('Transaksi berhasil! No. Transaksi: {$transaksi->no_transaksi}');
                window.location.href = 'transaksi.php';
            </script>";
        }
    } catch(Exception $e) {
        $db->rollback();
        $message = "Gagal menyimpan transaksi: " . $e->getMessage();
        $message_type = "danger";
    }
}

$produk_list = $produk->readAll();
$pelanggan_list = $pelanggan->readAll();
?>

<div class="row">
    <div class="col-12">
        <h2><i class="fas fa-shopping-cart"></i> Transaksi Penjualan</h2>
        <hr>
    </div>
</div>

<?php if($message): ?>
<div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<form method="POST" action="" id="formTransaksi">
    <div class="row">
        <!-- Panel Kiri: Pilih Produk -->
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-box"></i> Pilih Produk</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" id="searchProduk" class="form-control" placeholder="🔍 Cari produk...">
                    </div>
                    
                    <div id="produkList" style="max-height: 400px; overflow-y: auto;">
                        <?php 
                        $produk_list->data_seek(0);
                        while($row = $produk_list->fetch_assoc()): 
                        ?>
                        <div class="produk-item p-3 mb-2 border rounded" 
                             data-id="<?php echo $row['id_produk']; ?>"
                             data-nama="<?php echo $row['nama_produk']; ?>"
                             data-harga="<?php echo $row['harga_jual']; ?>"
                             data-stok="<?php echo $row['stok']; ?>"
                             style="cursor: pointer;">
                            <div class="row align-items-center">
                                <div class="col-7">
                                    <strong><?php echo $row['nama_produk']; ?></strong><br>
                                    <small class="text-muted"><?php echo $row['nama_kategori']; ?></small>
                                </div>
                                <div class="col-3 text-end">
                                    <strong class="text-primary">Rp <?php echo number_format($row['harga_jual'], 0, ',', '.'); ?></strong><br>
                                    <small>Stok: <?php echo $row['stok']; ?></small>
                                </div>
                                <div class="col-2 text-end">
                                    <button type="button" class="btn btn-sm btn-primary btn-add-cart">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Panel Kanan: Keranjang & Pembayaran -->
        <div class="col-lg-5">
            <!-- Pelanggan -->
            <div class="card shadow mb-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-user"></i> Pelanggan</h6>
                </div>
                <div class="card-body">
                    <select name="id_pelanggan" id="id_pelanggan" class="form-select" required>
                        <option value="">Pilih Pelanggan</option>
                        <?php 
                        $pelanggan_list->data_seek(0);
                        while($plg = $pelanggan_list->fetch_assoc()): 
                        ?>
                        <option value="<?php echo $plg['id_pelanggan']; ?>"><?php echo $plg['nama_pelanggan']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            
            <!-- Keranjang Belanja -->
            <div class="card shadow mb-3">
                <div class="card-header bg-warning">
                    <h6 class="mb-0"><i class="fas fa-shopping-basket"></i> Keranjang Belanja</h6>
                </div>
                <div class="card-body">
                    <div id="cartItems" style="max-height: 300px; overflow-y: auto;">
                        <p class="text-muted text-center">Keranjang kosong</p>
                    </div>
                </div>
            </div>
            
            <!-- Total & Pembayaran -->
            <div class="card shadow mb-3">
                <div class="card-body cart-total">
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span>Total Item:</span>
                            <strong><span id="totalItem">0</span> item</strong>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span>Subtotal:</span>
                            <strong>Rp <span id="subtotalText">0</span></strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Diskon (Rp):</label>
                        <input type="number" name="diskon" id="diskon" class="form-control" value="0" min="0">
                    </div>
                    <hr class="bg-white">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <h5>Total Bayar:</h5>
                            <h5>Rp <span id="totalBayarText">0</span></h5>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah Bayar:</label>
                        <input type="number" name="jumlah_bayar" id="jumlahBayar" class="form-control" value="0" min="0" required>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <h5>Kembalian:</h5>
                            <h5 class="text-warning">Rp <span id="kembalianText">0</span></h5>
                        </div>
                    </div>
                    
                    <!-- Hidden inputs -->
                    <input type="hidden" name="total_item" id="total_item">
                    <input type="hidden" name="subtotal" id="subtotal">
                    <input type="hidden" name="total_bayar" id="total_bayar">
                    <input type="hidden" name="kembalian" id="kembalian">
                    <input type="hidden" name="cart_items" id="cart_items">
                    
                    <button type="submit" name="simpan_transaksi" class="btn btn-light w-100 mt-3" id="btnSimpan">
                        <i class="fas fa-save"></i> SIMPAN TRANSAKSI
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
let cart = [];

// Tambah ke keranjang
document.querySelectorAll('.produk-item').forEach(item => {
    item.querySelector('.btn-add-cart').addEventListener('click', function(e) {
        e.stopPropagation();
        
        const id = item.dataset.id;
        const nama = item.dataset.nama;
        const harga = parseFloat(item.dataset.harga);
        const stok = parseInt(item.dataset.stok);
        
        if(stok <= 0) {
            alert('Stok habis!');
            return;
        }
        
        // Cek apakah sudah ada di cart
        const existingItem = cart.find(i => i.id_produk == id);
        
        if(existingItem) {
            if(existingItem.jumlah < stok) {
                existingItem.jumlah++;
                existingItem.subtotal = existingItem.jumlah * existingItem.harga_jual;
            } else {
                alert('Stok tidak cukup!');
                return;
            }
        } else {
            cart.push({
                id_produk: id,
                nama_produk: nama,
                harga_jual: harga,
                jumlah: 1,
                subtotal: harga
            });
        }
        
        updateCart();
    });
});

// Update tampilan keranjang
function updateCart() {
    const cartDiv = document.getElementById('cartItems');
    
    if(cart.length === 0) {
        cartDiv.innerHTML = '<p class="text-muted text-center">Keranjang kosong</p>';
        updateTotal();
        return;
    }
    
    let html = '';
    cart.forEach((item, index) => {
        html += `
        <div class="cart-item">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>${item.nama_produk}</strong>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeFromCart(${index})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div class="input-group input-group-sm" style="width: 120px;">
                    <button type="button" class="btn btn-outline-secondary" onclick="updateQuantity(${index}, -1)">-</button>
                    <input type="text" class="form-control text-center" value="${item.jumlah}" readonly>
                    <button type="button" class="btn btn-outline-secondary" onclick="updateQuantity(${index}, 1)">+</button>
                </div>
                <div class="text-end">
                    <small>@ Rp ${formatRupiah(item.harga_jual)}</small><br>
                    <strong>Rp ${formatRupiah(item.subtotal)}</strong>
                </div>
            </div>
        </div>
        `;
    });
    
    cartDiv.innerHTML = html;
    updateTotal();
}

// Hapus dari keranjang
function removeFromCart(index) {
    cart.splice(index, 1);
    updateCart();
}

// Update quantity
function updateQuantity(index, change) {
    const newQty = cart[index].jumlah + change;
    
    if(newQty <= 0) {
        removeFromCart(index);
        return;
    }
    
    cart[index].jumlah = newQty;
    cart[index].subtotal = cart[index].jumlah * cart[index].harga_jual;
    updateCart();
}

// Update total
function updateTotal() {
    const totalItem = cart.reduce((sum, item) => sum + item.jumlah, 0);
    const subtotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
    const diskon = parseFloat(document.getElementById('diskon').value) || 0;
    const totalBayar = subtotal - diskon;
    const jumlahBayar = parseFloat(document.getElementById('jumlahBayar').value) || 0;
    const kembalian = jumlahBayar - totalBayar;
    
    document.getElementById('totalItem').textContent = totalItem;
    document.getElementById('subtotalText').textContent = formatRupiah(subtotal);
    document.getElementById('totalBayarText').textContent = formatRupiah(totalBayar);
    document.getElementById('kembalianText').textContent = formatRupiah(kembalian);
    
    // Update hidden inputs
    document.getElementById('total_item').value = totalItem;
    document.getElementById('subtotal').value = subtotal;
    document.getElementById('total_bayar').value = totalBayar;
    document.getElementById('kembalian').value = kembalian;
    document.getElementById('cart_items').value = JSON.stringify(cart);
}

// Format Rupiah
function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID').format(angka);
}

// Event listeners
document.getElementById('diskon').addEventListener('input', updateTotal);
document.getElementById('jumlahBayar').addEventListener('input', updateTotal);

// Search produk
document.getElementById('searchProduk').addEventListener('input', function() {
    const search = this.value.toLowerCase();
    document.querySelectorAll('.produk-item').forEach(item => {
        const nama = item.dataset.nama.toLowerCase();
        item.style.display = nama.includes(search) ? 'block' : 'none';
    });
});

// Validasi sebelum submit
document.getElementById('formTransaksi').addEventListener('submit', function(e) {
    if(cart.length === 0) {
        e.preventDefault();
        alert('Keranjang belanja masih kosong!');
        return false;
    }
    
    const pelanggan = document.getElementById('id_pelanggan').value;
    if(!pelanggan) {
        e.preventDefault();
        alert('Pilih pelanggan terlebih dahulu!');
        return false;
    }
    
    const jumlahBayar = parseFloat(document.getElementById('jumlahBayar').value) || 0;
    const totalBayar = parseFloat(document.getElementById('total_bayar').value) || 0;
    
    if(jumlahBayar < totalBayar) {
        e.preventDefault();
        alert('Jumlah bayar kurang!');
        return false;
    }
    
    return confirm('Simpan transaksi ini?');
});
</script>

<?php
$database->closeConnection();
require_once 'includes/footer.php';
?>