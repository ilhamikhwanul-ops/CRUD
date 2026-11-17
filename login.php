<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika sudah login, langsung alihkan ke index.php
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

include_once("config.php");

$error_message = '';
$success_message = '';

// Pesan dari query parameter
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'registered') {
        $success_message = 'Pendaftaran akun berhasil! Silakan login menggunakan akun baru Anda.';
    } elseif ($_GET['status'] === 'logout') {
        $success_message = 'Anda telah berhasil keluar (logout) dari sistem.';
    }
}

// Proses submit form login
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error_message = 'Silakan isi username dan password Anda.';
    } else {
        $stmt = mysqli_prepare($mysqli, "SELECT id, username, password, nama_lengkap FROM users WHERE username = ?");
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if ($user && password_verify($password, $user['password'])) {
                // Login sukses
                session_regenerate_id(true);
                $_SESSION['user_id']      = $user['id'];
                $_SESSION['username']     = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];

                header("Location: index.php");
                exit;
            } else {
                $error_message = 'Username atau password yang Anda masukkan tidak sesuai.';
            }
        } else {
            $error_message = 'Terjadi kesalahan sistem saat memproses login.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Petugas - SIM RS Elektromedis</title>
    <!-- Google Font & Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Elegant Theme CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="auth-wrapper">
        <div class="auth-split-card">
            
            <!-- Left Hero Showcase Side -->
            <div class="auth-hero-side">
                <div class="auth-hero-brand">
                    <img src="assets/images/logo-simrs.svg" alt="Logo SIM RS" style="width: 44px; height: 44px; filter: drop-shadow(0 4px 10px rgba(0,0,0,0.3));">
                    <h3>SIM RS Elektromedis</h3>
                </div>

                <div class="auth-hero-main">
                    <h2>Manajemen Alat Medis Terintegrasi & Presisi</h2>
                    <p>
                        Solusi digital pengelolaan inventaris, kontrol lokasi ruangan, dan pemantauan status kelaikan perangkat elektromedis rumah sakit.
                    </p>

                    <!-- Smart Medical Hub Illustration -->
                    <img src="assets/images/auth-medical.svg" alt="Smart Hospital Biomedical Station" class="auth-hero-illustration">

                    <ul class="auth-feature-list">
                        <li>
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Inventarisasi alat kesehatan real-time</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Pemantauan ruangan penempatan perangkat</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Autentikasi terenkripsi standar keamanan</span>
                        </li>
                    </ul>
                </div>

                <div class="auth-hero-footer">
                    &copy; <?php echo date('Y'); ?> SIM RS Elektromedis. All rights reserved.
                </div>
            </div>

            <!-- Right Form Side -->
            <div class="auth-form-side">
                
                <div class="auth-form-header">
                    <h3>Selamat Datang Kembali 👋</h3>
                    <p>Silakan masukkan kredensial akun petugas Anda untuk mengakses sistem inventaris.</p>
                </div>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <div><?php echo htmlspecialchars($error_message); ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success">
                        <i class="fa-solid fa-circle-check"></i>
                        <div><?php echo htmlspecialchars($success_message); ?></div>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="post" autocomplete="off">
                    
                    <div class="form-group">
                        <label class="form-label" for="username">
                            Username <span class="required">*</span>
                        </label>
                        <div class="input-icon-group">
                            <input type="text" id="username" name="username" class="form-control" 
                                   placeholder="Masukkan username petugas" required autofocus 
                                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                            <i class="fa-solid fa-user input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">
                            Password <span class="required">*</span>
                        </label>
                        <div class="input-icon-group">
                            <input type="password" id="password" name="password" class="form-control" 
                                   placeholder="Masukkan kata sandi akun" required>
                            <i class="fa-solid fa-lock input-icon"></i>
                            <button type="button" class="toggle-password" onclick="togglePassword('password', this)" title="Lihat/Sembunyikan Password">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div style="margin-top: 26px;">
                        <button type="submit" name="login" class="btn btn-primary btn-block">
                            <i class="fa-solid fa-right-to-bracket"></i> Masuk ke Sistem
                        </button>
                    </div>

                </form>

                <div class="auth-footer">
                    Belum memiliki akun petugas? <a href="register.php">Daftar Akun Baru</a>
                </div>

            </div>

        </div>
    </div>

    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
