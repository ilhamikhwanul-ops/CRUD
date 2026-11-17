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

$nama_petugas = !empty($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : $_SESSION['username'];
$code_id = sprintf("#EM-%03d", $id);
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
                <img src="assets/images/logo-simrs.svg" alt="Logo SIM RS" class="brand-logo-img">
                <div class="brand-text">
                    <h1>SIM RS Elektromedis <span class="brand-tag">v2.0</span></h1>
                    <span>Sistem Manajemen & Inventarisasi Alat Medis</span>
                </div>
            </a>
            <div class="navbar-user-group">
                <span class="user-badge" title="Petugas yang Sedang Login">
                    <img src="elegant/img/avatar/avatar-illustrated-02.png" alt="Avatar Petugas" class="user-avatar-img">
                    <?php echo htmlspecialchars($nama_petugas); ?>
                </span>
                <a href="index.php" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left"></i> Home
                </a>
                <a href="logout.php" class="btn btn-action-delete btn-sm" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')" title="Keluar">
                    <i class="fa-solid fa-right-from-bracket"></i> Keluar
                </a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="container">
        
        <a href="index.php" class="nav-back-link">
            <i class="fa-solid fa-chevron-left"></i> Kembali ke Dashboard Inventaris
        </a>

        <?php if (!$user_data): ?>
            <div class="card" style="text-align: center; padding: 56px 24px; max-width: 580px; margin: 40px auto;">
                <div class="empty-icon" style="background: #fef2f2; color: #ef4444; border-color: #fecaca;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h3 style="font-size: 1.35rem; color: #0f172a; margin-bottom: 8px;">Data Alat Tidak Ditemukan</h3>
                <p style="color: #64748b; margin-bottom: 24px;">Data alat elektromedis dengan ID yang dicari tidak ada dalam basis data atau telah dihapus.</p>
                <a href="index.php" class="btn btn-primary">
                    <i class="fa-solid fa-house"></i> Kembali ke Beranda
                </a>
            </div>
        <?php else: ?>

            <div class="form-layout-grid">

                <!-- Form Input Card -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title-wrap">
                            <div class="card-title-icon">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </div>
                            <div>
                                <h3>Perbarui Informasi Alat Elektromedis</h3>
                                <p>Mengubah rincian data untuk nomor aset: <strong><?php echo $code_id; ?></strong></p>
                            </div>
                        </div>
                        <span class="badge badge-year"><?php echo $code_id; ?></span>
                    </div>

                    <div class="card-body" style="padding: 28px;">
                        <form name="update_user" method="post" action="edit.php" autocomplete="off">
                            
                            <input type="hidden" name="id" value="<?php echo $user_data['id']; ?>">

                            <div class="form-group">
                                <label class="form-label" for="nama_alat">
                                    Nama Peralatan Medis <span class="required">*</span>
                                </label>
                                <input type="text" id="nama_alat" name="nama_alat" class="form-control"
                                       value="<?php echo htmlspecialchars($user_data['nama_alat']); ?>" 
                                       oninput="updatePreview()" required autofocus>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="tahun">
                                    Tahun Pengadaan <span class="required">*</span>
                                </label>
                                <input type="number" id="tahun" name="tahun" class="form-control"
                                       min="1901" max="2155"
                                       value="<?php echo htmlspecialchars($user_data['tahun']); ?>" 
                                       oninput="updatePreview()" required>
                                <div class="form-helper">Rentang tahun pengadaan antara 1901 hingga 2155.</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="merek">
                                    Merek / Pabrikan (Vendor) <span class="required">*</span>
                                </label>
                                <input type="text" id="merek" name="merek" class="form-control" list="list-merek"
                                       value="<?php echo htmlspecialchars($user_data['merek']); ?>" 
                                       oninput="updatePreview()" required>
                                <datalist id="list-merek">
                                    <option value="GE Healthcare"></option>
                                    <option value="Philips Healthcare"></option>
                                    <option value="Mindray"></option>
                                    <option value="Siemens Healthineers"></option>
                                    <option value="Dräger"></option>
                                    <option value="Nihon Kohden"></option>
                                    <option value="Canon Medical Systems"></option>
                                    <option value="Olympus Medical"></option>
                                    <option value="B. Braun"></option>
                                    <option value="Medtronic"></option>
                                </datalist>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="lokasi">
                                    Lokasi Penempatan / Ruangan RS <span class="required">*</span>
                                </label>
                                <input type="text" id="lokasi" name="lokasi" class="form-control" list="list-lokasi"
                                       value="<?php echo htmlspecialchars($user_data['lokasi']); ?>" 
                                       oninput="updatePreview()" required>
                                <datalist id="list-lokasi">
                                    <option value="Ruang ICU (Intensive Care)"></option>
                                    <option value="IGD (Gawat Darurat)"></option>
                                    <option value="Ruang Operasi (OK Sentral)"></option>
                                    <option value="Radiologi & CT Scan"></option>
                                    <option value="Poli Jantung & Kardiovaskular"></option>
                                    <option value="Laboratorium Patologi"></option>
                                    <option value="Ruang Hemodialisa"></option>
                                    <option value="Ruang Rawat Inap Melati Lt. 3"></option>
                                    <option value="Instalasi Pemeliharaan Elektromedis"></option>
                                </datalist>
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

                <!-- Live Asset Tag Preview Card -->
                <div class="preview-sticky">
                    <div class="asset-tag-card">
                        <div class="asset-tag-header">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="assets/images/logo-simrs.svg" alt="Logo SIM RS" style="width: 34px; height: 34px; filter: drop-shadow(0 2px 6px rgba(0,0,0,0.3));">
                                <div>
                                    <div class="asset-tag-title">SIM RS &bull; Asset Tag Preview</div>
                                    <div class="asset-tag-code"><?php echo $code_id; ?></div>
                                </div>
                            </div>
                            <div class="asset-qr-mock" title="Barcode Digital Asset RS">
                                <i class="fa-solid fa-qrcode"></i>
                            </div>
                        </div>

                        <div class="asset-preview-name" id="previewName">
                            <?php echo htmlspecialchars($user_data['nama_alat']); ?>
                        </div>

                        <div class="asset-preview-details">
                            <div class="preview-detail-item">
                                <div class="preview-detail-label"><i class="fa-regular fa-calendar"></i> Tahun</div>
                                <div class="preview-detail-val" id="previewYear">
                                    <?php echo htmlspecialchars($user_data['tahun']); ?>
                                </div>
                            </div>
                            <div class="preview-detail-item">
                                <div class="preview-detail-label"><i class="fa-solid fa-industry"></i> Merek</div>
                                <div class="preview-detail-val" id="previewBrand">
                                    <?php echo htmlspecialchars($user_data['merek']); ?>
                                </div>
                            </div>
                            <div class="preview-detail-item" style="grid-column: span 2;">
                                <div class="preview-detail-label"><i class="fa-solid fa-location-dot"></i> Lokasi Penempatan</div>
                                <div class="preview-detail-val" id="previewLocation">
                                    <?php echo htmlspecialchars($user_data['lokasi']); ?>
                                </div>
                            </div>
                        </div>

                        <div class="asset-tag-footer">
                            <span class="asset-status-badge">
                                <span class="pulse-dot"></span> Status: Aktif & Terkalibrasi
                            </span>
                            <span>Unit Elektromedik RS</span>
                        </div>
                    </div>

                    <div style="margin-top: 18px; padding: 16px 20px; background: #ffffff; border: 1px solid var(--border-color); border-radius: var(--radius-lg); font-size: 0.85rem; color: #64748b; box-shadow: var(--shadow-subtle);">
                        <div style="font-weight: 700; color: #0f172a; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-circle-check" style="color: #10b981;"></i> Sinkronisasi Otomatis
                        </div>
                        Setiap perubahan nilai input pada formulir akan diperbarui secara langsung pada tanda pengenal aset (*Asset Tag*) di atas.
                    </div>
                </div>

            </div>

        <?php endif; ?>

    </main>

    <!-- Real-time Preview Script -->
    <script>
        function updatePreview() {
            const nama = document.getElementById('nama_alat').value.trim();
            const tahun = document.getElementById('tahun').value.trim();
            const merek = document.getElementById('merek').value.trim();
            const lokasi = document.getElementById('lokasi').value.trim();

            document.getElementById('previewName').innerText = nama || '[Nama Alat Medis]';
            document.getElementById('previewYear').innerText = tahun || '-';
            document.getElementById('previewBrand').innerText = merek || '-';
            document.getElementById('previewLocation').innerText = lokasi || '-';
        }
    </script>

</body>
</html>
