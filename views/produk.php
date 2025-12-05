<?php
$page_title = "Produk";
require_once 'includes/header.php';
require_once '../config/Database.php';
require_once '../models/Produk.php';
require_once '../models/Kategori.php';

$database = new Database();
$db = $database->getConnection();
$produk = new Produk($db);
$kategori = new Kategori($db);

// Proses CRUD
$message = '';
$message_type = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['action'])) {
        switch($_POST['action']) {
            case 'add':
                $produk->id_kategori = $_POST['id_kategori'];
                $produk->kode_produk = $produk->generateKode();
                $produk->nama_produk = $_POST['nama_produk'];
                $produk->deskripsi = $_POST['deskripsi'];
                $produk->harga_beli = $_POST['harga_beli'];
                $produk->harga_jual = $_POST['harga_jual'];
                $produk->stok = $_POST['stok'];
                $produk->satuan = $_POST['satuan'];
                $produk->gambar = '-';
                $produk->status = $_POST['status'];
                
                if($produk->create()) {
                    $message = "Produk berhasil ditambahkan!";
                    $message_type = "success";
                } else {
                    $message = "Gagal menambahkan produk!";
                    $message_type = "danger";
                }
                break;
                
            case 'edit':
                $produk->id_produk = $_POST['id_produk'];
                $produk->id_kategori = $_POST['id_kategori'];
                $produk->kode_produk = $_POST['kode_produk'];
                $produk->nama_produk = $_POST['nama_produk'];
                $produk->deskripsi = $_POST['deskripsi'];
                $produk->harga_beli = $_POST['harga_beli'];
                $produk->harga_jual = $_POST['harga_jual'];
                $produk->stok = $_POST['stok'];
                $produk->satuan = $_POST['satuan'];
                $produk->gambar = '-';
                $produk->status = $_POST['status'];
                
                if($produk->update()) {
                    $message = "Produk berhasil diupdate!";
                    $message_type = "success";
                } else {
                    $message = "Gagal mengupdate produk!";
                    $message_type = "danger";
                }
                break;
                
            case 'delete':
                $produk->id_produk = $_POST['id_produk'];
                
                if($produk->delete()) {
                    $message = "Produk berhasil dihapus!";
                    $message_type = "success";
                } else {
                    $message = "Gagal menghapus produk!";
                    $message_type = "danger";
                }
                break;
        }
    }
}

$result = $produk->readAll();
$kategori_list = $kategori->readAll();
?>

<div class="row">
    <div class="col-12">
        <h2><i class="fas fa-box"></i> Data Produk</h2>
        <hr>
    </div>
</div>

<?php if($message): ?>
<div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fas fa-plus"></i> Tambah Produk
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">No</th>
                        <th>Kode</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while($row = $result->fetch_assoc()): 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['kode_produk']; ?></td>
                        <td><?php echo $row['nama_produk']; ?></td>
                        <td><?php echo $row['nama_kategori']; ?></td>
                        <td>Rp <?php echo number_format($row['harga_beli'], 0, ',', '.'); ?></td>
                        <td>Rp <?php echo number_format($row['harga_jual'], 0, ',', '.'); ?></td>
                        <td><?php echo $row['stok']; ?> <?php echo $row['satuan']; ?></td>
                        <td>
                            <?php if($row['status'] == 'tersedia'): ?>
                            <span class="badge bg-success">Tersedia</span>
                            <?php else: ?>
                            <span class="badge bg-danger">Habis</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editProduk(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteProduk(<?php echo $row['id_produk']; ?>, '<?php echo $row['nama_produk']; ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kategori *</label>
                                <select name="id_kategori" class="form-select" required>
                                    <option value="">Pilih Kategori</option>
                                    <?php 
                                    $kategori_list->data_seek(0);
                                    while($kat = $kategori_list->fetch_assoc()): 
                                    ?>
                                    <option value="<?php echo $kat['id_kategori']; ?>"><?php echo $kat['nama_kategori']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Produk *</label>
                                <input type="text" name="nama_produk" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Harga Beli *</label>
                                <input type="number" name="harga_beli" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Harga Jual *</label>
                                <input type="number" name="harga_jual" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Stok *</label>
                                <input type="number" name="stok" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Satuan *</label>
                                <select name="satuan" class="form-select" required>
                                    <option value="pcs">Pcs</option>
                                    <option value="box">Box</option>
                                    <option value="kg">Kg</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Status *</label>
                                <select name="status" class="form-select" required>
                                    <option value="tersedia">Tersedia</option>
                                    <option value="habis">Habis</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id_produk" id="edit_id_produk">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kode Produk</label>
                                <input type="text" name="kode_produk" id="edit_kode_produk" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kategori *</label>
                                <select name="id_kategori" id="edit_id_kategori" class="form-select" required>
                                    <?php 
                                    $kategori_list->data_seek(0);
                                    while($kat = $kategori_list->fetch_assoc()): 
                                    ?>
                                    <option value="<?php echo $kat['id_kategori']; ?>"><?php echo $kat['nama_kategori']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Produk *</label>
                        <input type="text" name="nama_produk" id="edit_nama_produk" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Harga Beli *</label>
                                <input type="number" name="harga_beli" id="edit_harga_beli" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Harga Jual *</label>
                                <input type="number" name="harga_jual" id="edit_harga_jual" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Stok *</label>
                                <input type="number" name="stok" id="edit_stok" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Satuan *</label>
                                <select name="satuan" id="edit_satuan" class="form-select" required>
                                    <option value="pcs">Pcs</option>
                                    <option value="box">Box</option>
                                    <option value="kg">Kg</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Status *</label>
                                <select name="status" id="edit_status" class="form-select" required>
                                    <option value="tersedia">Tersedia</option>
                                    <option value="habis">Habis</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form Delete (hidden) -->
<form method="POST" action="" id="formDelete">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id_produk" id="delete_id_produk">
</form>

<script>
function editProduk(data) {
    document.getElementById('edit_id_produk').value = data.id_produk;
    document.getElementById('edit_kode_produk').value = data.kode_produk;
    document.getElementById('edit_id_kategori').value = data.id_kategori;
    document.getElementById('edit_nama_produk').value = data.nama_produk;
    document.getElementById('edit_deskripsi').value = data.deskripsi;
    document.getElementById('edit_harga_beli').value = data.harga_beli;
    document.getElementById('edit_harga_jual').value = data.harga_jual;
    document.getElementById('edit_stok').value = data.stok;
    document.getElementById('edit_satuan').value = data.satuan;
    document.getElementById('edit_status').value = data.status;
    
    var modal = new bootstrap.Modal(document.getElementById('modalEdit'));
    modal.show();
}

function deleteProduk(id, nama) {
    if(confirm('Apakah Anda yakin ingin menghapus produk "' + nama + '"?')) {
        document.getElementById('delete_id_produk').value = id;
        document.getElementById('formDelete').submit();
    }
}
</script>

<?php
$database->closeConnection();
require_once 'includes/footer.php';
?>