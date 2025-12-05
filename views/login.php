<?php
session_start();

// Jika sudah login, redirect ke dashboard
if(isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header("Location: dashboard.php");
    exit;
}

// Include database
require_once '../config/Database.php';

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM users WHERE username = ? AND password = ? AND status = 'aktif' LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Set session
        $_SESSION['login'] = true;
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['level'] = $user['level'];
        
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
    
    $database->closeConnection();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Toko Coklat</title>
    
    <!-- Bootstrap CSS Offline -->
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Offline -->
    <link href="../assets/fontawesome/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #8B4513 0%, #D2691E 50%, #A0522D 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            max-width: 400px;
            width: 100%;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .card-header {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }
        .btn-login {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            border: none;
            color: white;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #A0522D 0%, #8B4513 100%);
            color: white;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="card">
            <div class="card-header text-center">
                <i class="fas fa-candy-cane fa-3x mb-3"></i>
                <h3 class="mb-0">Toko Coklat</h3>
                <p class="mb-0">Sistem Informasi Penjualan</p>
            </div>
            <div class="card-body p-4">
                <?php if($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-user"></i> Username
                        </label>
                        <input type="text" name="username" class="form-control" required autofocus>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-login btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                    </div>
                </form>
                
                <hr class="my-4">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Offline -->
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>