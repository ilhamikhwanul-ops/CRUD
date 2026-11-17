<?php
// Proteksi autentikasi
include_once("auth.php");

$message = '';
$message_type = '';

if (isset($_POST['Submit'])) {
    $nama_alat = trim($_POST['nama_alat']);
    $tahun     = trim($_POST['tahun']);
    $merek     = trim($_POST['merek']);
    $lokasi    = trim($_POST['lokasi']);

    // Koneksi database
    include_once("config.php");

    // Memasukkan data ke tabel alat dengan prepared statement
    $stmt = mysqli_prepare(
        $mysqli,
        "INSERT INTO alat (nama_alat, tahun, merek, lokasi) VALUES (?, ?, ?, ?)"
    );

    if ($stmt) {
        mysqli_stmt_bind_param(
            $stmt,
            "ssss",
            $nama_alat,
            $tahun,
            $merek,
            $lokasi
        );

        if (mysqli_stmt_execute($stmt)) {
            $message_type = 'success';
            $message = 'Data alat <strong>' . htmlspecialchars($nama_alat) . '</strong> berhasil ditambahkan! <a href="index.php">Lihat Semua Alat &rarr;</a>';
        } else {
            $message_type = 'danger';
            $message = 'Gagal menambahkan data: ' . htmlspecialchars(mysqli_error($mysqli));
        }

        mysqli_stmt_close($stmt);
    } else {
        $message_type = 'danger';
        $message = 'Terjadi kesalahan sistem dalam penyiapan query: ' . htmlspecialchars(mysqli_error($mysqli));
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Alat - SIM RS Elektromedis</title>
    <!-- Google Font & Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Elegant Theme CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Header / Navbar -->
    <header class="navbar">
        <div class="navbar-container">
            <a href="index.php" class="brand">
                <div class="brand-icon">
                    <i class="fa-solid fa-heart-pulse"></i>
                </div>
                <div class="brand-text">
                    <h1>SIM RS Elektromedis</h1>
                    <span>Sistem Inventaris Alat Kesehatan</span>
                </div>
            </a>
            <div class="navbar-user-group">
                <span class="user-badge" title="Pengguna yang Sedang Login">
                    <i class="fa-solid fa-circle-user"></i>
                    <?php echo htmlspecialchars(!empty($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : $_SESSION['username']); ?>
                </span>
                <a href="index.php" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left"></i> Home
                </a>
                <a href="logout.php" class="btn btn-action-delete btn-sm" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')" title="Logout">
                    <i class="fa-solid fa-right-from-bracket"></i> Keluar
                </a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="container">
        
        <a href="index.php" class="nav-back-link">
            <i class="fa-solid fa-chevron-left"></i> Kembali ke Daftar Alat
        </a>

        <div class="form-card">
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php if ($message_type === 'success'): ?>
                        <i class="fa-solid fa-circle-check" style="font-size: 1.25rem;"></i>
                    <?php else: ?>
                        <i class="fa-solid fa-circle-exclamation" style="font-size: 1.25rem;"></i>
                    <?php endif; ?>
                    <div><?php echo $message; ?></div>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 style="font-size: 1.25rem; font-weight: 700; color: #0f172a;">
                            <i class="fa-solid fa-square-plus" style="color: #2563eb; margin-right: 8px;"></i>
                            Tambah Data Alat
                        </h2>
                        <p style="font-size: 0.85rem; color: #64748b; margin-top: 2px;">
                            Lengkapi formulir di bawah ini untuk menambahkan data alat baru ke inventaris.
                        </p>
                    </div>
                </div>

                <div class="card-body">
                    <form action="add.php" method="post" name="form1">
                        
                        <div class="form-group">
                            <label class="form-label" for="nama_alat">
                                Nama Alat <span class="required">*</span>
                            </label>
                            <input type="text" id="nama_alat" name="nama_alat" class="form-control" 
                                   placeholder="Contoh: USG 4D Doppler, Defibrillator, EKG Monitor" required autofocus>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="tahun">
                                Tahun Pengadaan <span class="required">*</span>
                            </label>
                            <input type="number" id="tahun" name="tahun" class="form-control" 
                                   min="1901" max="2155" placeholder="Contoh: 2024" required>
                            <div class="form-helper">Rentang tahun pengadaan antara 1901 hingga 2155.</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="merek">
                                Merek / Pabrikan <span class="required">*</span>
                            </label>
                            <input type="text" id="merek" name="merek" class="form-control" 
                                   placeholder="Contoh: GE Healthcare, Philips, Mindray, Siemens" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="lokasi">
                                Lokasi Penempatan <span class="required">*</span>
                            </label>
                            <input type="text" id="lokasi" name="lokasi" class="form-control" 
                                   placeholder="Contoh: Ruang ICU Lantai 2, Poli Jantung, IGD" required>
                        </div>

                        <div class="form-actions">
                            <a href="index.php" class="btn btn-secondary">
                                Batal
                            </a>
                            <button type="submit" name="Submit" class="btn btn-primary">
                                <i class="fa-solid fa-floppy-disk"></i> Simpan Data Alat
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>

    </main>

</body>
</html>
