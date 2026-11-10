<html>
<head>
    <title>Add Alat</title>
</head>

<body>
    <a href="index.php">Go to Home</a>
    <br><br>

    <p>Belajar Pemrograman Web</p>

    <form action="add.php" method="post" name="form1">
        <table width="25%" border="0">
            <tr>
                <td>Nama Alat</td>
                <td><input type="text" name="nama_alat" required></td>
            </tr>

            <tr>
                <td>Tahun</td>
                <td><input type="number" name="tahun"
                    min="1901" max="2155" required></td>
            </tr>

            <tr>
                <td>Merek</td>
                <td><input type="text" name="merek" required></td>
            </tr>

            <tr>
                <td>Lokasi</td>
                <td><input type="text" name="lokasi" required></td>
            </tr>

            <tr>
                <td></td>
                <td>
                    <input type="submit" name="Submit" value="Add">
                </td>
            </tr>
        </table>
    </form>

    <?php
    if (isset($_POST['Submit'])) {

        $nama_alat = $_POST['nama_alat'];
        $tahun = $_POST['tahun'];
        $merek = $_POST['merek'];
        $lokasi = $_POST['lokasi'];

        // Koneksi database
        include_once("config.php");

        // Memasukkan data ke tabel alat
        $stmt = mysqli_prepare(
            $mysqli,
            "INSERT INTO alat (nama_alat, tahun, merek, lokasi)
             VALUES (?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssss",
            $nama_alat,
            $tahun,
            $merek,
            $lokasi
        );

        if (mysqli_stmt_execute($stmt)) {
            echo "Data alat berhasil ditambahkan.
            <a href='index.php'>View Alat</a>";
        } else {
            echo "Gagal menambahkan data: " .
                 mysqli_error($mysqli);
        }

        mysqli_stmt_close($stmt);
    }
    ?>

</body>
</html>