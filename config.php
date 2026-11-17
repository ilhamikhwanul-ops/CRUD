<?php

$databaseHost = 'localhost';
$databaseName = 'simrs';
$databaseUsername = 'root';
$databasePassword = '';

$mysqli = mysqli_connect(
    $databaseHost,
    $databaseUsername,
    $databasePassword,
    $databaseName
);

if (!$mysqli) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Otomatis membuat tabel users jika belum ada
$createUsersTable = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($mysqli, $createUsersTable);

?>
