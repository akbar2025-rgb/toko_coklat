<?php
$page_title = "Profil Pengguna";
require_once 'includes/header.php';
require_once '../config/Database.php';

$database = new Database();
$db = $database->getConnection();

// Proses update profil
$message = '';
$message_type = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['update_profil'])) {
        $id_user = $_SESSION['id_user'];
        $nama_lengkap = htmlspecialchars(strip_tags($_POST['nama_lengkap']));
        $email = htmlspecialchars(strip_tags($_POST['email']));
        
        $query = "UPDATE users SET nama_lengkap = ?, email = ? WHERE id_user = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssi", $nama_lengkap, $email, $id_user);
        
        if($stmt->execute()) {
            $_SESSION['nama_lengkap'] = $nama_lengkap;
            $message = "Profil berhasil diperbarui!";
            $message_type = "success";
        } else {
            $message = "Gagal memperbarui profil!";
            $message_type = "danger";
        }
    }
    
    if(isset($_POST['update_password'])) {
        $id_user = $_SESSION['id_user'];
        $password_lama = md5($_POST['password_lama']);
        $password_baru = md5($_POST['password_baru']);
        $konfirmasi_password = md5($_POST['konfirmasi_password']);
        
        // Cek password lama
        $query = "SELECT password FROM users WHERE id_user = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if($user['password'] != $password_lama) {
            $message = "Password lama tidak sesuai!";
            $message_type = "danger";
        } elseif($password_baru != $konfirmasi_password) {
            $message = "Konfirmasi password baru tidak cocok!";
            $message_type = "danger";
        } else {
            $query = "UPDATE users SET password = ? WHERE id_user = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("si", $password_baru, $id_user);
            
            if($stmt->execute()) {
                $message = "Password berhasil diubah!";
                $message_type = "success";
            } else {
                $message = "Gagal mengubah password!";
                $message_type = "danger";
            }
        }
    }
}

// Get data user
$query = "SELECT * FROM users WHERE id_user = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $_SESSION['id_user']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<div class="row">
    <div class="col-12">
        <h2><i class="fas fa-user-circle"></i> Profil Pengguna</h2>
        <hr>
    </div>
</div>

<?php if($message): ?>
<div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row">
    <!-- Card Informasi Profil -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-body text-center">
                <div class="mb-3">
                    <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center" 
                         style="width: 120px; height: 120px;">
                        <i class="fas fa-user fa-4x text-white"></i>
                    </div>
                </div>
                <h4 class="mb-1"><?php echo $user['nama_lengkap']; ?></h4>
                <p class="text-muted mb-2">@<?php echo $user['username']; ?></p>
                <span class="badge bg-primary mb-3"><?php echo ucfirst($user['level']); ?></span>
                
                <hr>
                
                <div class="text-start">
                    <p class="mb-2">
                        <i class="fas fa-envelope text-primary me-2"></i>
                        <small><?php echo $user['email'] ? $user['email'] : 'Belum diisi'; ?></small>
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-shield-alt text-primary me-2"></i>
                        <small>Level: <?php echo ucfirst($user['level']); ?></small>
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-calendar text-primary me-2"></i>
                        <small>Bergabung: <?php echo date('d M Y', strtotime($user['created_at'])); ?></small>
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-clock text-primary me-2"></i>
                        <small>Update: <?php echo date('d M Y', strtotime($user['updated_at'])); ?></small>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Card Status -->
        <div class="card shadow mt-3">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="fas fa-check-circle"></i> Status Akun</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Status:</span>
                    <?php if($user['status'] == 'aktif'): ?>
                    <span class="badge bg-success">Aktif</span>
                    <?php else: ?>
                    <span class="badge bg-danger">Non-aktif</span>
                    <?php endif; ?>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Keamanan:</span>
                    <span class="badge bg-primary">Terenkripsi</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Form Update Profil & Password -->
    <div class="col-lg-8">
        <!-- Update Profil -->
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Profil</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" value="<?php echo $user['username']; ?>" readonly>
                                <small class="text-muted">Username tidak dapat diubah</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Level Akses</label>
                                <input type="text" class="form-control" value="<?php echo ucfirst($user['level']); ?>" readonly>
                                <small class="text-muted">Level ditentukan oleh administrator</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" name="nama_lengkap" class="form-control" 
                               value="<?php echo $user['nama_lengkap']; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="<?php echo $user['email']; ?>" placeholder="email@example.com">
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" name="update_profil" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Update Password -->
        <div class="card shadow mb-4">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="fas fa-key"></i> Ubah Password</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="" id="formPassword">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Penting:</strong> Gunakan password yang kuat dengan kombinasi huruf besar, huruf kecil, angka, dan simbol.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password Lama *</label>
                        <div class="input-group">
                            <input type="password" name="password_lama" id="password_lama" class="form-control" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_lama')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password Baru *</label>
                        <div class="input-group">
                            <input type="password" name="password_baru" id="password_baru" class="form-control" 
                                   minlength="6" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_baru')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted">Minimal 6 karakter</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password Baru *</label>
                        <div class="input-group">
                            <input type="password" name="konfirmasi_password" id="konfirmasi_password" 
                                   class="form-control" minlength="6" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('konfirmasi_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div id="passwordStrength" class="mb-3"></div>
                    
                    <div class="text-end">
                        <button type="submit" name="update_password" class="btn btn-warning">
                            <i class="fas fa-lock"></i> Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Aktivitas Terakhir -->
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-history"></i> Aktivitas Terakhir</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item mb-3">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-sign-in-alt text-success"></i>
                            </div>
                            <div>
                                <strong>Login Terakhir</strong><br>
                                <small class="text-muted">Hari ini, <?php echo date('H:i'); ?></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="timeline-item mb-3">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-user-edit text-primary"></i>
                            </div>
                            <div>
                                <strong>Profil Diperbarui</strong><br>
                                <small class="text-muted"><?php echo date('d M Y H:i', strtotime($user['updated_at'])); ?></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-user-plus text-info"></i>
                            </div>
                            <div>
                                <strong>Akun Dibuat</strong><br>
                                <small class="text-muted"><?php echo date('d M Y H:i', strtotime($user['created_at'])); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle show/hide password
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.parentElement.querySelector('button i');
    
    if(field.type === 'password') {
        field.type = 'text';
        button.classList.remove('fa-eye');
        button.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        button.classList.remove('fa-eye-slash');
        button.classList.add('fa-eye');
    }
}

// Password strength checker
document.getElementById('password_baru').addEventListener('input', function() {
    const password = this.value;
    const strengthDiv = document.getElementById('passwordStrength');
    
    if(password.length === 0) {
        strengthDiv.innerHTML = '';
        return;
    }
    
    let strength = 0;
    let message = '';
    let colorClass = '';
    
    // Check length
    if(password.length >= 8) strength++;
    if(password.length >= 12) strength++;
    
    // Check for lowercase and uppercase
    if(/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    
    // Check for numbers
    if(/\d/.test(password)) strength++;
    
    // Check for special characters
    if(/[^A-Za-z0-9]/.test(password)) strength++;
    
    // Determine strength level
    if(strength <= 2) {
        message = 'Lemah';
        colorClass = 'danger';
    } else if(strength <= 3) {
        message = 'Sedang';
        colorClass = 'warning';
    } else if(strength <= 4) {
        message = 'Kuat';
        colorClass = 'info';
    } else {
        message = 'Sangat Kuat';
        colorClass = 'success';
    }
    
    const percentage = (strength / 5) * 100;
    
    strengthDiv.innerHTML = `
        <div class="mb-1">
            <small>Kekuatan Password: <strong class="text-${colorClass}">${message}</strong></small>
        </div>
        <div class="progress" style="height: 5px;">
            <div class="progress-bar bg-${colorClass}" role="progressbar" style="width: ${percentage}%"></div>
        </div>
    `;
});

// Validate password match
document.getElementById('formPassword').addEventListener('submit', function(e) {
    const passwordBaru = document.getElementById('password_baru').value;
    const konfirmasi = document.getElementById('konfirmasi_password').value;
    
    if(passwordBaru !== konfirmasi) {
        e.preventDefault();
        alert('Password baru dan konfirmasi password tidak cocok!');
        return false;
    }
    
    return confirm('Apakah Anda yakin ingin mengubah password?');
});
</script>

<style>
.timeline {
    position: relative;
}

.timeline-item {
    border-left: 2px solid var(--cream);
    padding-left: 20px;
}

.timeline-item:last-child {
    border-left: none;
}

.rounded-circle {
    box-shadow: 0 4px 15px rgba(139, 69, 19, 0.2);
}
</style>

<?php
$database->closeConnection();
require_once 'includes/footer.php';
?>