<?php

// Menghubungkan ke database
include_once("config.php");

// Mengecek apakah tombol Update ditekan
if (isset($_POST['update'])) {

    $id = (int) $_POST['id'];
    $nama_alat = $_POST['nama_alat'];
    $tahun = $_POST['tahun'];
    $merek = $_POST['merek'];
    $lokasi = $_POST['lokasi'];

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

// Mengecek apakah data ditemukan
if (!$user_data) {
    die("Data alat tidak ditemukan!");
}

?>

<html>
<head>
    <title>Edit Data Alat</title>
</head>

<body>

    <a href="index.php">Home</a>
    <br><br>

    <h3>Edit Data Alat Elektromedis</h3>

    <form name="update_user" method="post" action="edit.php">

        <table border="0">

            <tr>
                <td>Nama Alat</td>
                <td>
                    <input type="text" name="nama_alat"
                    value="<?php echo htmlspecialchars($user_data['nama_alat']); ?>" required>
                </td>
            </tr>

            <tr>
                <td>Tahun</td>
                <td>
                    <input type="number" name="tahun"
                    value="<?php echo htmlspecialchars($user_data['tahun']); ?>" required>
                </td>
            </tr>

            <tr>
                <td>Merek</td>
                <td>
                    <input type="text" name="merek"
                    value="<?php echo htmlspecialchars($user_data['merek']); ?>" required>
                </td>
            </tr>

            <tr>
                <td>Lokasi</td>
                <td>
                    <input type="text" name="lokasi"
                    value="<?php echo htmlspecialchars($user_data['lokasi']); ?>" required>
                </td>
            </tr>

            <tr>
                <td>
                    <input type="hidden" name="id"
                    value="<?php echo $user_data['id']; ?>">
                </td>

                <td>
                    <input type="submit" name="update" value="Update">
                </td>
            </tr>

        </table>

    </form>

</body>
</html>