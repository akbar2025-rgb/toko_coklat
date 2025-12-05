<?php
$page_title = "Pelanggan";
require_once 'includes/header.php';
require_once '../config/Database.php';
require_once '../models/Pelanggan.php';

$database = new Database();
$db = $database->getConnection();
$pelanggan = new Pelanggan($db);

// Proses CRUD
$message = '';
$message_type = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['action'])) {
        switch($_POST['action']) {
            case 'add':
                $pelanggan->kode_pelanggan = $pelanggan->generateKode();
                $pelanggan->nama_pelanggan = $_POST['nama_pelanggan'];
                $pelanggan->alamat = $_POST['alamat'];
                $pelanggan->telepon = $_POST['telepon'];
                $pelanggan->email = $_POST['email'];
                
                if($pelanggan->create()) {
                    $message = "Pelanggan berhasil ditambahkan!";
                    $message_type = "success";
                } else {
                    $message = "Gagal menambahkan pelanggan!";
                    $message_type = "danger";
                }
                break;
                
            case 'edit':
                $pelanggan->id_pelanggan = $_POST['id_pelanggan'];
                $pelanggan->kode_pelanggan = $_POST['kode_pelanggan'];
                $pelanggan->nama_pelanggan = $_POST['nama_pelanggan'];
                $pelanggan->alamat = $_POST['alamat'];
                $pelanggan->telepon = $_POST['telepon'];
                $pelanggan->email = $_POST['email'];
                
                if($pelanggan->update()) {
                    $message = "Pelanggan berhasil diupdate!";
                    $message_type = "success";
                } else {
                    $message = "Gagal mengupdate pelanggan!";
                    $message_type = "danger";
                }
                break;
                
            case 'delete':
                $pelanggan->id_pelanggan = $_POST['id_pelanggan'];
                
                if($pelanggan->delete()) {
                    $message = "Pelanggan berhasil dihapus!";
                    $message_type = "success";
                } else {
                    $message = "Gagal menghapus pelanggan!";
                    $message_type = "danger";
                }
                break;
        }
    }
}

$result = $pelanggan->readAll();
?>

<div class="row">
    <div class="col-12">
        <h2><i class="fas fa-users"></i> Data Pelanggan</h2>
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
            <i class="fas fa-plus"></i> Tambah Pelanggan
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">No</th>
                        <th>Kode</th>
                        <th>Nama Pelanggan</th>
                        <th>Alamat</th>
                        <th>Telepon</th>
                        <th>Email</th>
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
                        <td><?php echo $row['kode_pelanggan']; ?></td>
                        <td><?php echo $row['nama_pelanggan']; ?></td>
                        <td><?php echo $row['alamat']; ?></td>
                        <td><?php echo $row['telepon']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editPelanggan(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deletePelanggan(<?php echo $row['id_pelanggan']; ?>, '<?php echo $row['nama_pelanggan']; ?>')">
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
                <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Pelanggan *</label>
                        <input type="text" name="nama_pelanggan" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Telepon</label>
                                <input type="text" name="telepon" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control">
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
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id_pelanggan" id="edit_id_pelanggan">
                    
                    <div class="mb-3">
                        <label class="form-label">Kode Pelanggan</label>
                        <input type="text" name="kode_pelanggan" id="edit_kode_pelanggan" class="form-control" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Pelanggan *</label>
                        <input type="text" name="nama_pelanggan" id="edit_nama_pelanggan" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" id="edit_alamat" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Telepon</label>
                                <input type="text" name="telepon" id="edit_telepon" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" id="edit_email" class="form-control">
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
    <input type="hidden" name="id_pelanggan" id="delete_id_pelanggan">
</form>

<script>
function editPelanggan(data) {
    document.getElementById('edit_id_pelanggan').value = data.id_pelanggan;
    document.getElementById('edit_kode_pelanggan').value = data.kode_pelanggan;
    document.getElementById('edit_nama_pelanggan').value = data.nama_pelanggan;
    document.getElementById('edit_alamat').value = data.alamat;
    document.getElementById('edit_telepon').value = data.telepon;
    document.getElementById('edit_email').value = data.email;
    
    var modal = new bootstrap.Modal(document.getElementById('modalEdit'));
    modal.show();
}

function deletePelanggan(id, nama) {
    if(confirm('Apakah Anda yakin ingin menghapus pelanggan "' + nama + '"?')) {
        document.getElementById('delete_id_pelanggan').value = id;
        document.getElementById('formDelete').submit();
    }
}
</script>

<?php
$database->closeConnection();
require_once 'includes/footer.php';
?>