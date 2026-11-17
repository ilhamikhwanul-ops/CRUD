<?php
// Proteksi autentikasi
include_once("auth.php");

// Menghubungkan ke database
include_once("config.php");

// Mengambil seluruh data alat
$result = mysqli_query(
    $mysqli,
    "SELECT * FROM alat ORDER BY id DESC"
);

$total_rows = mysqli_num_rows($result);

// Query Statistik
$stat_merek = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(DISTINCT merek) AS total FROM alat"));
$total_merek = $stat_merek['total'] ?? 0;

$stat_lokasi = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(DISTINCT lokasi) AS total FROM alat"));
$total_lokasi = $stat_lokasi['total'] ?? 0;

$stat_tahun = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT MAX(tahun) AS latest_year FROM alat"));
$latest_year = $stat_tahun['latest_year'] ?? '-';

// Mengambil daftar lokasi unik untuk filter
$lokasi_list_query = mysqli_query($mysqli, "SELECT DISTINCT lokasi FROM alat WHERE lokasi != '' ORDER BY lokasi ASC");

// Nama Petugas
$nama_petugas = !empty($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : $_SESSION['username'];

// Fungsi Helper Ikon Khusus Peralatan Elektromedik
function getDeviceIcon($nama) {
    $nama = strtolower($nama);
    if (strpos($nama, 'usg') !== false) return 'fa-solid fa-radiation';
    if (strpos($nama, 'ekg') !== false || strpos($nama, 'ecg') !== false) return 'fa-solid fa-heart-pulse';
    if (strpos($nama, 'defibrillator') !== false) return 'fa-solid fa-bolt';
    if (strpos($nama, 'ventilator') !== false) return 'fa-solid fa-lungs';
    if (strpos($nama, 'x-ray') !== false || strpos($nama, 'rontgen') !== false) return 'fa-solid fa-bone';
    if (strpos($nama, 'suction') !== false) return 'fa-solid fa-pump-medical';
    if (strpos($nama, 'syringe') !== false || strpos($nama, 'infus') !== false) return 'fa-solid fa-syringe';
    if (strpos($nama, 'incubator') !== false || strpos($nama, 'inkubator') !== false) return 'fa-solid fa-baby';
    if (strpos($nama, 'steril') !== false || strpos($nama, 'autoclave') !== false) return 'fa-solid fa-soap';
    if (strpos($nama, 'laboratorium') !== false || strpos($nama, 'mikroskop') !== false) return 'fa-solid fa-microscope';
    return 'fa-solid fa-kit-medical';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIM RS - Data Alat Elektromedis</title>
    <!-- Google Fonts & Font Awesome Icons -->
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
                <a href="add.php" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus"></i> Tambah Alat
                </a>
                <a href="logout.php" class="btn btn-action-delete btn-sm" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')" title="Keluar">
                    <i class="fa-solid fa-right-from-bracket"></i> Keluar
                </a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="container">
        
        <!-- Hero Welcome Banner -->
        <div class="hero-banner">
            <div class="hero-content" style="flex: 1; min-width: 300px;">
                <h2>
                    Halo, <?php echo htmlspecialchars($nama_petugas); ?> 👋
                </h2>
                <p>
                    Selamat datang di Portal Manajemen Alat Elektromedis. Pantau kesiapan teknis, jadwal pengadaan, dan persebaran perangkat rumah sakit secara efisien dan akurat.
                </p>
                <div class="hero-meta">
                    <div class="hero-meta-item">
                        <i class="fa-solid fa-calendar-day"></i>
                        <span><?php echo date('d M Y'); ?></span>
                    </div>
                    <div class="hero-meta-item">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Sistem Aktif & Terlindungi</span>
                    </div>
                    <div class="hero-meta-item">
                        <i class="fa-solid fa-hospital"></i>
                        <span>SIM RS Unit Elektromedik</span>
                    </div>
                </div>

                <div class="hero-actions" style="margin-top: 24px;">
                    <a href="add.php" class="btn btn-primary">
                        <i class="fa-solid fa-circle-plus"></i> Tambah Alat Baru
                    </a>
                    <button onclick="window.print()" class="btn btn-secondary">
                        <i class="fa-solid fa-print"></i> Cetak Laporan
                    </button>
                </div>
            </div>

            <!-- Hero Medical Tech Illustration -->
            <div class="hero-visual">
                <img src="assets/images/hero-medical.svg" alt="Medical Monitor Dashboard Illustration" style="width: 100%; height: auto;">
            </div>
        </div>

        <!-- Metric Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Inventaris</div>
                    <div class="stat-value"><?php echo $total_rows; ?></div>
                    <div class="stat-sub">
                        <span style="color: #10b981; font-weight: 600;"><i class="fa-solid fa-check-circle"></i> Siap Pakai</span>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fa-solid fa-industry"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Pabrikan / Merek</div>
                    <div class="stat-value"><?php echo $total_merek; ?></div>
                    <div class="stat-sub">
                        <span>Produsen Terdaftar</span>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon emerald">
                    <i class="fa-solid fa-hospital-user"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Area / Ruangan RS</div>
                    <div class="stat-value"><?php echo $total_lokasi; ?></div>
                    <div class="stat-sub">
                        <span>Lokasi Penempatan</span>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon amber">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Pengadaan Terbaru</div>
                    <div class="stat-value"><?php echo htmlspecialchars($latest_year); ?></div>
                    <div class="stat-sub">
                        <span>Tahun Registrasi Terkini</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card">
            
            <div class="card-header">
                <div class="card-title-wrap">
                    <div class="card-title-icon">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <div>
                        <h3>Daftar Inventaris Alat Elektromedis</h3>
                        <p>Daftar lengkap perangkat medis, nomor registrasi, merek, dan lokasi penempatan.</p>
                    </div>
                </div>
                <span class="badge badge-year" id="rowCountBadge">Total: <?php echo $total_rows; ?> Alat Terdaftar</span>
            </div>

            <!-- Table Toolbar (Live Search & Filter) -->
            <div class="table-toolbar">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="searchInput" class="search-input" 
                           placeholder="Cari nama alat, merek, atau lokasi penempatan..." 
                           onkeyup="filterTable()">
                </div>

                <div class="toolbar-actions">
                    <select id="locationFilter" class="select-filter" onchange="filterTable()">
                        <option value="">Semua Lokasi / Ruangan</option>
                        <?php 
                        if ($lokasi_list_query && mysqli_num_rows($lokasi_list_query) > 0) {
                            while ($loc = mysqli_fetch_assoc($lokasi_list_query)) {
                                echo '<option value="'.htmlspecialchars($loc['lokasi']).'">'.htmlspecialchars($loc['lokasi']).'</option>';
                            }
                        }
                        ?>
                    </select>

                    <button onclick="resetFilters()" class="btn btn-secondary btn-sm" title="Reset Pencarian & Filter">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </button>
                </div>
            </div>

            <!-- Table Content -->
            <div class="table-responsive">
                <?php if ($total_rows > 0): ?>
                    <table class="table-elegant" id="deviceTable">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Alat & Status</th>
                                <th>Tahun</th>
                                <th>Merek</th>
                                <th>Lokasi Penempatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            while ($user_data = mysqli_fetch_assoc($result)) {
                                $code_id = sprintf("#EM-%03d", $user_data['id']);
                            ?>
                                <tr>
                                    <td>
                                        <span style="font-family: var(--font-mono); font-weight: 700; color: #2563eb;">
                                            <?php echo $code_id; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="device-name-cell">
                                            <div class="device-avatar">
                                                <i class="<?php echo getDeviceIcon($user_data['nama_alat']); ?>"></i>
                                            </div>
                                            <div>
                                                <div class="device-title-bold">
                                                    <?php echo htmlspecialchars($user_data['nama_alat']); ?>
                                                </div>
                                                <div class="device-status-indicator">
                                                    <span class="pulse-dot"></span>
                                                    <span>Kondisi Normal / Siap Pakai</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-year">
                                            <i class="fa-regular fa-calendar"></i>
                                            <?php echo htmlspecialchars($user_data['tahun']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-brand">
                                            <i class="fa-solid fa-tag"></i>
                                            <?php echo htmlspecialchars($user_data['merek']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-location">
                                            <i class="fa-solid fa-location-dot"></i>
                                            <?php echo htmlspecialchars($user_data['lokasi']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                            <a href="edit.php?id=<?php echo $user_data['id']; ?>" class="btn-action-edit" title="Perbarui Data Alat">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </a>
                                            <a href="delete.php?id=<?php echo $user_data['id']; ?>" 
                                               class="btn-action-delete" 
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus data alat <?php echo htmlspecialchars(addslashes($user_data['nama_alat'])); ?>?')" 
                                               title="Hapus Data Alat">
                                                <i class="fa-solid fa-trash-can"></i> Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>

                    <div id="noMatchMessage" class="empty-state" style="display: none; padding: 40px 20px;">
                        <div class="empty-icon">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </div>
                        <h4 class="empty-title">Tidak Ada Data yang Cocok</h4>
                        <p class="empty-text">Tidak ditemukan alat medis yang sesuai dengan kata kunci atau filter lokasi yang dipilih.</p>
                        <button onclick="resetFilters()" class="btn btn-secondary btn-sm">
                            <i class="fa-solid fa-rotate-left"></i> Bersihkan Filter
                        </button>
                    </div>

                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fa-solid fa-folder-open"></i>
                        </div>
                        <h3 class="empty-title">Belum Ada Data Alat</h3>
                        <p class="empty-text">Daftar inventaris saat ini masih kosong. Silakan tambahkan data alat elektromedis pertama Anda.</p>
                        <a href="add.php" class="btn btn-primary">
                            <i class="fa-solid fa-plus"></i> Tambah Alat Sekarang
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="table-footer">
                <div>
                    <i class="fa-solid fa-circle-info" style="color: #2563eb; margin-right: 6px;"></i>
                    Data disinkronkan secara langsung dengan basis data SIM RS.
                </div>
                <div style="font-weight: 600;">
                    Status: <span style="color: #10b981;">Online & Terkoneksi</span>
                </div>
            </div>

        </div>

    </main>

    <!-- Client-side Interactive Filter Script -->
    <script>
        function filterTable() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase().trim();
            const locationFilter = document.getElementById('locationFilter').value.toLowerCase().trim();
            const table = document.getElementById('deviceTable');
            if (!table) return;

            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            let visibleCount = 0;

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const text = row.innerText.toLowerCase();
                const locationCell = row.cells[4] ? row.cells[4].innerText.toLowerCase() : '';

                const matchesSearch = text.includes(searchInput);
                const matchesLocation = locationFilter === '' || locationCell.includes(locationFilter);

                if (matchesSearch && matchesLocation) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            }

            // Update badge & empty state
            const badge = document.getElementById('rowCountBadge');
            if (badge) {
                badge.innerText = 'Menampilkan ' + visibleCount + ' Alat';
            }

            const noMatch = document.getElementById('noMatchMessage');
            if (noMatch) {
                noMatch.style.display = (visibleCount === 0) ? 'block' : 'none';
            }
        }

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('locationFilter').value = '';
            filterTable();
        }
    </script>

</body>
</html>
