<?php

// Menghubungkan ke database
include_once("config.php");

// Mengambil ID dari URL
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Mengecek apakah ID tersedia
if ($id > 0) {

    // Menghapus data alat berdasarkan ID
    $stmt = mysqli_prepare(
        $mysqli,
        "DELETE FROM alat WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);

    // Menjalankan perintah hapus
    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
}

// Kembali ke halaman utama
header("Location: index.php");
exit;

?>