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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIM RS - Data Alat Elektromedis</title>
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
        
        <div class="page-header">
            <div class="page-title-group">
                <h2>Data Alat Elektromedis</h2>
                <p>Kelola dan pantau seluruh inventaris peralatan medis secara rapi dan terorganisir.</p>
            </div>
            <div>
                <a href="add.php" class="btn btn-primary">
                    <i class="fa-solid fa-circle-plus"></i> Tambah Alat Baru
                </a>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card">
            <div class="card-header">
                <div style="font-weight: 700; color: #1e293b; font-size: 1rem;">
                    <i class="fa-solid fa-list-check" style="color: #2563eb; margin-right: 8px;"></i>
                    Daftar Inventaris Alat
                </div>
                <span class="badge badge-year">Total: <?php echo $total_rows; ?> Alat</span>
            </div>

            <div class="table-responsive">
                <?php if ($total_rows > 0): ?>
                    <table class="table-elegant">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Alat</th>
                                <th>Tahun</th>
                                <th>Merek</th>
                                <th>Lokasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            while ($user_data = mysqli_fetch_assoc($result)) {
                            ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td>
                                        <div style="font-weight: 600; color: #0f172a;">
                                            <i class="fa-solid fa-kit-medical" style="color: #3b82f6; margin-right: 8px; font-size: 0.85rem;"></i>
                                            <?php echo htmlspecialchars($user_data['nama_alat']); ?>
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
                                            <a href="edit.php?id=<?php echo $user_data['id']; ?>" class="btn-action-edit" title="Edit Alat">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </a>
                                            <a href="delete.php?id=<?php echo $user_data['id']; ?>" 
                                               class="btn-action-delete" 
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus alat ini?')" 
                                               title="Hapus Alat">
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
        </div>

    </main>

</body>
</html>
