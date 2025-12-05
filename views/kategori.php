<?php
$page_title = "Kategori";
require_once 'includes/header.php';
require_once '../config/Database.php';
require_once '../models/Kategori.php';

$database = new Database();
$db = $database->getConnection();
$kategori = new Kategori($db);

// Proses tambah/edit/hapus
$message = '';
$message_type = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['action'])) {
        switch($_POST['action']) {
            case 'add':
                $kategori->nama_kategori = $_POST['nama_kategori'];
                $kategori->deskripsi = $_POST['deskripsi'];
                
                if($kategori->create()) {
                    $message = "Kategori berhasil ditambahkan!";
                    $message_type = "success";
                } else {
                    $message = "Gagal menambahkan kategori!";
                    $message_type = "danger";
                }
                break;
                
            case 'edit':
                $kategori->id_kategori = $_POST['id_kategori'];
                $kategori->nama_kategori = $_POST['nama_kategori'];
                $kategori->deskripsi = $_POST['deskripsi'];
                
                if($kategori->update()) {
                    $message = "Kategori berhasil diupdate!";
                    $message_type = "success";
                } else {
                    $message = "Gagal mengupdate kategori!";
                    $message_type = "danger";
                }
                break;
                
            case 'delete':
                $kategori->id_kategori = $_POST['id_kategori'];
                
                if($kategori->delete()) {
                    $message = "Kategori berhasil dihapus!";
                    $message_type = "success";
                } else {
                    $message = "Gagal menghapus kategori!";
                    $message_type = "danger";
                }
                break;
        }
    }
}

$result = $kategori->readAll();
?>

<div class="row">
    <div class="col-12">
        <h2><i class="fas fa-list"></i> Data Kategori</h2>
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
            <i class="fas fa-plus"></i> Tambah Kategori
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
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
                        <td><?php echo $row['nama_kategori']; ?></td>
                        <td><?php echo $row['deskripsi']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editKategori(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteKategori(<?php echo $row['id_kategori']; ?>, '<?php echo $row['nama_kategori']; ?>')">
                                <i class="fas fa-trash"></i> Hapus
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori *</label>
                        <input type="text" name="nama_kategori" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id_kategori" id="edit_id_kategori">
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori *</label>
                        <input type="text" name="nama_kategori" id="edit_nama_kategori" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="3"></textarea>
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
    <input type="hidden" name="id_kategori" id="delete_id_kategori">
</form>

<script>
function editKategori(data) {
    document.getElementById('edit_id_kategori').value = data.id_kategori;
    document.getElementById('edit_nama_kategori').value = data.nama_kategori;
    document.getElementById('edit_deskripsi').value = data.deskripsi;
    
    var modal = new bootstrap.Modal(document.getElementById('modalEdit'));
    modal.show();
}

function deleteKategori(id, nama) {
    if(confirm('Apakah Anda yakin ingin menghapus kategori "' + nama + '"?')) {
        document.getElementById('delete_id_kategori').value = id;
        document.getElementById('formDelete').submit();
    }
}
</script>

<?php
$database->closeConnection();
require_once 'includes/footer.php';
?>