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

if (isset($_POST['register'])) {
    $nama_lengkap     = trim($_POST['nama_lengkap']);
    $username         = trim($_POST['username']);
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi input
    if (empty($nama_lengkap) || empty($username) || empty($password) || empty($confirm_password)) {
        $error_message = 'Semua bidang formulir wajib diisi!';
    } elseif (strlen($username) < 3) {
        $error_message = 'Username minimal harus 3 karakter.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error_message = 'Username hanya boleh berupa huruf, angka, dan garis bawah (_).';
    } elseif (strlen($password) < 6) {
        $error_message = 'Password minimal harus 6 karakter.';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Konfirmasi password tidak cocok!';
    } else {
        // Cek apakah username sudah digunakan
        $stmt = mysqli_prepare($mysqli, "SELECT id FROM users WHERE username = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) > 0) {
                $error_message = 'Username "<strong>' . htmlspecialchars($username) . '</strong>" sudah terdaftar. Silakan pilih username lain.';
                mysqli_stmt_close($stmt);
            } else {
                mysqli_stmt_close($stmt);

                // Hash password menggunakan BCRYPT standar
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Simpan user baru ke database
                $insert_stmt = mysqli_prepare(
                    $mysqli,
                    "INSERT INTO users (username, password, nama_lengkap) VALUES (?, ?, ?)"
                );

                if ($insert_stmt) {
                    mysqli_stmt_bind_param($insert_stmt, "sss", $username, $hashed_password, $nama_lengkap);

                    if (mysqli_stmt_execute($insert_stmt)) {
                        mysqli_stmt_close($insert_stmt);
                        // Redirect ke login dengan notifikasi sukses
                        header("Location: login.php?status=registered");
                        exit;
                    } else {
                        $error_message = 'Gagal menyimpan data: ' . htmlspecialchars(mysqli_error($mysqli));
                    }
                    mysqli_stmt_close($insert_stmt);
                } else {
                    $error_message = 'Terjadi kesalahan sistem dalam penyiapan pendaftaran.';
                }
            }
        } else {
            $error_message = 'Terjadi kesalahan sistem dalam pengecekan username.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - SIM RS Elektromedis</title>
    <!-- Google Font & Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Elegant Theme CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="auth-wrapper">
        <div class="auth-card">
            
            <div class="auth-header">
                <div class="brand-icon">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <h2>Daftar Akun Baru</h2>
                <p>Registrasi akun petugas SIM RS Elektromedis</p>
            </div>

            <div class="auth-body">

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <div><?php echo $error_message; ?></div>
                    </div>
                <?php endif; ?>

                <form action="register.php" method="post" autocomplete="off">
                    
                    <div class="form-group">
                        <label class="form-label" for="nama_lengkap">
                            Nama Lengkap <span class="required">*</span>
                        </label>
                        <div class="input-icon-group">
                            <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control" 
                                   placeholder="Contoh: Budi Santoso, S.Tr.EM" required autofocus
                                   value="<?php echo isset($_POST['nama_lengkap']) ? htmlspecialchars($_POST['nama_lengkap']) : ''; ?>">
                            <i class="fa-solid fa-id-card input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="username">
                            Username <span class="required">*</span>
                        </label>
                        <div class="input-icon-group">
                            <input type="text" id="username" name="username" class="form-control" 
                                   placeholder="Contoh: budi_teknisi" required
                                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                            <i class="fa-solid fa-user input-icon"></i>
                        </div>
                        <div class="form-helper">Hanya huruf, angka, dan garis bawah (_). Min. 3 karakter.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">
                            Password <span class="required">*</span>
                        </label>
                        <div class="input-icon-group">
                            <input type="password" id="password" name="password" class="form-control" 
                                   placeholder="Minimal 6 karakter" required>
                            <i class="fa-solid fa-lock input-icon"></i>
                            <button type="button" class="toggle-password" onclick="togglePassword('password', this)" title="Lihat/Sembunyikan Password">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="confirm_password">
                            Konfirmasi Password <span class="required">*</span>
                        </label>
                        <div class="input-icon-group">
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                                   placeholder="Ulangi password di atas" required>
                            <i class="fa-solid fa-shield-halved input-icon"></i>
                            <button type="button" class="toggle-password" onclick="togglePassword('confirm_password', this)" title="Lihat/Sembunyikan Password">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div style="margin-top: 26px;">
                        <button type="submit" name="register" class="btn btn-primary btn-block">
                            <i class="fa-solid fa-check"></i> Daftarkan Akun
                        </button>
                    </div>

                </form>

                <div class="auth-footer">
                    Sudah memiliki akun? <a href="login.php">Masuk di sini</a>
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
