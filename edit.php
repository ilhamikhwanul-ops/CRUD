<?php
// Proteksi autentikasi
include_once("auth.php");

// Menghubungkan ke database
include_once("config.php");

// Mengecek apakah tombol Update ditekan
if (isset($_POST['update'])) {

    $id        = (int) $_POST['id'];
    $nama_alat = trim($_POST['nama_alat']);
    $tahun     = trim($_POST['tahun']);
    $merek     = trim($_POST['merek']);
    $lokasi    = trim($_POST['lokasi']);

    // Mengupdate data alat
    $stmt = mysqli_prepare(
        $mysqli,
        "UPDATE alat SET
        nama_alat = ?,
        tahun = ?,
        merek = ?,
        lokasi = ?
        WHERE id = ?"
    );

    if ($stmt) {
        mysqli_stmt_bind_param(
            $stmt,
            "ssssi",
            $nama_alat,
            $tahun,
            $merek,
            $lokasi,
            $id
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // Kembali ke halaman utama
    header("Location: index.php");
    exit;
}

// Mengambil ID dari URL
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Mengambil data alat berdasarkan ID
$stmt = mysqli_prepare(
    $mysqli,
    "SELECT * FROM alat WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user_data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Alat - SIM RS Elektromedis</title>
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

            <?php if (!$user_data): ?>
                <div class="card" style="text-align: center; padding: 48px 24px;">
                    <div style="font-size: 3rem; color: #ef4444; margin-bottom: 16px;">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <h3 style="font-size: 1.3rem; color: #0f172a; margin-bottom: 8px;">Data Alat Tidak Ditemukan</h3>
                    <p style="color: #64748b; margin-bottom: 24px;">Data alat elektromedis dengan ID yang dicari tidak ada dalam basis data atau telah dihapus.</p>
                    <a href="index.php" class="btn btn-primary">
                        <i class="fa-solid fa-house"></i> Kembali ke Beranda
                    </a>
                </div>
            <?php else: ?>

                <div class="card">
                    <div class="card-header">
                        <div>
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #0f172a;">
                                <i class="fa-solid fa-pen-to-square" style="color: #2563eb; margin-right: 8px;"></i>
                                Edit Data Alat Elektromedis
                            </h2>
                            <p style="font-size: 0.85rem; color: #64748b; margin-top: 2px;">
                                Mengubah informasi alat: <strong><?php echo htmlspecialchars($user_data['nama_alat']); ?></strong>
                            </p>
                        </div>
                        <span class="badge badge-year">ID: #<?php echo $user_data['id']; ?></span>
                    </div>

                    <div class="card-body">
                        <form name="update_user" method="post" action="edit.php">
                            
                            <input type="hidden" name="id" value="<?php echo $user_data['id']; ?>">

                            <div class="form-group">
                                <label class="form-label" for="nama_alat">
                                    Nama Alat <span class="required">*</span>
                                </label>
                                <input type="text" id="nama_alat" name="nama_alat" class="form-control"
                                       value="<?php echo htmlspecialchars($user_data['nama_alat']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="tahun">
                                    Tahun Pengadaan <span class="required">*</span>
                                </label>
                                <input type="number" id="tahun" name="tahun" class="form-control"
                                       min="1901" max="2155"
                                       value="<?php echo htmlspecialchars($user_data['tahun']); ?>" required>
                                <div class="form-helper">Rentang tahun pengadaan antara 1901 hingga 2155.</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="merek">
                                    Merek / Pabrikan <span class="required">*</span>
                                </label>
                                <input type="text" id="merek" name="merek" class="form-control"
                                       value="<?php echo htmlspecialchars($user_data['merek']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="lokasi">
                                    Lokasi Penempatan <span class="required">*</span>
                                </label>
                                <input type="text" id="lokasi" name="lokasi" class="form-control"
                                       value="<?php echo htmlspecialchars($user_data['lokasi']); ?>" required>
                            </div>

                            <div class="form-actions">
                                <a href="index.php" class="btn btn-secondary">
                                    Batal
                                </a>
                                <button type="submit" name="update" class="btn btn-primary">
                                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            <?php endif; ?>

        </div>

    </main>

</body>
</html>
